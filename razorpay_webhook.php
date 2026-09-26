<?php
/**
 * Razorpay Webhook Handler
 * Path: /razorpay_webhook.php (and /api/razorpay_webhook.php)
 *
 * Receives POST webhook notifications from Razorpay for payment events,
 * verifies signature, updates tbl_orders payment_status & order_status,
 * logs event to tbl_razorpay_webhook_logs, and triggers auto courier dispatch.
 */

ob_start();
include_once(__DIR__ . "/include/config.php");
include_once(__DIR__ . "/include/razorpay_config.php");

// Set header
header('Content-Type: application/json; charset=UTF-8');

// Read raw POST payload and signature header
$raw_input = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

// Helper function to insert webhook log
function log_razorpay_webhook($event_type, $payment_id, $rzp_order_id, $order_id, $amount, $status, $payload, $code, $msg) {
    try {
        $db = connect();
        $db->insert(
            "INSERT INTO tbl_razorpay_webhook_logs 
                (event_type, payment_id, razorpay_order_id, order_id, amount, status, payload, response_code, response_message, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
            'sssidsisi',
            $event_type,
            $payment_id,
            $rzp_order_id,
            $order_id ? (int)$order_id : null,
            (float)$amount,
            $status,
            $payload,
            (int)$code,
            $msg
        );
        $db->close();
    } catch (Throwable $e) {
        error_log("Razorpay webhook log insert error: " . $e->getMessage());
    }
}

// 1. Verify Webhook Signature
$webhook_secret = defined('RAZORPAY_WEBHOOK_SECRET') ? RAZORPAY_WEBHOOK_SECRET : '';
if (!empty($webhook_secret)) {
    $expected_signature = hash_hmac('sha256', $raw_input, $webhook_secret);
    if (!hash_equals($expected_signature, $signature)) {
        http_response_code(400);
        log_razorpay_webhook('invalid_signature', '', '', null, 0, 'failed', $raw_input, 400, 'Invalid signature');
        echo json_encode(['success' => false, 'message' => 'Invalid signature']);
        exit;
    }
}

// Parse JSON Payload
$data = json_decode($raw_input, true) ?: [];
$event = $data['event'] ?? 'unknown';

// We only process payment.captured or payment.authorized
if ($event !== 'payment.captured' && $event !== 'payment.authorized') {
    http_response_code(200);
    log_razorpay_webhook($event, '', '', null, 0, 'ignored', $raw_input, 200, 'Ignored non-payment event');
    echo json_encode(['success' => true, 'message' => 'Ignored event: ' . $event]);
    exit;
}

$payment          = $data['payload']['payment']['entity'] ?? [];
$payment_id       = $payment['id'] ?? '';          // e.g. pay_XXXXXX
$rzp_order_id     = $payment['order_id'] ?? '';    // e.g. order_XXXXXX
$amount_in_paise   = (int) ($payment['amount'] ?? 0);
$amount           = (float) ($amount_in_paise / 100);
$status           = $payment['status'] ?? '';      // 'captured' or 'authorized'

$notes_order_id   = (int) ($payment['notes']['order_id'] ?? $payment['notes']['db_order_id'] ?? 0);
$receipt          = $payment['receipt'] ?? '';
$receipt_order_id = 0;
if (preg_match('/rcpt_(\d+)/', $receipt, $m)) {
    $receipt_order_id = (int)$m[1];
}

$db = connect();
$order = null;

// Search 1: By razorpay_order_id
if (!empty($rzp_order_id)) {
    $stmt = $db->select(
        "SELECT order_id, grand_total, payment_method, payment_status, order_status 
         FROM tbl_orders 
         WHERE razorpay_order_id = ? 
         LIMIT 1",
        's',
        $rzp_order_id
    );
    if ($stmt && $stmt->num_rows > 0) {
        $order = $stmt->fetch_assoc();
    }
    if ($stmt) $stmt->close();
}

// Search 2: Fallback by notes.order_id
if (!$order && $notes_order_id > 0) {
    $stmt = $db->select(
        "SELECT order_id, grand_total, payment_method, payment_status, order_status 
         FROM tbl_orders 
         WHERE order_id = ? 
         LIMIT 1",
        'i',
        $notes_order_id
    );
    if ($stmt && $stmt->num_rows > 0) {
        $order = $stmt->fetch_assoc();
    }
    if ($stmt) $stmt->close();
}

// Search 3: Fallback by receipt order_id
if (!$order && $receipt_order_id > 0) {
    $stmt = $db->select(
        "SELECT order_id, grand_total, payment_method, payment_status, order_status 
         FROM tbl_orders 
         WHERE order_id = ? 
         LIMIT 1",
        'i',
        $receipt_order_id
    );
    if ($stmt && $stmt->num_rows > 0) {
        $order = $stmt->fetch_assoc();
    }
    if ($stmt) $stmt->close();
}

if (!$order) {
    log_razorpay_webhook($event, $payment_id, $rzp_order_id, null, $amount, $status, $raw_input, 200, 'Order not found in database');
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Order not found in database']);
    exit;
}

$db_order_id = (int)$order['order_id'];

// Check idempotency - already paid?
if (in_array($order['payment_status'], ['paid', 'partial_paid', 'shipping_paid'])) {
    log_razorpay_webhook($event, $payment_id, $rzp_order_id, $db_order_id, $amount, $status, $raw_input, 200, 'Already paid / Idempotent duplicate');
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Order already processed']);
    exit;
}

// Determine target statuses
$is_cod = ($order['payment_method'] === 'cod');
$target_payment_status = $is_cod ? 'partial_paid' : 'paid';
$target_order_status   = 'success';
$target_paid_amt       = $amount > 0 ? $amount : ($is_cod ? max(0.0, (float)$order['grand_total'] - (float)$order['subtotal']) : (float)$order['grand_total']);

// Update order status in tbl_orders
$db->update(
    "UPDATE tbl_orders 
     SET payment_status = ?, 
         order_status = ?,
         paid_amount = ?,
         razorpay_payment_id = ?,
         razorpay_order_id = IF(razorpay_order_id IS NULL OR razorpay_order_id = '', ?, razorpay_order_id)
     WHERE order_id = ?",
    'ssdssi',
    $target_payment_status,
    $target_order_status,
    $target_paid_amt,
    $payment_id,
    $rzp_order_id,
    $db_order_id
);

// Deduct inventory stock
if (function_exists('deduct_order_stock')) {
    deduct_order_stock($db_order_id);
}

// Dispatch courier shipment
if (function_exists('dispatchOrderById')) {
    dispatchOrderById($db_order_id);
}

// Dispatch order confirmation SMS (guarded against duplicates)
if (function_exists('send_order_placed_sms_by_id')) {
    send_order_placed_sms_by_id($db_order_id);
}

$db->close();

log_razorpay_webhook($event, $payment_id, $rzp_order_id, $db_order_id, $amount, $status, $raw_input, 200, 'Order updated successfully to ' . $target_payment_status);

http_response_code(200);
echo json_encode([
    'success'  => true,
    'message'  => 'Webhook processed successfully',
    'order_id' => $db_order_id,
    'status'   => $target_payment_status
]);