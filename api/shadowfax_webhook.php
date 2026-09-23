<?php
/**
 * Shadowfax (MAX360 / Dale) Webhook Endpoint Handler
 * Path: /api/shadowfax_webhook.php
 *
 * Receives POST webhook notifications from Shadowfax courier engine for order tracking,
 * status updates (Picked up, In Transit, Out for Delivery, Delivered, Cancelled, RTO),
 * logs events in `tbl_shadowfax_webhook_logs`, and updates `tbl_orders`.
 */

ob_start();
require_once(__DIR__ . "/config.php");

// Set JSON response header
header('Content-Type: application/json; charset=UTF-8');

// Get IP address of sender
$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

// Handle GET request (Health check / Verification Ping from Shadowfax portal)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(200);
    echo json_encode([
        'success'   => true,
        'status'    => 200,
        'service'   => 'Shadowfax Webhook Listener',
        'message'   => 'Webhook endpoint is active and listening for events.',
        'timestamp' => date('Y-m-d H:i:s'),
        'urls'      => [
            'staging'    => _BASEURL . 'api/shadowfax_webhook.php',
            'production' => 'https://bunnyboss.in/api/shadowfax_webhook.php'
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Read raw request payload
$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true);

// Fallback to $_POST or $_REQUEST if form-encoded
if (empty($data) && !empty($_POST)) {
    $data = $_POST;
}
if (empty($data) && !empty($_REQUEST)) {
    $data = $_REQUEST;
}

if (empty($data) && empty($raw_input)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'status'  => 400,
        'message' => 'Empty webhook payload received.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Extract fields from various Shadowfax v1/v2/v3 webhook payload schemas
$order_id = $data['client_order_id']
    ?? $data['order_id']
    ?? $data['order_details']['client_order_id']
    ?? $data['order_details']['order_id']
    ?? $data['order_number']
    ?? null;

$awb_number = $data['awb_number']
    ?? $data['awb']
    ?? $data['waybill']
    ?? $data['tracking_number']
    ?? $data['order_details']['awb_number']
    ?? null;

$raw_status = $data['status']
    ?? $data['current_status']
    ?? $data['event']
    ?? $data['shipment_status']
    ?? $data['order_details']['status']
    ?? 'update';

$status_code = $data['status_code']
    ?? $data['code']
    ?? $data['event_code']
    ?? null;

$location = $data['location']
    ?? $data['hub_name']
    ?? $data['city']
    ?? $data['current_location']
    ?? null;

$remarks = $data['remarks']
    ?? $data['reason']
    ?? $data['comment']
    ?? $data['message']
    ?? null;

// Connect to Database
$db = connect();

// Log incoming raw payload to tbl_shadowfax_webhook_logs
$order_id_clean = !empty($order_id) ? trim((string)$order_id) : null;
$awb_clean      = !empty($awb_number) ? trim((string)$awb_number) : null;
$status_clean   = !empty($raw_status) ? trim((string)$raw_status) : 'received';
$code_clean     = !empty($status_code) ? trim((string)$status_code) : null;
$loc_clean      = !empty($location) ? trim((string)$location) : null;
$rem_clean      = !empty($remarks) ? trim((string)$remarks) : null;
$json_payload   = !empty($raw_input) ? $raw_input : json_encode($data);

$db->insert(
    "INSERT INTO tbl_shadowfax_webhook_logs (order_id, awb_number, status, status_code, location, remarks, payload, ip_address, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
    'ssssssss',
    $order_id_clean,
    $awb_clean,
    $status_clean,
    $code_clean,
    $loc_clean,
    $rem_clean,
    $json_payload,
    $ip_address
);

// Match target order in tbl_orders
$orderRow = null;
if (!empty($order_id_clean)) {
    $stmt = $db->select("SELECT * FROM tbl_orders WHERE order_id = ? OR razorpay_order_id = ? LIMIT 1", 'ss', $order_id_clean, $order_id_clean);
    if ($stmt && $stmt->num_rows > 0) {
        $orderRow = $stmt->fetch_assoc();
        $stmt->close();
    }
}

if (!$orderRow && !empty($awb_clean)) {
    $stmt = $db->select("SELECT * FROM tbl_orders WHERE courier_awb = ? OR delhivery_awb = ? LIMIT 1", 'ss', $awb_clean, $awb_clean);
    if ($stmt && $stmt->num_rows > 0) {
        $orderRow = $stmt->fetch_assoc();
        $stmt->close();
    }
}

$updated = false;
$update_msg = 'Webhook logged successfully.';

if ($orderRow) {
    $target_order_id = (int)$orderRow['order_id'];
    $normalized_status = strtolower($status_clean);

    // Map Shadowfax webhook status to order status & dispatch status
    if (strpos($normalized_status, 'delivered') !== false || $code_clean === 'DL') {
        $new_dispatch = 'dispatched';
        $new_order_status = 'completed';
        
        // If order was partial_paid or cod, mark paid on delivery confirmation
        $new_payment_status = ($orderRow['payment_status'] === 'partial_paid' || strtolower($orderRow['payment_method']) === 'cod') 
            ? 'paid' 
            : $orderRow['payment_status'];

        $db->update(
            "UPDATE tbl_orders SET dispatch_status = ?, order_status = ?, payment_status = ?, courier_name = 'shadowfax', courier_awb = COALESCE(NULLIF(?, ''), courier_awb), dispatch_error = NULL WHERE order_id = ?",
            'ssssi',
            $new_dispatch,
            $new_order_status,
            $new_payment_status,
            $awb_clean,
            $target_order_id
        );
        $updated = true;
        $update_msg = "Order #{$target_order_id} marked as DELIVERED.";

    } elseif (strpos($normalized_status, 'out_for_delivery') !== false || strpos($normalized_status, 'out for delivery') !== false || $code_clean === 'OFD') {
        $new_dispatch = 'shadowfax';
        $db->update(
            "UPDATE tbl_orders SET dispatch_status = ?, courier_name = 'shadowfax', courier_awb = COALESCE(NULLIF(?, ''), courier_awb) WHERE order_id = ?",
            'ssi',
            $new_dispatch,
            $awb_clean,
            $target_order_id
        );
        $updated = true;
        $update_msg = "Order #{$target_order_id} updated to OUT FOR DELIVERY.";

    } elseif (strpos($normalized_status, 'in_transit') !== false || strpos($normalized_status, 'in transit') !== false || strpos($normalized_status, 'picked') !== false || $code_clean === 'IT' || $code_clean === 'PU') {
        $new_dispatch = 'shadowfax';
        $db->update(
            "UPDATE tbl_orders SET dispatch_status = ?, courier_name = 'shadowfax', courier_awb = COALESCE(NULLIF(?, ''), courier_awb) WHERE order_id = ?",
            'ssi',
            $new_dispatch,
            $awb_clean,
            $target_order_id
        );
        $updated = true;
        $update_msg = "Order #{$target_order_id} updated to IN TRANSIT.";

    } elseif (strpos($normalized_status, 'cancel') !== false || strpos($normalized_status, 'rto') !== false || strpos($normalized_status, 'failed') !== false || $code_clean === 'CAN' || $code_clean === 'RTO') {
        $new_dispatch = 'failed';
        $error_desc = "Shadowfax Status: {$status_clean}" . (!empty($rem_clean) ? " ({$rem_clean})" : '');

        $db->update(
            "UPDATE tbl_orders SET dispatch_status = ?, dispatch_error = ?, courier_name = 'shadowfax', courier_awb = COALESCE(NULLIF(?, ''), courier_awb) WHERE order_id = ?",
            'sssi',
            $new_dispatch,
            $error_desc,
            $awb_clean,
            $target_order_id
        );
        $updated = true;
        $update_msg = "Order #{$target_order_id} status updated to {$status_clean}.";
    } else {
        // Generic status update
        $db->update(
            "UPDATE tbl_orders SET courier_name = 'shadowfax', courier_awb = COALESCE(NULLIF(?, ''), courier_awb) WHERE order_id = ?",
            'si',
            $awb_clean,
            $target_order_id
        );
        $updated = true;
        $update_msg = "Order #{$target_order_id} AWB details updated.";
    }

    // Keep tbl_order_items in sync with parent order (preserve skipped test items)
    $disp_status_sync = !empty($new_dispatch) ? $new_dispatch : (!empty($orderRow['dispatch_status']) ? $orderRow['dispatch_status'] : 'shadowfax');
    $db->update(
        "UPDATE tbl_order_items 
         SET courier_name = 'shadowfax', 
             courier_awb = COALESCE(NULLIF(?, ''), courier_awb), 
             dispatch_status = ? 
         WHERE order_id = ? AND (dispatch_status IS NULL OR dispatch_status != 'skipped_test')",
        'ssi',
        $awb_clean,
        $disp_status_sync,
        $target_order_id
    );
}

$db->close();

ob_clean();
http_response_code(200);
echo json_encode([
    'success'    => true,
    'status'     => 200,
    'message'    => $update_msg,
    'order_id'   => $order_id_clean,
    'awb_number' => $awb_clean,
    'updated'    => $updated,
    'timestamp'  => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
