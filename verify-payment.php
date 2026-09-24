<?php
include_once("include/config.php");
include_once("include/razorpay_config.php");

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

use Razorpay\Api\Errors\SignatureVerificationError;

header('Content-Type: application/json');

$order_id   = (int) ($_POST['db_order_id'] ?? 0);
$rzp_id     = $_POST['razorpay_payment_id'] ?? '';
$rzp_order  = $_POST['razorpay_order_id'] ?? '';
$signature  = $_POST['razorpay_signature'] ?? '';

if (!$order_id || !$rzp_id || !$rzp_order || !$signature) {
    echo json_encode(['success' => false, 'message' => 'Missing critical signature verification metadata.']);
    exit;
}

$api = getRazorpayApi();

try {
    // Verify signature securely
    $api->utility->verifyPaymentSignature([
        'razorpay_order_id'   => $rzp_order,
        'razorpay_payment_id' => $rzp_id,
        'razorpay_signature'  => $signature
    ]);

    $db = connect();

    // Match actual tbl_orders columns
    $order_stmt = $db->select(
        "SELECT order_id, first_name, last_name, street_address, city, postcode, phone,
                payment_method, subtotal, gst_amount, grand_total
         FROM tbl_orders
         WHERE order_id = ?",
        'i', $order_id
    );
    $orderData = $order_stmt->fetch_assoc();
    $order_stmt->close();

    if (empty($orderData)) {
        echo json_encode(['success' => false, 'message' => 'Order trace not found.']);
        exit;
    }

    // Item count / description for Delhivery products_desc field
    $items_stmt = $db->select(
        "SELECT COUNT(*) AS item_count FROM tbl_order_items WHERE order_id = ?",
        'i', $order_id
    );
    $itemsRow   = $items_stmt->fetch_assoc();
    $item_count = (int) ($itemsRow['item_count'] ?? 1);
    $items_stmt->close();

    $payment_method = $orderData['payment_method'];

    if ($payment_method === 'cod') {
        $cod_includes_gst = defined('_COD_INCLUDES_GST_') ? (bool)_COD_INCLUDES_GST_ : false;
        $order_status   = 'success';
        $payment_status = 'partial_paid';
        if ($cod_includes_gst) {
            $collect_amt = (float) $orderData['subtotal'];
        } else {
            $collect_amt = (float) $orderData['subtotal'] + (float) ($orderData['gst_amount'] ?? 0);
        }
        $paid_amount = max(0.0, round((float)$orderData['grand_total'] - $collect_amt, 2));
        $delhivery_mode = 'COD';
    } else {
        $order_status   = 'success';
        $payment_status = 'paid';
        $collect_amt    = (float) $orderData['grand_total'];
        $paid_amount    = (float) $orderData['grand_total'];
        $delhivery_mode = 'Prepaid';
    }

    $db->update(
        "UPDATE tbl_orders SET payment_status = ?, order_status = ?, paid_amount = ?, razorpay_payment_id = ?, razorpay_order_id = ? WHERE order_id = ?",
        'ssdssi',
        $payment_status,
        $order_status,
        $paid_amount,
        $rzp_id,
        $rzp_order,
        $order_id
    );

    // Deduct product stock now that payment is confirmed
    try {
        deduct_order_stock($order_id);
    } catch (\Throwable $e) {
        error_log("Stock deduction error for order {$order_id}: " . $e->getMessage());
    }

    // --- Fire Courier Shipment now that payment is confirmed ---
    if (function_exists('dispatchOrderById')) {
        try {
            $disp_res = dispatchOrderById($order_id);
            if (!$disp_res['success']) {
                error_log("Courier shipment creation failed for order {$order_id}: " . json_encode($disp_res));
            }
        } catch (\Throwable $e) {
            error_log("Courier shipment exception for order {$order_id}: " . $e->getMessage());
        }
    }

    // --- Fire Order Confirmation SMS ---
    if (function_exists('send_order_placed_sms_by_id')) {
        try {
            send_order_placed_sms_by_id($order_id);
        } catch (\Throwable $e) {
            error_log("Order SMS exception for order {$order_id}: " . $e->getMessage());
        }
    }

    // Clear user shopping cart contents completely
    $cart_session = get_cart_session();
    $db->delete("DELETE FROM tbl_cart_items WHERE cart_session = ?", 's', $cart_session);
    $db->close();

    unset($_SESSION['checkout_products'], $_SESSION['checkout_summary'], $_SESSION['rzp_order_id'], $_SESSION['rzp_amount']);

    echo json_encode(['success' => true]);
    exit;

} catch (SignatureVerificationError $e) {
    echo json_encode(['success' => false, 'message' => 'Signature mismatch: ' . $e->getMessage()]);
    exit;
}