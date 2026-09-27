<?php
header('Content-Type: application/json; charset=utf-8');
include_once("include/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$name    = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone   = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email   = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validation
$errors = [];

if (empty($name) || mb_strlen($name) < 2) {
    $errors[] = 'Please enter your full name (at least 2 characters).';
} elseif (mb_strlen($name) > 150) {
    $errors[] = 'Name is too long (maximum 150 characters).';
}

// Clean phone (keep only digits)
$clean_phone = preg_replace('/[^\d]/', '', $phone);
if (strlen($clean_phone) > 10 && substr($clean_phone, 0, 2) === '91') {
    $clean_phone = substr($clean_phone, 2);
}

if (empty($clean_phone) || !preg_match('/^[6-9]\d{9}$/', $clean_phone)) {
    $errors[] = 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.';
}

if (!empty($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
        $errors[] = 'Please enter a valid email address.';
    }
}

if (empty($message) || mb_strlen($message) < 5) {
    $errors[] = 'Please enter your message (at least 5 characters).';
} elseif (mb_strlen($message) > 4000) {
    $errors[] = 'Message is too long (maximum 4000 characters).';
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(' ', $errors),
        'errors'  => $errors
    ]);
    exit;
}

$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
if (strpos($ip_address, ',') !== false) {
    $ip_address = trim(explode(',', $ip_address)[0]);
}

try {
    $db = connect();

    // Basic anti-spam: check if same IP submitted within the last 15 seconds
    if (!empty($ip_address)) {
        $check_stmt = $db->select(
            "SELECT id FROM tbl_contact_inquiries WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 15 SECOND) LIMIT 1",
            "s",
            $ip_address
        );
        if ($check_stmt && $check_stmt->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'You are sending messages too quickly. Please wait a moment before trying again.'
            ]);
            exit;
        }
    }

    $insert_id = $db->insert(
        "INSERT INTO tbl_contact_inquiries (name, email, phone, subject, message, ip_address, status) VALUES (?, ?, ?, ?, ?, ?, 'unread')",
        "ssssss",
        $name,
        $email,
        $clean_phone,
        $subject,
        $message,
        $ip_address
    );

    if ($insert_id > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for reaching out! Your inquiry has been submitted successfully. Our team will contact you shortly.',
            'inquiry_id' => $insert_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Unable to save your inquiry. Please try again or contact us directly via phone.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'A system error occurred. Please try again later.'
    ]);
}
