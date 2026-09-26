<?php

/**
 * SMS Gateway Dispatch Service
 * Provider: Hindit SMS Gateway (pushsms.aspx)
 * File: include/sms_service.php
 */

require_once(__DIR__ . "/sms_config.php");

/**
 * Log SMS operations for audit and debugging
 *
 * @param string $action
 * @param array $payload
 * @param bool $success
 * @param string|null $response
 * @param string|null $error
 */
function log_sms_activity($action, array $payload, $success, $response = null, $error = null)
{
    if (!defined('SMS_LOG_ENABLED') || !SMS_LOG_ENABLED) {
        return;
    }

    $log_file = defined('SMS_LOG_FILE') ? SMS_LOG_FILE : __DIR__ . '/../uploads/sms_logs.log';
    $log_dir = dirname($log_file);

    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $status_str = $success ? 'SUCCESS' : 'FAILURE';
    $masked_mobile = isset($payload['mobile']) ? substr($payload['mobile'], 0, 2) . '******' . substr($payload['mobile'], -2) : 'N/A';

    $entry = sprintf(
        "[%s] [%s] Action: %s | Mobile: %s | Status: %s | Response: %s | Error: %s\n",
        $timestamp,
        $status_str,
        $action,
        $masked_mobile,
        $status_str,
        trim($response ?? 'N/A'),
        trim($error ?? 'None')
    );

    @file_put_contents($log_file, $entry, FILE_APPEND | LOCK_EX);
}

/**
 * Retrieve SMS template details from database (with config constants fallback)
 *
 * @param string $template_key
 * @return array
 */
function get_sms_template($template_key = 'login_otp')
{
    $default_id = ($template_key === 'order_placed' && defined('SMS_ORDER_TEMPLATE_ID'))
        ? SMS_ORDER_TEMPLATE_ID
        : (defined('SMS_TEMPLATE_ID') ? SMS_TEMPLATE_ID : '1777179005799027454');

    $default_text = ($template_key === 'order_placed' && defined('SMS_ORDER_TEMPLATE_TEXT'))
        ? SMS_ORDER_TEMPLATE_TEXT
        : (defined('SMS_TEMPLATE_TEXT') ? SMS_TEMPLATE_TEXT : 'BunnyBoss: Your verification OTP is {#alp#}. Valid for {#num#} minutes. Please do not share this OTP with anyone.');

    $default_template = [
        'template_key'  => $template_key,
        'template_id'   => $default_id,
        'sender_id'     => defined('SMS_SENDER_ID') ? SMS_SENDER_ID : 'BUNNYB',
        'template_text' => $default_text
    ];

    try {
        if (function_exists('connect')) {
            $db = connect();
            if ($db) {
                $stmt = $db->select("SELECT template_id, sender_id, template_text FROM tbl_sms_template WHERE template_key = ? AND status = 'active' LIMIT 1", 's', $template_key);
                if ($stmt && $row = $stmt->fetch_assoc()) {
                    return [
                        'template_key'  => $template_key,
                        'template_id'   => !empty($row['template_id']) ? $row['template_id'] : $default_template['template_id'],
                        'sender_id'     => !empty($row['sender_id']) ? $row['sender_id'] : $default_template['sender_id'],
                        'template_text' => !empty($row['template_text']) ? $row['template_text'] : $default_template['template_text']
                    ];
                }
            }
        }
    } catch (\Throwable $e) {
        // Fallback silently to defaults
    }

    return $default_template;
}

/**
 * Send an OTP message to a mobile number using the configured template.
 *
 * @param string $mobile Recipient 10-digit mobile number
 * @param string $otp 6-digit OTP code
 * @param int|null $validity_minutes Validity duration in minutes (defaults to SMS_OTP_EXPIRY_MINUTES)
 * @return array ['success' => bool, 'message' => string, 'response' => string, 'error' => string|null]
 */
function send_sms_otp($mobile, $otp, $validity_minutes = null)
{
    // Sanitize mobile number
    $clean_mobile = preg_replace('/[^0-9]/', '', (string)$mobile);
    if (strlen($clean_mobile) > 10) {
        $clean_mobile = substr($clean_mobile, -10);
    }

    if (strlen($clean_mobile) !== 10 || !preg_match('/^[6-9]\d{9}$/', $clean_mobile)) {
        return [
            'success' => false,
            'message' => 'Invalid mobile number. Must be a 10-digit Indian mobile number.',
            'response' => '',
            'error'   => 'INVALID_MOBILE'
        ];
    }

    if ($validity_minutes === null) {
        $validity_minutes = defined('SMS_OTP_EXPIRY_MINUTES') ? SMS_OTP_EXPIRY_MINUTES : 5;
    }

    // Fetch template from database or fallback to config
    $tmpl_data = get_sms_template('login_otp');
    $template = $tmpl_data['template_text'];
    $template_id = $tmpl_data['template_id'];

    $message_text = str_replace(
        ['{#alp#}', '{#num#}'],
        [(string)$otp, (string)$validity_minutes],
        $template
    );

    return send_sms_message($clean_mobile, $message_text, $template_id, $tmpl_data['sender_id']);
}

/**
 * Send an Order Placed confirmation SMS to the customer.
 * Template: "Hi {#alp#}, your order {#alp#} has been successfully placed with BunnyBoss."
 *
 * @param string $mobile Recipient 10-digit mobile number
 * @param string $customer_name Customer name (first name or full name)
 * @param string|int $order_id Order reference number
 * @return array ['success' => bool, 'message' => string, 'response' => string, 'error' => string|null]
 */
function send_sms_order_placed($mobile, $customer_name, $order_id)
{
    // Sanitize mobile number
    $clean_mobile = preg_replace('/[^0-9]/', '', (string)$mobile);
    if (strlen($clean_mobile) > 10) {
        $clean_mobile = substr($clean_mobile, -10);
    }

    if (strlen($clean_mobile) !== 10 || !preg_match('/^[6-9]\d{9}$/', $clean_mobile)) {
        return [
            'success' => false,
            'message' => 'Invalid mobile number. Must be a 10-digit Indian mobile number.',
            'response' => '',
            'error'   => 'INVALID_MOBILE'
        ];
    }

    // Clean name - if blank, fallback to 'Customer'
    $name = trim(preg_replace('/[^a-zA-Z0-9\s]/', '', (string)$customer_name));
    if ($name === '') {
        $name = 'Customer';
    }

    // Format order ID (e.g. #2466)
    $clean_order = trim((string)$order_id);
    if ($clean_order === '') {
        $clean_order = 'N/A';
    }
    $order_val = (strpos($clean_order, '#') === 0) ? $clean_order : '#' . $clean_order;

    // Fetch template from database or fallback to config
    $tmpl_data = get_sms_template('order_placed');
    $template = $tmpl_data['template_text'];
    $template_id = $tmpl_data['template_id'];

    // Replace the two {#alp#} placeholders sequentially
    // First {#alp#} => customer name
    // Second {#alp#} => order id
    $message_text = $template;
    $pos1 = strpos($message_text, '{#alp#}');
    if ($pos1 !== false) {
        $message_text = substr_replace($message_text, $name, $pos1, strlen('{#alp#}'));
    }
    $pos2 = strpos($message_text, '{#alp#}');
    if ($pos2 !== false) {
        $message_text = substr_replace($message_text, $order_val, $pos2, strlen('{#alp#}'));
    }

    return send_sms_message($clean_mobile, $message_text, $template_id, $tmpl_data['sender_id']);
}

/**
 * Dispatch Order Placed SMS directly by Order ID.
 * Loads customer phone and name from tbl_orders and guards against duplicate dispatch.
 *
 * @param int $order_id
 * @return array
 */
function send_order_placed_sms_by_id($order_id)
{
    $order_id = (int)$order_id;
    if ($order_id <= 0) {
        return ['success' => false, 'message' => 'Invalid order ID.', 'error' => 'INVALID_ORDER_ID'];
    }

    try {
        $db = function_exists('connect') ? connect() : null;
        if (!$db) {
            return ['success' => false, 'message' => 'Database connection failed.', 'error' => 'DB_CONN_FAILED'];
        }

        // Fetch order details
        $stmt = $db->select("SELECT order_id, first_name, last_name, phone, is_order_sms_sent FROM tbl_orders WHERE order_id = ? LIMIT 1", 'i', $order_id);
        if (!$stmt || !($order = $stmt->fetch_assoc())) {
            return ['success' => false, 'message' => 'Order not found.', 'error' => 'ORDER_NOT_FOUND'];
        }

        // Check if already sent
        if (!empty($order['is_order_sms_sent'])) {
            return ['success' => true, 'message' => 'Order SMS already sent.', 'response' => 'ALREADY_SENT', 'error' => null];
        }

        $phone = $order['phone'] ?? '';
        $name = trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''));
        if (empty($name)) {
            $name = $order['first_name'] ?? '';
        }

        // Send SMS
        $res = send_sms_order_placed($phone, $name, $order_id);

        // Mark as sent in database if successfully dispatched via gateway
        if (!empty($res['success']) && ($res['response'] ?? '') !== 'SMS_DISABLED_SIMULATED') {
            $db->update("UPDATE tbl_orders SET is_order_sms_sent = 1 WHERE order_id = ?", 'i', $order_id);
        }

        return $res;
    } catch (\Throwable $e) {
        error_log("Failed to dispatch order placed SMS for order {$order_id}: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage(), 'error' => 'EXCEPTION'];
    }
}


/**
 * Dispatch an SMS via Hindit Push SMS Gateway HTTP API
 *
 * @param string $mobile 10-digit recipient number
 * @param string $message_text Complete message text
 * @param string|null $template_id DLT approved Template ID
 * @param string|null $custom_sender_id Sender ID (defaults to SMS_SENDER_ID)
 * @return array ['success' => bool, 'message' => string, 'response' => string, 'error' => string|null]
 */
function send_sms_message($mobile, $message_text, $template_id = null, $custom_sender_id = null)
{
    // Check if SMS dispatch is globally enabled
    if (!defined('_ENABLE_SMS_') || !_ENABLE_SMS_) {
        log_sms_activity('send_sms_disabled', ['mobile' => $mobile], true, 'SMS dispatch disabled in config');
        return [
            'success' => true,
            'message' => 'SMS service is simulated (disabled in config).',
            'response' => 'SMS_DISABLED_SIMULATED',
            'error'   => null
        ];
    }

    $gateway_url = defined('SMS_GATEWAY_URL') ? SMS_GATEWAY_URL : 'http://hindit.co.in/API/pushsms.aspx';
    $login_id    = defined('SMS_LOGIN_ID') ? SMS_LOGIN_ID : '';
    $password    = defined('SMS_PASSWORD') ? SMS_PASSWORD : '';
    $sender_id   = $custom_sender_id ?: (defined('SMS_SENDER_ID') ? SMS_SENDER_ID : 'BUNNYB');
    $route_id    = defined('SMS_ROUTE_ID') ? SMS_ROUTE_ID : '2';
    $unicode     = defined('SMS_UNICODE') ? SMS_UNICODE : '0';
    $ip          = defined('SMS_IP') ? SMS_IP : 'x.x.x.x';
    $tmpl_id     = $template_id ?: (defined('SMS_TEMPLATE_ID') ? SMS_TEMPLATE_ID : '');
    $timeout     = defined('SMS_TIMEOUT_SECONDS') ? SMS_TIMEOUT_SECONDS : 7;

    // Assemble query parameters matching Hindit SMS API specification
    $params = [
        'loginID'     => $login_id,
        'password'    => $password,
        'mobile'      => $mobile,
        'text'        => $message_text,
        'senderid'    => $sender_id,
        'route_id'    => $route_id,
        'Unicode'     => $unicode,
        'IP'          => $ip,
        'Template_id' => $tmpl_id
    ];

    $query_string = http_build_query($params);
    $full_url = $gateway_url . '?' . $query_string;

    $response = null;
    $error_msg = null;
    $http_code = 0;

    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $full_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT      => 'BunnyBoss-SMS-Client/2.0'
        ]);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        if ($curl_err) {
            $error_msg = 'cURL Error: ' . $curl_err;
        }
    } else {
        // Fallback to stream context if curl is not present
        $ctx = stream_context_create([
            'http' => [
                'timeout'    => $timeout,
                'user_agent' => 'BunnyBoss-SMS-Client/2.0'
            ]
        ]);
        $response = @file_get_contents($full_url, false, $ctx);
        if ($response === false) {
            $error_msg = 'Failed to open HTTP stream to SMS gateway.';
        }
    }

    $is_success = false;
    $status_msg = '';

    if (!empty($error_msg)) {
        $status_msg = 'Gateway connection error: ' . $error_msg;
        log_sms_activity('send_sms', ['mobile' => $mobile, 'text' => $message_text], false, $response, $error_msg);
        return [
            'success'  => false,
            'message'  => $status_msg,
            'response' => (string)$response,
            'error'    => $error_msg
        ];
    }

    // Evaluate gateway response
    $trimmed_resp = trim((string)$response);
    $has_sent = preg_match('/messages?\s+has\s+been\s+sent/i', $trimmed_resp);
    $is_err = preg_match('/error|fail|invalid|denied|err:|insufficient|balance\s*:\s*0/i', $trimmed_resp);

    if ($trimmed_resp !== '' && stripos($trimmed_resp, 'insufficient balance') !== false) {
        $is_success = false;
        $status_msg = 'SMS Gateway Error: Insufficient SMS credits/balance on account.';
        $error_msg  = 'INSUFFICIENT_SMS_BALANCE';
    } elseif ($has_sent || ($trimmed_resp !== '' && !$is_err)) {
        $is_success = true;
        $status_msg = 'SMS dispatched successfully.';
    } elseif ($trimmed_resp !== '' && $is_err) {
        $is_success = false;
        $status_msg = 'SMS Gateway reported error: ' . strip_tags($trimmed_resp);
        $error_msg  = strip_tags($trimmed_resp);
    } else {
        // Empty response
        $is_success = false;
        $status_msg = 'Empty response from SMS Gateway.';
        $error_msg  = 'EMPTY_RESPONSE';
    }

    log_sms_activity('send_sms', ['mobile' => $mobile, 'text' => $message_text], $is_success, $trimmed_resp, $error_msg);

    return [
        'success'  => $is_success,
        'message'  => $status_msg,
        'response' => $trimmed_resp,
        'error'    => $error_msg
    ];
}
