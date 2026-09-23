<?php include_once("include/config.php"); ?>
<?php include('include/top.php'); ?>

<?php
$validationHelper = new validation();

$db = connect();

$product_id = $_GET['product-id'] ?? '';
if ($product_id) {

    $pur_stmt = $db->select("SELECT tbl_item_master.*, 
     (SELECT image_path FROM tbl_item_images WHERE item_id = tbl_item_master.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture,
     (SELECT GROUP_CONCAT(DISTINCT size_name ORDER BY id ASC SEPARATOR ',') FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND size_name != '') AS size_name,
     COALESCE((SELECT MIN(price) FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND price > 0), 0) AS sp,
     COALESCE((SELECT SUM(quantity) FROM tbl_item_variants WHERE item_id = tbl_item_master.id), 0) AS min_qty
     FROM tbl_item_master WHERE id=?", 's', $product_id);

    if ($pur_stmt) {
        $res = $pur_stmt->fetch_assoc();
        if ($res) {
            foreach ($res as $key => $value) {
                $$key = $validationHelper->filterText($value);
            }
        }
        $pur_stmt->close();
    }

    // Fetch size variants mapping for price & stock quantity
    $variants_by_size = [];
    $v_res = $db->select("SELECT size_name, price, quantity FROM tbl_item_variants WHERE item_id = ? AND size_name != ''", 's', $product_id);
    if ($v_res) {
        while ($v_row = $v_res->fetch_assoc()) {
            $s_name = trim($v_row['size_name']);
            if ($s_name !== '') {
                $variants_by_size[$s_name] = [
                    'price' => (float)$v_row['price'],
                    'quantity' => (int)$v_row['quantity']
                ];
            }
        }
    }

    // Fetch all images for gallery
    $product_gallery_images = [];
    $g_res = $db->select("SELECT image_path FROM tbl_item_images WHERE item_id=? ORDER BY sort_order ASC, id ASC", 's', $product_id);
    if ($g_res) {
        while ($g_row = $g_res->fetch_assoc()) {
            if (!empty($g_row['image_path'])) {
                $product_gallery_images[] = $g_row['image_path'];
            }
        }
    }
    if (empty($product_gallery_images) && !empty($picture)) {
        $product_gallery_images[] = $picture;
    }
}

if (empty($item_name)) {
    echo "<script>window.location.href='" . _BASEURL . "product-list.php';</script>";
    exit;
}

$raw_size_str = $size_name ?? '';
$size_name_list = array_filter(array_map('trim', explode(',', $validationHelper->filterText($raw_size_str))));

$active_size = '';
if (!empty($size_name_list)) {
    foreach ($size_name_list as $sz) {
        $sz_qty = !empty($variants_by_size) ? (isset($variants_by_size[$sz]) ? (int)$variants_by_size[$sz]['quantity'] : 0) : (isset($min_qty) ? (int)$min_qty : 0);
        if ($sz_qty > 0) {
            $active_size = $sz;
            break;
        }
    }
    if ($active_size !== '' && isset($variants_by_size[$active_size])) {
        $sp = $variants_by_size[$active_size]['price'];
        $min_qty = $variants_by_size[$active_size]['quantity'];
    } elseif ($active_size === '') {
        $min_qty = 0;
    }
}

$product_min_qty = isset($min_qty) ? (int)$min_qty : 0;
$mrp_num = !empty($mrp) ? (float)$mrp : 0;
$sp_num = (float)($sp ?? 0);
$has_discount = ($mrp_num > $sp_num && $sp_num > 0);
$discount_pct = $has_discount ? round((($mrp_num - $sp_num) / $mrp_num) * 100) : 0;

$imageUrl = _IMAGE_PATH;
$total_gallery_images = count($product_gallery_images);
?>

<main class="main product-details-page">
    <nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-2">
        <div class="container d-flex align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>product-list.php">Products</a></li>
                <?php if (!empty($group_name)) { ?>
                    <li class="breadcrumb-item"><a href="<?= _BASEURL ?>product-list.php?category=<?= urlencode($group_name) ?>"><?= $validationHelper->filterText($group_name) ?></a></li>
                <?php } ?>
                <li class="breadcrumb-item active" aria-current="page"><?= $validationHelper->filterText($item_name) ?></li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="product-details-top">
                <div class="row">
                    <!-- Left: Product Image Gallery -->
                    <div class="col-lg-6 col-md-6 mb-3 mb-md-0">
                        <div class="product-gallery product-gallery-vertical">
                            <div class="row">
                                <figure class="product-main-image">
                                    <img id="product-zoom" src="<?= $imageUrl ?>item-master/<?= $picture ?>" data-zoom-image="<?= $imageUrl ?>item-master/<?= $picture ?>" alt="<?= htmlspecialchars($item_name) ?>">

                                    <a href="#" id="btn-product-gallery" class="btn-product-gallery" title="View Fullscreen">
                                        <i class="icon-arrows"></i>
                                    </a>
                                </figure><!-- End .product-main-image -->

                                <div id="product-zoom-gallery" class="product-image-gallery">
                                    <?php foreach ($product_gallery_images as $g_idx => $g_img) { ?>
                                        <a class="product-gallery-item<?= $g_idx === 0 ? ' active' : '' ?>" href="#" data-image="<?= $imageUrl ?>item-master/<?= htmlspecialchars($g_img) ?>" data-zoom-image="<?= $imageUrl ?>item-master/<?= htmlspecialchars($g_img) ?>">
                                            <img src="<?= $imageUrl ?>item-master/<?= htmlspecialchars($g_img) ?>" alt="<?= htmlspecialchars($item_name) ?>">
                                        </a>
                                    <?php } ?>
                                </div><!-- End .product-image-gallery -->
                            </div><!-- End .row -->
                        </div><!-- End .product-gallery -->
                    </div><!-- End .col-lg-6 -->

                    <!-- Right: Product Details Info -->
                    <div class="col-lg-6 col-md-6">
                        <div class="product-details">
                            <div class="product-meta-header mb-1">
                                <?php if (!empty($brand_name)) { ?>
                                    <span class="product-meta-brand"><i class="icon-tag mr-1"></i><?= $validationHelper->filterText($brand_name) ?></span>
                                    <span class="meta-sep">•</span>
                                <?php } ?>
                                <?php if (!empty($group_name)) { ?>
                                    <a href="<?= _BASEURL ?>product-list.php?category=<?= urlencode($group_name) ?>" class="product-meta-cat"><?= $validationHelper->filterText($group_name) ?></a>
                                    <span class="meta-sep">•</span>
                                <?php } ?>
                                <span class="product-meta-sku">SKU: <?= $validationHelper->filterText(!empty($sku_no) ? $sku_no : $item_code) ?></span>
                            </div>

                            <h1 class="product-title"><?= $validationHelper->filterText($item_name) ?></h1>

                            <div class="ratings-container mb-2">
                                <div class="ratings">
                                    <div class="ratings-val" style="width: 80%;"></div>
                                </div>
                                <a class="ratings-text" href="#product-review-link" id="review-link">( 2 Reviews )</a>
                                <span class="meta-sep mx-2">•</span>
                                <?php if ($product_min_qty > 0) { ?>
                                    <span class="stock-badge in-stock"><i class="icon-check mr-1"></i>In Stock</span>
                                <?php } else { ?>
                                    <span class="stock-badge out-of-stock"><i class="icon-close mr-1"></i>Out of Stock</span>
                                <?php } ?>
                            </div>

                            <div class="product-price">
                                <span class="current-price">₹<?= number_format((float)$sp, 2, '.', '') ?></span>
                                <?php if ($has_discount) { ?>
                                    <span class="original-price ml-2">₹<?= number_format($mrp_num, 2, '.', '') ?></span>
                                    <span class="discount-badge ml-2"><?= $discount_pct ?>% OFF</span>
                                <?php } ?>
                            </div>

                            <?php if (!empty(trim($description))) { ?>
                                <div class="product-content mb-3">
                                    <p><?= $validationHelper->filterText($description) ?></p>
                                </div>
                            <?php } ?>

                            <?php if (!empty($size_name_list)) { ?>
                                <div class="details-filter-row details-row-size">
                                    <label for="size" class="size-label">Size:</label>
                                    <div class="product-size-select detail-size-select">
                                        <?php
                                        foreach ($size_name_list as $size_name) {
                                            $v_price = isset($variants_by_size[$size_name]) ? number_format((float)$variants_by_size[$size_name]['price'], 2, '.', '') : number_format((float)$sp, 2, '.', '');
                                            $v_qty = !empty($variants_by_size) ? (isset($variants_by_size[$size_name]) ? (int)$variants_by_size[$size_name]['quantity'] : 0) : (int)$min_qty;
                                            $is_disabled = ($v_qty <= 0);
                                            $active_class = ($size_name === $active_size && !$is_disabled) ? ' active' : '';
                                            $disabled_class = $is_disabled ? ' disabled' : '';
                                            $title_attr = $is_disabled ? ' title="Out of Stock"' : '';
                                        ?>
                                            <span class="size-option<?= $active_class ?><?= $disabled_class ?>" data-size="<?= htmlspecialchars($size_name) ?>" data-price="<?= htmlspecialchars($v_price) ?>" data-qty="<?= htmlspecialchars($v_qty) ?>"<?= $title_attr ?>><?= htmlspecialchars($size_name) ?></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="details-filter-row details-row-size">
                                <label for="qty">Qty:</label>
                                <div class="product-details-quantity">
                                    <input type="number" id="qty" class="form-control" value="1" min="1" max="10" step="1" data-decimals="0" required>
                                </div>
                            </div>

                            <!-- Add to Cart / Order Now Action Buttons -->
                            <div id="product-action-wrapper" data-id="<?= $id ?>" class="mt-3">
                                <?php if ($product_min_qty <= 0) { ?>
                                    <div class="product-details-action-split d-flex mb-3">
                                        <a href="JavaScript:void(0);" class="btn-product btn-out-of-stock disabled flex-fill" style="background-color: #e5e5e5; color: #777; border-color: #ddd; cursor: not-allowed; pointer-events: none; padding: 1.2rem; text-align: center; font-weight: 600;"><i class="icon-ban mr-1"></i><span>Out of Stock</span></a>
                                    </div>
                                <?php } else { ?>
                                    <div class="product-details-action-split d-flex mb-3">
                                        <a href="JavaScript:void(0);" class="btn-product btn-cart product-detail-btn-cart flex-fill mr-2" data-id="<?= $id ?>" data-size="<?= htmlspecialchars($active_size) ?>"><i class="icon-shopping-cart mr-1"></i><span>add to cart</span></a>
                                        <a href="JavaScript:void(0);" class="btn-product btn-order-now product-detail-btn-order-now flex-fill ml-2" data-id="<?= $id ?>" data-size="<?= htmlspecialchars($active_size) ?>"><i class="icon-rocket mr-1"></i><span>order now</span></a>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Trust Badges & Guarantee Features -->
                            <div class="product-trust-features my-4">
                                <div class="trust-grid">
                                    <div class="trust-item">
                                        <div class="trust-icon"><i class="icon-truck"></i></div>
                                        <div class="trust-info">
                                            <strong>Fast Delivery</strong>
                                            <span>Ship in 24-48 hrs</span>
                                        </div>
                                    </div>
                                    <div class="trust-item">
                                        <div class="trust-icon"><i class="icon-refresh"></i></div>
                                        <div class="trust-info">
                                            <strong>Easy Returns</strong>
                                            <span>7 Days Policy</span>
                                        </div>
                                    </div>
                                    <div class="trust-item">
                                        <div class="trust-icon"><i class="icon-shield"></i></div>
                                        <div class="trust-info">
                                            <strong>100% Genuine</strong>
                                            <span>Quality Assured</span>
                                        </div>
                                    </div>
                                    <div class="trust-item">
                                        <div class="trust-icon"><i class="icon-money"></i></div>
                                        <div class="trust-info">
                                            <strong>Pay on Delivery</strong>
                                            <span>Cash / UPI</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-details-footer">
                                <div class="social-icons social-icons-sm">
                                    <span class="social-label mr-2">Share:</span>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(_BASEURL . 'product-details.php?product-id=' . $id) ?>" class="social-icon" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                                    <a href="https://api.whatsapp.com/send?text=<?= urlencode($item_name . ' - ' . _BASEURL . 'product-details.php?product-id=' . $id) ?>" class="social-icon" title="WhatsApp" target="_blank"><i class="icon-whatsapp"></i></a>
                                    <a href="https://www.instagram.com/official_bunny__boss____" class="social-icon" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS" class="social-icon" title="Youtube" target="_blank"><i class="icon-youtube"></i></a>
                                </div>
                            </div><!-- End .product-details-footer -->
                        </div><!-- End .product-details -->
                    </div><!-- End .col-lg-6 -->
                </div><!-- End .row -->
            </div><!-- End .product-details-top -->

            <!-- Product Information Tabs -->
            <div class="product-details-tab mt-4 mb-5">
                <ul class="nav nav-pills justify-content-center" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="product-desc-link" data-toggle="tab" href="#product-desc-tab" role="tab" aria-controls="product-desc-tab" aria-selected="true">Description</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="product-info-link" data-toggle="tab" href="#product-info-tab" role="tab" aria-controls="product-info-tab" aria-selected="false">Specifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="product-shipping-link" data-toggle="tab" href="#product-shipping-tab" role="tab" aria-controls="product-shipping-tab" aria-selected="false">Shipping & Returns</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="product-review-link" data-toggle="tab" href="#product-review-tab" role="tab" aria-controls="product-review-tab" aria-selected="false">Reviews (2)</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel" aria-labelledby="product-desc-link">
                        <div class="product-desc-content">
                            <h3>Product Overview</h3>
                            <?php if (!empty(trim($description))) { ?>
                                <p><?= nl2br($validationHelper->filterText($description)) ?></p>
                            <?php } else { ?>
                                <p>Upgrade your wardrobe with the premium <strong><?= $validationHelper->filterText($item_name) ?></strong>. Crafted for everyday comfort, superior durability, and modern athletic styling that adapts effortlessly to work, casual outings, or active workouts.</p>
                            <?php } ?>
                            <h4 class="mt-3 mb-2" style="font-size: 15px; font-weight: 700; color: #222;">Highlights:</h4>
                            <ul>
                                <li>High-grade breathable fabric designed for extended wear</li>
                                <li>Cushioned ergonomic sole providing exceptional shock absorption</li>
                                <li>Durable anti-slip patterned outsole for firm grip on all surfaces</li>
                                <li>Lightweight, flexible silhouette tailored for modern style</li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="product-info-tab" role="tabpanel" aria-labelledby="product-info-link">
                        <div class="product-desc-content">
                            <h3>Product Specifications</h3>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-spec mb-0">
                                    <tbody>
                                        <?php if (!empty($brand_name)) { ?>
                                            <tr><th style="width: 30%; background: #fafafa;">Brand</th><td><?= $validationHelper->filterText($brand_name) ?></td></tr>
                                        <?php } ?>
                                        <?php if (!empty($group_name)) { ?>
                                            <tr><th style="background: #fafafa;">Category</th><td><?= $validationHelper->filterText($group_name) ?></td></tr>
                                        <?php } ?>
                                        <?php if (!empty($subgroup_name)) { ?>
                                            <tr><th style="background: #fafafa;">Sub-Category</th><td><?= $validationHelper->filterText($subgroup_name) ?></td></tr>
                                        <?php } ?>
                                        <tr><th style="background: #fafafa;">SKU / Code</th><td><?= $validationHelper->filterText(!empty($sku_no) ? $sku_no : $item_code) ?></td></tr>
                                        <?php if (!empty($mou_name)) { ?>
                                            <tr><th style="background: #fafafa;">Unit</th><td><?= $validationHelper->filterText($mou_name) ?></td></tr>
                                        <?php } ?>
                                        <?php if (!empty($size_name_list)) { ?>
                                            <tr><th style="background: #fafafa;">Available Sizes</th><td><?= implode(', ', $size_name_list) ?></td></tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="product-shipping-tab" role="tabpanel" aria-labelledby="product-shipping-link">
                        <div class="product-desc-content">
                            <h3>Delivery & Returns Policy</h3>
                            <p>We take pride in fast, reliable shipping across all states in India. Every order is verified, securely packed, and delivered directly to your doorstep.</p>
                            <ul>
                                <li><strong>Dispatch Window:</strong> All orders are dispatched within 24 to 48 working hours.</li>
                                <li><strong>Delivery Timeline:</strong> Typically arrives in 3 to 7 business days depending on location.</li>
                                <li><strong>Cash on Delivery:</strong> Available across supported pin codes.</li>
                                <li><strong>7-Day Easy Exchange & Return:</strong> If the size does not fit or if you encounter any defect, initiate a hassle-free exchange or return within 7 days of delivery.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="product-review-tab" role="tabpanel" aria-labelledby="product-review-link">
                        <div class="reviews">
                            <h3 class="mb-3">Customer Reviews (2)</h3>
                            <div class="review">
                                <div class="row no-gutters">
                                    <div class="col-auto">
                                        <h4><a href="#">Rahul Sharma</a></h4>
                                        <div class="ratings-container">
                                            <div class="ratings">
                                                <div class="ratings-val" style="width: 100%;"></div>
                                            </div>
                                        </div>
                                        <span class="review-date">3 days ago</span>
                                    </div>
                                    <div class="col">
                                        <h4>Superb comfort and look!</h4>
                                        <div class="review-content">
                                            <p>Really happy with this purchase. Fits true to size, cushioning is top-notch, and the color looks exactly as shown. Delivered in 3 days!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="review">
                                <div class="row no-gutters">
                                    <div class="col-auto">
                                        <h4><a href="#">Amit Verma</a></h4>
                                        <div class="ratings-container">
                                            <div class="ratings">
                                                <div class="ratings-val" style="width: 80%;"></div>
                                            </div>
                                        </div>
                                        <span class="review-date">1 week ago</span>
                                    </div>
                                    <div class="col">
                                        <h4>Great value for money</h4>
                                        <div class="review-content">
                                            <p>Solid build quality and very comfortable for all-day wear. Highly recommended for anyone looking for good quality at a reasonable price.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End .tab-content -->
            </div><!-- End .product-details-tab -->

        </div><!-- End .container -->
    </div><!-- End .page-content -->

    <!-- Related Products ("You May Also Like") -->
    <div class="container mb-4">
        <h2 class="title text-center mb-4 font-weight-bold" style="font-size: 22px; color: #222;">You May Also Like</h2>
        <div class="products mb-2">
            <div class="row justify-content-center">
                <?php echo getHTMLProductList(0, 8); ?>
            </div>
        </div>
    </div>

    <!-- Discount Banner -->
    <div class="container mb-4">
        <?php include('include/hp-discountbanner.php'); ?>
    </div>

    <style>
        /* ===================================================
           PRODUCT DETAILS PAGE LAYOUT & RESPONSIVE STYLES
           =================================================== */

        /* Breadcrumbs */
        .product-details-page .breadcrumb-nav {
            background-color: transparent;
            padding: 10px 0;
        }
        .product-details-page .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin: 0;
            font-size: 13px;
        }
        @media (max-width: 767px) {
            .product-details-page .breadcrumb {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
                scrollbar-width: none;
            }
            .product-details-page .breadcrumb::-webkit-scrollbar {
                display: none;
            }
        }

        /* ===================================================
           PRODUCT GALLERY & THUMBNAILS (Desktop & Mobile)
           =================================================== */

        /* Disable Molla's pseudo-elements that cause offset double-borders and white haze */
        .product-gallery-item:before,
        .product-gallery-item:after {
            display: none !important;
            content: none !important;
        }

        .product-main-image {
            position: relative;
            overflow: hidden;
            cursor: zoom-in;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #ebebeb;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-main-image img#product-zoom {
            width: 100%;
            height: auto;
            max-height: 520px;
            display: block;
            object-fit: contain;
            transition: transform 0.25s cubic-bezier(0.25, 1, 0.5, 1);
            transform-origin: center center;
        }

        /* Desktop Gallery (>= 992px) */
        @media screen and (min-width: 992px) {
            .product-gallery-vertical .row {
                margin-left: -6px;
                margin-right: -6px;
                display: flex;
                flex-direction: row-reverse;
            }
            .product-gallery-vertical .product-main-image {
                flex: 0 0 80% !important;
                max-width: 80% !important;
                padding-left: 6px;
                padding-right: 6px;
            }
            .product-gallery-vertical .product-image-gallery {
                display: block !important;
                flex: 0 0 20% !important;
                max-width: 20% !important;
                padding-left: 6px;
                padding-right: 6px;
                margin: 0 !important;
                width: auto !important;
            }
            .product-gallery-vertical .product-gallery-item {
                display: block !important;
                width: 100% !important;
                height: auto !important;
                flex: none !important;
                margin-bottom: 10px !important;
                border: 2px solid #e0e0e0 !important;
                border-radius: 6px !important;
                overflow: hidden !important;
                padding: 0 !important;
                background: #ffffff !important;
                cursor: pointer !important;
                transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
            }
            .product-gallery-vertical .product-gallery-item:last-child {
                margin-bottom: 0 !important;
            }
            .product-gallery-vertical .product-gallery-item img {
                display: block !important;
                width: 100% !important;
                height: auto !important;
                border-radius: 4px !important;
                object-fit: contain !important;
            }
            .product-gallery-vertical .product-gallery-item:hover {
                border-color: #37475a !important;
            }
            .product-gallery-vertical .product-gallery-item.active {
                border-color: #37475a !important;
                box-shadow: 0 2px 8px rgba(55, 71, 90, 0.3) !important;
            }
        }

        /* Mobile & Tablet Gallery (<= 991px) */
        @media screen and (max-width: 991px) {
            .product-gallery-vertical .row {
                margin: 0 !important;
                display: block !important;
            }
            .product-gallery-vertical .product-main-image {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                border-radius: 8px;
            }
            .product-gallery-vertical .product-main-image img#product-zoom {
                max-height: 380px;
                width: 100%;
                object-fit: contain;
            }
            .product-gallery-vertical .product-image-gallery {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                gap: 10px !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                padding: 10px 2px 6px !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                scrollbar-width: thin;
            }
            .product-gallery-vertical .product-image-gallery::-webkit-scrollbar {
                height: 4px;
            }
            .product-gallery-vertical .product-image-gallery::-webkit-scrollbar-thumb {
                background: #ccc;
                border-radius: 4px;
            }
            .product-gallery-vertical .product-gallery-item {
                display: block !important;
                flex: 0 0 68px !important;
                width: 68px !important;
                height: 68px !important;
                max-width: 68px !important;
                border: 2px solid #e0e0e0 !important;
                border-radius: 6px !important;
                overflow: hidden !important;
                padding: 0 !important;
                margin: 0 !important;
                background: #ffffff !important;
                cursor: pointer !important;
                flex-shrink: 0 !important;
            }
            .product-gallery-vertical .product-gallery-item img {
                display: block !important;
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                border-radius: 4px !important;
            }
            .product-gallery-vertical .product-gallery-item:hover {
                border-color: #37475a !important;
            }
            .product-gallery-vertical .product-gallery-item.active {
                border-color: #37475a !important;
                box-shadow: 0 2px 6px rgba(55, 71, 90, 0.3) !important;
            }
        }

        /* Fullscreen Lightbox Button */
        .btn-product-gallery {
            position: absolute;
            right: 14px;
            bottom: 14px;
            z-index: 10 !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            color: #444;
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.92);
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.25s ease;
        }
        .btn-product-gallery:hover {
            color: #fff;
            background-color: #37475a;
            transform: scale(1.08);
        }

        /* Product Details Info Section */
        .product-meta-header {
            font-size: 13px;
            color: #777;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }
        .product-meta-brand {
            font-weight: 600;
            color: #333;
        }
        .product-meta-cat {
            color: #666;
            text-decoration: none;
        }
        .product-meta-cat:hover {
            color: #37475a;
            text-decoration: underline;
        }
        .meta-sep {
            color: #bbb;
        }

        /* Main Product Title - Scoped to .product-details */
        .product-details .product-title {
            font-size: 24px !important;
            font-weight: 700 !important;
            line-height: 1.35 !important;
            color: #1a1a1a !important;
            margin: 6px 0 10px 0 !important;
            letter-spacing: -0.2px;
        }
        @media (max-width: 767px) {
            .product-details .product-title {
                font-size: 18px !important;
                margin: 4px 0 8px 0 !important;
            }
        }

        /* Stock Status Badge */
        .stock-badge {
            font-size: 12px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
        }
        .stock-badge.in-stock {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .stock-badge.out-of-stock {
            background-color: #ffebee;
            color: #c62828;
        }

        /* Main Product Price - Scoped to .product-details */
        .product-details .product-price {
            font-size: 26px !important;
            font-weight: 800 !important;
            color: #111111 !important;
            margin: 10px 0 14px 0 !important;
            display: flex !important;
            align-items: baseline !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
            line-height: 1.2 !important;
        }
        .product-details .product-price .original-price {
            font-size: 17px !important;
            font-weight: 500 !important;
            color: #888888 !important;
            text-decoration: line-through !important;
        }
        .product-details .product-price .discount-badge {
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #e53935 !important;
            background: #ffebee !important;
            padding: 3px 8px !important;
            border-radius: 4px !important;
            letter-spacing: 0.3px;
        }
        @media (max-width: 767px) {
            .product-details .product-price {
                font-size: 22px !important;
            }
            .product-details .product-price .original-price {
                font-size: 15px !important;
            }
        }

        /* Size Selection - Scoped */
        .detail-size-select {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }
        .detail-size-select .size-option {
            min-width: 40px !important;
            height: 40px !important;
            line-height: 38px !important;
            padding: 0 8px !important;
            border-radius: 6px !important;
            border: 1.5px solid #d5d5d5 !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #222 !important;
            text-align: center !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            background: #ffffff !important;
            display: inline-block !important;
        }
        .detail-size-select .size-option:hover {
            border-color: #37475a !important;
            color: #37475a !important;
        }
        .detail-size-select .size-option.active {
            background-color: #37475a !important;
            color: #ffffff !important;
            border-color: #37475a !important;
            box-shadow: 0 2px 6px rgba(55, 71, 90, 0.3) !important;
        }
        .detail-size-select .size-option.disabled {
            background-color: #f5f5f5 !important;
            color: #b5b5b5 !important;
            border-color: #e5e5e5 !important;
            cursor: not-allowed !important;
            text-decoration: line-through !important;
            opacity: 0.6 !important;
        }
        @media (max-width: 576px) {
            .detail-size-select .size-option {
                min-width: 36px !important;
                height: 36px !important;
                line-height: 34px !important;
                font-size: 12.5px !important;
            }
        }

        /* Quantity Spinner */
        .product-details-quantity {
            max-width: 120px;
        }
        .product-details-quantity .form-control {
            height: 42px;
            font-weight: 600;
            text-align: center;
        }

        /* Action Buttons (Add to Cart / Order Now) */
        .product-details-action-split {
            display: flex !important;
            gap: 10px !important;
            width: 100% !important;
        }
        .product-details-action-split .btn-product {
            flex: 1 1 50% !important;
            height: 48px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 6px !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.4px !important;
            transition: all 0.25s ease !important;
            cursor: pointer !important;
            text-decoration: none !important;
            padding: 0 12px !important;
            gap: 6px !important;
        }
        .product-details-action-split .btn-cart {
            background-color: #ffffff !important;
            color: #222222 !important;
            border: 1.5px solid #222222 !important;
        }
        .product-details-action-split .btn-cart:hover {
            background-color: #222222 !important;
            color: #ffffff !important;
        }
        .product-details-action-split .btn-order-now {
            background-color: #37475a !important;
            color: #ffffff !important;
            border: 1.5px solid #37475a !important;
        }
        .product-details-action-split .btn-order-now:hover {
            background-color: #232f3e !important;
            border-color: #232f3e !important;
            color: #ffffff !important;
        }
        @media (max-width: 576px) {
            .product-details-action-split .btn-product {
                height: 46px !important;
                font-size: 12.5px !important;
                padding: 0 6px !important;
            }
        }

        /* Trust Badges Features Row */
        .product-trust-features {
            background: #fbfbfb;
            border: 1px solid #ededed;
            border-radius: 8px;
            padding: 14px 12px;
        }
        .trust-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .trust-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .trust-icon {
            font-size: 22px;
            color: #37475a;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .trust-info strong {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #222;
            line-height: 1.2;
        }
        .trust-info span {
            display: block;
            font-size: 10.5px;
            color: #777;
            line-height: 1.2;
        }
        @media (max-width: 767px) {
            .trust-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px 10px;
            }
            .trust-icon {
                font-size: 20px;
            }
        }

        /* Social Icons in Footer */
        .product-details-footer {
            border-top: 1px solid #eeeeee;
            padding-top: 14px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        .product-details-footer .social-icons {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .product-details-footer .social-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f0f0f0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #555;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .product-details-footer .social-icon:hover {
            background: #37475a;
            color: #ffffff;
            transform: translateY(-2px);
        }

        /* Product Details Tabs */
        .product-details-tab .nav-pills {
            border-bottom: 2px solid #eaeaea;
            gap: 10px;
            margin-bottom: 20px;
        }
        .product-details-tab .nav-pills .nav-link {
            font-size: 15px;
            font-weight: 600;
            color: #666;
            padding: 10px 18px;
            border-radius: 0;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            background: transparent !important;
            transition: all 0.2s ease;
        }
        .product-details-tab .nav-pills .nav-link:hover {
            color: #37475a;
        }
        .product-details-tab .nav-pills .nav-link.active {
            color: #37475a !important;
            border-bottom-color: #37475a !important;
            font-weight: 700;
        }
        .product-details-tab .tab-content {
            background: #ffffff;
            border: 1px solid #ebebeb;
            border-radius: 8px;
            padding: 24px;
        }
        .table-spec th,
        .table-spec td {
            padding: 10px 14px;
            font-size: 13.5px;
        }
        @media (max-width: 767px) {
            .product-details-tab .nav-pills {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                justify-content: flex-start !important;
                scrollbar-width: none;
            }
            .product-details-tab .nav-pills::-webkit-scrollbar {
                display: none;
            }
            .product-details-tab .nav-pills .nav-link {
                white-space: nowrap;
                padding: 8px 12px;
                font-size: 13.5px;
            }
            .product-details-tab .tab-content {
                padding: 16px;
            }
        }

        /* ===================================================
           RELATED PRODUCTS CARDS STYLING (Strictly Scoped)
           =================================================== */
        .products .product {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
            height: 100%;
            border: 1px solid #ebebeb;
            display: flex;
            flex-direction: column;
        }
        .products .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        }
        .products .product .product-media {
            position: relative;
            overflow: hidden;
            background: #fbfbfb;
            margin-bottom: 0;
        }
        .products .product .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .products .product:hover .product-image {
            transform: scale(1.06);
        }
        .products .product .product-body {
            padding: 14px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .products .product .product-cat a {
            color: #888;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .products .product .product-title {
            font-size: 14.5px !important;
            font-weight: 600 !important;
            margin: 6px 0 !important;
            line-height: 1.35 !important;
            color: #222 !important;
        }
        .products .product .product-price {
            color: #111 !important;
            font-size: 16.5px !important;
            font-weight: 700 !important;
            margin-bottom: 8px !important;
        }
        .products .product .product-label {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 2;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }
        .products .product .label-new {
            background: #37475a;
        }
        .products .product .product-action-vertical {
            position: absolute;
            top: 12px;
            right: -45px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            transition: 0.25s;
        }
        .products .product:hover .product-action-vertical {
            right: 12px;
        }
        .products .product .btn-product-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
            color: #444;
        }
        @media (max-width: 768px) {
            .products .product .product-image {
                height: 175px;
            }
            .products .product .product-body {
                padding: 10px 8px;
            }
            .products .product .product-title {
                font-size: 13.5px !important;
            }
            .products .product .product-price {
                font-size: 15px !important;
            }
            .products .product .product-action-vertical {
                right: 8px;
                opacity: 1;
            }
        }
    </style>
</main><!-- End .main -->

<script src="<?= _BASEURL ?>assets/js/page/product-details.js?v=3.0"></script>
<?php include('include/backto-top.php'); ?>
<?php include('include/bottom.php'); ?>