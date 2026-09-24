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
$test_type   = $_POST['sms_type'] ?? 'otp';
$test_name   = $_POST['customer_name'] ?? 'Ajit Kumar';
$test_order  = $_POST['order_id'] ?? '2466';
$action      = $_POST['action'] ?? ($argv[1] ? 'send' : '');

$result = null;

if ($action === 'send' && !empty($test_mobile)) {
    if ($test_type === 'order') {
        $result = send_sms_order_placed($test_mobile, $test_name, $test_order);
    } else {
        $result = send_sms_otp($test_mobile, $test_otp, 5);
    }
}

// Read last 15 lines of log file
$log_lines = [];
$log_file = defined('SMS_LOG_FILE') ? SMS_LOG_FILE : __DIR__ . '/uploads/sms_logs.log';
if (file_exists($log_file)) {
    $all_lines = file($log_file);
    $log_lines = array_slice($all_lines, -15);
}

// Fetch templates from database
$db_templates = [];
try {
    $db = connect();
    if ($db) {
        $t_res = $db->select("SELECT * FROM tbl_sms_template ORDER BY id ASC");
        while ($r = $t_res->fetch_assoc()) {
            $db_templates[] = $r;
        }
    }
} catch (\Throwable $e) {}

if ($cli_mode) {
    echo "=== BunnyBoss Hindit SMS Gateway CLI Diagnostic ===\n\n";
    echo "Gateway URL:     " . SMS_GATEWAY_URL . "\n";
    echo "Login ID:        " . SMS_LOGIN_ID . "\n";
    echo "Sender ID:       " . SMS_SENDER_ID . "\n";
    echo "Route ID:        " . SMS_ROUTE_ID . "\n";
    echo "Configured IP:   " . SMS_IP . "\n";
    echo "SMS Enabled:     " . (_ENABLE_SMS_ ? 'YES' : 'NO') . "\n";
    echo "Debug Mode:      " . (SMS_DEBUG_MODE ? 'YES' : 'NO') . "\n\n";

    if ($action === 'send') {
        echo "Testing dispatch ({$test_type}) to: {$test_mobile}\n";
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
                    <tr><th>Authorized Server IP</th><td><code><?= htmlspecialchars(SMS_IP) ?></code></td></tr>
                    <tr><th>Debug Mode</th><td><?= SMS_DEBUG_MODE ? '<span class="badge badge-warning text-dark">Active (Debug OTP Enabled)</span>' : '<span class="badge badge-secondary">Production (Debug OTP Hidden)</span>' ?></td></tr>
                    <tr><th>Log File</th><td><code><?= htmlspecialchars(SMS_LOG_FILE) ?></code></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Registered SMS Templates in tbl_sms_template -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Database SMS Templates (<code>tbl_sms_template</code>)</span>
            <span class="badge badge-info"><?= count($db_templates) ?> templates</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 140px;">Template Key</th>
                            <th style="width: 160px;">Name</th>
                            <th style="width: 170px;">Template ID</th>
                            <th>Template Text</th>
                            <th style="width: 70px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($db_templates)): ?>
                            <?php foreach ($db_templates as $tmpl): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($tmpl['template_key']) ?></code></td>
                                    <td><?= htmlspecialchars($tmpl['template_name']) ?></td>
                                    <td><code><?= htmlspecialchars($tmpl['template_id']) ?></code></td>
                                    <td><?= htmlspecialchars($tmpl['template_text']) ?></td>
                                    <td><span class="badge <?= $tmpl['status'] === 'active' ? 'badge-success' : 'badge-danger' ?>"><?= htmlspecialchars($tmpl['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-muted">No templates found in database.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Dispatch Test Form -->
    <div class="card">
        <div class="card-header">Dispatch Test SMS</div>
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
                    <div class="col-md-4 form-group">
                        <label>Template Type:</label>
                        <select name="sms_type" id="sms_type" class="form-control" onchange="toggleTemplateFields()">
                            <option value="otp" <?= $test_type === 'otp' ? 'selected' : '' ?>>Login OTP (login_otp)</option>
                            <option value="order" <?= $test_type === 'order' ? 'selected' : '' ?>>Order Confirmation (order_placed)</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Recipient Mobile Number:</label>
                        <input type="tel" name="mobile" class="form-control" value="<?= htmlspecialchars($test_mobile) ?>" placeholder="10-digit mobile" required pattern="[6-9][0-9]{9}">
                    </div>
                    <div class="col-md-4 form-group" id="field_otp" style="<?= $test_type === 'order' ? 'display:none;' : '' ?>">
                        <label>OTP Code to Send:</label>
                        <input type="text" name="otp" class="form-control" value="<?= htmlspecialchars($test_otp) ?>" maxlength="6">
                    </div>
                    <div class="col-md-4 form-group" id="field_name" style="<?= $test_type === 'order' ? '' : 'display:none;' ?>">
                        <label>Customer Name:</label>
                        <input type="text" name="customer_name" class="form-control" value="<?= htmlspecialchars($test_name) ?>">
                    </div>
                    <div class="col-md-4 form-group" id="field_order" style="<?= $test_type === 'order' ? '' : 'display:none;' ?>">
                        <label>Order ID (e.g. 2466):</label>
                        <input type="text" name="order_id" class="form-control" value="<?= htmlspecialchars($test_order) ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Dispatch Test SMS</button>
            </form>
            <script>
                function toggleTemplateFields() {
                    var val = document.getElementById('sms_type').value;
                    var isOrder = (val === 'order');
                    document.getElementById('field_otp').style.display = isOrder ? 'none' : 'block';
                    document.getElementById('field_name').style.display = isOrder ? 'block' : 'none';
                    document.getElementById('field_order').style.display = isOrder ? 'block' : 'none';
                }
            </script>
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
