<?php
require_once __DIR__ . '/config.php';

send_api_headers();

$pincode = trim($_GET['pincode'] ?? $_POST['pincode'] ?? '');

if (empty($pincode)) {
    echo json_encode([
        'success'     => false,
        'serviceable' => false,
        'message'     => 'Pincode is required.'
    ]);
    exit;
}

if (!preg_match('/^[0-9]{6}$/', $pincode)) {
    echo json_encode([
        'success'     => false,
        'serviceable' => false,
        'message'     => 'Please enter a valid 6-digit pincode.'
    ]);
    exit;
}

$result = checkCourierServiceability($pincode);
echo json_encode($result);
exit;
