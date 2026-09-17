<?php
include_once("include/config.php");

function generatePaginationHTML($page, $total_pages) {
    if ($total_pages <= 1) {
        return '';
    }

    $html = '<ul class="pagination justify-content-center">';

    if ($page > 1) {
        $prev_page = $page - 1;
        $html .= '<li class="page-item"><a class="page-link page-link-prev" href="javascript:void(0);" data-page="' . $prev_page . '" aria-label="Previous"><span aria-hidden="true"><i class="icon-long-arrow-left"></i></span>Prev</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><a class="page-link page-link-prev" href="javascript:void(0);" tabindex="-1" aria-disabled="true"><span aria-hidden="true"><i class="icon-long-arrow-left"></i></span>Prev</a></li>';
    }

    $start_page = max(1, $page - 2);
    $end_page   = min($total_pages, $page + 2);

    if ($start_page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="1">1</a></li>';
        if ($start_page > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $page) {
            $html .= '<li class="page-item active" aria-current="page"><a class="page-link" href="javascript:void(0);" data-page="' . $i . '">' . $i . '</a></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="' . $i . '">' . $i . '</a></li>';
        }
    }

    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="javascript:void(0);" data-page="' . $total_pages . '">' . $total_pages . '</a></li>';
    }

    if ($page < $total_pages) {
        $next_page = $page + 1;
        $html .= '<li class="page-item"><a class="page-link page-link-next" href="javascript:void(0);" data-page="' . $next_page . '" aria-label="Next">Next <span aria-hidden="true"><i class="icon-long-arrow-right"></i></span></a></li>';
    } else {
        $html .= '<li class="page-item disabled"><a class="page-link page-link-next" href="javascript:void(0);" tabindex="-1" aria-disabled="true">Next <span aria-hidden="true"><i class="icon-long-arrow-right"></i></span></a></li>';
    }

    $html .= '</ul>';
    return $html;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $db = connect();

    $conditions = [];
    $types = '';
    $params = [];

    // --- Dynamic Binding Processing Matrix ---

    // 1. Category Filter Mapping
    if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
        $placeholders = implode(',', array_fill(0, count($_POST['categories']), '?'));
        $conditions[] = "group_name IN ($placeholders)";
        foreach ($_POST['categories'] as $cat) {
            $types .= 's';
            $params[] = $cat;
        }
    }

    // 2. Size Filter Mapping
    if (!empty($_POST['sizes']) && is_array($_POST['sizes'])) {
        $placeholders = implode(',', array_fill(0, count($_POST['sizes']), '?'));
        $conditions[] = "id IN (SELECT DISTINCT item_id FROM tbl_item_variants WHERE size_name IN ($placeholders))";
        foreach ($_POST['sizes'] as $size) {
            $types .= 's';
            $params[] = $size;
        }
    }

    // 3. Color Filter Mapping
    if (!empty($_POST['colors']) && is_array($_POST['colors'])) {
        $placeholders = implode(',', array_fill(0, count($_POST['colors']), '?'));
        $conditions[] = "id IN (SELECT DISTINCT item_id FROM tbl_item_variants WHERE color_name IN ($placeholders))";
        foreach ($_POST['colors'] as $color) {
            $types .= 's';
            $params[] = $color;
        }
    }

    // 4. Brand Filter Mapping
    if (!empty($_POST['brands']) && is_array($_POST['brands'])) {
        $placeholders = implode(',', array_fill(0, count($_POST['brands']), '?'));
        $conditions[] = "brand_name IN ($placeholders)";
        foreach ($_POST['brands'] as $brand) {
            $types .= 's';
            $params[] = $brand;
        }
    }

    // 5. Price Range Filter Mapping
    if (isset($_POST['min_price']) && isset($_POST['max_price'])) {
        $min_price = floatval($_POST['min_price']);
        $max_price = floatval($_POST['max_price']);

        $conditions[] = "id IN (SELECT DISTINCT item_id FROM tbl_item_variants WHERE price BETWEEN ? AND ?)";
        $types .= 'dd';
        $params[] = $min_price;
        $params[] = $max_price;
    }

    $page  = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;
    $limit = defined('_PRODUCTS_PER_PAGE_') ? (int)_PRODUCTS_PER_PAGE_ : 12;

    // --- Total Count Calculation ---
    $count_sql = "SELECT COUNT(DISTINCT tbl_item_master.id) as total FROM tbl_item_master";
    if (!empty($conditions)) {
        $count_sql .= " WHERE " . implode(" AND ", $conditions);
    }
    $count_res = empty($types) ? $db->select($count_sql) : $db->select($count_sql, $types, ...$params);
    $total_records = 0;
    if ($count_res && $c_row = $count_res->fetch_assoc()) {
        $total_records = (int)$c_row['total'];
    }

    $total_pages = ($limit > 0) ? (int)ceil($total_records / $limit) : 1;
    if ($page > $total_pages && $total_pages > 0) {
        $page = $total_pages;
    }
    $offset = ($page - 1) * $limit;

    // --- Query Compilation ---
    $sql = "SELECT tbl_item_master.*, 
      (SELECT image_path FROM tbl_item_images WHERE item_id = tbl_item_master.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture,
      (SELECT GROUP_CONCAT(DISTINCT size_name ORDER BY id ASC SEPARATOR ',') FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND size_name != '') AS size_name,
      COALESCE((SELECT MIN(price) FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND price > 0), 0) AS sp,
      COALESCE((SELECT SUM(quantity) FROM tbl_item_variants WHERE item_id = tbl_item_master.id), 0) AS min_qty
      FROM tbl_item_master";
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    $sql .= " ORDER BY tbl_item_master.id DESC LIMIT ? OFFSET ?";

    $data_types = $types . 'ii';
    $data_params = array_merge($params, [$limit, $offset]);

    $result = $db->select($sql, $data_types, ...$data_params);

    $imageUrl = _IMAGE_PATH;

    ob_start();

    // --- Output Layout Generation ---
    if ($result && $result->num_rows > 0) {
        while ($products = $result->fetch_assoc()) {

            $picture       = htmlspecialchars($products['picture']);
            $raw_size_str  = $products['size_name'] ?? '';
            $size_name_list = array_filter(array_map('trim', explode(',', $raw_size_str)));

            $variants_res = $db->select("SELECT size_name, price, quantity FROM tbl_item_variants WHERE item_id = ? AND size_name != ''", 's', $products['id']);
            $variants_by_size = [];
            if ($variants_res) {
                while ($v_row = $variants_res->fetch_assoc()) {
                    $s_name = trim($v_row['size_name']);
                    if ($s_name !== '') {
                        $variants_by_size[$s_name] = [
                            'price' => (float)$v_row['price'],
                            'quantity' => (int)$v_row['quantity']
                        ];
                    }
                }
            }
            $is_new_flag = isset($products['is_new']) ? ($products['is_new'] !== 'false' && $products['is_new'] !== '0' && $products['is_new'] !== 'no') : true;
?>
            <div class="col-6 col-md-4 col-lg-4 col-xl-3">
                <div class="product product-7 text-center">
                    <figure class="product-media">
                        <?php if ($is_new_flag): ?>
                            <span class="product-label label-new">New</span>
                        <?php endif; ?>
                        <a href="<?= _BASEURL ?>product-details.php/?product-id=<?= strtolower($products['id']) ?>">
                            <img src="<?= $imageUrl ?>item-master/<?= $picture ?>" alt="Product image" class="product-image">
                        </a>
                        <div class="product-action-vertical">
                            <a href="javascript:void(0);" class="btn-product-icon btn-share" title="Share Product" data-url="<?= _BASEURL ?>product-details.php/?product-id=<?= strtolower($products['id']) ?>" data-title="<?= htmlspecialchars($products['item_name']) ?>"><span>Share Product</span></a>
                        </div>
                    </figure>
                    <!-- End .product-media -->

                    <div class="product-body">
                        <div class="product-cat">
                            <a href="<?= _BASEURL ?>product-details.php/?product-id=<?= strtolower($products['id']) ?>"><?= htmlspecialchars($products['item_name']) ?></a>
                        </div>
                        <!-- End .product-cat -->
                        <h3 class="product-title"><a href="<?= _BASEURL ?>product-details.php/?product-id=<?= strtolower($products['id']) ?>"><?= htmlspecialchars(!empty($products['sku_no']) ? $products['sku_no'] : $products['item_code']) ?></a></h3>
                        <!-- End .product-title -->
                        <div class="product-size-select">
                            <?php
                            $active_size = '';
                            if (!empty($size_name_list)) {
                                foreach ($size_name_list as $sz) {
                                    $sz_qty = !empty($variants_by_size) ? (isset($variants_by_size[$sz]) ? (int)$variants_by_size[$sz]['quantity'] : 0) : (isset($products['min_qty']) ? (int)$products['min_qty'] : 0);
                                    if ($sz_qty > 0) {
                                        $active_size = $sz;
                                        break;
                                    }
                                }
                            }

                            $active_qty = (isset($products['min_qty']) ? (int)$products['min_qty'] : 0);
                            $card_price = (float)$products['sp'];
                            if ($active_size !== '') {
                                $active_qty = isset($variants_by_size[$active_size]) ? (int)$variants_by_size[$active_size]['quantity'] : $active_qty;
                                if (isset($variants_by_size[$active_size])) {
                                    $card_price = (float)$variants_by_size[$active_size]['price'];
                                }
                            }

                            foreach ($size_name_list as $size_name) {
                                $v_price = isset($variants_by_size[$size_name]) ? number_format((float)$variants_by_size[$size_name]['price'], 2, '.', '') : number_format((float)$products['sp'], 2, '.', '');
                                $v_qty = !empty($variants_by_size) ? (isset($variants_by_size[$size_name]) ? (int)$variants_by_size[$size_name]['quantity'] : 0) : (int)$products['min_qty'];
                                $is_disabled = ($v_qty <= 0);
                                $active_class = ($size_name === $active_size && !$is_disabled) ? ' active' : '';
                                $disabled_class = $is_disabled ? ' disabled' : '';
                                $title_attr = $is_disabled ? ' title="Out of Stock"' : '';
                            ?>
                                <span class="size-option<?= $active_class ?><?= $disabled_class ?>" data-size="<?= htmlspecialchars($size_name) ?>" data-price="<?= htmlspecialchars($v_price) ?>" data-qty="<?= htmlspecialchars($v_qty) ?>"<?= $title_attr ?>><?= htmlspecialchars($size_name) ?></span>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="product-price">
                            ₹<?= number_format((float)$card_price, 2, '.', '') ?>
                        </div>
                        <!-- End .product-price -->
                    </div>
                    <!-- End .product-body -->

                    <?php
                    if ($active_qty <= 0) {
                    ?>
                        <div class="product-action product-action-split" data-id="<?= $products['id'] ?>">
                            <a class="btn-product btn-out-of-stock disabled" role="button" style="width: 100%; background-color: #e5e5e5; color: #777; border-color: #ddd; cursor: not-allowed; pointer-events: none;"><i class="icon-ban"></i><span>Out of Stock</span></a>
                        </div>
                    <?php } else { ?>
                        <div class="product-action product-action-split" data-id="<?= $products['id'] ?>">
                            <a class="btn-product btn-cart add-cart-btn" role="button" data-id="<?= $products['id'] ?>" data-size="<?= htmlspecialchars($active_size) ?>"><i class="icon-shopping-cart"></i><span>Add to Cart</span></a>
                            <a class="btn-product btn-order-now order-now-btn" role="button" data-id="<?= $products['id'] ?>" data-size="<?= htmlspecialchars($active_size) ?>"><i class="icon-rocket"></i><span>Order Now</span></a>
                        </div>
                    <?php } ?>
                    <!-- End .product-action -->
                </div>
                <!-- End .product -->
            </div>
<?php
        }
    } else {
        echo '<div class="col-12 text-center my-5">
                <i class="icon-warning-empty" style="font-size: 48px; opacity: 0.5;"></i>
                <p class="mt-2">No items match your active checkbox filters.</p>
              </div>';
    }
    $grid_html = ob_get_clean();

    $pagination_html = generatePaginationHTML($page, $total_pages);

    echo json_encode([
        'grid_html'       => $grid_html,
        'pagination_html' => $pagination_html,
        'total_records'   => $total_records,
        'current_page'    => $page,
        'total_pages'     => $total_pages
    ]);
    exit;
}
