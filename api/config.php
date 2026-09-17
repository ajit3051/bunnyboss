<?php

/**
 * API Configuration & Central Settings File
 * Path: /api/config.php
 */

// Load core system configuration (Database, base URLs, courier settings, etc.)
require_once(__DIR__ . "/../include/config.php");

// ---------------------------------------------------------
// 1. API METADATA & ENVIRONMENT
// ---------------------------------------------------------
if (!defined('API_NAME')) {
    define('API_NAME', 'BunnyBoss User-End REST API Gateway');
}

if (!defined('API_VERSION')) {
    define('API_VERSION', '2.0.0');
}

if (!defined('API_ENVIRONMENT')) {
    define('API_ENVIRONMENT', 'production');
}

if (!defined('API_DEBUG')) {
    define('API_DEBUG', false);
}

// ---------------------------------------------------------
// 2. CORS & HTTP RESPONSE HEADERS
// ---------------------------------------------------------
if (!defined('API_ALLOW_ORIGIN')) {
    define('API_ALLOW_ORIGIN', '*');
}

if (!defined('API_ALLOW_METHODS')) {
    define('API_ALLOW_METHODS', 'GET, POST, PUT, DELETE, OPTIONS');
}

if (!defined('API_ALLOW_HEADERS')) {
    define('API_ALLOW_HEADERS', 'Content-Type, Authorization, X-Api-Token, X-Auth-Token, X-Requested-With');
}

if (!defined('API_JSON_FLAGS')) {
    define('API_JSON_FLAGS', JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

// ---------------------------------------------------------
// 3. SECURITY & AUTHENTICATION CONFIG
// ---------------------------------------------------------
if (!defined('_API_SECRET_KEY_')) {
    define('_API_SECRET_KEY_', 'bbin_app_secret_token_2026');
}

if (!defined('API_TOKEN_PREFIX')) {
    define('API_TOKEN_PREFIX', 'bin_tok_');
}

if (!defined('API_OTP_EXPIRY_MINUTES')) {
    define('API_OTP_EXPIRY_MINUTES', 5);
}

if (!defined('API_OTP_LENGTH')) {
    define('API_OTP_LENGTH', 6);
}

if (!defined('API_OTP_DEBUG')) {
    define('API_OTP_DEBUG', true);
}

if (!defined('API_STATIC_OTP')) {
    define('API_STATIC_OTP', '123456');
}

// ---------------------------------------------------------
// 4. PAGINATION & CATALOG DEFAULTS
// ---------------------------------------------------------
if (!defined('API_DEFAULT_PAGE')) {
    define('API_DEFAULT_PAGE', 1);
}

if (!defined('API_DEFAULT_LIMIT')) {
    define('API_DEFAULT_LIMIT', 12);
}

if (!defined('API_MAX_LIMIT')) {
    define('API_MAX_LIMIT', 100);
}

// ---------------------------------------------------------
// 5. API HELPER FUNCTIONS
// ---------------------------------------------------------

/**
 * Automatically sets standard JSON response headers and CORS rules.
 * Handles OPTIONS preflight requests automatically.
 */
if (!function_exists('send_api_headers')) {
    function send_api_headers()
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
            header('Access-Control-Allow-Origin: ' . API_ALLOW_ORIGIN);
            header('Access-Control-Allow-Methods: ' . API_ALLOW_METHODS);
            header('Access-Control-Allow-Headers: ' . API_ALLOW_HEADERS);
        }

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}

/**
 * Output JSON payload with proper HTTP status code and headers.
 * 
 * @param array|object $data
 * @param int $http_code
 */
if (!function_exists('send_json_response')) {
    function send_json_response($data, $http_code = 200)
    {
        send_api_headers();
        http_response_code($http_code);
        echo json_encode($data, API_JSON_FLAGS);
        exit;
    }
}

// ---------------------------------------------------------
// 6. PRODUCT IMAGE PATH HELPER
// ---------------------------------------------------------
if (!defined('API_PRODUCT_IMAGE_PATH')) {
    define('API_PRODUCT_IMAGE_PATH', _IMAGE_PATH . 'item-master/');
}

/**
 * Get the full public URL for a product image.
 * 
 * @param string|null $image_file
 * @return string
 */
if (!function_exists('get_product_image_url')) {
    function get_product_image_url($image_file = null)
    {
        if (empty($image_file)) {
            return _BASEURL . 'assets/images/no-image.jpg';
        }

        // If already a full URL
        if (preg_match('/^https?:\/\//i', $image_file)) {
            return $image_file;
        }

        $cleaned = ltrim($image_file, '/');

        // If already starts with 'item-master/'
        if (strpos($cleaned, 'item-master/') === 0) {
            return _IMAGE_PATH . $cleaned;
        }

        // If already starts with 'uploads/'
        if (strpos($cleaned, 'uploads/') === 0) {
            return _ADMIN_URL . $cleaned;
        }

        return _IMAGE_PATH . 'item-master/' . $cleaned;
    }
}

