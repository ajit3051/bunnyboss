<?php

/**
 * SMS Gateway Configuration Settings
 * Provider: Hindit SMS Gateway (pushsms.aspx)
 * File: include/sms_config.php
 */

// Master toggle to enable or disable SMS dispatch
if (!defined('_ENABLE_SMS_')) {
    define('_ENABLE_SMS_', false);
}

// SMS Gateway Endpoint URL
if (!defined('SMS_GATEWAY_URL')) {
    define('SMS_GATEWAY_URL', 'http://hindit.co.in/API/pushsms.aspx');
}

// SMS Gateway Account Credentials
if (!defined('SMS_LOGIN_ID')) {
    define('SMS_LOGIN_ID', 'T1BUNNYB');
}

if (!defined('SMS_PASSWORD')) {
    define('SMS_PASSWORD', 'bunny1234');
}

// Sender ID registered on DLT portal (e.g. BUNNYB)
if (!defined('SMS_SENDER_ID')) {
    define('SMS_SENDER_ID', 'BUNNYB');
}

// Route ID (2 = Transactional / OTP route)
if (!defined('SMS_ROUTE_ID')) {
    define('SMS_ROUTE_ID', '2');
}

// Unicode flag (0 = standard English GSM 7-bit, 1 = Unicode)
if (!defined('SMS_UNICODE')) {
    define('SMS_UNICODE', '0');
}

// Registered IP address authorized by SMS provider (defaults to server IP or fallback)
if (!defined('SMS_IP')) {
    $server_ip = $_SERVER['SERVER_ADDR'] ?? ($_SERVER['LOCAL_ADDR'] ?? 'x.x.x.x');
    define('SMS_IP', $server_ip);
}

// DLT Approved Template ID for Login OTP
if (!defined('SMS_TEMPLATE_ID')) {
    define('SMS_TEMPLATE_ID', '1777179005799027454');
}

// DLT Approved Template Message Text
// Note: {#alp#} is placeholder for OTP, {#num#} is placeholder for minutes
if (!defined('SMS_TEMPLATE_TEXT')) {
    define('SMS_TEMPLATE_TEXT', 'BunnyBoss: Your verification OTP is {#alp#}. Valid for {#num#} minutes. Please do not share this OTP with anyone.');
}

// DLT Approved Template ID for Order Placed
if (!defined('SMS_ORDER_TEMPLATE_ID')) {
    define('SMS_ORDER_TEMPLATE_ID', '1777178998165829019');
}

// DLT Approved Template Message Text for Order Placed
// Note: First {#alp#} is customer name, second {#alp#} is order ID (e.g. #2466)
if (!defined('SMS_ORDER_TEMPLATE_TEXT')) {
    define('SMS_ORDER_TEMPLATE_TEXT', 'Hi {#alp#}, your order {#alp#} has been successfully placed with BunnyBoss.');
}


// OTP Settings
if (!defined('SMS_OTP_LENGTH')) {
    define('SMS_OTP_LENGTH', 6);
}

if (!defined('SMS_OTP_EXPIRY_MINUTES')) {
    define('SMS_OTP_EXPIRY_MINUTES', 5);
}

// HTTP request timeout in seconds
if (!defined('SMS_TIMEOUT_SECONDS')) {
    define('SMS_TIMEOUT_SECONDS', 7);
}

// Debug Mode:
// Set to true only when testing offline without an active gateway
// Set to false in live production (default: false)
if (!defined('SMS_DEBUG_MODE')) {
    define('SMS_DEBUG_MODE', false);
}

// Log outgoing SMS requests and provider responses
if (!defined('SMS_LOG_ENABLED')) {
    define('SMS_LOG_ENABLED', true);
}

if (!defined('SMS_LOG_FILE')) {
    $log_dir = defined('_BASEPATH') ? _BASEPATH . 'uploads/' : __DIR__ . '/../uploads/';
    define('SMS_LOG_FILE', $log_dir . 'sms_logs.log');
}
