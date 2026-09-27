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

$action = $_POST['action'] ?? $_POST['ajax_action'] ?? '';

if ($action === 'get_details') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $inquiry_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($inquiry_id > 0) {
        $stmt = $db->select("SELECT * FROM tbl_contact_inquiries WHERE id = ?", "i", $inquiry_id);
        if ($stmt && $row = $stmt->fetch_assoc()) {
            if ($row['status'] === 'unread') {
                $db->query("UPDATE tbl_contact_inquiries SET status = 'read' WHERE id = " . $inquiry_id);
                $row['status'] = 'read';
            }
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Inquiry record not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid inquiry ID.']);
    }
    exit;
}

if ($action === 'update_status') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $inquiry_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $admin_notes = isset($_POST['admin_notes']) ? trim($_POST['admin_notes']) : null;

    if ($inquiry_id > 0 && in_array($status, ['unread', 'read', 'replied'], true)) {
        if ($admin_notes !== null) {
            $updated = $db->update(
                "UPDATE tbl_contact_inquiries SET status = ?, admin_notes = ? WHERE id = ?",
                "ssi",
                $status,
                $admin_notes,
                $inquiry_id
            );
        } else {
            $updated = $db->update(
                "UPDATE tbl_contact_inquiries SET status = ? WHERE id = ?",
                "si",
                $status,
                $inquiry_id
            );
        }
        echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
    }
    exit;
}

if ($action === 'delete_inquiry') {
    ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    $inquiry_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($inquiry_id > 0) {
        $deleted = $db->delete("DELETE FROM tbl_contact_inquiries WHERE id = ?", "i", $inquiry_id);
        echo json_encode(['success' => true, 'message' => 'Inquiry deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid inquiry ID.']);
    }
    exit;
}

ob_clean();
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['success' => false, 'message' => 'Invalid action.']);
exit;
