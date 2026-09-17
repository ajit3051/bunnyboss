<?php
/**
 * Shopping Cart & Session Checkout API Endpoint
 * Path: /api/cart.php
 */
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/auth_helper.php");

send_api_headers();

// Validate Bearer token
$token_data = validate_api_token(false);

$raw_input = file_get_contents('php_input') ?: file_get_contents('php://input');
$json_data = json_decode($raw_input, true) ?: [];
$request = array_merge($_GET, $_POST, $json_data);

$action = $request['action'] ?? 'get_cart';

$db = connect();
$cart_session = get_cart_session();

// Helper to compute cart totals
function get_api_cart_summary($cart_session, $db) {
    $stmt = $db->select(
        "SELECT ci.*, 
                (SELECT image_path FROM tbl_item_images WHERE item_id = ci.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS image_path
         FROM tbl_cart_items ci 
         WHERE ci.cart_session = ? 
         ORDER BY ci.id DESC",
        's',
        $cart_session
    );

    $items = [];
    $subtotal = 0.00;
    $total_qty = 0;

    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {
            $qty = (int)$row['quantity'];
            $price = (float)$row['price'];
            $row_total = $qty * $price;
            $subtotal += $row_total;
            $total_qty += $qty;

            $image_url = get_product_image_url($row['image_path'] ?? null);

            $items[] = [
                'id' => (int)$row['id'],
                'product_id' => (int)$row['product_id'],
                'product_name' => $row['product_name'],
                'size' => $row['size'],
                'price' => $price,
                'quantity' => $qty,
                'row_total' => $row_total,
                'image_url' => $image_url
            ];
        }
    }

    $shipping_charge = defined('_SHIPPING_CHARGE_') ? (float)_SHIPPING_CHARGE_ : 100.00;
    $gst_rate = defined('_GST_') ? (float)_GST_ : 5.00;

    if ($subtotal <= 0) {
        $shipping = 0.00;
        $gst_amount = 0.00;
        $grand_total = 0.00;
    } else {
        $shipping = $shipping_charge;
        $total_plus_shipping = $subtotal + $shipping;
        $gst_amount = round(($total_plus_shipping * $gst_rate) / 100, 2);
        $grand_total = $total_plus_shipping + $gst_amount;
    }

    // Save session checkout summaries for order processing
    $_SESSION['checkout_summary'] = [
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'gst' => $gst_amount,
        'grand_total' => $grand_total,
        'gst_percent' => $gst_rate
    ];

    $_SESSION['checkout_products'] = array_map(function($i) {
        return [
            'product_id' => $i['product_id'],
            'product_title' => $i['product_name'],
            'unit_price' => $i['price'],
            'quantity' => $i['quantity'],
            'size' => $i['size'],
            'row_total' => $i['row_total']
        ];
    }, $items);

    return [
        'items' => $items,
        'summary' => [
            'total_qty' => $total_qty,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'gst_rate' => $gst_rate,
            'gst_amount' => $gst_amount,
            'grand_total' => $grand_total
        ]
    ];
}

// ---------------------------------------------------------
// 1. GET CART
// ---------------------------------------------------------
if ($action === 'get_cart') {
    $cart_data = get_api_cart_summary($cart_session, $db);
    echo json_encode(array_merge(['success' => true], $cart_data));
    exit;
}

// ---------------------------------------------------------
// 2. ADD TO CART
// ---------------------------------------------------------
if ($action === 'add_to_cart') {
    $product_id = (int)($request['product_id'] ?? 0);
    $quantity   = max(1, (int)($request['quantity'] ?? 1));
    $size       = trim($request['size'] ?? '');

    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
        exit;
    }

    if (empty($size)) {
        echo json_encode(['success' => false, 'message' => 'Please select a size variant.']);
        exit;
    }

    // Fetch product name
    $stmt = $db->select("SELECT item_name FROM tbl_item_master WHERE id = ? AND status = 'true'", 'i', $product_id);
    $product = $stmt ? $stmt->fetch_assoc() : null;

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found or inactive.']);
        exit;
    }

    // Fetch variant price and stock quantity
    $v_stmt = $db->select("SELECT price, quantity FROM tbl_item_variants WHERE item_id = ? AND size_name = ? LIMIT 1", 'is', $product_id, $size);
    $variant = ($v_stmt && $v_stmt->num_rows > 0) ? $v_stmt->fetch_assoc() : null;

    if (!$variant || (int)$variant['quantity'] <= 0) {
        echo json_encode(['success' => false, 'message' => 'Selected size (' . $size . ') is Out of Stock.']);
        exit;
    }

    $price = (float)$variant['price'];

    // Check if item already exists in cart for session
    $ex_stmt = $db->select("SELECT id, quantity FROM tbl_cart_items WHERE cart_session = ? AND product_id = ? AND size = ?", 'sis', $cart_session, $product_id, $size);
    $existing = $ex_stmt ? $ex_stmt->fetch_assoc() : null;

    if ($existing) {
        $new_qty = $existing['quantity'] + $quantity;
        $db->update("UPDATE tbl_cart_items SET quantity = ? WHERE id = ?", 'ii', $new_qty, $existing['id']);
    } else {
        $db->insert(
            "INSERT INTO tbl_cart_items (cart_session, product_id, product_name, price, quantity, size) VALUES (?, ?, ?, ?, ?, ?)",
            'sisdis',
            $cart_session, $product_id, $product['item_name'], $price, $quantity, $size
        );
    }

    $cart_data = get_api_cart_summary($cart_session, $db);
    echo json_encode(array_merge([
        'success' => true,
        'message' => $product['item_name'] . ' (Size: ' . $size . ') added to cart.'
    ], $cart_data));
    exit;
}

// ---------------------------------------------------------
// 3. UPDATE ITEM QUANTITY
// ---------------------------------------------------------
if ($action === 'update_quantity') {
    $cart_item_id = (int)($request['id'] ?? $request['cart_item_id'] ?? 0);
    $quantity     = max(1, (int)($request['quantity'] ?? $request['qty'] ?? 1));

    if ($cart_item_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart item ID.']);
        exit;
    }

    $updated = $db->update("UPDATE tbl_cart_items SET quantity = ? WHERE id = ? AND cart_session = ?", 'iis', $quantity, $cart_item_id, $cart_session);

    if ($updated !== false) {
        $cart_data = get_api_cart_summary($cart_session, $db);
        echo json_encode(array_merge(['success' => true, 'message' => 'Cart updated.'], $cart_data));
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update item quantity.']);
    }
    exit;
}

// ---------------------------------------------------------
// 4. REMOVE ITEM FROM CART
// ---------------------------------------------------------
if ($action === 'remove_item') {
    $cart_item_id = (int)($request['id'] ?? $request['cart_item_id'] ?? 0);

    if ($cart_item_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart item ID.']);
        exit;
    }

    $db->delete("DELETE FROM tbl_cart_items WHERE id = ? AND cart_session = ?", 'is', $cart_item_id, $cart_session);

    $cart_data = get_api_cart_summary($cart_session, $db);
    echo json_encode(array_merge(['success' => true, 'message' => 'Item removed from cart.'], $cart_data));
    exit;
}

// ---------------------------------------------------------
// 5. CLEAR CART
// ---------------------------------------------------------
if ($action === 'clear_cart') {
    $db->delete("DELETE FROM tbl_cart_items WHERE cart_session = ?", 's', $cart_session);
    unset($_SESSION['checkout_summary'], $_SESSION['checkout_products']);

    $cart_data = get_api_cart_summary($cart_session, $db);
    echo json_encode(array_merge(['success' => true, 'message' => 'Cart cleared.'], $cart_data));
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid cart API action request.']);
exit;
