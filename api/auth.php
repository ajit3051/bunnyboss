<?php

/**
 * Authentication & Session API Endpoint
 * Path: /api/auth.php
 */
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/auth_helper.php");

send_api_headers();

// Support JSON POST body or standard $_POST / $_GET
$raw_input = file_get_contents('php_input') ?: file_get_contents('php://input');
$json_data = json_decode($raw_input, true) ?: [];
$request = array_merge($_GET, $_POST, $json_data);

$action = $request['action'] ?? '';

$db = connect();

// ---------------------------------------------------------
// 1. SEND OTP
// ---------------------------------------------------------
if ($action === 'send_otp') {
    $raw_mobile = $request['mobile'] ?? '';
    $mobile = preg_replace('/[^0-9]/', '', $raw_mobile);

    if (strlen($mobile) !== 10 || !preg_match('/^[6-9]\d{9}$/', $mobile)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid 10-digit mobile number starting with 6-9.']);
        exit;
    }

    $otp = (defined('API_STATIC_OTP') && API_STATIC_OTP !== '' && API_STATIC_OTP !== false) 
        ? (string) API_STATIC_OTP 
        : (string) rand(pow(10, API_OTP_LENGTH - 1), pow(10, API_OTP_LENGTH) - 1);
    $expiry = date('Y-m-d H:i:s', strtotime('+' . API_OTP_EXPIRY_MINUTES . ' minutes'));

    // Check user record
    $stmt = $db->select("SELECT id, status FROM tbl_users WHERE mobile = ?", 's', $mobile);
    $user = $stmt ? $stmt->fetch_assoc() : null;

    if ($user) {
        if ($user['status'] === 'inactive') {
            echo json_encode(['success' => false, 'message' => 'Your account is inactive. Please contact support.']);
            exit;
        }
        $db->update("UPDATE tbl_users SET otp = ?, otp_expiry = ? WHERE mobile = ?", 'sss', $otp, $expiry, $mobile);
    } else {
        $db->insert("INSERT INTO tbl_users (mobile, role, otp, otp_expiry, status) VALUES (?, 'customer', ?, ?, 'active')", 'sss', $mobile, $otp, $expiry);
    }

    $_SESSION['otp_mobile'] = $mobile;
    $_SESSION['otp_code'] = $otp;
    $_SESSION['otp_expiry'] = $expiry;

    echo json_encode([
        'success' => true,
        'message' => 'OTP sent successfully to +91-' . $mobile,
        'debug_otp' => $otp
    ]);
    exit;
}

// ---------------------------------------------------------
// 2. VERIFY OTP & GENERATE BEARER TOKEN
// ---------------------------------------------------------
if ($action === 'verify_otp') {
    $raw_mobile = $request['mobile'] ?? '';
    $mobile = preg_replace('/[^0-9]/', '', $raw_mobile);
    $otp = trim($request['otp'] ?? '');

    if (empty($mobile) || empty($otp)) {
        echo json_encode(['success' => false, 'message' => 'Mobile number and OTP are required.']);
        exit;
    }

    $stmt = $db->select("SELECT * FROM tbl_users WHERE mobile = ? AND status = 'active'", 's', $mobile);
    $user = $stmt ? $stmt->fetch_assoc() : null;

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User record not found. Please request a new OTP.']);
        exit;
    }

    $now = date('Y-m-d H:i:s');
    $is_valid_db_otp = ($user['otp'] === $otp && $user['otp_expiry'] >= $now);
    $is_valid_session_otp = (isset($_SESSION['otp_code']) && $_SESSION['otp_code'] === $otp && isset($_SESSION['otp_mobile']) && $_SESSION['otp_mobile'] === $mobile);
    $is_valid_static_otp = (defined('API_STATIC_OTP') && API_STATIC_OTP !== '' && API_STATIC_OTP !== false && $otp === (string)API_STATIC_OTP);

    if ($is_valid_db_otp || $is_valid_session_otp || $is_valid_static_otp) {
        $db->update("UPDATE tbl_users SET otp = NULL, otp_expiry = NULL WHERE id = ?", 'i', $user['id']);
        unset($_SESSION['otp_code'], $_SESSION['otp_mobile'], $_SESSION['otp_expiry']);

        $user_token = generate_user_api_token($user['id']);

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = !empty($user['name']) ? $user['name'] : 'User (' . substr($user['mobile'], -4) . ')';
        $_SESSION['user_mobile'] = $user['mobile'];
        $_SESSION['user_role'] = $user['role'];

        $redirect_url = in_array($user['role'], ['admin', 'staff'], true) ? _ADMIN_URL . 'index.php' : (_BASEURL . 'index.php');

        echo json_encode([
            'success' => true,
            'message' => 'Login successful!',
            'token' => $user_token,
            'user' => [
                'id' => (int)$user['id'],
                'name' => $_SESSION['user_name'],
                'mobile' => $user['mobile'],
                'email' => $user['email'] ?? '',
                'role' => $user['role']
            ],
            'redirect_url' => $redirect_url
        ]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP code.']);
        exit;
    }
}

// ---------------------------------------------------------
// 3. GET CURRENT USER PROFILE
// ---------------------------------------------------------
if ($action === 'get_user') {
    $token_data = validate_api_token(true);
    $user = $token_data['user'];

    echo json_encode([
        'success' => true,
        'logged_in' => true,
        'user' => [
            'id' => (int)$user['id'],
            'name' => $user['name'] ?? '',
            'mobile' => $user['mobile'],
            'email' => $user['email'] ?? '',
            'role' => $user['role']
        ]
    ]);
    exit;
}

// ---------------------------------------------------------
// 4. LOGOUT
// ---------------------------------------------------------
if ($action === 'logout') {
    $token_data = validate_api_token(true);
    if (!empty($token_data['user']['id'])) {
        $db->update("UPDATE tbl_users SET api_token = NULL WHERE id = ?", 'i', $token_data['user']['id']);
    }
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_mobile'], $_SESSION['user_role'], $_SESSION['api_token']);
    session_destroy();
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully.',
        'redirect_url' => _BASEURL . 'index.php'
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid auth action request.']);
exit;
