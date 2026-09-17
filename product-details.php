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

    $res = $pur_stmt->fetch_assoc();

    foreach ($res as $key => $value) {
        $$key = $validationHelper->filterText($value);
    }
    $pur_stmt->close();

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
            $product_gallery_images[] = $g_row['image_path'];
        }
    }
    if (empty($product_gallery_images) && !empty($picture)) {
        $product_gallery_images[] = $picture;
    }
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


$imageUrl     = _IMAGE_PATH;
?>
<main class="main">
    <nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-0">
        <div class="container d-flex align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>product-list.php">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $validationHelper->filterText($item_name) ?></li>
            </ol>

            <nav class="product-pager ml-auto" aria-label="Product">
                <a class="product-pager-link product-pager-prev" href="#" aria-label="Previous" tabindex="-1">
                    <i class="icon-angle-left"></i>
                    <span>Prev</span>
                </a>

                <a class="product-pager-link product-pager-next" href="#" aria-label="Next" tabindex="-1">
                    <span>Next</span>
                    <i class="icon-angle-right"></i>
                </a>
            </nav><!-- End .pager-nav -->
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="product-details-top">
                <div class="row">
                    <div class="col-md-6">
                        <div class="product-gallery product-gallery-vertical">
                            <div class="row">
                                <figure class="product-main-image">
                                    <img id="product-zoom" src="<?= $imageUrl ?>item-master/<?= $picture ?>" data-zoom-image="<?= $imageUrl ?>item-master/<?= $picture ?>" alt="product image">

                                    <a href="#" id="btn-product-gallery" class="btn-product-gallery">
                                        <i class="icon-arrows"></i>
                                    </a>
                                </figure><!-- End .product-main-image -->

                                <div id="product-zoom-gallery" class="product-image-gallery">
                                    <?php foreach ($product_gallery_images as $g_idx => $g_img) { ?>
                                        <a class="product-gallery-item<?= $g_idx === 0 ? ' active' : '' ?>" href="#" data-image="<?= $imageUrl ?>item-master/<?= htmlspecialchars($g_img) ?>" data-zoom-image="<?= $imageUrl ?>item-master/<?= htmlspecialchars($g_img) ?>">
                                            <img src="<?= $imageUrl ?>item-master/<?= htmlspecialchars($g_img) ?>" alt="product image">
                                        </a>
                                    <?php } ?>
                                </div><!-- End .product-image-gallery -->
                            </div><!-- End .row -->
                        </div><!-- End .product-gallery -->
                    </div><!-- End .col-md-6 -->

                    <div class="col-md-6">
                        <div class="product-details">
                            <div class="product-cat">
                                <span>SKU: <?= $validationHelper->filterText(!empty($sku_no) ? $sku_no : $item_code) ?></span>
                            </div>
                            <h1 class="product-title"><?= $validationHelper->filterText($item_name) ?></h1><!-- End .product-title -->

                            <div class="ratings-container">
                                <div class="ratings">
                                    <div class="ratings-val" style="width: 80%;"></div><!-- End .ratings-val -->
                                </div><!-- End .ratings -->
                                <a class="ratings-text" href="#product-review-link" id="review-link">( 2 Reviews )</a>
                            </div><!-- End .rating-container -->

                            <div class="product-price">
                                ₹<?= number_format((float)$sp, 2, '.', '') ?>
                            </div><!-- End .product-price -->

                            <div class="product-content">
                                <p><?= $validationHelper->filterText($description) ?></p>
                            </div><!-- End .product-content -->

                            <!--<div class="details-filter-row details-row-size">-->
                            <!--    <label>Color:</label>-->

                            <!--    <div class="product-nav product-nav-thumbs">-->
                            <!--        <a href="#" class="active">-->
                            <!--            <img src="<?= _BASEURL ?>assets/images/products/single/1-thumb.jpg" alt="product desc">-->
                            <!--        </a>-->
                            <!--        <a href="#">-->
                            <!--            <img src="<?= _BASEURL ?>assets/images/products/single/2-thumb.jpg" alt="product desc">-->
                            <!--        </a>-->
                            <!--    </div>-->

                            <!--</div>-->
                            <!-- End .product-nav -->
                            <!-- End .details-filter-row -->

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

                                     <?php
                                     }
                                     ?>
                                </div>




                            </div><!-- End .details-filter-row -->

                            <div class="details-filter-row details-row-size">
                                <label for="qty">Qty:</label>
                                <div class="product-details-quantity">
                                    <input type="number" id="qty" class="form-control" value="1" min="1" max="10" step="1" data-decimals="0" required>
                                </div><!-- End .product-details-quantity -->
                            </div><!-- End .details-filter-row -->

                            <div id="product-action-wrapper" data-id="<?= $id ?>">
                            <?php
                            $product_min_qty = isset($min_qty) ? (int)$min_qty : 0;
                            if ($product_min_qty <= 0) {
                            ?>
                                <div class="product-details-action-split d-flex mb-3">
                                    <a href="JavaScript:void(0);" class="btn-product btn-out-of-stock disabled flex-fill" style="background-color: #e5e5e5; color: #777; border-color: #ddd; cursor: not-allowed; pointer-events: none; padding: 1.2rem; text-align: center; font-weight: 600;"><i class="icon-ban"></i><span>Out of Stock</span></a>
                                </div>
                            <?php } else { ?>
                                <div class="product-details-action-split d-flex mb-3">
                                    <a href="JavaScript:void(0);" class="btn-product btn-cart product-detail-btn-cart flex-fill mr-2" data-id="<?= $id ?>"><i class="icon-shopping-cart"></i><span>add to cart</span></a>
                                    <a href="JavaScript:void(0);" class="btn-product btn-order-now product-detail-btn-order-now flex-fill ml-2" data-id="<?= $id ?>"><i class="icon-rocket"></i><span>order now</span></a>
                                </div><!-- End .product-details-action-split -->
                            <?php } ?>
                            </div>

                            <div class="product-details-footer">


                                <div class="social-icons social-icons-sm">
                                    <span class="social-label">Connect With Us :</span>
                                    <a href="#" class="social-icon" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>

                                    <a href="#" class="social-icon" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                                    <a href="#" class="social-icon" title="Youtube" target="_blank"><i class="icon-youtube"></i></a>
                                </div>
                            </div><!-- End .product-details-footer -->
                        </div><!-- End .product-details -->
                    </div><!-- End .col-md-6 -->
                </div><!-- End .row -->
            </div><!-- End .product-details-top -->






        </div><!-- End .container -->
    </div><!-- End .page-content -->





    <!--<link rel="stylesheet" href="assets/style.css?v=2">-->

    <style>
        .product {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.35s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
            height: 100%;
            background: rgba(255, 255, 255, .85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, .3);
        }

        .product:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, .12);
        }

        .product-media {
            position: relative;
            overflow: hidden;
            margin-bottom: 10px;
        }

        /*.product-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            transition: transform .5s ease;
            }*/
        .product:hover .product-image {
            transform: scale(1.08);
        }

        .product-action-vertical {
            position: absolute;
            top: 15px;
            right: -50px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: .3s;
        }

        .product:hover .product-action-vertical {
            right: 15px;
        }

        .btn-product-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .1);
        }

        .product-body {
            padding: 18px;
        }

        .product-cat a {
            color: #8b8b8b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .product-title {
            font-size: 16px;
            font-weight: 600;
            margin: 10px 0;
            line-height: 1.4;
        }

        .product-price {
            color: #111;
            font-size: 20px;
            font-weight: 700;
        }

        .product-label {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 2;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
        }

        .label-new {
            background: #10b981;
        }

        .label-out {
            background: #ef4444;
        }

        .label-top {
            background: #f59e0b;
        }

        @media (max-width:768px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .product-image {
                height: 180px;
            }

            .product-title {
                font-size: 14px;
            }

            .product-price {
                font-size: 16px;
            }

            .btn-cart {
                padding: 10px;
                font-size: 13px;
            }

            .product-action-vertical {
                right: 10px;
                opacity: 1;
            }
        }

        /* Tablet */
        @media (max-width: 768px) {
            .product-image {
                width: 100%;
                height: 200px;
                object-fit: cover;
                transition: transform .5s ease;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            .product-image {

                width: 100%;
                height: 200px;
                object-fit: cover;
                transition: transform .5s ease;

            }
        }
    </style>
    <h2 class="title text-center mb-4">You May Also Like</h2><!-- End .title text-center -->
    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="products mb-1">
                        <div class="row justify-content-center">
                            <?php
                            echo getHTMLProductList(0, 8);
                            ?>

                        </div>
                        <!-- End .row -->
                    </div>
                    <!-- End .products -->
                </div>
                <!-- End .col-lg-9 -->
                <!-- End .col-lg-3 -->
            </div>
            <!-- End .row -->
        </div>
        <!-- End .container -->
    </div>
    <div class="row" style="margin-left:1px; margin-right: 1px; margin-top: -30px;">
        <?php include('include/hp-discountbanner.php'); ?>
    </div>
    <style>
        .product-label.label-new {
            color: #fff;
            background-color: #37475a;
        }

        /* Product Gallery Main Image Zoom Box */
        .product-main-image {
            position: relative;
            overflow: hidden;
            cursor: zoom-in;
            border-radius: 4px;
        }
        .product-main-image img#product-zoom {
            width: 100%;
            height: auto;
            display: block;
            object-fit: contain;
            transition: transform 0.25s cubic-bezier(0.25, 1, 0.5, 1);
            transform-origin: center center;
        }

        /* Prevent flex container height stretching on thumbnails */
        .product-gallery-vertical .product-image-gallery {
            display: block !important;
        }

        /* Thumbnail tight active border styling - zero empty space inside box */
        .product-gallery-item:before,
        .product-gallery-item:after {
            display: none !important;
        }
        .product-gallery-item {
            display: block !important;
            height: auto !important;
            flex: none !important;
            align-self: flex-start !important;
            border: 2px solid transparent !important;
            border-radius: 6px !important;
            overflow: hidden !important;
            padding: 0 !important;
            margin-bottom: 10px !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .product-gallery-item:last-child {
            margin-bottom: 0 !important;
        }
        .product-gallery-item img {
            display: block !important;
            width: 100% !important;
            height: auto !important;
            border-radius: 4px !important;
        }
        .product-gallery-item:hover {
            border-color: #c96 !important;
        }
        .product-gallery-item.active {
            border-color: #c96 !important;
            box-shadow: 0 2px 8px rgba(204, 153, 102, 0.25) !important;
        }

        /* Fullscreen Popup Gallery Icon Button on Main Image */
        .btn-product-gallery {
            position: absolute;
            right: 1.5rem;
            bottom: 1.5rem;
            z-index: 100 !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 3.6rem;
            height: 3.6rem;
            color: #777;
            font-size: 1.8rem;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }
        .btn-product-gallery:hover,
        .btn-product-gallery:focus {
            color: #fff;
            background-color: #c96;
            transform: scale(1.1);
        }
    </style>
</main><!-- End .main -->
<!--<script src="<?= _BASEURL ?>assets/js/page/product-details.js"></script>-->
<script src="<?= _BASEURL ?>assets/js/page/product-details.js?v=2.8"></script>
<?php include('include/backto-top.php'); ?>
<?php include('include/bottom.php'); ?>