<?php
/**
 * Track Order Endpoint
 * Supports tracking by Order ID, AWB number, or Waybill
 * Path: track-order.php
 */
include_once("include/config.php");

header('Content-Type: application/json');

function trackShipment($waybill, $courier = null) {
    if (function_exists('trackCourierShipment')) {
        return trackCourierShipment($waybill, $courier);
    }
    return ['success' => false, 'message' => 'Courier service unavailable'];
}

$raw_query = $_GET['query'] ?? $_GET['order_id'] ?? $_GET['waybill'] ?? $_POST['query'] ?? $_POST['order_id'] ?? $_POST['waybill'] ?? '';
$query = trim((string)$raw_query);
$courier_param = isset($_GET['courier']) ? trim($_GET['courier']) : (isset($_POST['courier']) ? trim($_POST['courier']) : null);

if (empty($query)) {
    echo json_encode([
        'success' => false,
        'message' => 'Order ID or Tracking number is required.'
    ]);
    exit;
}

// Strip leading '#' if passed like '#2466'
$clean_query = ltrim($query, '#');

$db = connect();

// 1. Check if the query matches an Order in tbl_orders
$order = null;
if (is_numeric($clean_query)) {
    $stmt = $db->select("SELECT * FROM tbl_orders WHERE order_id = ? LIMIT 1", 'i', (int)$clean_query);
    if ($stmt && $stmt->num_rows > 0) {
        $order = $stmt->fetch_assoc();
    }
}

if (!$order) {
    // Also check by AWB / Tracking number or Phone in tbl_orders
    $stmt = $db->select("SELECT * FROM tbl_orders WHERE courier_awb = ? OR delhivery_awb = ? OR phone = ? ORDER BY order_id DESC LIMIT 1", 'sss', $clean_query, $clean_query, $clean_query);
    if ($stmt && $stmt->num_rows > 0) {
        $order = $stmt->fetch_assoc();
    }
}

if ($order) {
    $awb_val = !empty($order['courier_awb']) ? trim($order['courier_awb']) : (!empty($order['delhivery_awb']) ? trim($order['delhivery_awb']) : '');
    $courier_name = !empty($courier_param) ? $courier_param : (!empty($order['courier_name']) ? strtolower($order['courier_name']) : null);
    $dest_city = !empty($order['city']) ? trim($order['city']) : 'Customer Address';

    // If order has an assigned AWB, try live tracking from courier
    if (!empty($awb_val)) {
        $live_res = trackShipment($awb_val, $courier_name);

        if (!empty($live_res['success'])) {
            if (empty($live_res['origin'])) {
                $live_res['origin'] = 'Warehouse';
            }
            if (empty($live_res['destination'])) {
                $live_res['destination'] = $dest_city;
            }
            $live_res['order_id'] = $order['order_id'];
            $live_res['city'] = $dest_city;
            echo json_encode($live_res);
            exit;
        }

        // AWB exists but courier API has not indexed it or returned an error
        $dispatch_label = strtoupper(!empty($order['dispatch_status']) ? $order['dispatch_status'] : 'DISPATCHED');
        $created_time = !empty($order['created_at']) ? date('d M Y, h:i A', strtotime($order['created_at'])) : date('d M Y, h:i A');

        echo json_encode([
            'success' => true,
            'order_id' => $order['order_id'],
            'courier_name' => $courier_name ? ucfirst($courier_name) : 'Courier Partner',
            'awb' => $awb_val,
            'status' => $dispatch_label,
            'status_type' => 'IT',
            'origin' => 'BunnyBoss Fulfillment Hub',
            'destination' => $dest_city,
            'city' => $dest_city,
            'scans' => [
                [
                    'ScanDetail' => [
                        'Scan' => 'Shipment Manifested & Handed to Courier',
                        'ScannedLocation' => 'Warehouse Dispatch Bay',
                        'ScanDateTime' => $created_time
                    ]
                ],
                [
                    'ScanDetail' => [
                        'Scan' => 'AWB Generated (' . $awb_val . ')',
                        'ScannedLocation' => ($courier_name ? ucfirst($courier_name) : 'Courier') . ' Logistics Network',
                        'ScanDateTime' => $created_time
                    ]
                ]
            ]
        ]);
        exit;
    }

    // Order placed but AWB not yet generated (Pending Dispatch)
    $order_status_clean = strtolower($order['order_status'] ?? 'pending');
    $status_title = 'ORDER CONFIRMED';
    $status_type = 'UD';

    if ($order_status_clean === 'processing') {
        $status_title = 'PROCESSING ORDER';
    } elseif ($order_status_clean === 'shipped') {
        $status_title = 'SHIPPED';
        $status_type = 'IT';
    } elseif ($order_status_clean === 'delivered') {
        $status_title = 'DELIVERED';
        $status_type = 'DL';
    } elseif ($order_status_clean === 'cancelled') {
        $status_title = 'CANCELLED';
        $status_type = 'UD';
    }

    $created_time = !empty($order['created_at']) ? date('d M Y, h:i A', strtotime($order['created_at'])) : date('d M Y, h:i A');

    echo json_encode([
        'success' => true,
        'order_id' => $order['order_id'],
        'courier_name' => !empty($order['courier_name']) ? ucfirst($order['courier_name']) : 'Delhivery / Shadowfax',
        'awb' => 'To be assigned upon pickup',
        'status' => $status_title,
        'status_type' => $status_type,
        'origin' => 'Warehouse',
        'destination' => $dest_city,
        'city' => $dest_city,
        'scans' => [
            [
                'ScanDetail' => [
                    'Scan' => 'Order Placed & Confirmed',
                    'ScannedLocation' => 'BunnyBoss Online Store',
                    'ScanDateTime' => $created_time
                ]
            ],
            [
                'ScanDetail' => [
                    'Scan' => 'Quality check and packing in progress at warehouse',
                    'ScannedLocation' => 'Fulfillment Hub',
                    'ScanDateTime' => $created_time
                ]
            ]
        ]
    ]);
    exit;
}

// 2. If no matching order was found, attempt direct tracking via courier API using the raw query as waybill
$direct_res = trackShipment($clean_query, $courier_param);
if (!empty($direct_res['success'])) {
    echo json_encode($direct_res);
    exit;
}

// 3. Fallback error message
echo json_encode([
    'success' => false,
    'message' => 'No tracking or order details found for "' . htmlspecialchars($query) . '". Please check the Order ID or AWB.'
]);
exit;