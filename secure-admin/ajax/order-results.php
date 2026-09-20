<?php
ob_start();
include_once("../include/config.php"); 
if (defined('_FRONTEND_PATH')) {
    require_once(_FRONTEND_PATH . "include/courier_service.php");
} else {
    require_once(__DIR__ . "/../../include/courier_service.php");
}

$validationHelper = new validation();
$db = connect();

if (function_exists('ensure_order_items_schema')) {
    ensure_order_items_schema($db);
}

if (isset($_POST['action']) && $_POST['action'] === 'bulk_dispatch') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $order_ids = isset($_POST['order_ids']) && is_array($_POST['order_ids']) ? array_map('intval', $_POST['order_ids']) : array();
    if (!empty($order_ids)) {
        $ids_str = implode(',', $order_ids);
        $db->query("UPDATE tbl_orders SET dispatch_status = 'dispatched' WHERE order_id IN ($ids_str)");
        $db->query("UPDATE tbl_order_items SET dispatch_status = 'dispatched' WHERE order_id IN ($ids_str) AND (dispatch_status IS NULL OR dispatch_status != 'skipped_test')");
        echo json_encode(['status' => 'success', 'message' => count($order_ids) . ' order(s) marked as dispatched.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No orders selected.']);
    }
    die;
}

if (isset($_POST['action']) && $_POST['action'] === 'update_single_dispatch_status') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $item_id  = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $dispatch_status = isset($_POST['dispatch_status']) ? trim($_POST['dispatch_status']) : '';

    $allowed_statuses = ['pending', 'shadowfax', 'dispatch', 'dispatched', 'out_of_stock', 'out of stock', 'failed', 'skipped_test'];
    if ($order_id > 0 && in_array(strtolower($dispatch_status), $allowed_statuses)) {
        $status_val = strtolower($dispatch_status);
        if ($status_val === 'dispatch') {
            $status_val = 'dispatched';
        }
        $escaped_status = $db->real_escape_string($status_val);

        $db->query("UPDATE tbl_orders SET dispatch_status = '$escaped_status', dispatch_error = NULL WHERE order_id = $order_id");
        if ($item_id > 0) {
            $pk = function_exists('get_order_items_primary_key') ? get_order_items_primary_key($db) : 'item_id';
            $db->query("UPDATE tbl_order_items SET dispatch_status = '$escaped_status', dispatch_error = NULL WHERE $pk = $item_id");
        }
        echo json_encode(['status' => 'success', 'message' => 'Dispatch status updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid order ID or status.']);
    }
    die;
}


$today = date("Y-m-d");

$search    = isset($_POST['search']) ? trim($_POST['search']) : '';
$from_date = isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
$to_date   = isset($_POST['to_date']) ? trim($_POST['to_date']) : '';
$status_filter = isset($_POST['payment_status_filter']) ? trim($_POST['payment_status_filter']) : '';
$page       = $_POST['page'];
$sortOrder  = !empty($_POST['sortOrder']) ? $_POST['sortOrder'] : 'DESC';
$sortField  = !empty($_POST['sortField']) ? $_POST['sortField'] : 'O.order_id';
$exportType = $_REQUEST['export'];

$where = "WHERE 1=1";
$params = '';
$fields = array();

if ($status_filter === 'paid') {
    $where .= " AND (O.payment_status = 'paid' OR O.order_status = 'paid')";
} elseif ($status_filter === 'partial_paid') {
    $where .= " AND O.payment_status = 'partial_paid'";
} elseif ($status_filter === 'pending') {
    $where .= " AND O.payment_status = 'pending'";
} elseif ($status_filter === 'dispatched') {
    $where .= " AND (O.dispatch_status IN ('dispatched', 'shadowfax', 'delhivery') OR (O.delhivery_awb IS NOT NULL AND O.delhivery_awb != '') OR (O.courier_awb IS NOT NULL AND O.courier_awb != ''))";
} elseif ($status_filter === 'dispatch_pending') {
    $where .= " AND (O.dispatch_status = 'pending' AND (O.delhivery_awb IS NULL OR O.delhivery_awb = '') AND (O.courier_awb IS NULL OR O.courier_awb = ''))";
} else {
    $where .= " AND (O.payment_status IN ('paid', 'partial_paid') OR O.order_status = 'paid')";
}

if ($search !== '') {
    $cleanSearch = ltrim($search, '#');
    $searchTerm = '%' . $cleanSearch . '%';

    $where .= " AND (
        O.order_id LIKE ? OR 
        O.first_name LIKE ? OR 
        O.last_name LIKE ? OR 
        CONCAT(O.first_name, ' ', O.last_name) LIKE ? OR 
        O.phone LIKE ? OR 
        O.street_address LIKE ? OR 
        O.city LIKE ? OR 
        O.postcode LIKE ? OR 
        O.payment_status LIKE ? OR 
        O.order_status LIKE ? OR 
        O.dispatch_status LIKE ? OR 
        O.payment_method LIKE ? OR 
        O.delhivery_awb LIKE ? OR 
        O.razorpay_payment_id LIKE ? OR 
        OI.product_title LIKE ? OR 
        OI.size LIKE ? OR 
        OI.price LIKE ? OR 
        OI.row_total LIKE ? OR 
        IM.item_code LIKE ? OR 
        IM.brand_name LIKE ?
    )";

    $searchFieldsCount = 20;
    $params .= str_repeat('s', $searchFieldsCount);
    for ($sf = 0; $sf < $searchFieldsCount; $sf++) {
        $fields[] = $searchTerm;
    }
}

if ($from_date !== '' && $to_date !== '') {
    $where .= " AND DATE(O.created_at) BETWEEN ? AND ?";
    $params .= 'ss';
    $fields[] = $from_date;
    $fields[] = $to_date;
} elseif ($from_date !== '') {
    $where .= " AND DATE(O.created_at) >= ?";
    $params .= 's';
    $fields[] = $from_date;
} elseif ($to_date !== '') {
    $where .= " AND DATE(O.created_at) <= ?";
    $params .= 's';
    $fields[] = $to_date;
}

if ($exportType) {
    $columns = "O.order_id, OI.transaction_id, O.first_name, O.last_name, O.street_address, O.city, O.postcode, O.phone, O.ship_to_different, O.order_notes, O.payment_method, O.payment_status, O.order_status, DATE_FORMAT(O.created_at, '%d/%m/%Y %h:%i %p') as order_date, OI.product_title, OI.qty, OI.size, OI.price, OI.row_total as total_price, OI.shipping, IM.item_code, IM.description, IM.group_name, IM.brand_name, (SELECT GROUP_CONCAT(DISTINCT color_name SEPARATOR ',') FROM tbl_item_variants WHERE item_id = IM.id AND color_name != '') AS color_name";
} else {
    $columns = "O.*, OI.*, OI.transaction_id AS item_transaction_id, OI.courier_awb AS item_courier_awb, OI.dispatch_status AS item_dispatch_status, OI.dispatch_error AS item_dispatch_error, (SELECT image_path FROM tbl_item_images WHERE item_id = IM.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture";
}

$baseQuery = "FROM `tbl_orders` AS O INNER JOIN tbl_order_items AS OI ON OI.order_id=O.order_id INNER JOIN tbl_item_master AS IM ON IM.id = OI.product_id $where";

$query = "SELECT $columns $baseQuery";

if (empty($fields)) {
    $stmt = $db->select($query);
} else {
    $stmt = $db->select($query, $params, $fields);
}

if ($exportType == 'excel') {
    exportResult('Order-details', $stmt);
    exit();
}
if ($exportType == 'pdf') {
    exportResultPdf('Order-details', $stmt);
    exit();
}

$totalRecords = $stmt->num_rows();

// Total Qty (calculated based on filtered orders matching active search / date / status filter)
$totalQtyQuery = "SELECT SUM(OI.qty) AS total_qty FROM `tbl_orders` AS O INNER JOIN tbl_order_items AS OI ON OI.order_id=O.order_id INNER JOIN tbl_item_master AS IM ON IM.id = OI.product_id $where";
if (empty($fields)) {
    $totalQtyResult = $db->select($totalQtyQuery);
} else {
    $totalQtyResult = $db->select($totalQtyQuery, $params, $fields);
}
$totalQtyRow = $totalQtyResult ? $totalQtyResult->fetch_assoc() : null;
$totalQty = ($totalQtyRow && isset($totalQtyRow['total_qty'])) ? (int)$totalQtyRow['total_qty'] : 0;

// Current Qty (today only, daily basis, calculated with active search / date / status filter)
$currentQtyQuery = "SELECT SUM(OI.qty) AS current_qty FROM `tbl_orders` AS O INNER JOIN tbl_order_items AS OI ON OI.order_id=O.order_id INNER JOIN tbl_item_master AS IM ON IM.id = OI.product_id $where AND DATE(O.created_at) = '$today'";
if (empty($fields)) {
    $currentQtyResult = $db->select($currentQtyQuery);
} else {
    $currentQtyResult = $db->select($currentQtyQuery, $params, $fields);
}
$currentQtyRow = $currentQtyResult ? $currentQtyResult->fetch_assoc() : null;
$currentQty = ($currentQtyRow && isset($currentQtyRow['current_qty'])) ? (int)$currentQtyRow['current_qty'] : 0;

// Pending COD Balance Amount (calculated based on filtered orders matching active search / date / status filter)
$pendingBalanceQuery = "SELECT SUM(CASE WHEN (LOWER(O.payment_status) = 'paid' OR LOWER(O.order_status) = 'paid' OR LOWER(O.order_status) = 'cancelled') THEN 0 ELSE OI.row_total END) AS pending_balance FROM `tbl_orders` AS O INNER JOIN tbl_order_items AS OI ON OI.order_id=O.order_id INNER JOIN tbl_item_master AS IM ON IM.id = OI.product_id $where";
if (empty($fields)) {
    $pendingBalanceResult = $db->select($pendingBalanceQuery);
} else {
    $pendingBalanceResult = $db->select($pendingBalanceQuery, $params, $fields);
}
$pendingBalanceRow = $pendingBalanceResult ? $pendingBalanceResult->fetch_assoc() : null;
$pendingBalance = ($pendingBalanceRow && isset($pendingBalanceRow['pending_balance'])) ? (float)$pendingBalanceRow['pending_balance'] : 0.00;

$recordsPerPage = isset($_SESSION["_RECORDPERPAGE_"]) ? $_SESSION["_RECORDPERPAGE_"] : _RECORDPERPAGE_;
$offSet = ($page - 1) * $recordsPerPage;

$query .= " ORDER BY $sortField $sortOrder LIMIT $offSet,$recordsPerPage";

if (empty($fields)) {
    $stmt = $db->select($query);
} else {
    $stmt = $db->select($query, $params, $fields);
}

$totalRecordsWithLimit = $stmt->num_rows();

if ($totalRecordsWithLimit > 0) {
    $html = '';
    for ($i = 1; $i <= $totalRecordsWithLimit; $i++) {
        $row = $stmt->fetch_assoc();

        $imgSrc = !empty($row['picture']) ? 'uploads/item-master/' . htmlspecialchars($row['picture']) : 'assets/dist/img/no-image.png';

        $displayStatus = !empty($row['payment_status']) ? $row['payment_status'] : $row['order_status'];
        if ($displayStatus === 'paid') {
            $badgeClass = 'label-success';
            $statusText = 'PAID';
        } else if (in_array($displayStatus, ['partial_paid', 'shipping_paid', 'shipping_and_gst_paid'])) {
            $badgeClass = 'label-info';
            $statusText = 'PARTIAL PAID';
        } else {
            $badgeClass = 'label-default';
            $statusText = strtoupper($displayStatus);
        }

        $itemPkCol = function_exists('get_order_items_primary_key') ? get_order_items_primary_key($db) : 'item_id';
        $itemIdVal = (int)($row[$itemPkCol] ?? $row['item_id'] ?? $row['id'] ?? 0);
        $tx_id     = !empty($row['item_transaction_id']) ? $row['item_transaction_id'] : (!empty($row['transaction_id']) ? $row['transaction_id'] : '-');
        $itemAwb   = !empty($row['item_courier_awb']) ? $row['item_courier_awb'] : (!empty($row['courier_awb']) ? $row['courier_awb'] : '');

        $dispatchStatusRaw = !empty($row['item_dispatch_status']) ? strtolower($row['item_dispatch_status']) : (!empty($row['dispatch_status']) ? strtolower($row['dispatch_status']) : 'pending');
        if ($dispatchStatusRaw === 'shadowfax') {
            $dispatchBadgeClass = 'label-info';
            $dispatchStatusText = 'SHADOWFAX' . ($itemAwb ? ' (' . htmlspecialchars($itemAwb) . ')' : '');
            $dispatchIcon = 'fa-truck';
        } elseif ($dispatchStatusRaw === 'skipped_test') {
            $dispatchBadgeClass = 'label-default';
            $dispatchStatusText = 'TEST ITEM (SKIPPED)';
            $dispatchIcon = 'fa-ban';
        } elseif ($dispatchStatusRaw === 'out_of_stock' || $dispatchStatusRaw === 'out of stock') {
            $dispatchBadgeClass = 'label-danger';
            $dispatchStatusText = 'OUT OF STOCK';
            $dispatchIcon = 'fa-ban';
        } elseif ($dispatchStatusRaw === 'failed') {
            $dispatchBadgeClass = 'label-danger';
            $dispatchStatusText = 'FAILED';
            $dispatchIcon = 'fa-exclamation-triangle';
        } elseif ($dispatchStatusRaw === 'dispatched' || $dispatchStatusRaw === 'dispatch' || !empty($itemAwb)) {
            $dispatchBadgeClass = 'label-success';
            $dispatchStatusText = 'DISPATCHED' . ($itemAwb ? ' (' . htmlspecialchars($itemAwb) . ')' : '');
            $dispatchIcon = 'fa-truck';
        } else {
            $dispatchBadgeClass = 'label-warning';
            $dispatchStatusText = 'PENDING';
            $dispatchIcon = 'fa-clock-o';
        }

        // Calculate Balance Amount (unpaid amount for this item row to collect on delivery)
        $isPaidOrder = (strtolower($row['payment_status']) === 'paid' || strtolower($row['order_status']) === 'paid');
        $rowBalanceAmt = $isPaidOrder ? 0.00 : (float)$row['row_total'];
        if ($isPaidOrder) {
            $balanceDisplay = '<span class="label label-success" style="padding: 4px 8px; font-size: 11px;">₹0.00</span>';
        } else {
            $balanceDisplay = '<span class="label label-warning" style="padding: 4px 8px; font-size: 11px; font-weight: bold;">₹' . number_format($rowBalanceAmt, 2) . '</span>';
        }

        $dispatchTooltip = !empty($row['item_dispatch_error']) ? htmlspecialchars($row['item_dispatch_error']) : (!empty($row['dispatch_error']) ? htmlspecialchars($row['dispatch_error']) : 'Click to change dispatch status');

        $html .= '<tr>
            <td>
                <div class="checkbox checkbox-info">
                    <input id="checkbox' . $i . '" type="checkbox" class="order-checkbox" value="' . $row['order_id'] . '">
                    <label for="checkbox' . $i . '">' . ($offSet + $i) . '</label>
                </div>
            </td>
            <td>
                <a class="btn-add btn-xs" onclick="view_details(' . $row['order_id'] . ');" href="javascript:void(0);" data-toggle="tooltip" title="View Details">
               <span class="fa fa-list-alt"></span></a>
            </td>
            <td>
                <span class="label ' . $badgeClass . '" style="padding: 4px 8px; font-size: 11px; font-weight: bold;">' . htmlspecialchars($statusText) . '</span>
            </td>
            <td>
                <span class="label ' . $dispatchBadgeClass . ' dispatch-status-trigger" data-order-id="' . $row['order_id'] . '" data-item-id="' . $itemIdVal . '" data-status="' . htmlspecialchars($dispatchStatusRaw) . '" onclick="event.stopPropagation(); openDispatchModal(this);" style="padding: 4px 8px; font-size: 11px; font-weight: bold; cursor: pointer;" title="' . $dispatchTooltip . '"><i class="fa ' . $dispatchIcon . '"></i> ' . $dispatchStatusText . ' <i class="fa fa-caret-down" style="margin-left: 2px;"></i></span>
            </td>
             <td>' . htmlspecialchars($row['order_id']) . '</td>
             <td><span class="label label-primary" style="font-family: monospace; font-size: 11px; letter-spacing: 0.5px; padding: 3px 6px;">' . htmlspecialchars($tx_id) . '</span></td>
             <td>
                 <img src="' . $imgSrc . '" class="img-thumbnail img-popup-trigger" alt="Product Image" style="max-height: 50px; max-width: 50px; object-fit: contain;" title="Click to view full image" onclick="event.stopPropagation(); showImageModal(this.src, \'' . htmlspecialchars(addslashes($row['product_title']), ENT_QUOTES) . '\', event);">
             </td>
            <td>' . htmlspecialchars($row['size']) . '</td>
            <td>' . htmlspecialchars($row['qty']) . '</td>
            <td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>
            <td>' . htmlspecialchars($row['product_title']) . '</td>
            <td>₹' . htmlspecialchars($row['price']) . '</td>
            <td>₹' . htmlspecialchars($row['row_total']) . '</td>
            <td>₹' . htmlspecialchars($row['shipping']) . '</td>
            <td>' . $balanceDisplay . '</td>
            <td>' . (!empty($row['created_at']) && $row['created_at'] !== '0000-00-00 00:00:00' ? date('d/m/Y h:i A', strtotime($row['created_at'])) : '') . '</td>
            <td>' . htmlspecialchars($row['street_address']) . '</td>
            <td>' . htmlspecialchars($row['city']) . '</td>
            <td>' . ((isset($row['is_pincode_serviceable']) && (int)$row['is_pincode_serviceable'] === 0) 
                ? '<span class="label label-danger" style="padding: 3px 6px; font-size: 11px;" title="Pincode Not Found in Courier Network (0)"><i class="fa fa-times"></i> ' . htmlspecialchars($row['postcode']) . ' (0)</span>' 
                : '<span class="label label-success" style="padding: 3px 6px; font-size: 11px;" title="Pincode Found in Courier Network (1)"><i class="fa fa-check"></i> ' . htmlspecialchars($row['postcode']) . ' (1)</span>') . '</td>
            <td>' . htmlspecialchars($row['phone']) . '</td>
        </tr>';
    }

    $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
    $html = '<tr>
      <td colspan="20"> No Recently added</td>
   </tr>';
    $data['pagination'] = '';
}

$data['htmlData']       = $html;
$data['totalQty']       = $totalQty;
$data['currentQty']     = $currentQty;
$data['pendingBalance'] = '₹' . number_format($pendingBalance, 2);

echo json_encode($data);
die;
