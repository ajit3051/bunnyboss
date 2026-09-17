<?php
include_once("include/config.php"); 

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'get_cart_dropdown') {
    $cart_session = get_cart_session();
    $cart_data = get_cart_dropdown_data($cart_session);
    $dropdown_html = get_cart_dropdown_html($cart_session);

    echo json_encode([
        "status" => "success",
        "cart_count" => $cart_data['total_qty'],
        "cart_total" => number_format($cart_data['total_price'], 2),
        "dropdown_html" => $dropdown_html
    ]);
    exit;
}

if ($action === 'add_to_cart') {

    $cart_session = get_cart_session();
    $product_id   = (int) ($_POST['product_id'] ?? 0);
    $quantity     = (int) ($_POST['quantity'] ?? 1);
    $size         = trim($_POST['size'] ?? '');

    if ($quantity < 1) $quantity = 1;

    if ($product_id <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid product"]);
        exit;
    }

    if ($size === '') {
        echo json_encode(["status" => "error", "message" => "Please select a size"]);
        exit;
    }

    // Fetch product details
    $db = connect();

    $stmt = $db->select("SELECT item_name FROM tbl_item_master WHERE id = ?", 'i', $product_id);
    $product = $stmt->fetch_assoc();

    if (!$product) {
        echo json_encode(["status" => "error", "message" => "Product not found"]);
        exit;
    }

    // Fetch exact size variant price and stock quantity from tbl_item_variants
    $v_stmt = $db->select("SELECT price, quantity FROM tbl_item_variants WHERE item_id = ? AND size_name = ? LIMIT 1", 'is', $product_id, $size);
    $variant = ($v_stmt && $v_stmt->num_rows > 0) ? $v_stmt->fetch_assoc() : null;

    if ($variant) {
        $price = (float)$variant['price'];
        $stock = (int)$variant['quantity'];
    } else {
        $price = 0.00;
        $stock = 0;
    }

    if ($stock <= 0) {
        echo json_encode(["status" => "error", "message" => "Sorry, size ($size) is Out of Stock."]);
        exit;
    }

    // Check if product+size already exists in cart for this session
    $stmt = $db->select("SELECT id, quantity FROM tbl_cart_items WHERE cart_session = ? AND product_id = ? AND size = ?", 'sis', $cart_session, $product_id, $size);
    $existing = $stmt->fetch_assoc();

    if ($existing) {
        $new_qty = $existing['quantity'] + $quantity;

        $db->update("UPDATE tbl_cart_items SET quantity = ? WHERE id = ?", 'ii', $new_qty, $existing['id']);
    } else {
        
        $db->insert(
            "INSERT INTO tbl_cart_items (cart_session, product_id, product_name, price, quantity, size) VALUES (?, ?, ?, ?, ?, ?)", 
            'sisdis', 
            $cart_session, 
            $product_id, 
            $product['item_name'], 
            $price, 
            $quantity,
            $size
        );
    }

    $cart_data = get_cart_dropdown_data($cart_session);
    $dropdown_html = get_cart_dropdown_html($cart_session);

    echo json_encode([
        "status" => "success",
        "message" => $product['item_name'] . " (Size: $size) added to cart",
        "cart_count" => $cart_data['total_qty'],
        "cart_total" => number_format($cart_data['total_price'], 2),
        "dropdown_html" => $dropdown_html
    ]);
    exit;

}

if ($action === 'remove_from_cart') {
    $cart_session = get_cart_session();
    $id = (int) ($_POST['id'] ?? 0);

    $db = connect();

    $db->delete("DELETE FROM tbl_cart_items WHERE id = ? AND cart_session = ?", 'is', $id, $cart_session);

    $cart_data = get_cart_dropdown_data($cart_session);
    $dropdown_html = get_cart_dropdown_html($cart_session);

    echo json_encode([
        "status" => "success",
        "cart_count" => $cart_data['total_qty'],
        "cart_total" => number_format($cart_data['total_price'], 2),
        "dropdown_html" => $dropdown_html
    ]);
    exit;
}

if ($action === 'save_cart') {
    
    // Clean and store summaries
    $_SESSION['checkout_summary'] = [
        'subtotal'    => isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0.00,
        'gst'         => isset($_POST['gst']) ? floatval($_POST['gst']) : 0.00,
        'shipping'    => isset($_POST['shipping']) ? floatval($_POST['shipping']) : 0.00,
        'grand_total' => isset($_POST['grand_total']) ? floatval($_POST['grand_total']) : 0.00
    ];

    // Clean and store individual product breakdowns
    $_SESSION['checkout_products'] = [];
    if (isset($_POST['products']) && is_array($_POST['products'])) {
        foreach ($_POST['products'] as $item) {
            $_SESSION['checkout_products'][] = [
                'product_id' => intval($item['product_id']),
                'product_title' => isset($item['product_title']) ? htmlspecialchars(trim($item['product_title'])) : 'Unknown Item',
                'unit_price' => floatval($item['unit_price']),
                'quantity'   => intval($item['quantity']),
                'size'   => intval($item['size']),
                'row_total'  => floatval($item['row_total'])
            ];
        }
    }

    // Return a JSON confirmation response to the AJAX caller
    echo json_encode(['status' => 'success']);
    exit;
}


if ($action === 'update_cart_quantity') {
    $id = intval($_POST['id']);
    $qty = intval($_POST['qty']);

    if ($qty < 1) {
        $qty = 1; // Fallback defense against negative values
    }

    $cart_session = get_cart_session();

    if (empty($cart_session)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid session context.']);
        exit;
    }

    $db = connect();

    // Securely update the specific item tracking row inside tbl_cart_items
    $updated = $db->update(
        "UPDATE tbl_cart_items SET quantity = ? WHERE id = ? AND cart_session = ?", 
        'iis', 
        $qty, 
        $id, 
        $cart_session
    );

    if ($updated) {
        $cart_data = get_cart_dropdown_data($cart_session);
        $dropdown_html = get_cart_dropdown_html($cart_session);
        echo json_encode([
            'status' => 'success',
            'cart_count' => $cart_data['total_qty'],
            'cart_total' => number_format($cart_data['total_price'], 2),
            'dropdown_html' => $dropdown_html
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to execute query update.']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid Request routing parameters.']);
exit;
?>