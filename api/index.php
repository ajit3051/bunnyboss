<?php
/**
 * Unified API Router & Gateway Entry Point
 * Path: /api/index.php
 */
require_once(__DIR__ . "/config.php");
send_api_headers();

$raw_input = file_get_contents('php_input') ?: file_get_contents('php://input');
$json_data = json_decode($raw_input, true) ?: [];
$request = array_merge($_GET, $_POST, $json_data);

$has_endpoint_param = isset($request['endpoint']) || isset($request['module']);
$endpoint = strtolower(trim($request['endpoint'] ?? $request['module'] ?? ''));

if (!$has_endpoint_param || $endpoint === '') {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'status' => 200,
        'message' => API_NAME . ' is active and operational.',
        'service' => API_NAME,
        'version' => API_VERSION,
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoints' => [
            'auth' => _BASEURL . 'api/auth.php',
            'user' => _BASEURL . 'api/user.php',
            'products' => _BASEURL . 'api/products.php',
            'cart' => _BASEURL . 'api/cart.php',
            'orders' => _BASEURL . 'api/orders.php'
        ],
        'usage' => [
            'direct' => 'Access endpoint PHP files directly (e.g. ' . _BASEURL . 'api/products.php?action=list_products)',
            'router' => 'Access via gateway parameter (e.g. ' . _BASEURL . 'api/?endpoint=products&action=list_products)'
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

switch ($endpoint) {
    case 'auth':
        require __DIR__ . '/auth.php';
        break;

    case 'user':
        require __DIR__ . '/user.php';
        break;

    case 'products':
    case 'catalog':
        require __DIR__ . '/products.php';
        break;

    case 'cart':
        require __DIR__ . '/cart.php';
        break;

    case 'orders':
    case 'checkout':
        require __DIR__ . '/orders.php';
        break;

    case 'shadowfax_webhook':
    case 'shadowfax-webhook':
        require __DIR__ . '/shadowfax_webhook.php';
        break;

    case 'razorpay_webhook':
    case 'razorpay-webhook':
        require __DIR__ . '/razorpay_webhook.php';
        break;

    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'status' => 400,
            'message' => "Invalid API endpoint '{$endpoint}'. Valid endpoints are: auth, user, products, cart, orders.",
            'available_endpoints' => ['auth', 'user', 'products', 'cart', 'orders']
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
}

