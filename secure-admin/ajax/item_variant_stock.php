<?php
header('Content-Type: application/json');
include_once(__DIR__ . '/../include/db.php');

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Support both standard POST/REQUEST and JSON input payload
$action = $_REQUEST['action'] ?? '';

if (empty($action)) {
    $rawInput = file_get_contents('php://input');
    if (!empty($rawInput)) {
        $json = json_decode($rawInput, true);
        if (is_array($json)) {
            $action = $json['action'] ?? '';
            $_POST = array_merge($_POST, $json);
            $_REQUEST = array_merge($_REQUEST, $json);
        }
    }
}

$action = strtolower(trim($action));

// Helper to get master lists
function getMasterLists($conn) {
    $colors = [];
    $sizes  = [];
    $styles = [];

    $c_res = mysqli_query($conn, "SELECT color_name FROM tbl_color_master ORDER BY color_name ASC");
    while ($c = mysqli_fetch_assoc($c_res)) {
        if (!empty($c['color_name'])) $colors[] = $c['color_name'];
    }

    $s_res = mysqli_query($conn, "SELECT size_name FROM tbl_size_master ORDER BY id ASC");
    while ($s = mysqli_fetch_assoc($s_res)) {
        if (!empty($s['size_name'])) $sizes[] = $s['size_name'];
    }

    $st_res = mysqli_query($conn, "SELECT style_name FROM tbl_styledesign_master ORDER BY style_name ASC");
    while ($st = mysqli_fetch_assoc($st_res)) {
        if (!empty($st['style_name'])) $styles[] = $st['style_name'];
    }

    return [
        'colors' => array_values(array_unique($colors)),
        'sizes'  => array_values(array_unique($sizes)),
        'styles' => array_values(array_unique($styles))
    ];
}

// 1. Get item variants + master lists
if ($action === 'get_variants' || $action === 'get_item_variants') {
    $item_id = (int)($_REQUEST['item_id'] ?? 0);
    if ($item_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Item ID']);
        exit;
    }

    $item_query = mysqli_query($conn, "SELECT id, item_name, item_code, purchase_price, mrp FROM tbl_item_master WHERE id = '$item_id'");
    $item = mysqli_fetch_assoc($item_query);

    if (!$item) {
        echo json_encode(['status' => 'error', 'message' => 'Item not found']);
        exit;
    }

    $v_res = mysqli_query($conn, "SELECT * FROM tbl_item_variants WHERE item_id = '$item_id' ORDER BY id ASC");
    $variants = [];
    $total_qty = 0;

    while ($row = mysqli_fetch_assoc($v_res)) {
        $variants[] = [
            'id'         => (int)$row['id'],
            'color_name' => $row['color_name'] ?? '',
            'size_name'  => $row['size_name'] ?? '',
            'style_name' => $row['style_name'] ?? '',
            'quantity'   => (int)$row['quantity'],
            'price'      => (float)$row['price'],
            'mrp'        => (float)($row['mrp'] ?? 0),
            'status'     => $row['status'] ?? 'active'
        ];
        $total_qty += (int)$row['quantity'];
    }

    $masters = getMasterLists($conn);

    echo json_encode([
        'status'    => 'success',
        'item'      => $item,
        'variants'  => $variants,
        'total_qty' => $total_qty,
        'masters'   => $masters
    ]);
    exit;
}

// 2. Update a single variant (Color, Size, Style, Price, Stock)
if ($action === 'update_single_stock' || $action === 'update_stock' || $action === 'save_variant') {
    $variant_id = (int)($_REQUEST['variant_id'] ?? 0);
    $item_id    = (int)($_REQUEST['item_id'] ?? 0);
    $quantity   = max(0, (int)($_REQUEST['quantity'] ?? 0));
    $color_name = trim(mysqli_real_escape_string($conn, $_REQUEST['color_name'] ?? ''));
    $size_name  = trim(mysqli_real_escape_string($conn, $_REQUEST['size_name'] ?? ''));
    $style_name = trim(mysqli_real_escape_string($conn, $_REQUEST['style_name'] ?? ''));
    $price      = isset($_REQUEST['price']) ? (float)$_REQUEST['price'] : -1;

    if ($variant_id <= 0 || $item_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters: variant_id and item_id are required']);
        exit;
    }

    $set_fields = ["quantity = '$quantity'"];
    if (isset($_REQUEST['color_name'])) $set_fields[] = "color_name = '$color_name'";
    if (isset($_REQUEST['size_name']))  $set_fields[] = "size_name = '$size_name'";
    if (isset($_REQUEST['style_name'])) $set_fields[] = "style_name = '$style_name'";
    if ($price >= 0) $set_fields[] = "price = '$price'";

    $set_sql = implode(', ', $set_fields);
    $upd = mysqli_query($conn, "UPDATE tbl_item_variants SET $set_sql WHERE id = '$variant_id' AND item_id = '$item_id'");

    if ($upd) {
        $tot_res = mysqli_query($conn, "SELECT SUM(quantity) as total_qty FROM tbl_item_variants WHERE item_id = '$item_id'");
        $tot_row = mysqli_fetch_assoc($tot_res);
        $total_qty = (int)($tot_row['total_qty'] ?? 0);

        $v_res = mysqli_query($conn, "SELECT id, color_name, size_name, style_name, quantity FROM tbl_item_variants WHERE item_id = '$item_id' ORDER BY id ASC");
        $updated_variants = [];
        while ($v = mysqli_fetch_assoc($v_res)) {
            $updated_variants[] = $v;
        }

        echo json_encode([
            'status'           => 'success',
            'message'          => 'Variant updated successfully',
            'variant_id'       => $variant_id,
            'quantity'         => $quantity,
            'total_qty'        => $total_qty,
            'updated_variants' => $updated_variants
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update variant']);
    }
    exit;
}

// 3. Update ALL variants of an item
if ($action === 'update_all_stocks' || $action === 'save_all_stocks' || $action === 'update_all') {
    $item_id  = (int)($_REQUEST['item_id'] ?? 0);
    $variants = $_REQUEST['variants'] ?? [];

    if ($item_id <= 0 || !is_array($variants)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request payload']);
        exit;
    }

    foreach ($variants as $v) {
        $vid   = (int)($v['id'] ?? 0);
        $qty   = max(0, (int)($v['quantity'] ?? 0));
        $color = trim(mysqli_real_escape_string($conn, $v['color_name'] ?? ''));
        $size  = trim(mysqli_real_escape_string($conn, $v['size_name'] ?? ''));
        $style = trim(mysqli_real_escape_string($conn, $v['style_name'] ?? ''));
        $price = isset($v['price']) ? (float)$v['price'] : -1;

        if ($vid > 0) {
            $set_fields = ["quantity = '$qty'"];
            if (isset($v['color_name'])) $set_fields[] = "color_name = '$color'";
            if (isset($v['size_name']))  $set_fields[] = "size_name = '$size'";
            if (isset($v['style_name'])) $set_fields[] = "style_name = '$style'";
            if ($price >= 0) $set_fields[] = "price = '$price'";

            $set_sql = implode(', ', $set_fields);
            mysqli_query($conn, "UPDATE tbl_item_variants SET $set_sql WHERE id = '$vid' AND item_id = '$item_id'");
        }
    }

    $tot_res = mysqli_query($conn, "SELECT SUM(quantity) as total_qty FROM tbl_item_variants WHERE item_id = '$item_id'");
    $tot_row = mysqli_fetch_assoc($tot_res);
    $total_qty = (int)($tot_row['total_qty'] ?? 0);

    $v_res = mysqli_query($conn, "SELECT id, color_name, size_name, style_name, quantity FROM tbl_item_variants WHERE item_id = '$item_id' ORDER BY id ASC");
    $updated_variants = [];
    while ($v = mysqli_fetch_assoc($v_res)) {
        $updated_variants[] = $v;
    }

    echo json_encode([
        'status'           => 'success',
        'message'          => 'All variant stocks updated successfully',
        'total_qty'        => $total_qty,
        'updated_variants' => $updated_variants
    ]);
    exit;
}

// 4. Quick update item stock (support legacy/row quick save buttons)
if ($action === 'quick_update_item_stock' || $action === 'quick_update' || $action === 'update_item_stock') {
    $item_id   = (int)($_REQUEST['item_id'] ?? 0);
    $total_qty = max(0, (int)($_REQUEST['total_qty'] ?? $_REQUEST['quantity'] ?? 0));

    if ($item_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Item ID']);
        exit;
    }

    $v_res = mysqli_query($conn, "SELECT id FROM tbl_item_variants WHERE item_id = '$item_id' ORDER BY id ASC");
    $variant_count = mysqli_num_rows($v_res);

    if ($variant_count > 0) {
        $first_var = mysqli_fetch_assoc($v_res);
        $first_id  = $first_var['id'];
        mysqli_query($conn, "UPDATE tbl_item_variants SET quantity = '$total_qty' WHERE id = '$first_id'");
    } else {
        $m_res = mysqli_query($conn, "SELECT purchase_price, mrp FROM tbl_item_master WHERE id = '$item_id'");
        $m_row = mysqli_fetch_assoc($m_res);
        $price = (float)($m_row['purchase_price'] ?? 0.00);
        $mrp   = (float)($m_row['mrp'] ?? 0.00);

        mysqli_query($conn, "INSERT INTO tbl_item_variants (item_id, color_name, size_name, style_name, quantity, price, mrp, status)
                             VALUES ('$item_id', '', '', '', '$total_qty', '$price', '$mrp', 'active')");
    }

    $tot_res = mysqli_query($conn, "SELECT SUM(quantity) as total_qty FROM tbl_item_variants WHERE item_id = '$item_id'");
    $tot_row = mysqli_fetch_assoc($tot_res);
    $total_qty = (int)($tot_row['total_qty'] ?? 0);

    $v_res = mysqli_query($conn, "SELECT id, color_name, size_name, style_name, quantity FROM tbl_item_variants WHERE item_id = '$item_id' ORDER BY id ASC");
    $updated_variants = [];
    while ($v = mysqli_fetch_assoc($v_res)) {
        $updated_variants[] = $v;
    }

    echo json_encode([
        'status'           => 'success',
        'message'          => 'Stock updated successfully',
        'total_qty'        => $total_qty,
        'updated_variants' => $updated_variants
    ]);
    exit;
}

// 5. Add a new variant record for an item
if ($action === 'add_variant' || $action === 'add_new_variant') {
    $item_id    = (int)($_REQUEST['item_id'] ?? 0);
    $color_name = trim(mysqli_real_escape_string($conn, $_REQUEST['color_name'] ?? ''));
    $size_name  = trim(mysqli_real_escape_string($conn, $_REQUEST['size_name'] ?? ''));
    $style_name = trim(mysqli_real_escape_string($conn, $_REQUEST['style_name'] ?? ''));
    $quantity   = max(0, (int)($_REQUEST['quantity'] ?? 0));
    $price      = (float)($_REQUEST['price'] ?? 0.00);

    if ($item_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Item ID']);
        exit;
    }

    if ($price <= 0) {
        $m_res = mysqli_query($conn, "SELECT purchase_price, mrp FROM tbl_item_master WHERE id = '$item_id'");
        $m_row = mysqli_fetch_assoc($m_res);
        $price = (float)($m_row['purchase_price'] ?? 0.00);
        $mrp   = (float)($m_row['mrp'] ?? 0.00);
    } else {
        $mrp   = $price;
    }

    $ins = mysqli_query($conn, "INSERT INTO tbl_item_variants (item_id, color_name, size_name, style_name, quantity, price, mrp, status)
                                VALUES ('$item_id', '$color_name', '$size_name', '$style_name', '$quantity', '$price', '$mrp', 'active')");

    if ($ins) {
        $tot_res = mysqli_query($conn, "SELECT SUM(quantity) as total_qty FROM tbl_item_variants WHERE item_id = '$item_id'");
        $tot_row = mysqli_fetch_assoc($tot_res);
        $total_qty = (int)($tot_row['total_qty'] ?? 0);

        $v_res = mysqli_query($conn, "SELECT id, color_name, size_name, style_name, quantity FROM tbl_item_variants WHERE item_id = '$item_id' ORDER BY id ASC");
        $updated_variants = [];
        while ($v = mysqli_fetch_assoc($v_res)) {
            $updated_variants[] = $v;
        }

        echo json_encode([
            'status'           => 'success',
            'message'          => 'New variant added successfully',
            'total_qty'        => $total_qty,
            'updated_variants' => $updated_variants
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add variant']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action received: ' . ($action ?: 'empty')]);
exit;
