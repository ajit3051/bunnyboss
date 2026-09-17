<?php
/**
 * Products & Catalog API Endpoint
 * Path: /api/products.php
 */
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/auth_helper.php");

send_api_headers();

// Validate Bearer Token
$token_data = validate_api_token(false);

$raw_input = file_get_contents('php_input') ?: file_get_contents('php://input');
$json_data = json_decode($raw_input, true) ?: [];
$request = array_merge($_GET, $_POST, $json_data);

$action = $request['action'] ?? 'list_products';

$db = connect();

// ---------------------------------------------------------
// 1. LIST & FILTER PRODUCTS
// ---------------------------------------------------------
if ($action === 'list_products') {
    $conditions = ["status = 'true'"];
    $types = '';
    $params = [];

    // Category / Group Filter
    if (!empty($request['categories'])) {
        $cats = is_array($request['categories']) ? $request['categories'] : explode(',', $request['categories']);
        $cats = array_filter(array_map('trim', $cats));
        if (!empty($cats)) {
            $placeholders = implode(',', array_fill(0, count($cats), '?'));
            $conditions[] = "group_name IN ($placeholders)";
            foreach ($cats as $cat) {
                $types .= 's';
                $params[] = $cat;
            }
        }
    }

    // Size Filter
    if (!empty($request['sizes'])) {
        $sizes = is_array($request['sizes']) ? $request['sizes'] : explode(',', $request['sizes']);
        $sizes = array_filter(array_map('trim', $sizes));
        if (!empty($sizes)) {
            $placeholders = implode(',', array_fill(0, count($sizes), '?'));
            $conditions[] = "id IN (SELECT DISTINCT item_id FROM tbl_item_variants WHERE size_name IN ($placeholders))";
            foreach ($sizes as $size) {
                $types .= 's';
                $params[] = $size;
            }
        }
    }

    // Color Filter
    if (!empty($request['colors'])) {
        $colors = is_array($request['colors']) ? $request['colors'] : explode(',', $request['colors']);
        $colors = array_filter(array_map('trim', $colors));
        if (!empty($colors)) {
            $placeholders = implode(',', array_fill(0, count($colors), '?'));
            $conditions[] = "id IN (SELECT DISTINCT item_id FROM tbl_item_variants WHERE color_name IN ($placeholders))";
            foreach ($colors as $color) {
                $types .= 's';
                $params[] = $color;
            }
        }
    }

    // Brand Filter
    if (!empty($request['brands'])) {
        $brands = is_array($request['brands']) ? $request['brands'] : explode(',', $request['brands']);
        $brands = array_filter(array_map('trim', $brands));
        if (!empty($brands)) {
            $placeholders = implode(',', array_fill(0, count($brands), '?'));
            $conditions[] = "brand_name IN ($placeholders)";
            foreach ($brands as $brand) {
                $types .= 's';
                $params[] = $brand;
            }
        }
    }

    // Search Query
    if (!empty($request['search'])) {
        $q = '%' . trim($request['search']) . '%';
        $conditions[] = "(item_name LIKE ? OR description LIKE ? OR item_code LIKE ? OR group_name LIKE ? OR brand_name LIKE ?)";
        $types .= 'sssss';
        $params[] = $q;
        $params[] = $q;
        $params[] = $q;
        $params[] = $q;
        $params[] = $q;
    }

    // Price Range Filter
    $min_price = isset($request['min_price']) ? floatval($request['min_price']) : null;
    $max_price = isset($request['max_price']) ? floatval($request['max_price']) : null;

    if ($min_price !== null && $max_price !== null && $max_price > 0) {
        $conditions[] = "id IN (SELECT DISTINCT item_id FROM tbl_item_variants WHERE price BETWEEN ? AND ?)";
        $types .= 'dd';
        $params[] = $min_price;
        $params[] = $max_price;
    } elseif ($min_price !== null) {
        $conditions[] = "id IN (SELECT DISTINCT item_id FROM tbl_item_variants WHERE price >= ?)";
        $types .= 'd';
        $params[] = $min_price;
    }

    $whereSQL = implode(' AND ', $conditions);

    // Sorting
    $sort = $request['sort'] ?? 'newest';
    $orderSQL = "tbl_item_master.id DESC";
    if ($sort === 'price_asc') {
        $orderSQL = "(SELECT MIN(price) FROM tbl_item_variants WHERE item_id = tbl_item_master.id) ASC";
    } elseif ($sort === 'price_desc') {
        $orderSQL = "(SELECT MIN(price) FROM tbl_item_variants WHERE item_id = tbl_item_master.id) DESC";
    } elseif ($sort === 'popularity') {
        $orderSQL = "tbl_item_master.id DESC";
    }

    // Count Total
    $countQuery = "SELECT COUNT(*) as total FROM tbl_item_master WHERE $whereSQL";
    $countStmt = empty($params) ? $db->select($countQuery) : $db->select($countQuery, $types, ...$params);
    $total_records = ($countStmt && $c_row = $countStmt->fetch_assoc()) ? (int)$c_row['total'] : 0;

    // Pagination
    $per_page = isset($request['per_page']) ? min(API_MAX_LIMIT, max(1, (int)$request['per_page'])) : API_DEFAULT_LIMIT;
    $page = isset($request['page']) ? max(1, (int)$request['page']) : API_DEFAULT_PAGE;
    $total_pages = ceil($total_records / $per_page);
    $offset = ($page - 1) * $per_page;

    // Fetch Products
    $query = "SELECT tbl_item_master.*, 
            (SELECT image_path FROM tbl_item_images WHERE item_id = tbl_item_master.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture,
            (SELECT GROUP_CONCAT(DISTINCT size_name ORDER BY id ASC SEPARATOR ',') FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND size_name != '') AS size_name,
            COALESCE((SELECT MIN(price) FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND price > 0), 0) AS sp,
            COALESCE((SELECT SUM(quantity) FROM tbl_item_variants WHERE item_id = tbl_item_master.id), 0) AS min_qty
            FROM tbl_item_master WHERE $whereSQL ORDER BY $orderSQL LIMIT ? OFFSET ?";

    $fetchTypes = $types . 'ii';
    $fetchParams = array_merge($params, [$per_page, $offset]);

    $stmt = $db->select($query, $fetchTypes, ...$fetchParams);

    $products = [];
    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {
            $imgSrc = get_product_image_url($row['picture'] ?? null);
            $sizes = !empty($row['size_name']) ? explode(',', $row['size_name']) : [];
            $products[] = [
                'id' => (int)$row['id'],
                'item_code' => $row['item_code'] ?? '',
                'item_name' => $row['item_name'] ?? '',
                'group_name' => $row['group_name'] ?? '',
                'brand_name' => $row['brand_name'] ?? '',
                'description' => $row['description'] ?? '',
                'price' => (float)$row['sp'],
                'image' => $imgSrc,
                'sizes' => $sizes,
                'total_stock' => (int)$row['min_qty'],
                'in_stock' => ((int)$row['min_qty'] > 0)
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'page' => $page,
        'per_page' => $per_page,
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'products' => $products
    ]);
    exit;
}

// ---------------------------------------------------------
// 2. GET SINGLE PRODUCT DETAILS
// ---------------------------------------------------------
if ($action === 'get_product') {
    $product_id = (int)($request['id'] ?? 0);
    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
        exit;
    }

    $stmt = $db->select("SELECT * FROM tbl_item_master WHERE id = ? AND status = 'true'", 'i', $product_id);
    if (!$stmt || $stmt->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found or inactive.']);
        exit;
    }

    $product = $stmt->fetch_assoc();

    // Fetch Gallery Images
    $img_stmt = $db->select("SELECT image_path FROM tbl_item_images WHERE item_id = ? ORDER BY sort_order ASC, id ASC", 'i', $product_id);
    $gallery = [];
    if ($img_stmt) {
        while ($img_row = $img_stmt->fetch_assoc()) {
            if (!empty($img_row['image_path'])) {
                $gallery[] = get_product_image_url($img_row['image_path']);
            }
        }
    }

    if (empty($gallery)) {
        $gallery[] = get_product_image_url(null);
    }

    // Fetch Variants (size, color, price, quantity)
    $var_stmt = $db->select("SELECT id, size_name, color_name, price, quantity FROM tbl_item_variants WHERE item_id = ? ORDER BY id ASC", 'i', $product_id);
    $variants = [];
    $available_sizes = [];
    $available_colors = [];
    $min_price = null;

    if ($var_stmt) {
        while ($v_row = $var_stmt->fetch_assoc()) {
            $price = (float)$v_row['price'];
            if ($min_price === null || ($price > 0 && $price < $min_price)) {
                $min_price = $price;
            }
            if (!empty($v_row['size_name']) && !in_array($v_row['size_name'], $available_sizes)) {
                $available_sizes[] = $v_row['size_name'];
            }
            if (!empty($v_row['color_name']) && !in_array($v_row['color_name'], $available_colors)) {
                $available_colors[] = $v_row['color_name'];
            }
            $variants[] = [
                'variant_id' => (int)$v_row['id'],
                'size' => $v_row['size_name'],
                'color' => $v_row['color_name'],
                'price' => $price,
                'quantity' => (int)$v_row['quantity'],
                'in_stock' => ((int)$v_row['quantity'] > 0)
            ];
        }
    }

    // Fetch Related Products (same group/category)
    $rel_stmt = $db->select("SELECT id, item_name, group_name, (SELECT image_path FROM tbl_item_images WHERE item_id = tbl_item_master.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture, COALESCE((SELECT MIN(price) FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND price > 0), 0) AS sp FROM tbl_item_master WHERE group_name = ? AND id != ? AND status = 'true' ORDER BY id DESC LIMIT 4", 'si', $product['group_name'], $product_id);
    $related = [];
    if ($rel_stmt) {
        while ($r_row = $rel_stmt->fetch_assoc()) {
            $related[] = [
                'id' => (int)$r_row['id'],
                'name' => $r_row['item_name'],
                'group_name' => $r_row['group_name'],
                'price' => (float)$r_row['sp'],
                'image' => get_product_image_url($r_row['picture'] ?? null)
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'product' => [
            'id' => (int)$product['id'],
            'item_code' => $product['item_code'] ?? '',
            'item_name' => $product['item_name'] ?? '',
            'group_name' => $product['group_name'] ?? '',
            'brand_name' => $product['brand_name'] ?? '',
            'description' => $product['description'] ?? '',
            'main_image' => $gallery[0],
            'gallery' => $gallery,
            'price' => $min_price !== null ? $min_price : 0.00,
            'sizes' => $available_sizes,
            'colors' => $available_colors,
            'variants' => $variants,
            'related_products' => $related
        ]
    ]);
    exit;
}

// ---------------------------------------------------------
// 3. GET SIDEBAR FILTERS (Categories, Brands, Sizes, Colors, Price Range)
// ---------------------------------------------------------
if ($action === 'get_filters') {
    // Categories / Groups
    $cat_stmt = $db->select("SELECT group_name, COUNT(*) as count FROM tbl_item_master WHERE status = 'true' AND group_name != '' GROUP BY group_name ORDER BY group_name ASC");
    $categories = [];
    if ($cat_stmt) {
        while ($row = $cat_stmt->fetch_assoc()) {
            $categories[] = ['name' => $row['group_name'], 'count' => (int)$row['count']];
        }
    }

    // Brands
    $brand_stmt = $db->select("SELECT brand_name, COUNT(*) as count FROM tbl_item_master WHERE status = 'true' AND brand_name != '' GROUP BY brand_name ORDER BY brand_name ASC");
    $brands = [];
    if ($brand_stmt) {
        while ($row = $brand_stmt->fetch_assoc()) {
            $brands[] = ['name' => $row['brand_name'], 'count' => (int)$row['count']];
        }
    }

    // Sizes
    $size_stmt = $db->select("SELECT DISTINCT size_name FROM tbl_item_variants WHERE size_name != '' ORDER BY size_name ASC");
    $sizes = [];
    if ($size_stmt) {
        while ($row = $size_stmt->fetch_assoc()) {
            $sizes[] = $row['size_name'];
        }
    }

    // Colors
    $color_stmt = $db->select("SELECT DISTINCT color_name FROM tbl_item_variants WHERE color_name != '' ORDER BY color_name ASC");
    $colors = [];
    if ($color_stmt) {
        while ($row = $color_stmt->fetch_assoc()) {
            $colors[] = $row['color_name'];
        }
    }

    // Price Bounds
    $price_stmt = $db->select("SELECT MIN(price) as min_p, MAX(price) as max_p FROM tbl_item_variants WHERE price > 0");
    $price_row = $price_stmt ? $price_stmt->fetch_assoc() : null;
    $min_price = $price_row ? (float)$price_row['min_p'] : 0;
    $max_price = $price_row ? (float)$price_row['max_p'] : 5000;

    echo json_encode([
        'success' => true,
        'filters' => [
            'categories' => $categories,
            'brands' => $brands,
            'sizes' => $sizes,
            'colors' => $colors,
            'min_price' => $min_price,
            'max_price' => $max_price
        ]
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid products API action request.']);
exit;
