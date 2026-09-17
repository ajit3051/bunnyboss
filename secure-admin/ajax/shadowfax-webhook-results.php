<?php
/**
 * Shadowfax Webhook AJAX Results Handler & Simulator
 * Path: /secure-admin/ajax/shadowfax-webhook-results.php
 */
ob_start();
include_once("../include/config.php");

$db = connect();

// Handle Simulate Webhook Action
if (isset($_POST['action']) && $_POST['action'] === 'simulate_webhook') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');

    $order_id   = isset($_POST['order_id']) ? trim($_POST['order_id']) : '';
    $awb_number = isset($_POST['awb_number']) ? trim($_POST['awb_number']) : '';
    $status     = isset($_POST['status']) ? trim($_POST['status']) : 'delivered';
    $location   = isset($_POST['location']) ? trim($_POST['location']) : 'Local Hub';
    $remarks    = isset($_POST['remarks']) ? trim($_POST['remarks']) : 'Simulated webhook update from admin panel';

    if (empty($order_id) && empty($awb_number)) {
        echo json_encode(['status' => 'error', 'message' => 'Please provide Order ID or AWB Number.']);
        exit;
    }

    $status_code_map = [
        'delivered'        => 'DL',
        'out_for_delivery' => 'OFD',
        'in_transit'       => 'IT',
        'rto'              => 'RTO',
        'cancelled'        => 'CAN',
        'failed'           => 'FAIL'
    ];
    $status_code = $status_code_map[strtolower($status)] ?? 'UPD';

    $payload = [
        'client_order_id' => $order_id,
        'awb_number'      => $awb_number,
        'status'          => $status,
        'status_code'     => $status_code,
        'location'        => $location,
        'remarks'         => $remarks,
        'timestamp'       => date('Y-m-d H:i:s')
    ];

    // Trigger internal POST to webhook endpoint
    $webhook_url = _FRONTEND_URL . 'api/shadowfax_webhook.php';
    
    $ch = curl_init($webhook_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            "Content-Type: application/json",
            "User-Agent: Shadowfax-Webhook-Simulator/1.0"
        ],
        CURLOPT_TIMEOUT        => 10
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err       = curl_error($ch);
    curl_close($ch);

    if ($err || $http_code !== 200) {
        // Fallback: execute direct internal query if cURL to localhost fails
        $order_id_clean = !empty($order_id) ? $order_id : null;
        $awb_clean      = !empty($awb_number) ? $awb_number : null;
        $json_payload   = json_encode($payload);

        $db->query(
            "INSERT INTO tbl_shadowfax_webhook_logs (order_id, awb_number, status, status_code, location, remarks, payload, ip_address, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, '127.0.0.1', NOW())",
            'sssssss',
            $order_id_clean,
            $awb_clean,
            $status,
            $status_code,
            $location,
            $remarks,
            $json_payload
        );

        if (!empty($order_id_clean)) {
            $new_disp = (strtolower($status) === 'delivered') ? 'dispatched' : ((strtolower($status) === 'failed' || strtolower($status) === 'rto') ? 'failed' : 'shadowfax');
            $db->update("UPDATE tbl_orders SET dispatch_status = ? WHERE order_id = ?", 'ss', $new_disp, $order_id_clean);
        }

        echo json_encode(['status' => 'success', 'message' => 'Simulated webhook recorded internally.']);
        exit;
    }

    $decoded = json_decode($response, true);
    echo json_encode([
        'status'  => 'success',
        'message' => $decoded['message'] ?? 'Webhook simulated successfully.',
        'data'    => $decoded
    ]);
    exit;
}

// Handle Clear Logs Action
if (isset($_POST['action']) && $_POST['action'] === 'clear_logs') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $db->query("TRUNCATE TABLE tbl_shadowfax_webhook_logs");
    echo json_encode(['status' => 'success', 'message' => 'All webhook logs have been cleared.']);
    exit;
}

// Listing Query
$search    = isset($_POST['search']) ? trim($_POST['search']) : '';
$from_date = isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
$to_date   = isset($_POST['to_date']) ? trim($_POST['to_date']) : '';
$status_filter = isset($_POST['status_filter']) ? trim($_POST['status_filter']) : '';
$page       = !empty($_POST['page']) ? (int)$_POST['page'] : 1;
$sortOrder  = !empty($_POST['sortOrder']) ? $_POST['sortOrder'] : 'DESC';
$sortField  = !empty($_POST['sortField']) ? $_POST['sortField'] : 'id';

$where = "WHERE 1=1";
$params = '';
$fields = array();

if ($status_filter !== '') {
    $where .= " AND LOWER(status) LIKE ?";
    $params .= 's';
    $fields[] = '%' . strtolower($status_filter) . '%';
}

if ($search !== '') {
    $searchTerm = '%' . $search . '%';
    $where .= " AND (
        order_id LIKE ? OR 
        awb_number LIKE ? OR 
        status LIKE ? OR 
        status_code LIKE ? OR 
        location LIKE ? OR 
        remarks LIKE ? OR 
        payload LIKE ?
    )";
    $params .= 'sssssss';
    for ($sf = 0; $sf < 7; $sf++) {
        $fields[] = $searchTerm;
    }
}

if ($from_date !== '' && $to_date !== '') {
    $where .= " AND DATE(created_at) BETWEEN ? AND ?";
    $params .= 'ss';
    $fields[] = $from_date;
    $fields[] = $to_date;
} elseif ($from_date !== '') {
    $where .= " AND DATE(created_at) >= ?";
    $params .= 's';
    $fields[] = $from_date;
} elseif ($to_date !== '') {
    $where .= " AND DATE(created_at) <= ?";
    $params .= 's';
    $fields[] = $to_date;
}

$countQuery = "SELECT COUNT(*) as total FROM tbl_shadowfax_webhook_logs $where";
if (empty($fields)) {
    $countStmt = $db->select($countQuery);
} else {
    $countStmt = $db->select($countQuery, $params, $fields);
}
$countRow = $countStmt ? $countStmt->fetch_assoc() : null;
$totalRecords = (int)($countRow['total'] ?? 0);

$recordsPerPage = isset($_SESSION["_RECORDPERPAGE_"]) ? $_SESSION["_RECORDPERPAGE_"] : _RECORDPERPAGE_;
$offSet = ($page - 1) * $recordsPerPage;

$query = "SELECT * FROM tbl_shadowfax_webhook_logs $where ORDER BY $sortField $sortOrder LIMIT $offSet, $recordsPerPage";

if (empty($fields)) {
    $stmt = $db->select($query);
} else {
    $stmt = $db->select($query, $params, $fields);
}

$totalRecordsWithLimit = $stmt ? $stmt->num_rows() : 0;

$html = '';
if ($totalRecordsWithLimit > 0) {
    for ($i = 1; $i <= $totalRecordsWithLimit; $i++) {
        $row = $stmt->fetch_assoc();

        $st = strtolower($row['status'] ?? '');
        if (strpos($st, 'delivered') !== false || $row['status_code'] === 'DL') {
            $badgeClass = 'label-success';
            $badgeIcon  = 'fa-check-circle';
        } elseif (strpos($st, 'out_for_delivery') !== false || strpos($st, 'out for delivery') !== false || $row['status_code'] === 'OFD') {
            $badgeClass = 'label-warning';
            $badgeIcon  = 'fa-truck';
        } elseif (strpos($st, 'transit') !== false || strpos($st, 'picked') !== false || $row['status_code'] === 'IT') {
            $badgeClass = 'label-info';
            $badgeIcon  = 'fa-ship';
        } elseif (strpos($st, 'cancel') !== false || strpos($st, 'rto') !== false || strpos($st, 'failed') !== false) {
            $badgeClass = 'label-danger';
            $badgeIcon  = 'fa-times-circle';
        } else {
            $badgeClass = 'label-primary';
            $badgeIcon  = 'fa-info-circle';
        }

        $formatted_date = !empty($row['created_at']) && $row['created_at'] !== '0000-00-00 00:00:00' 
            ? date('d/m/Y h:i A', strtotime($row['created_at'])) 
            : '-';

        $payload_escaped = htmlspecialchars($row['payload'] ?? '', ENT_QUOTES, 'UTF-8');

        $html .= '<tr>
            <td>' . ($offSet + $i) . '</td>
            <td><strong>#' . htmlspecialchars($row['order_id'] ?? '-') . '</strong></td>
            <td><span class="label label-default" style="font-size: 11px; padding: 4px 8px;">' . htmlspecialchars($row['awb_number'] ?? '-') . '</span></td>
            <td><span class="label ' . $badgeClass . '" style="padding: 4px 8px; font-size: 11px; font-weight: bold;"><i class="fa ' . $badgeIcon . '"></i> ' . strtoupper(htmlspecialchars($row['status'] ?? 'RECEIVED')) . '</span></td>
            <td>' . (!empty($row['status_code']) ? '<span class="label label-inverse" style="padding: 3px 6px; background:#333; color:#fff;">' . htmlspecialchars($row['status_code']) . '</span>' : '-') . '</td>
            <td>' . htmlspecialchars($row['location'] ?? '-') . '</td>
            <td>' . htmlspecialchars($row['remarks'] ?? '-') . '</td>
            <td><span class="text-muted">' . htmlspecialchars($row['ip_address'] ?? '127.0.0.1') . '</span></td>
            <td>' . $formatted_date . '</td>
            <td class="text-center">
                <button type="button" class="btn btn-info btn-xs btn-view-payload" data-payload="' . $payload_escaped . '" data-id="' . $row['id'] . '" title="View Raw Payload"><i class="fa fa-code"></i> Payload</button>
            </td>
        </tr>';
    }
    $pagination = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
    $html = '<tr><td colspan="10" class="text-center text-muted" style="padding: 25px;"><i class="fa fa-inbox fa-3x" style="margin-bottom: 10px; display: block; color: #ccc;"></i> No Shadowfax webhook logs found matching your filters.</td></tr>';
    $pagination = '';
}

$data['htmlData']    = $html;
$data['pagination']  = $pagination;
$data['totalLogs']   = $totalRecords;

ob_clean();
echo json_encode($data);
die;
