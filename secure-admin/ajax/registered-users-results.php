<?php
ob_start();
include_once("../include/config.php");

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

$action = $_POST['action'] ?? '';

// 1. Get Detailed Customer Profile (Addresses + Recent Orders)
if ($action === 'get_user_details') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $user_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($user_id > 0) {
        $user_stmt = $db->select("
            SELECT 
                u.*,
                COALESCE(
                    NULLIF(TRIM(u.name), ''),
                    (SELECT CONCAT(first_name, ' ', last_name) FROM tbl_orders WHERE user_id = u.id ORDER BY order_id DESC LIMIT 1),
                    (SELECT CONCAT(first_name, ' ', last_name) FROM tbl_user_addresses WHERE user_id = u.id ORDER BY id DESC LIMIT 1),
                    'Customer'
                ) AS display_name,
                (SELECT COUNT(*) FROM tbl_orders WHERE user_id = u.id) AS total_orders,
                (SELECT COALESCE(SUM(grand_total), 0) FROM tbl_orders WHERE user_id = u.id) AS total_spent
            FROM tbl_users u
            WHERE u.id = ?
        ", "i", $user_id);

        if ($user_stmt && $user = $user_stmt->fetch_assoc()) {
            // Fetch Saved Addresses
            $addresses = [];
            $addr_stmt = $db->select("SELECT * FROM tbl_user_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC", "i", $user_id);
            if ($addr_stmt) {
                while ($addr = $addr_stmt->fetch_assoc()) {
                    $addresses[] = $addr;
                }
            }

            // Fetch Orders History
            $orders = [];
            $orders_stmt = $db->select("
                SELECT order_id, grand_total, payment_method, payment_status, order_status, dispatch_status, courier_name, courier_awb, delhivery_awb, created_at 
                FROM tbl_orders 
                WHERE user_id = ? 
                ORDER BY order_id DESC 
                LIMIT 10
            ", "i", $user_id);
            if ($orders_stmt) {
                while ($ord = $orders_stmt->fetch_assoc()) {
                    $orders[] = $ord;
                }
            }

            echo json_encode([
                'success'   => true,
                'user'      => $user,
                'addresses' => $addresses,
                'orders'    => $orders
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    }
    exit;
}

// 2. Toggle Status (Active / Inactive)
if ($action === 'toggle_status') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $user_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $new_status = isset($_POST['status']) && in_array($_POST['status'], ['active', 'inactive'], true) ? $_POST['status'] : '';

    if ($user_id > 0 && !empty($new_status)) {
        $updated = $db->update("UPDATE tbl_users SET status = ? WHERE id = ?", "si", $new_status, $user_id);
        echo json_encode(['success' => true, 'message' => 'User status updated to ' . ucfirst($new_status) . '.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    }
    exit;
}

// 3. Delete User
if ($action === 'delete_user') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $user_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($user_id > 0) {
        $deleted = $db->delete("DELETE FROM tbl_users WHERE id = ?", "i", $user_id);
        echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    }
    exit;
}

ob_clean();
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['success' => false, 'message' => 'Invalid action.']);
exit;
