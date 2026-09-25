<?php
/**
 * Orders, Checkout & Live Tracking API Endpoint
 * Path: /api/orders.php
 */
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/../include/razorpay_config.php");
require_once(__DIR__ . "/auth_helper.php");

send_api_headers();

// Validate Bearer token
$token_data = validate_api_token(false);

$raw_input = file_get_contents('php_input') ?: file_get_contents('php://input');
$json_data = json_decode($raw_input, true) ?: [];
$request = array_merge($_GET, $_POST, $json_data);

$action = $request['action'] ?? '';

$db = connect();
$cart_session = get_cart_session();
$user_id = $_SESSION['user_id'] ?? 0;
$user_mobile = $_SESSION['user_mobile'] ?? '';

// ---------------------------------------------------------
// 1. SUBMIT / PROCESS NEW ORDER
// ---------------------------------------------------------
if ($action === 'process_order') {
    if (function_exists('restore_checkout_session')) {
        restore_checkout_session();
    }

    if (empty($_SESSION['checkout_products']) || empty($_SESSION['checkout_summary'])) {
        echo json_encode(['success' => false, 'message' => 'Your cart is empty or session expired.']);
        exit;
    }

    $first_name     = trim($request['first_name'] ?? '');
    $last_name      = trim($request['last_name'] ?? '');
    $street_address = trim($request['street_address'] ?? '');
    $city           = trim($request['city'] ?? '');
    $postcode       = trim($request['postcode'] ?? '');
    $phone          = trim($request['phone'] ?? '');
    $ship_diff      = isset($request['ship_to_different']) ? 1 : 0;
    $order_notes    = trim($request['order_notes'] ?? '');
    $payment_method = trim($request['payment_method'] ?? 'cod');

    $errors = [];
    if (empty($first_name))     $errors[] = 'First name is required.';
    if (empty($street_address)) $errors[] = 'Street address is required.';
    if (empty($city))           $errors[] = 'City is required.';
    if (empty($postcode) || !preg_match('/^[0-9]{4,10}$/', $postcode))  $errors[] = 'Valid postcode is required.';
    if (empty($phone) || !preg_match('/^[6-9][0-9]{9}$/', $phone))   $errors[] = 'Valid 10-digit phone number is required.';

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
        exit;
    }

    $is_pincode_serviceable = 1;
    if (function_exists('checkCourierServiceability')) {
        $check_svc = checkCourierServiceability($postcode);
        $is_pincode_serviceable = (!empty($check_svc['serviceable'])) ? 1 : 0;
    }

    $subtotal        = (float)($_SESSION['checkout_summary']['subtotal'] ?? 0.00);
    $shipping        = (float)($_SESSION['checkout_summary']['shipping'] ?? 0.00);
    $gst_percent_val = defined('_GST_') ? (float)_GST_ : 5.00;
    $gst_amount_val  = (float)($_SESSION['checkout_summary']['gst'] ?? (($subtotal + $shipping) * ($gst_percent_val / 100)));
    $grand_total     = (float)($_SESSION['checkout_summary']['grand_total'] ?? ($subtotal + $shipping + $gst_amount_val));

    $enable_cod_online_deposit = defined('_ENABLE_COD_ONLINE_DEPOSIT_') ? (bool)_ENABLE_COD_ONLINE_DEPOSIT_ : false;
    $enable_razorpay           = defined('_ENABLE_RAZORPAY_')           ? (bool)_ENABLE_RAZORPAY_           : false;
    $requires_razorpay         = ($payment_method === 'razorpay') || 
                                  ($payment_method === 'cod' && $enable_cod_online_deposit && $enable_razorpay);
    $is_direct_order           = !$requires_razorpay;

    $order_status = $is_direct_order ? 'success' : 'pending';
    if ($payment_method === 'cod') {
        $payment_status = $enable_cod_online_deposit ? 'partial_paid' : 'cod';
    } else {
        $payment_status = $is_direct_order ? 'paid' : 'pending';
    }
    $current_date   = date("Y-m-d H:i:s");
    $user_id_val    = !empty($user_id) ? (int)$user_id : null;

    $new_order_id = $db->insert(
        "INSERT INTO tbl_orders 
            (user_id, first_name, last_name, street_address, city, postcode, phone, ship_to_different, order_notes, 
             subtotal, gst_amount, grand_total, payment_method, payment_status, created_at, order_status, is_pincode_serviceable) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        'issssssisdddssssi',
        $user_id_val, $first_name, $last_name, $street_address, $city, $postcode, $phone, $ship_diff, $order_notes,
        $subtotal, $gst_amount_val, $grand_total, $payment_method, $payment_status, $current_date, $order_status, $is_pincode_serviceable
    );

    if (!$new_order_id) {
        echo json_encode(['success' => false, 'message' => 'Could not create order in database.']);
        exit;
    }

    // Save customer name if user logged in
    $full_name = trim($first_name . ' ' . $last_name);
    if (!empty($full_name) && !empty($user_id_val)) {
        $db->update("UPDATE tbl_users SET name = ? WHERE id = ? AND (name IS NULL OR name = '')", 'si', $full_name, $user_id_val);
    }

    // Ensure schema has transaction_id & related courier columns
    if (function_exists('ensure_order_items_schema')) {
        ensure_order_items_schema($db);
    }

    // Insert order items & update variant stock
    foreach ($_SESSION['checkout_products'] as $item) {
        $product_id     = isset($item['product_id'])    ? (int)$item['product_id']    : null;
        $product_title  = isset($item['product_title']) ? $item['product_title']     : 'Product';
        $qty            = isset($item['quantity'])      ? (int)$item['quantity']       : 1;
        $size           = isset($item['size'])          ? (int)$item['size']           : null;
        $price          = isset($item['unit_price'])    ? (float)$item['unit_price']     : 0.00;
        $row_total      = isset($item['row_total'])     ? (float)$item['row_total']      : 0.00;
        $item_gst_amt   = round(($row_total * $gst_percent_val) / 100, 2);
        $transaction_id = function_exists('generate_item_transaction_id') ? generate_item_transaction_id($db) : ('BB' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT));
        $is_test_item   = function_exists('is_testing_order_item') ? is_testing_order_item($item) : false;
        $disp_status    = $is_test_item ? 'skipped_test' : 'pending';
        $disp_err       = $is_test_item ? 'Testing item excluded from courier push' : null;

        $db->insert(
            "INSERT INTO tbl_order_items (order_id, product_id, product_title, qty, size, price, gst_percent, gst_amount, row_total, shipping, transaction_id, dispatch_status, dispatch_error) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'iisiidddddsss',
            $new_order_id, $product_id, $product_title, $qty, $size, $price, $gst_percent_val, $item_gst_amt, $row_total, $shipping, $transaction_id, $disp_status, $disp_err
        );

    }

    $is_pure_cod = ($payment_method === 'cod') && (!$enable_cod_online_deposit || !$enable_razorpay);

    if ($is_pure_cod) {
        deduct_order_stock($new_order_id);
        if (function_exists('dispatchOrderById')) {
            dispatchOrderById($new_order_id);
        }
        if (function_exists('send_order_placed_sms_by_id')) {
            send_order_placed_sms_by_id($new_order_id);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Order created successfully.', 'order_id' => $new_order_id]);
    exit;
}

// ---------------------------------------------------------
// 2. CREATE RAZORPAY ORDER
// ---------------------------------------------------------
if ($action === 'create_razorpay_order') {
    if (empty($_SESSION['checkout_summary'])) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty or session expired.']);
        exit;
    }

    $payment_method = trim($request['payment_method'] ?? 'razorpay');
    $enable_cod = defined('_ENABLE_COD_') ? (bool)_ENABLE_COD_ : true;
    $enable_razorpay = defined('_ENABLE_RAZORPAY_') ? (bool)_ENABLE_RAZORPAY_ : true;
    $cod_includes_gst = defined('_COD_INCLUDES_GST_') ? (bool)_COD_INCLUDES_GST_ : true;

    if ($payment_method === 'cod') {
        if (!$enable_cod) {
            echo json_encode(['success' => false, 'message' => 'Cash on Delivery is currently disabled.']);
            exit;
        }
        $shipping = (float)($_SESSION['checkout_summary']['shipping'] ?? 100);
        $gst = $cod_includes_gst ? (float)($_SESSION['checkout_summary']['gst'] ?? 0) : 0;
        $amount = $shipping + $gst;
    } else {
        if (!$enable_razorpay) {
            echo json_encode(['success' => false, 'message' => 'Online payment is currently disabled.']);
            exit;
        }
        $amount = (float)($_SESSION['checkout_summary']['grand_total'] ?? 0);
    }

    if ($amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid order total amount.']);
        exit;
    }

    $amount_paise = (int)round($amount * 100);

    try {
        $api = getRazorpayApi();
        $razorpayOrder = $api->order->create([
            'receipt' => 'rcpt_' . time() . '_' . rand(1000, 9999),
            'amount' => $amount_paise,
            'currency' => 'INR',
            'payment_capture' => 1
        ]);

        $_SESSION['rzp_order_id'] = $razorpayOrder['id'];
        $_SESSION['rzp_amount']   = $amount_paise;

        echo json_encode([
            'success'  => true,
            'order_id' => $razorpayOrder['id'],
            'amount'   => $amount_paise,
            'key'      => RAZORPAY_KEY_ID
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ---------------------------------------------------------
// 3. VERIFY PAYMENT
// ---------------------------------------------------------
if ($action === 'verify_payment') {
    $rzp_id       = trim($request['razorpay_payment_id'] ?? '');
    $rzp_order    = trim($request['razorpay_order_id']   ?? '');
    $rzp_signature= trim($request['razorpay_signature']  ?? '');
    $order_id     = (int)($request['order_id']             ?? 0);

    if (empty($rzp_id) || empty($rzp_order) || empty($rzp_signature) || $order_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Incomplete payment parameters.']);
        exit;
    }

    // Verify signature
    $generated_signature = hash_hmac('sha256', $rzp_order . '|' . $rzp_id, RAZORPAY_KEY_SECRET);

    if ($generated_signature !== $rzp_signature) {
        echo json_encode(['success' => false, 'message' => 'Razorpay payment signature verification failed.']);
        exit;
    }

    $order_stmt = $db->select("SELECT * FROM tbl_orders WHERE order_id = ?", 'i', $order_id);
    $orderData = $order_stmt ? $order_stmt->fetch_assoc() : null;

    if (!$orderData) {
        echo json_encode(['success' => false, 'message' => 'Order not found.']);
        exit;
    }

    $payment_method = $orderData['payment_method'];

    if ($payment_method === 'cod') {
        $cod_includes_gst = defined('_COD_INCLUDES_GST_') ? (bool)_COD_INCLUDES_GST_ : false;
        $order_status   = 'success';
        $payment_status = 'partial_paid';
        $collect_amt    = $cod_includes_gst ? (float)$orderData['subtotal'] : ((float)$orderData['subtotal'] + (float)($orderData['gst_amount'] ?? 0));
        $paid_amount    = max(0.0, round((float)$orderData['grand_total'] - $collect_amt, 2));
        $courier_mode   = 'COD';
    } else {
        $order_status   = 'success';
        $payment_status = 'paid';
        $collect_amt    = (float)$orderData['grand_total'];
        $paid_amount    = (float)$orderData['grand_total'];
        $courier_mode   = 'Prepaid';
    }

    $db->update(
        "UPDATE tbl_orders SET payment_status = ?, order_status = ?, paid_amount = ?, razorpay_payment_id = ?, razorpay_order_id = ? WHERE order_id = ?",
        'ssdssi',
        $payment_status, $order_status, $paid_amount, $rzp_id, $rzp_order, $order_id
    );

    // Deduct product stock now that payment is confirmed
    deduct_order_stock($order_id);

    // Empty user cart
    $db->delete("DELETE FROM tbl_cart_items WHERE cart_session = ?", 's', $cart_session);
    unset($_SESSION['checkout_products'], $_SESSION['checkout_summary'], $_SESSION['rzp_order_id'], $_SESSION['rzp_amount']);

    // Fire Courier Integration if enabled
    $awb_number = '';
    try {
        if (function_exists('dispatchOrderById')) {
            $disp_res = dispatchOrderById($order_id);
            if ($disp_res['success']) {
                $awb_number = $disp_res['waybill'] ?? '';
            }
        }
    } catch (Exception $e) {
        error_log("API Courier creation error: " . $e->getMessage());
    }

    echo json_encode([
        'success' => true,
        'message' => 'Payment verified successfully!',
        'order_id' => $order_id,
        'payment_status' => $payment_status,
        'awb' => $awb_number
    ]);
    exit;
}

// ---------------------------------------------------------
// 4. GET MY ORDERS (USER END)
// ---------------------------------------------------------
if ($action === 'get_my_orders') {
    if (empty($user_id) && empty($user_mobile)) {
        echo json_encode(['success' => false, 'message' => 'Please log in to view your orders.']);
        exit;
    }

    $stmt = $db->select("SELECT * FROM tbl_orders WHERE user_id = ? OR phone = ? ORDER BY order_id DESC", 'is', $user_id, $user_mobile);
    $orders = [];

    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {
            $o_id = (int)$row['order_id'];
            $items_stmt = $db->select("SELECT OI.*, (SELECT image_path FROM tbl_item_images WHERE item_id = OI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture FROM tbl_order_items OI WHERE OI.order_id = ?", 'i', $o_id);
            $items = [];
            if ($items_stmt) {
                while ($i_row = $items_stmt->fetch_assoc()) {
                    $i_row['image_url'] = get_product_image_url($i_row['picture'] ?? null);
                    $items[] = $i_row;
                }
            }
            $row['items'] = $items;
            $orders[] = $row;
        }
    }

    echo json_encode(['success' => true, 'orders' => $orders]);
    exit;
}

// ---------------------------------------------------------
// 5. GET ORDER DETAILS (BY ID)
// ---------------------------------------------------------
if ($action === 'get_order_details') {
    $order_id = (int)($request['order_id'] ?? 0);
    if ($order_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
        exit;
    }

    $ord_stmt = $db->select("SELECT * FROM tbl_orders WHERE order_id = ?", 'i', $order_id);
    if (!$ord_stmt || $ord_stmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found.']);
        exit;
    }

    $order = $ord_stmt->fetch_assoc();

    $item_stmt = $db->select("SELECT OI.*, (SELECT image_path FROM tbl_item_images WHERE item_id = OI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture FROM tbl_order_items OI WHERE OI.order_id = ?", 'i', $order_id);
    $items = [];
    if ($item_stmt) {
        while ($i_row = $item_stmt->fetch_assoc()) {
            $i_row['image_url'] = get_product_image_url($i_row['picture'] ?? null);
            $items[] = $i_row;
        }
    }

    echo json_encode(['success' => true, 'order' => $order, 'items' => $items]);
    exit;
}

// ---------------------------------------------------------
// 6. LIVE TRACK ORDER
// ---------------------------------------------------------
if ($action === 'track_order') {
    $query_str = trim($request['query'] ?? $request['order_id'] ?? $request['phone'] ?? '');

    if (empty($query_str)) {
        echo json_encode(['success' => false, 'message' => 'Order ID or 10-digit Phone number is required.']);
        exit;
    }

    $stmt = $db->select("SELECT * FROM tbl_orders WHERE order_id = ? OR phone = ? ORDER BY order_id DESC LIMIT 1", 'ss', $query_str, $query_str);
    if (!$stmt || $stmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No order found with the provided details.']);
        exit;
    }

    $order = $stmt->fetch_assoc();
    $o_id = (int)$order['order_id'];

    $item_stmt = $db->select("SELECT OI.*, (SELECT image_path FROM tbl_item_images WHERE item_id = OI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture FROM tbl_order_items OI WHERE OI.order_id = ?", 'i', $o_id);
    $items = [];
    if ($item_stmt) {
        while ($i_row = $item_stmt->fetch_assoc()) {
            $i_row['image_url'] = get_product_image_url($i_row['picture'] ?? null);
            $items[] = $i_row;
        }
    }

    // Live Tracking Status from Courier if AWB present
    $tracking_data = null;
    $awb_val = !empty($order['courier_awb']) ? $order['courier_awb'] : (!empty($order['delhivery_awb']) ? $order['delhivery_awb'] : '');

    if (!empty($awb_val) && class_exists('CourierService')) {
        try {
            $courier = CourierService::getInstance();
            $tracking_res = $courier->trackShipment($awb_val, $order['courier_name'] ?? 'delhivery');
            if ($tracking_res['success']) {
                $tracking_data = $tracking_res['data'];
            }
        } catch (Exception $e) {
            // Tracking fetch error log
        }
    }

    echo json_encode([
        'success' => true,
        'order' => $order,
        'items' => $items,
        'awb' => $awb_val,
        'courier_name' => $order['courier_name'] ?? '',
        'tracking' => $tracking_data
    ]);
    exit;
}

// ---------------------------------------------------------
// 7. CANCEL ORDER
// ---------------------------------------------------------
if ($action === 'cancel_order') {
    $order_id = (int)($request['order_id'] ?? 0);
    $reason   = trim($request['reason'] ?? '');
    $comments = trim($request['comments'] ?? '');

    if ($order_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
        exit;
    }

    if (empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'Please provide a reason for cancellation.']);
        exit;
    }

    $ord_stmt = $db->select("SELECT * FROM tbl_orders WHERE order_id = ?", 'i', $order_id);
    if (!$ord_stmt || $ord_stmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found.']);
        exit;
    }

    $order = $ord_stmt->fetch_assoc();
    $current_status = strtolower(trim($order['order_status'] ?? 'pending'));

    if ($current_status === 'cancelled') {
        echo json_encode(['success' => false, 'message' => 'This order is already cancelled.']);
        exit;
    }

    if (in_array($current_status, ['delivered', 'completed', 'rto', 'returned'])) {
        echo json_encode(['success' => false, 'message' => 'Delivered orders cannot be cancelled online.']);
        exit;
    }

    $dispatch_status = strtolower(trim($order['dispatch_status'] ?? ''));
    if (in_array($current_status, ['shipped', 'dispatched']) || in_array($dispatch_status, ['shipped', 'dispatched', 'in_transit', 'out_for_delivery'])) {
        echo json_encode(['success' => false, 'message' => 'This order has already been dispatched with courier and cannot be cancelled online.']);
        exit;
    }

    $full_reason = $reason;
    if (!empty($comments)) {
        $full_reason .= ' - ' . $comments;
    }

    if (function_exists('restore_order_stock')) {
        restore_order_stock($order_id);
    }

    $updated = $db->update(
        "UPDATE tbl_orders 
         SET order_status = 'cancelled', 
             cancel_reason = ?, 
             cancelled_at = NOW(), 
             cancelled_by = 'user',
             dispatch_status = 'cancelled'
         WHERE order_id = ?",
        'si',
        $full_reason,
        $order_id
    );

    if ($updated !== false) {
        $refund_note = '';
        $pay_status = strtolower(trim($order['payment_status'] ?? ''));
        $paid_amt = (float)($order['paid_amount'] ?? 0);
        if (($pay_status === 'paid' || $pay_status === 'partial_paid') && $paid_amt > 0) {
            $refund_note = " A refund of ₹" . number_format($paid_amt, 2) . " will be initiated within 5-7 business days.";
        }

        echo json_encode([
            'success' => true,
            'message' => 'Order #' . $order_id . ' has been successfully cancelled.' . $refund_note,
            'order_id' => $order_id,
            'order_status' => 'cancelled',
            'cancel_reason' => $full_reason
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel order.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid order API action request.']);
exit;

