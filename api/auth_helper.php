<?php
/**
 * API Authentication & Bearer Token Middleware Helper
 * Path: /api/auth_helper.php
 */
require_once(__DIR__ . "/config.php");

/**
 * Extract Bearer or Custom Token from HTTP Headers or Request Parameters
 */
function get_bearer_token() {
    $headers = [];

    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers() ?: [];
    }

    $normalized_headers = [];
    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) === 'HTTP_') {
            $header_name = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $normalized_headers[$header_name] = $value;
        }
    }
    foreach ($headers as $key => $val) {
        $normalized_headers[ucwords(strtolower($key), '-')] = $val;
    }

    // 1. Check Authorization header (Bearer <token>)
    $authHeader = $normalized_headers['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if (!empty($authHeader) && preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
        return trim($matches[1]);
    }

    // 2. Check X-Api-Token / X-Auth-Token header
    if (!empty($normalized_headers['X-Api-Token'])) return trim($normalized_headers['X-Api-Token']);
    if (!empty($normalized_headers['X-Auth-Token'])) return trim($normalized_headers['X-Auth-Token']);

    // 3. Check GET / POST / JSON payload token
    $raw_input = file_get_contents('php_input') ?: file_get_contents('php://input');
    $json_data = json_decode($raw_input, true) ?: [];
    $request = array_merge($_GET, $_POST, $json_data);

    if (!empty($request['token'])) return trim($request['token']);
    if (!empty($request['api_token'])) return trim($request['api_token']);
    if (!empty($request['access_token'])) return trim($request['access_token']);

    return '';
}

/**
 * Validate incoming API request token.
 * 
 * @param bool $require_user_login If true, strictly requires a valid logged-in user token.
 * @return array Validated token payload
 */
function validate_api_token($require_user_login = false) {
    $token = get_bearer_token();

    if (empty($token)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'status' => 401,
            'message' => 'Unauthorized API access. Valid Authorization Bearer token is required.'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Check system app token/secret
    if (!$require_user_login && $token === _API_SECRET_KEY_) {
        return ['type' => 'app', 'user' => null];
    }

    // Check user token in tbl_users
    $db = connect();
    $stmt = $db->select("SELECT id, name, mobile, email, role, status FROM tbl_users WHERE api_token = ? AND api_token IS NOT NULL AND status = 'active' LIMIT 1", 's', $token);

    if ($stmt && $user = $stmt->fetch_assoc()) {
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_mobile'] = $user['mobile'];
        $_SESSION['user_name'] = !empty($user['name']) ? $user['name'] : 'User (' . substr($user['mobile'], -4) . ')';
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['api_token'] = $token;

        return ['type' => 'user', 'user' => $user];
    }

    // Check fallback session user if token matches active session token
    if (isset($_SESSION['api_token']) && $_SESSION['api_token'] === $token && !empty($_SESSION['user_id'])) {
        $u_id = (int)$_SESSION['user_id'];
        $u_stmt = $db->select("SELECT id, name, mobile, email, role, status FROM tbl_users WHERE id = ? LIMIT 1", 'i', $u_id);
        if ($u_stmt && $u_row = $u_stmt->fetch_assoc()) {
            return ['type' => 'user', 'user' => $u_row];
        }
    }

    http_response_code(401);
    echo json_encode([
        'success' => false,
        'status' => 401,
        'message' => 'Invalid or expired Authorization Bearer token.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Generate a new secure API token for user
 */
function generate_user_api_token($user_id) {
    $db = connect();
    $token = API_TOKEN_PREFIX . bin2hex(random_bytes(24));
    $db->update("UPDATE tbl_users SET api_token = ? WHERE id = ?", 'si', $token, $user_id);
    $_SESSION['api_token'] = $token;
    return $token;
}
