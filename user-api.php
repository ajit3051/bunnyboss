<?php
require_once(__DIR__ . "/include/config.php");

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;
$user_mobile = $_SESSION['user_mobile'] ?? '';

$action = $_REQUEST['action'] ?? '';

// Actions that don't require login check
if (empty($user_id) && empty($user_mobile)) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please sign in.']);
    exit;
}

$db = connect();

// Helper to get effective user_id
if (empty($user_id) && !empty($user_mobile)) {
    $u_stmt = $db->select("SELECT id FROM tbl_users WHERE mobile = ? LIMIT 1", 's', $user_mobile);
    if ($u_stmt && $u_res = $u_stmt->fetch_assoc()) {
        $user_id = (int)$u_res['id'];
        $_SESSION['user_id'] = $user_id;
    }
}

// ---------------------------------------------------------
// 1. UPDATE BASIC DETAILS / PROFILE
// ---------------------------------------------------------
if ($action === 'update_profile') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Full name is required.']);
        exit;
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }

    $updated = $db->update("UPDATE tbl_users SET name = ?, email = ? WHERE id = ?", 'ssi', $name, $email, $user_id);

    if ($updated !== false) {
        $_SESSION['user_name'] = $name;
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully!', 'name' => $name, 'email' => $email]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile. Please try again.']);
    }
    exit;
}

// ---------------------------------------------------------
// 2. GET ALL ADDRESSES
// ---------------------------------------------------------
if ($action === 'get_addresses') {
    $stmt = $db->select("SELECT * FROM tbl_user_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC", 'i', $user_id);
    $addresses = [];
    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {
            $addresses[] = $row;
        }
    }
    echo json_encode(['success' => true, 'addresses' => $addresses]);
    exit;
}

// ---------------------------------------------------------
// 3. GET SINGLE ADDRESS
// ---------------------------------------------------------
if ($action === 'get_address') {
    $address_id = (int)($_REQUEST['address_id'] ?? 0);
    $stmt = $db->select("SELECT * FROM tbl_user_addresses WHERE id = ? AND user_id = ?", 'ii', $address_id, $user_id);
    if ($stmt && $row = $stmt->fetch_assoc()) {
        echo json_encode(['success' => true, 'address' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Address not found.']);
    }
    exit;
}

// ---------------------------------------------------------
// 4. SAVE ADDRESS (ADD / EDIT)
// ---------------------------------------------------------
if ($action === 'save_address') {
    $address_id     = (int)($_POST['address_id'] ?? 0);
    $title          = trim($_POST['title'] ?? 'Home');
    $first_name     = trim($_POST['first_name'] ?? '');
    $last_name      = trim($_POST['last_name'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');
    $street_address = trim($_POST['street_address'] ?? '');
    $city           = trim($_POST['city'] ?? '');
    $state          = trim($_POST['state'] ?? '');
    $postcode       = trim($_POST['postcode'] ?? '');
    $is_default     = isset($_POST['is_default']) ? 1 : 0;

    // Validation
    if (empty($first_name)) {
        echo json_encode(['success' => false, 'message' => 'First name is required.']);
        exit;
    }
    if (empty($street_address)) {
        echo json_encode(['success' => false, 'message' => 'Street address is required.']);
        exit;
    }
    if (empty($city)) {
        echo json_encode(['success' => false, 'message' => 'City is required.']);
        exit;
    }
    if (empty($postcode) || !preg_match('/^[0-9]{4,10}$/', $postcode)) {
        echo json_encode(['success' => false, 'message' => 'Valid pincode/postcode is required.']);
        exit;
    }
    if (empty($phone) || !preg_match('/^[6-9][0-9]{9}$/', $phone)) {
        echo json_encode(['success' => false, 'message' => 'Valid 10-digit phone number starting with 6-9 is required.']);
        exit;
    }

    // Check if this is the user's first address, make it default automatically if none exists
    $count_stmt = $db->select("SELECT COUNT(*) as cnt FROM tbl_user_addresses WHERE user_id = ?", 'i', $user_id);
    $addr_cnt = ($count_stmt && $c_row = $count_stmt->fetch_assoc()) ? (int)$c_row['cnt'] : 0;
    if ($addr_cnt === 0) {
        $is_default = 1;
    }

    if ($is_default === 1) {
        $db->update("UPDATE tbl_user_addresses SET is_default = 0 WHERE user_id = ?", 'i', $user_id);
    }

    if ($address_id > 0) {
        // Edit address
        $updated = $db->update(
            "UPDATE tbl_user_addresses SET title = ?, first_name = ?, last_name = ?, phone = ?, street_address = ?, city = ?, state = ?, postcode = ?, is_default = ? WHERE id = ? AND user_id = ?",
            'ssssssssiii',
            $title,
            $first_name,
            $last_name,
            $phone,
            $street_address,
            $city,
            $state,
            $postcode,
            $is_default,
            $address_id,
            $user_id
        );

        if ($updated !== false) {
            echo json_encode(['success' => true, 'message' => 'Address updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not update address.']);
        }
    } else {
        // Insert new address
        $new_id = $db->insert(
            "INSERT INTO tbl_user_addresses (user_id, title, first_name, last_name, phone, street_address, city, state, postcode, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'issssssssi',
            $user_id,
            $title,
            $first_name,
            $last_name,
            $phone,
            $street_address,
            $city,
            $state,
            $postcode,
            $is_default
        );

        if ($new_id) {
            echo json_encode(['success' => true, 'message' => 'New address saved successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not save new address.']);
        }
    }
    exit;
}

// ---------------------------------------------------------
// 5. DELETE ADDRESS
// ---------------------------------------------------------
if ($action === 'delete_address') {
    $address_id = (int)($_POST['address_id'] ?? 0);
    if ($address_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid address ID.']);
        exit;
    }

    $deleted = $db->delete("DELETE FROM tbl_user_addresses WHERE id = ? AND user_id = ?", 'ii', $address_id, $user_id);
    if ($deleted) {
        // If deleted address was default, set the latest remaining address as default
        $def_stmt = $db->select("SELECT id FROM tbl_user_addresses WHERE user_id = ? AND is_default = 1", 'i', $user_id);
        if (!$def_stmt || $def_stmt->num_rows === 0) {
            $db->update("UPDATE tbl_user_addresses SET is_default = 1 WHERE user_id = ? ORDER BY id DESC LIMIT 1", 'i', $user_id);
        }
        echo json_encode(['success' => true, 'message' => 'Address deleted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete address.']);
    }
    exit;
}

// ---------------------------------------------------------
// 6. SET DEFAULT ADDRESS
// ---------------------------------------------------------
if ($action === 'set_default_address') {
    $address_id = (int)($_POST['address_id'] ?? 0);
    if ($address_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid address ID.']);
        exit;
    }

    $db->update("UPDATE tbl_user_addresses SET is_default = 0 WHERE user_id = ?", 'i', $user_id);
    $updated = $db->update("UPDATE tbl_user_addresses SET is_default = 1 WHERE id = ? AND user_id = ?", 'ii', $address_id, $user_id);

    if ($updated !== false) {
        echo json_encode(['success' => true, 'message' => 'Default address set successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to set default address.']);
    }
    exit;
}

// ---------------------------------------------------------
// 7. GET ORDER DETAILS (FOR MODAL IN MY ORDERS)
// ---------------------------------------------------------
if ($action === 'get_order_details') {
    $order_id = (int)($_REQUEST['order_id'] ?? 0);
    if ($order_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
        exit;
    }

    // Verify order belongs to current user by user_id OR user mobile number
    $ord_stmt = $db->select(
        "SELECT * FROM tbl_orders WHERE order_id = ? AND (user_id = ? OR phone = ?)",
        'iis',
        $order_id,
        $user_id,
        $user_mobile
    );

    if (!$ord_stmt || $ord_stmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found or access denied.']);
        exit;
    }

    $order = $ord_stmt->fetch_assoc();

    // Fetch order items with main image
    $item_stmt = $db->select(
        "SELECT OI.*, 
                (SELECT image_path FROM tbl_item_images WHERE item_id = OI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS image_path
         FROM tbl_order_items OI 
         WHERE OI.order_id = ?",
        'i',
        $order_id
    );

    $items = [];
    if ($item_stmt) {
        while ($i_row = $item_stmt->fetch_assoc()) {
            if (!empty($i_row['image_path'])) {
                $i_row['image_url'] = _IMAGE_PATH . 'item-master/' . $i_row['image_path'];
            } else {
                $i_row['image_url'] = _BASEURL . 'assets/images/no-image.jpg';
            }
            $items[] = $i_row;
        }
    }

    echo json_encode([
        'success' => true,
        'order' => $order,
        'items' => $items
    ]);
    exit;
}

// ---------------------------------------------------------
// 8. CANCEL ORDER (BY USER)
// ---------------------------------------------------------
if ($action === 'cancel_order') {
    $order_id = (int)($_POST['order_id'] ?? 0);
    $reason   = trim($_POST['reason'] ?? '');
    $comments = trim($_POST['comments'] ?? '');

    if ($order_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
        exit;
    }

    if (empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'Please select a reason for cancellation.']);
        exit;
    }

    // Verify order exists and belongs to current user
    $ord_stmt = $db->select(
        "SELECT * FROM tbl_orders WHERE order_id = ? AND (user_id = ? OR phone = ?)",
        'iis',
        $order_id,
        $user_id,
        $user_mobile
    );

    if (!$ord_stmt || $ord_stmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found or unauthorized.']);
        exit;
    }

    $order = $ord_stmt->fetch_assoc();
    $current_status = strtolower(trim($order['order_status'] ?? 'pending'));

    // Check if already cancelled
    if ($current_status === 'cancelled') {
        echo json_encode(['success' => false, 'message' => 'This order has already been cancelled.']);
        exit;
    }

    // Check non-cancellable statuses (delivered, completed, rto, returned)
    if (in_array($current_status, ['delivered', 'completed', 'rto', 'returned'])) {
        echo json_encode(['success' => false, 'message' => 'Delivered orders cannot be cancelled online. Please contact customer care.']);
        exit;
    }

    // Check if physically dispatched with courier partner
    $dispatch_status = strtolower(trim($order['dispatch_status'] ?? ''));
    if (in_array($current_status, ['shipped', 'dispatched']) || in_array($dispatch_status, ['shipped', 'dispatched', 'in_transit', 'out_for_delivery'])) {
        echo json_encode(['success' => false, 'message' => 'This order has already been dispatched with the courier partner and cannot be cancelled online. You may decline delivery at doorstep or contact customer support.']);
        exit;
    }

    // Construct full cancellation reason string
    $full_reason = $reason;
    if (!empty($comments)) {
        $full_reason .= ' - ' . $comments;
    }

    // 1. Restore stock if previously deducted
    if (function_exists('restore_order_stock')) {
        restore_order_stock($order_id);
    }

    // 2. Update order status to 'cancelled'
    $updated = $db->update(
        "UPDATE tbl_orders 
         SET order_status = 'cancelled', 
             cancel_reason = ?, 
             cancelled_at = NOW(), 
             cancelled_by = 'user',
             dispatch_status = 'cancelled'
         WHERE order_id = ?",
        'si',
        $full_reason,
        $order_id
    );

    if ($updated !== false) {
        $refund_note = '';
        $pay_status = strtolower(trim($order['payment_status'] ?? ''));
        $paid_amt = (float)($order['paid_amount'] ?? 0);
        if (($pay_status === 'paid' || $pay_status === 'partial_paid') && $paid_amt > 0) {
            $refund_note = " A refund of ₹" . number_format($paid_amt, 2) . " will be processed to your source account within 5-7 working days.";
        }

        echo json_encode([
            'success' => true,
            'message' => 'Order #' . $order_id . ' has been successfully cancelled.' . $refund_note,
            'order_id' => $order_id,
            'order_status' => 'cancelled',
            'cancel_reason' => $full_reason
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel order. Please try again or contact customer care.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action request.']);
exit;

