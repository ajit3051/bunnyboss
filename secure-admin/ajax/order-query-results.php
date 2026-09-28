<?php
ob_start();
include_once(__DIR__ . "/../include/config.php");

$db = connect();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic auth check
if (!isset($_SESSION['login_user']) || empty($_SESSION['login_user'])) {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

$action = $_POST['action'] ?? $_POST['ajax_action'] ?? '';

// 1. Get Query Details & Linked Order
if ($action === 'get_details') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $query_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($query_id > 0) {
        $stmt = $db->select("SELECT * FROM tbl_order_queries WHERE id = ?", "i", $query_id);
        if ($stmt && $query_row = $stmt->fetch_assoc()) {
            
            // Fetch associated order details
            $order_id = (int)$query_row['order_id'];
            $order_data = null;
            $order_items = [];

            if ($order_id > 0) {
                $ord_stmt = $db->select("SELECT * FROM tbl_orders WHERE order_id = ?", "i", $order_id);
                if ($ord_stmt && $ord_res = $ord_stmt->fetch_assoc()) {
                    $order_data = $ord_res;
                }

                $items_stmt = $db->select(
                    "SELECT OI.*, 
                            (SELECT image_path FROM tbl_item_images WHERE item_id = OI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture
                     FROM tbl_order_items OI 
                     WHERE OI.order_id = ?",
                    "i",
                    $order_id
                );
                if ($items_stmt) {
                    while ($it = $items_stmt->fetch_assoc()) {
                        $order_items[] = $it;
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'data' => $query_row,
                'order' => $order_data,
                'items' => $order_items
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Query record not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid query ID.']);
    }
    exit;
}

// 2. Update Query Status, Admin Reply & Notes
if ($action === 'update_status') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $query_id    = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $status      = isset($_POST['status']) ? trim($_POST['status']) : '';
    $admin_reply = isset($_POST['admin_reply']) ? trim($_POST['admin_reply']) : '';
    $admin_notes = isset($_POST['admin_notes']) ? trim($_POST['admin_notes']) : '';

    if ($query_id > 0 && in_array($status, ['open', 'in_progress', 'resolved', 'closed'], true)) {
        $updated = $db->update(
            "UPDATE tbl_order_queries SET status = ?, admin_reply = ?, admin_notes = ? WHERE id = ?",
            "sssi",
            $status,
            $admin_reply,
            $admin_notes,
            $query_id
        );

        echo json_encode(['success' => true, 'message' => 'Order query response and status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
    }
    exit;
}

// 3. Delete Query
if ($action === 'delete_query') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $query_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($query_id > 0) {
        $del = $db->delete("DELETE FROM tbl_order_queries WHERE id = ?", "i", $query_id);
        if ($del !== false) {
            echo json_encode(['success' => true, 'message' => 'Order query deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete query record.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid query ID.']);
    }
    exit;
}

ob_clean();
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['success' => false, 'message' => 'Invalid action.']);
exit;
