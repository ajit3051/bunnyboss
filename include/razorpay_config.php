<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Razorpay\Api\Api;

// Enable / Disable Razorpay Payment Gateway
if (!defined('_ENABLE_RAZORPAY_')) {
    define('_ENABLE_RAZORPAY_', false); // Set to true to enable, false to disable
}

// Test Mode
// define('RAZORPAY_KEY_ID', 'rzp_test_T8lN9Z9yWycEaW');
// define('RAZORPAY_KEY_SECRET', '616kNnS5u2nuCFTR4wKS1nDO');

// Live Mode
define('RAZORPAY_KEY_ID', 'rzp_live_TZGQRV8Fdr7PoK');
define('RAZORPAY_KEY_SECRET', 'TezaLnffskn3W11Kfz0vwIzf');

// Test Mode Webhook Secret
// define('RAZORPAY_WEBHOOK_SECRET', '_5G8BPMyiawBbuS@P');

// Live Mode
define('RAZORPAY_WEBHOOK_SECRET', 'QyF9EMU5xEshE@P');

function getRazorpayApi()
{
    return new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
}
