<?php

/**
 * Diagnostic & Testing Script for SMS Gateway
 * Provider: Hindit SMS Gateway (pushsms.aspx)
 * File: test_sms.php
 */

require_once(__DIR__ . "/include/config.php");

$cli_mode = (php_sapi_name() === 'cli');

$test_mobile = $_POST['mobile'] ?? ($argv[1] ?? '9999441707');
$test_otp    = $_POST['otp'] ?? ($argv[2] ?? '123456');
$action      = $_POST['action'] ?? ($argv[1] ? 'send' : '');

$result = null;

if ($action === 'send' && !empty($test_mobile)) {
    $result = send_sms_otp($test_mobile, $test_otp, 5);
}

// Read last 15 lines of log file
$log_lines = [];
$log_file = defined('SMS_LOG_FILE') ? SMS_LOG_FILE : __DIR__ . '/uploads/sms_logs.log';
if (file_exists($log_file)) {
    $all_lines = file($log_file);
    $log_lines = array_slice($all_lines, -15);
}

if ($cli_mode) {
    echo "=== BunnyBoss Hindit SMS Gateway CLI Diagnostic ===\n\n";
    echo "Gateway URL:     " . SMS_GATEWAY_URL . "\n";
    echo "Login ID:        " . SMS_LOGIN_ID . "\n";
    echo "Sender ID:       " . SMS_SENDER_ID . "\n";
    echo "Route ID:        " . SMS_ROUTE_ID . "\n";
    echo "Template ID:     " . SMS_TEMPLATE_ID . "\n";
    echo "Template Text:   " . SMS_TEMPLATE_TEXT . "\n";
    echo "Configured IP:   " . SMS_IP . "\n";
    echo "SMS Enabled:     " . (_ENABLE_SMS_ ? 'YES' : 'NO') . "\n";
    echo "Debug Mode:      " . (SMS_DEBUG_MODE ? 'YES' : 'NO') . "\n\n";

    if ($action === 'send') {
        echo "Testing dispatch to: {$test_mobile} with OTP: {$test_otp}\n";
        echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Usage: php test_sms.php <mobile_number> [optional_otp]\n";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SMS Gateway Diagnostic - BunnyBoss</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; padding: 40px 20px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .card { border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .card-header { background: #ffffff; border-bottom: 1px solid #e2e8f0; font-weight: 600; font-size: 16px; }
        pre { background: #1e293b; color: #f8fafc; padding: 15px; border-radius: 6px; font-size: 13px; max-height: 350px; overflow-y: auto; }
        .badge-enabled { background-color: #10b981; color: white; padding: 4px 8px; border-radius: 4px; }
        .badge-disabled { background-color: #ef4444; color: white; padding: 4px 8px; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>SMS Gateway Diagnostic & Testing Tool</h2>
        <a href="<?= _BASEURL ?>login.php" class="btn btn-outline-secondary btn-sm">&larr; Back to Login</a>
    </div>

    <!-- Configuration Overview -->
    <div class="card">
        <div class="card-header">Current Configuration (from <code>include/sms_config.php</code>)</div>
        <div class="card-body">
            <table class="table table-sm table-bordered mb-0">
                <tbody>
                    <tr><th style="width: 25%;">Master Switch (_ENABLE_SMS_)</th><td><?= _ENABLE_SMS_ ? '<span class="badge-enabled">Enabled</span>' : '<span class="badge-disabled">Disabled</span>' ?></td></tr>
                    <tr><th>Gateway URL</th><td><code><?= htmlspecialchars(SMS_GATEWAY_URL) ?></code></td></tr>
                    <tr><th>Login ID</th><td><code><?= htmlspecialchars(SMS_LOGIN_ID) ?></code></td></tr>
                    <tr><th>Sender ID</th><td><span class="badge badge-primary"><?= htmlspecialchars(SMS_SENDER_ID) ?></span></td></tr>
                    <tr><th>Route ID</th><td><?= htmlspecialchars(SMS_ROUTE_ID) ?> (Transactional / OTP)</td></tr>
                    <tr><th>Template ID</th><td><code><?= htmlspecialchars(SMS_TEMPLATE_ID) ?></code></td></tr>
                    <tr><th>Template Text</th><td><?= htmlspecialchars(SMS_TEMPLATE_TEXT) ?></td></tr>
                    <tr><th>Authorized Server IP</th><td><code><?= htmlspecialchars(SMS_IP) ?></code></td></tr>
                    <tr><th>Debug Mode</th><td><?= SMS_DEBUG_MODE ? '<span class="badge badge-warning text-dark">Active (Debug OTP Enabled)</span>' : '<span class="badge badge-secondary">Production (Debug OTP Hidden)</span>' ?></td></tr>
                    <tr><th>Log File</th><td><code><?= htmlspecialchars(SMS_LOG_FILE) ?></code></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dispatch Test Form -->
    <div class="card">
        <div class="card-header">Send Test OTP</div>
        <div class="card-body">
            <?php if ($result !== null): ?>
                <div class="alert <?= $result['success'] ? 'alert-success' : 'alert-danger' ?>">
                    <strong>Status:</strong> <?= $result['success'] ? 'SUCCESS' : 'FAILURE' ?><br>
                    <strong>Message:</strong> <?= htmlspecialchars($result['message']) ?><br>
                    <strong>Gateway Response:</strong> <code><?= htmlspecialchars($result['response'] ?: 'None') ?></code><br>
                    <?php if (!empty($result['error'])): ?>
                        <strong>Error Details:</strong> <code><?= htmlspecialchars($result['error']) ?></code>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="action" value="send">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Recipient Mobile Number:</label>
                        <input type="tel" name="mobile" class="form-control" value="<?= htmlspecialchars($test_mobile) ?>" placeholder="10-digit mobile" required pattern="[6-9][0-9]{9}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>OTP Code to Send:</label>
                        <input type="text" name="otp" class="form-control" value="<?= htmlspecialchars($test_otp) ?>" maxlength="6" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Dispatch Test SMS</button>
            </form>
        </div>
    </div>

    <!-- Recent Logs -->
    <div class="card">
        <div class="card-header">Recent SMS Logs (Last 15 Entries)</div>
        <div class="card-body">
            <?php if (!empty($log_lines)): ?>
                <pre><?= htmlspecialchars(implode("", $log_lines)) ?></pre>
            <?php else: ?>
                <p class="text-muted mb-0">No log entries found yet in <code><?= htmlspecialchars(SMS_LOG_FILE) ?></code>.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
