<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
error_reporting(0);

$allowed_domains = array('localhost', 'bunnyboss.in');

if (php_sapi_name() !== 'cli' && (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_domains, TRUE))) {
    header("HTTP/1.1 400 Redirect", true);
    echo '<div style="text-align:center;"> <h4>The Server Has Refused To Fulfill Your Request</h4></div>';
    die();
}

session_start();

############ General Settings ############################
date_default_timezone_set('Asia/Kolkata');

// define("_BASEURL", 'https://bunnyboss.in/');
// define("_ADMIN_URL", 'https://bunnyboss.in/secure-admin/');
// $doc_root = !empty($_SERVER['DOCUMENT_ROOT']) ? htmlspecialchars($_SERVER['DOCUMENT_ROOT']) . '/' : realpath(__DIR__ . '/../') . '/';



if (!defined('_BASEURL')) {
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $folder = (strpos($script_dir, '/bbin') !== false) ? 'bbin' : 'bunnyboss';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if ($host === 'bunnyboss.in' || $host === 'www.bunnyboss.in') {
        define("_BASEURL", 'https://bunnyboss.in/');
        define("_ADMIN_URL", 'https://bunnyboss.in/secure-admin/');
        $doc_root = !empty($_SERVER['DOCUMENT_ROOT']) ? htmlspecialchars($_SERVER['DOCUMENT_ROOT']) . '/' : realpath(__DIR__ . '/../') . '/';
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        define("_BASEURL", $protocol . $host . '/' . $folder . '/');
        define("_ADMIN_URL", $protocol . $host . '/' . $folder . '/secure-admin/');
        $doc_root = !empty($_SERVER['DOCUMENT_ROOT']) ? htmlspecialchars($_SERVER['DOCUMENT_ROOT']) . '/' . $folder . '/' : realpath(__DIR__ . '/../') . '/';
    }
}

if (!defined('_BASEPATH'))
    define("_BASEPATH", $doc_root);

if (!defined('_CLASS_PATH'))
    define("_CLASS_PATH", _BASEPATH . "classes/");

if (!defined('_SENDMAIL_'))
    define("_SENDMAIL_", true);

if (!defined('_MAIL_FROM_EMAIL_'))
    define("_MAIL_FROM_EMAIL_", 'ramalayasales@prabhushriram.com');

if (!defined('_MAIL_FROM_NAME_'))
    define("_MAIL_FROM_NAME_", 'Awesomesoft');

/*---=== Assets Path ====----*/
if (!defined('_IMAGE_PATH'))
    define("_IMAGE_PATH", _ADMIN_URL . "uploads/");

if (!defined('_CSS_PATH'))
    define("_CSS_PATH", _BASEURL . "assets/css/");

############ Upload path ############################	
if (!defined('_UPLOAD_DIR'))
    define("_UPLOAD_DIR", _BASEPATH . "uploads/");

if (!defined('_UPLOAD_FILE_URL'))
    define("_UPLOAD_FILE_URL", _BASEURL . "uploads/");

############ Javascript path setting ############################	
if (!defined('_JS_PATH'))
    define("_JS_PATH", _BASEURL . "assets/js/");

############ Store & Payment Settings ############################	
if (!defined('_PAGEARRY_')) {
    define('_PAGEARRY_', array('10', '20', '50', '100'));
}
if (!defined('_RECORDPERPAGE_')) {
    define("_RECORDPERPAGE_", "20");
}
if (!defined('_GST_')) {
    define("_GST_", "5");
}
if (!defined('_SHIPPING_CHARGE_')) {
    define("_SHIPPING_CHARGE_", "150");
}
if (!defined('_SHIPPING_CHARGE_PER_ITEM_')) {
    define("_SHIPPING_CHARGE_PER_ITEM_", "150");
}

if (!defined('_ENABLE_COD_')) {
    define("_ENABLE_COD_", true);
}
// COD Upfront Online Deposit Setting (Set to true to require upfront shipping/GST payment via Razorpay for COD, or false for 100% Pure COD)
if (!defined('_ENABLE_COD_ONLINE_DEPOSIT_')) {
    define("_ENABLE_COD_ONLINE_DEPOSIT_", false);
}
if (!defined('_COD_INCLUDES_GST_')) {
    define("_COD_INCLUDES_GST_", true);
}
if (!defined('_PRODUCTS_PER_PAGE_')) {
    define("_PRODUCTS_PER_PAGE_", 12);
}

############ DataBase setting ############################	
// if (!defined('_HOST_'))
//     define("_HOST_", "localhost");
// if (!defined('_USER_'))
//     define("_USER_", "u488042670_bunnyboss_in");
// if (!defined('_PASS_'))
//     define("_PASS_", "C|yUjed8Tu!8");
// if (!defined('_DB_'))
//     define("_DB_", "u488042670_bunnyboss_in"); 

if (!defined('_HOST_'))
    define("_HOST_", "localhost");
if (!defined('_USER_'))
    define("_USER_", "admin");
if (!defined('_PASS_'))
    define("_PASS_", "admin");
if (!defined('_DB_'))
    define("_DB_", "u488042670_bunnyboss_in");

function connect()
{
    static $db = null;
    if ($db === null) {
        $db = new mysqli_ext(_HOST_, _USER_, _PASS_, _DB_);
    }
    return $db;
}

############  Courier & Payment Integration Settings ############################
if (!defined('_ACTIVE_COURIER_'))
    define('_ACTIVE_COURIER_', 'shadowfax'); // Options: 'shadowfax', 'delhivery', 'auto'

if (!defined('DELHIVERY_API_TOKEN'))
    define('DELHIVERY_API_TOKEN', 'f1715d10fef84d24fa366892dbc29818ffdc4aca');
if (!defined('DELHIVERY_TRACK_URL'))
    define('DELHIVERY_TRACK_URL', 'https://track.delhivery.com/api/v1/packages/json/');
if (!defined('DELHIVERY_CREATE_URL'))
    define('DELHIVERY_CREATE_URL', 'https://track.delhivery.com/api/cmu/create.json');
if (!defined('DELHIVERY_PINCODE_URL'))
    define('DELHIVERY_PINCODE_URL', 'https://track.delhivery.com/c/api/pin-codes/json/');
if (!defined('DELHIVERY_ENABLED'))
    define('DELHIVERY_ENABLED', true);
if (!defined('PICKUP_LOCATION_NAME'))
    define('PICKUP_LOCATION_NAME', 'BunnyBoss Warehouse');
if (!defined('DELHIVERY_PICKUP_NAME'))
    define('DELHIVERY_PICKUP_NAME', 'BunnyBoss Warehouse');


// Shadowfax Courier Integration Config
if (!defined('SHADOWFAX_API_TOKEN'))
    define('SHADOWFAX_API_TOKEN', 'f1715d10fef84d24fa366892dbc29818ffdc4aca_test');
if (!defined('SHADOWFAX_CREATE_URL'))
    define('SHADOWFAX_CREATE_URL', 'https://dale.shadowfax.in/api/v3/clients/orders/');
if (!defined('SHADOWFAX_TRACK_URL'))
    define('SHADOWFAX_TRACK_URL', 'https://api.shadowfax.in/api/v1/tracking/');
if (!defined('SHADOWFAX_CANCEL_URL'))
    define('SHADOWFAX_CANCEL_URL', 'https://dale.shadowfax.in/api/v2/clients/orders/cancel/');

if (!defined('SHADOWFAX_PICKUP_NAME'))
    define('SHADOWFAX_PICKUP_NAME', 'BunnyBoss Warehouse');
if (!defined('SHADOWFAX_PICKUP_CONTACT'))
    define('SHADOWFAX_PICKUP_CONTACT', '7838384314');
if (!defined('SHADOWFAX_PICKUP_ADDRESS'))
    define('SHADOWFAX_PICKUP_ADDRESS', 'A1-40, Chanakya Place Part-1, 25 Foota Road (C-1 Janak Puri), Opp. Mata Chanan Devi Hospital');
if (!defined('SHADOWFAX_PICKUP_CITY'))
    define('SHADOWFAX_PICKUP_CITY', 'New Delhi');
if (!defined('SHADOWFAX_PICKUP_STATE'))
    define('SHADOWFAX_PICKUP_STATE', 'Delhi');
if (!defined('SHADOWFAX_PICKUP_PINCODE'))
    define('SHADOWFAX_PICKUP_PINCODE', '110059');
if (!defined('SHADOWFAX_PICKUP_STORE_CODE'))
    define('SHADOWFAX_PICKUP_STORE_CODE', 'SHOES_01');

if (!defined('SHADOWFAX_RTO_NAME'))
    define('SHADOWFAX_RTO_NAME', 'BunnyBoss Warehouse');
if (!defined('SHADOWFAX_RTO_CONTACT'))
    define('SHADOWFAX_RTO_CONTACT', '7838384314');
if (!defined('SHADOWFAX_RTO_ADDRESS'))
    define('SHADOWFAX_RTO_ADDRESS', 'A1-40, Chanakya Place Part-1, 25 Foota Road (C-1 Janak Puri), Opp. Mata Chanan Devi Hospital');
if (!defined('SHADOWFAX_RTO_CITY'))
    define('SHADOWFAX_RTO_CITY', 'New Delhi');
if (!defined('SHADOWFAX_RTO_STATE'))
    define('SHADOWFAX_RTO_STATE', 'Delhi');
if (!defined('SHADOWFAX_RTO_PINCODE'))
    define('SHADOWFAX_RTO_PINCODE', '110059');
if (!defined('SHADOWFAX_ENABLED'))
    define('SHADOWFAX_ENABLED', false);

// Excluded test product IDs and SKUs (never pushed to Shadowfax)
if (!defined('SHADOWFAX_TEST_PRODUCT_IDS')) {
    define('SHADOWFAX_TEST_PRODUCT_IDS', []);
}
if (!defined('SHADOWFAX_TEST_SKUS')) {
    define('SHADOWFAX_TEST_SKUS', []);
}

############ Included Libraries & Services ############################
require_once(_CLASS_PATH . "pager_v2.cls.php");
require_once(_CLASS_PATH . "validation.php");
require_once(_CLASS_PATH . "mysqli.class.php");
require_once(__DIR__ . "/functions.php");
include_once(__DIR__ . "/ajax_functions.php");
include_once(__DIR__ . "/send_mail.php");
require_once(__DIR__ . "/razorpay_config.php");
require_once(__DIR__ . "/courier_service.php");
require_once(__DIR__ . "/sms_config.php");
require_once(__DIR__ . "/sms_service.php");
