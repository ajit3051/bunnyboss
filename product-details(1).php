<?php include_once("include/config.php"); ?>
<?php include('include/top.php');?> 
<?php include('include/hp-banner.php');?> 
<?php
$validationHelper = new validation();

$db = connect();

$product_id = $_GET['product-id'] ?? '';
if ($product_id) {

   $pur_stmt = $db->select("SELECT * FROM tbl_item_master WHERE id=?", 's', $product_id);

   $res = $pur_stmt->fetch_assoc();

   foreach ($res as $key => $value) {
      $$key = $validationHelper->filterText($value);
   }
   $pur_stmt->close();
}

$size_name_list = explode(',', $validationHelper->filterText($size_name));


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
                                            <a class="product-gallery-item active" href="#" data-image="<?= $imageUrl ?>item-master/<?= $picture ?>" data-zoom-image="<?= $imageUrl ?>item-master/<?= $picture ?>">
                                                <img src="<?= $imageUrl ?>item-master/<?= $picture ?>" alt="product side">
                                            </a>

                                            <a class="product-gallery-item" href="#" data-image="<?= $imageUrl ?>item-master/<?= $picture ?>" data-zoom-image="<?= $imageUrl ?>item-master/<?= $picture ?>">
                                                <img src="<?= $imageUrl ?>item-master/<?= $picture ?>" alt="product cross">
                                            </a>

                                            <a class="product-gallery-item" href="#" data-image="<?= $imageUrl ?>item-master/<?= $picture ?>" data-zoom-image="<?= $imageUrl ?>item-master/<?= $picture ?>">
                                                <img src="<?= $imageUrl ?>item-master/<?= $picture ?>" alt="product with model">
                                            </a>

                                            <a class="product-gallery-item" href="#" data-image="<?= $imageUrl ?>item-master/<?= $picture ?>" data-zoom-image="<?= $imageUrl ?>item-master/<?= $picture ?>">
                                                <img src="<?= $imageUrl ?>item-master/<?= $picture ?>" alt="product back">
                                            </a>
                                        </div><!-- End .product-image-gallery -->
                                    </div><!-- End .row -->
                                </div><!-- End .product-gallery -->
                            </div><!-- End .col-md-6 -->

                            <div class="col-md-6">
                                <div class="product-details">
                                    <h1 class="product-title"><?= $validationHelper->filterText($item_name) ?></h1><!-- End .product-title -->

                                    <div class="ratings-container">
                                        <div class="ratings">
                                            <div class="ratings-val" style="width: 80%;"></div><!-- End .ratings-val -->
                                        </div><!-- End .ratings -->
                                        <a class="ratings-text" href="#product-review-link" id="review-link">( 2 Reviews )</a>
                                    </div><!-- End .rating-container -->

                                    <div class="product-price">
                                       ₹<?= $validationHelper->filterText($sp) ?>
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
                    $is_first = true;
                    foreach ($size_name_list as $size_name) {
                        $active_class = $is_first ? ' active' : '';
                        ?>
                        <span class="size-option<?=  $active_class ?>" data-size="<?=  $size_name ?>"><?= $size_name ?></span>

                        <?php
                        $is_first = false;
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

                                    <div class="product-details-action product-detail-btn-cart" data-id="<?= $id ?>">
                                        <a href="JavaScript:void(0);" class="btn-product btn-cart"><span>add to cart</span></a>

                                       <!-- End .details-action-wrapper -->
                                    </div><!-- End .product-details-action -->

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
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
            height: 100%;
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.3);
            }
            .product:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,.12);
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
            box-shadow: 0 4px 10px rgba(0,0,0,.1);
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
            @media (max-width:768px){
            .products-grid{
            grid-template-columns: repeat(2,1fr);
            gap:15px;
            }
            .product-image{
            height:180px;
            }
            .product-title{
            font-size:14px;
            }
            .product-price{
            font-size:16px;
            }
            .btn-cart{
            padding:10px;
            font-size:13px;
            }
            .product-action-vertical{
            right:10px;
            opacity:1;
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
            <?php include('include/hp-discountbanner.php');?>
         </div>
         <style>
             /* Mobile View */
@media (max-width: 767px) {
    .product.product-7 .btn-product {
        padding-top: 2.1rem;
        padding-bottom: 2.1rem;
    }
}

.product-label.label-new {
    color: #fff;
    background-color: #37475a; }
         </style>
        </main><!-- End .main -->
<!--<script src="<?= _BASEURL ?>assets/js/page/product-details.js"></script>-->
<script src="<?= _BASEURL ?>assets/js/page/product-details.js?v=2.1"></script>
<?php include('include/backto-top.php');?> 
<?php include('include/bottom.php');?> 