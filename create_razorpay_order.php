<?php
include_once("include/config.php");
include_once("include/razorpay_config.php");

header('Content-Type: application/json');

// Auto-restore summary from database cart items if session was cleared or expired
if (function_exists('restore_checkout_session')) {
    restore_checkout_session();
}

if (empty($_SESSION['checkout_summary'])) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty or session expired.']);
    exit;
}

// Read incoming payment method sent from JS
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'razorpay';

$enable_cod = defined('_ENABLE_COD_') ? (bool)_ENABLE_COD_ : true;
$enable_razorpay = defined('_ENABLE_RAZORPAY_') ? (bool)_ENABLE_RAZORPAY_ : true;
$cod_includes_gst = defined('_COD_INCLUDES_GST_') ? (bool)_COD_INCLUDES_GST_ : true;

if ($payment_method === 'cod') {
    if (!$enable_cod) {
        echo json_encode(['success' => false, 'message' => 'Cash on Delivery is currently disabled.']);
        exit;
    }
    if (!isset($_SESSION['checkout_summary']['shipping'])) {
        echo json_encode(['success' => false, 'message' => 'Shipping charge missing from session.']);
        exit;
    }
    $shipping = (float) $_SESSION['checkout_summary']['shipping'];
    $gst = $cod_includes_gst ? (float) ($_SESSION['checkout_summary']['gst'] ?? 0) : 0;
    $amount = $shipping + $gst;
} else {
    if (!$enable_razorpay) {
        echo json_encode(['success' => false, 'message' => 'Online payment is currently disabled.']);
        exit;
    }
    if (!isset($_SESSION['checkout_summary']['grand_total'])) {
        echo json_encode(['success' => false, 'message' => 'Grand total missing from session.']);
        exit;
    }
    $amount = (float) $_SESSION['checkout_summary']['grand_total'];
}

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order amount.']);
    exit;
}

$amount_paise = (int) round($amount * 100);
$db_order_id = isset($_POST['db_order_id']) ? (int)$_POST['db_order_id'] : (isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0);

try {
    $api = getRazorpayApi();

    $orderPayload = [
        'receipt'         => $db_order_id ? ('rcpt_' . $db_order_id) : ('rcpt_' . time() . '_' . rand(1000, 9999)),
        'amount'          => $amount_paise,
        'currency'        => 'INR',
        'payment_capture' => 1
    ];

    if ($db_order_id > 0) {
        $orderPayload['notes'] = [
            'order_id'    => $db_order_id,
            'db_order_id' => $db_order_id
        ];
    }

    $razorpayOrder = $api->order->create($orderPayload);

    // Store in session for verification later
    $_SESSION['rzp_order_id'] = $razorpayOrder['id'];
    $_SESSION['rzp_amount']   = $amount_paise; // store paise

    // Save razorpay_order_id into tbl_orders immediately if db_order_id is provided
    if ($db_order_id > 0) {
        $db = connect();
        $db->update(
            "UPDATE tbl_orders SET razorpay_order_id = ? WHERE order_id = ?",
            'si',
            $razorpayOrder['id'],
            $db_order_id
        );
        $db->close();
    }

    echo json_encode([
        'success'  => true,
        'order_id' => $razorpayOrder['id'],
        'amount'   => $amount_paise,
        'key'      => RAZORPAY_KEY_ID
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}