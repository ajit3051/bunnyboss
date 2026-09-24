<?php
include_once("include/config.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (function_exists('restore_checkout_session')) {
    restore_checkout_session();
}

if (empty($_SESSION['checkout_products']) || empty($_SESSION['checkout_summary'])) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit;
}

// --- Sanitize input ---
$first_name     = trim($_POST['first_name'] ?? '');
$last_name      = trim($_POST['last_name'] ?? '');
$street_address = trim($_POST['street_address'] ?? '');
$city           = trim($_POST['city'] ?? '');
$postcode       = trim($_POST['postcode'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$ship_diff      = isset($_POST['ship_to_different']) ? 1 : 0;
$order_notes    = trim($_POST['order_notes'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? 'cod');

// --- Server-side validation ---
$errors = [];
if ($first_name === '')                          $errors[] = 'Name is required.';
if ($street_address === '')                      $errors[] = 'Street address is required.';
if ($city === '')                                $errors[] = 'City is required.';
if (!preg_match('/^[0-9]{4,10}$/', $postcode))  $errors[] = 'Valid postcode is required.';
if (!preg_match('/^[6-9][0-9]{9}$/', $phone))   $errors[] = 'Valid 10-digit phone number is required.';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Check pincode serviceability flag (1 for found, 0 for not found)
$is_pincode_serviceable = 1;
if (function_exists('checkCourierServiceability')) {
    $check_svc = checkCourierServiceability($postcode);
    $is_pincode_serviceable = (!empty($check_svc['serviceable'])) ? 1 : 0;
}

// ════════════════════════════════════════════════════════════
// INITIAL SAVE: Everything starts as Pending
// ════════════════════════════════════════════════════════════
$subtotal       = (float) ($_SESSION['checkout_summary']['subtotal']    ?? 0.00);
$shipping       = (float) ($_SESSION['checkout_summary']['shipping']    ?? 0.00);
$gst_percent_val = defined('_GST_') ? (float)_GST_ : 5.00;
$subtotal_plus_shipping = $subtotal + $shipping;
$gst_amount_val  = (float) ($_SESSION['checkout_summary']['gst'] ?? ($subtotal_plus_shipping * ($gst_percent_val / 100)));
$grand_total    = (float) ($_SESSION['checkout_summary']['grand_total'] ?? ($subtotal_plus_shipping + $gst_amount_val));

$enable_cod_online_deposit = defined('_ENABLE_COD_ONLINE_DEPOSIT_') ? (bool)_ENABLE_COD_ONLINE_DEPOSIT_ : false;
$enable_razorpay           = defined('_ENABLE_RAZORPAY_')           ? (bool)_ENABLE_RAZORPAY_           : false;
$is_direct_order           = (!$enable_razorpay) || (!$enable_cod_online_deposit) || ($payment_method === 'cod');

$order_status   = $is_direct_order ? 'success' : 'pending';
$payment_status = $is_direct_order ? ($payment_method === 'cod' ? 'pending' : 'paid') : 'pending';

$db = connect();
$current_date = date("Y-m-d H:i:s");
$user_id_val = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

$new_order_id = $db->insert(
    "INSERT INTO tbl_orders 
        (user_id, first_name, last_name, street_address, city, postcode, phone, ship_to_different, order_notes, 
         subtotal, gst_amount, grand_total, payment_method, payment_status, created_at, order_status, is_pincode_serviceable) 
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
    'issssssisdddssssi',
    $user_id_val,
    $first_name,
    $last_name,
    $street_address,
    $city,
    $postcode,
    $phone,
    $ship_diff,
    $order_notes,
    $subtotal,
    $gst_amount_val,
    $grand_total,
    $payment_method,
    $payment_status,
    $current_date,
    $order_status,
    $is_pincode_serviceable
);

if (!$new_order_id) {
    echo json_encode(['success' => false, 'message' => 'Could not create order sequence.']);
    exit;
}

// Update user's name in tbl_users table if not already populated
$full_customer_name = trim($first_name . ' ' . $last_name);
if (!empty($full_customer_name)) {
    if (!empty($_SESSION['user_id'])) {
        $db->update("UPDATE tbl_users SET name = ? WHERE id = ? AND (name IS NULL OR name = '')", 'si', $full_customer_name, $_SESSION['user_id']);
    } else if (!empty($phone)) {
        $db->update("UPDATE tbl_users SET name = ? WHERE mobile = ? AND (name IS NULL OR name = '')", 'ss', $full_customer_name, $phone);
    }
}

// Ensure schema has transaction_id & related courier columns
if (function_exists('ensure_order_items_schema')) {
    ensure_order_items_schema($db);
}

// Insert items into database
foreach ($_SESSION['checkout_products'] as $item) {
    $product_id     = isset($item['product_id'])    ? (int)   $item['product_id']    : null;
    $product_title  = isset($item['product_title']) ?         $item['product_title'] : 'Unknown Item';
    $qty            = isset($item['quantity'])      ? (int)   $item['quantity']       : 1;
    $size           = isset($item['size'])          ? (int)   $item['size']           : null;
    $price          = isset($item['unit_price'])    ? (float) $item['unit_price']     : 0.00;
    $row_total      = isset($item['row_total'])     ? (float) $item['row_total']      : 0.00;
    $item_gst_amt   = round(($row_total * $gst_percent_val) / 100, 2);
    $transaction_id = function_exists('generate_item_transaction_id') ? generate_item_transaction_id($db) : ('BB' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT));
    $is_test_item   = function_exists('is_testing_order_item') ? is_testing_order_item($item) : false;
    $disp_status    = $is_test_item ? 'skipped_test' : 'pending';
    $disp_err       = $is_test_item ? 'Testing item excluded from courier push' : null;

    $db->insert(
        "INSERT INTO tbl_order_items (order_id, product_id, product_title, qty, size, price, gst_percent, gst_amount, row_total, shipping, transaction_id, dispatch_status, dispatch_error) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        'iisiidddddsss',
        $new_order_id,
        $product_id,
        $product_title,
        $qty,
        $size,
        $price,
        $gst_percent_val,
        $item_gst_amt,
        $row_total,
        $shipping,
        $transaction_id,
        $disp_status,
        $disp_err
    );
}

// Deduct stock & dispatch order to Shadowfax immediately ONLY for Pure COD orders
$is_pure_cod = ($payment_method === 'cod') && (!$enable_cod_online_deposit || !$enable_razorpay);

if ($is_pure_cod) {
    deduct_order_stock($new_order_id);
    if (function_exists('dispatchOrderById')) {
        $disp_res = dispatchOrderById($new_order_id);
        if (!$disp_res['success']) {
            error_log("Shadowfax auto-dispatch failed for order {$new_order_id}: " . json_encode($disp_res));
        }
    }
    if (function_exists('send_order_placed_sms_by_id')) {
        send_order_placed_sms_by_id($new_order_id);
    }
}

// Pass order_id back to Javascript
echo json_encode(['success' => true, 'order_id' => $new_order_id]);
exit;
