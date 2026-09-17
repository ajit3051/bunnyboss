<?php
/**
 * User Profile & Address Management API Endpoint
 * Path: /api/user.php
 */
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/auth_helper.php");

send_api_headers();

// Require valid logged-in user token
$token_data = validate_api_token(true);
$user_data = $token_data['user'];

$raw_input = file_get_contents('php_input') ?: file_get_contents('php://input');
$json_data = json_decode($raw_input, true) ?: [];
$request = array_merge($_GET, $_POST, $json_data);

$action = $request['action'] ?? '';

$db = connect();

$user_id = (int)$user_data['id'];
$user_mobile = $user_data['mobile'];

// ---------------------------------------------------------
// 1. UPDATE PROFILE
// ---------------------------------------------------------
if ($action === 'update_profile') {
    $name = trim($request['name'] ?? '');
    $email = trim($request['email'] ?? '');

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
        echo json_encode(['success' => false, 'message' => 'Failed to update profile.']);
    }
    exit;
}

// ---------------------------------------------------------
// 2. GET ALL SAVED ADDRESSES
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
// 3. GET SINGLE ADDRESS BY ID
// ---------------------------------------------------------
if ($action === 'get_address') {
    $address_id = (int)($request['address_id'] ?? 0);
    $stmt = $db->select("SELECT * FROM tbl_user_addresses WHERE id = ? AND user_id = ?", 'ii', $address_id, $user_id);
    if ($stmt && $row = $stmt->fetch_assoc()) {
        echo json_encode(['success' => true, 'address' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Address not found.']);
    }
    exit;
}

// ---------------------------------------------------------
// 4. SAVE ADDRESS (INSERT OR UPDATE)
// ---------------------------------------------------------
if ($action === 'save_address') {
    $address_id     = (int)($request['address_id'] ?? 0);
    $title          = trim($request['title'] ?? 'Home');
    $first_name     = trim($request['first_name'] ?? '');
    $last_name      = trim($request['last_name'] ?? '');
    $phone          = trim($request['phone'] ?? '');
    $street_address = trim($request['street_address'] ?? '');
    $city           = trim($request['city'] ?? '');
    $state          = trim($request['state'] ?? '');
    $postcode       = trim($request['postcode'] ?? '');
    $is_default     = isset($request['is_default']) ? 1 : 0;

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
        echo json_encode(['success' => false, 'message' => 'Valid 10-digit phone number is required.']);
        exit;
    }

    $count_stmt = $db->select("SELECT COUNT(*) as cnt FROM tbl_user_addresses WHERE user_id = ?", 'i', $user_id);
    $addr_cnt = ($count_stmt && $c_row = $count_stmt->fetch_assoc()) ? (int)$c_row['cnt'] : 0;
    if ($addr_cnt === 0) {
        $is_default = 1;
    }

    if ($is_default === 1) {
        $db->update("UPDATE tbl_user_addresses SET is_default = 0 WHERE user_id = ?", 'i', $user_id);
    }

    if ($address_id > 0) {
        $updated = $db->update(
            "UPDATE tbl_user_addresses SET title = ?, first_name = ?, last_name = ?, phone = ?, street_address = ?, city = ?, state = ?, postcode = ?, is_default = ? WHERE id = ? AND user_id = ?",
            'ssssssssiii',
            $title, $first_name, $last_name, $phone, $street_address, $city, $state, $postcode, $is_default, $address_id, $user_id
        );

        if ($updated !== false) {
            echo json_encode(['success' => true, 'message' => 'Address updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not update address.']);
        }
    } else {
        $new_id = $db->insert(
            "INSERT INTO tbl_user_addresses (user_id, title, first_name, last_name, phone, street_address, city, state, postcode, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            'issssssssi',
            $user_id, $title, $first_name, $last_name, $phone, $street_address, $city, $state, $postcode, $is_default
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
    $address_id = (int)($request['address_id'] ?? 0);
    if ($address_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid address ID.']);
        exit;
    }

    $deleted = $db->delete("DELETE FROM tbl_user_addresses WHERE id = ? AND user_id = ?", 'ii', $address_id, $user_id);
    if ($deleted) {
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
    $address_id = (int)($request['address_id'] ?? 0);
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

echo json_encode(['success' => false, 'message' => 'Invalid user API action request.']);
exit;
