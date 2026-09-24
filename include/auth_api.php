<?php
require_once(__DIR__ . "/config.php");

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

if ($action === 'send_otp') {
    $raw_mobile = $_POST['mobile'] ?? '';
    $mobile = preg_replace('/[^0-9]/', '', $raw_mobile);

    if (strlen($mobile) !== 10 || !preg_match('/^[6-9]\d{9}$/', $mobile)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid 10-digit mobile number starting with 6-9.']);
        exit;
    }

    $otp = (string) rand(100000, 999999);
    $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    $db = connect();

    // Check if user exists
    $stmt = $db->select("SELECT id, status FROM tbl_users WHERE mobile = ?", 's', $mobile);
    $user = $stmt ? $stmt->fetch_assoc() : null;

    if ($user) {
        if ($user['status'] === 'inactive') {
            echo json_encode(['success' => false, 'message' => 'Your account is inactive. Please contact support.']);
            exit;
        }
        // Update OTP
        $db->update("UPDATE tbl_users SET otp = ?, otp_expiry = ? WHERE mobile = ?", 'sss', $otp, $expiry, $mobile);
    } else {
        // Insert new user as customer
        $db->insert("INSERT INTO tbl_users (mobile, role, otp, otp_expiry, status) VALUES (?, 'customer', ?, ?, 'active')", 'sss', $mobile, $otp, $expiry);
    }

    $_SESSION['otp_mobile'] = $mobile;
    $_SESSION['otp_code'] = $otp;
    $_SESSION['otp_expiry'] = $expiry;

    // Send OTP via configured SMS Gateway
    $validity_min = defined('SMS_OTP_EXPIRY_MINUTES') ? (int)SMS_OTP_EXPIRY_MINUTES : 5;
    $sms_result = ['success' => true, 'message' => ''];
    if (defined('_ENABLE_SMS_') && _ENABLE_SMS_) {
        $sms_result = send_sms_otp($mobile, $otp, $validity_min);
    }

    $response_payload = [
        'success' => true,
        'message' => 'OTP sent successfully to +91-' . $mobile
    ];

    // Expose debug_otp only if SMS_DEBUG_MODE is active
    if (defined('SMS_DEBUG_MODE') && SMS_DEBUG_MODE) {
        $response_payload['debug_otp'] = $otp;
        if (!$sms_result['success']) {
            $response_payload['sms_debug_info'] = $sms_result['message'];
        }
    }

    echo json_encode($response_payload);
    exit;
}

if ($action === 'verify_otp') {
    $raw_mobile = $_POST['mobile'] ?? '';
    $mobile = preg_replace('/[^0-9]/', '', $raw_mobile);
    $otp = trim($_POST['otp'] ?? '');

    if (empty($mobile) || empty($otp)) {
        echo json_encode(['success' => false, 'message' => 'Mobile number and OTP are required.']);
        exit;
    }

    $db = connect();
    $stmt = $db->select("SELECT * FROM tbl_users WHERE mobile = ? AND status = 'active'", 's', $mobile);
    $user = $stmt ? $stmt->fetch_assoc() : null;

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found. Please request a new OTP.']);
        exit;
    }

    $now = date('Y-m-d H:i:s');
    $is_valid_db_otp = ($user['otp'] === $otp && $user['otp_expiry'] >= $now);
    $is_valid_session_otp = (isset($_SESSION['otp_code']) && $_SESSION['otp_code'] === $otp && isset($_SESSION['otp_mobile']) && $_SESSION['otp_mobile'] === $mobile);

    if ($is_valid_db_otp || $is_valid_session_otp) {
        // Clear used OTP
        $db->update("UPDATE tbl_users SET otp = NULL, otp_expiry = NULL WHERE id = ?", 'i', $user['id']);
        unset($_SESSION['otp_code'], $_SESSION['otp_mobile'], $_SESSION['otp_expiry']);

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = !empty($user['name']) ? $user['name'] : 'User (' . substr($user['mobile'], -4) . ')';
        $_SESSION['user_mobile'] = $user['mobile'];
        $_SESSION['user_role'] = $user['role'];

        // Determine redirect route based on role
        if (in_array($user['role'], ['admin', 'staff'], true)) {
            $redirect_url = _ADMIN_URL . 'index.php';
        } else {
            $redirect_url = $_SESSION['redirect_after_login'] ?? _BASEURL . 'index.php';
        }

        echo json_encode([
            'success' => true,
            'message' => 'Login successful! Welcome back.',
            'role' => $user['role'],
            'redirect_url' => $redirect_url
        ]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP. Please try again.']);
        exit;
    }
}

if ($action === 'logout') {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_mobile'], $_SESSION['user_role']);
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully.',
        'redirect_url' => _BASEURL . 'index.php'
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
exit;
