<?php include_once("include/config.php"); ?>
<?php include('include/top.php');?>  

<?php
$validationHelper = new validation();
$category = $_GET['category'] ?? '';

?>
      <main class="main">
         <div class="" style="margin-top:-50px; margin-bottom:50px;">
             <?php include('include/category-name.php');?> 
            <!-- End .col-lg-6 -->
         </div>
         <!-- End .row -->
         
         <div class="row">
            <div class="col-lg-4 col-sm-3">
               <div class="icon-box text-center">
                  <span class="icon-box-icon text-dark">
                  <i class="icon-truck"></i>
                  </span>
                  <div class="icon-box-content">
                     <h3 class="icon-box-title">Payment & Delivery</h3>
                     <!-- End .icon-box-title -->
                     <p>shipping for orders 150</p>
                  </div>
                  <!-- End .icon-box-content -->
               </div>
               <!-- End .icon-box -->
            </div>
            <!-- End .col-lg-3 col-sm-6 -->
            <!--<div class="col-lg-3 col-sm-6">-->
            <!--   <div class="icon-box text-center">-->
            <!--      <span class="icon-box-icon text-dark">-->
            <!--      <i class="icon-rotate-left"></i>-->
            <!--      </span>-->
            <!--      <div class="icon-box-content">-->
            <!--         <h3 class="icon-box-title">Return & Refund</h3>-->
                     <!-- End .icon-box-title -->
            <!--         <p>Free 100% money back guarantee</p>-->
            <!--      </div>-->
                  <!-- End .icon-box-content -->
            <!--   </div>-->
               <!-- End .icon-box -->
            <!--</div>-->
            <!-- End .col-lg-3 col-sm-6 -->
            <div class="col-lg-4 col-sm-3">
               <div class="icon-box text-center">
                  <span class="icon-box-icon text-dark">
                  <i class="icon-rotate-left"></i>
                  </span>
                  <div class="icon-box-content">
                     <h3 class="icon-box-title">Secure Payment</h3>
                     <!-- End .icon-box-title -->
                     <p>100% secure payment</p>
                  </div>
                  <!-- End .icon-box-content -->
               </div>
               <!-- End .icon-box -->
            </div>
            <!-- End .col-lg-3 col-sm-6 -->
            <div class="col-lg-4 col-sm-3">
               <div class="icon-box text-center">
                  <span class="icon-box-icon text-dark">
                  <i class="icon-headphones"></i>
                  </span>
                  <div class="icon-box-content">
                     <h3 class="icon-box-title">Quality Support</h3>
                     <!-- End .icon-box-title -->
                     <p>Alway online feedback 24/7</p>
                  </div>
                  <!-- End .icon-box-content -->
               </div>
               <!-- End .icon-box -->
            </div>
            <!-- End .col-lg-3 col-sm-6 -->
         </div>
         <!-- End .row -->
         
         <div class="page-content">
            <div class="container">
               <div class="row">
                  <div class="col-lg-10">
                     <div class="products mb-1">
                        <div class="row justify-content-center" id="product-list">
                           
                        
                        </div>
                        <!-- End .row -->
                     </div>
                     <!-- End .products -->

                     <nav aria-label="Page navigation" id="pagination-container" class="d-flex justify-content-center mt-4 mb-5">
                     </nav>
                  </div>
                  <!-- End .col-lg-9 -->
                  <?php $filtered_counts = getFilterCounts(); ?>
                  <aside class="col-lg-2 order-lg-first">
                     <div class="sidebar sidebar-shop">
                        
                        <div class="widget widget-collapsible">
                           <h3 class="widget-title">
                              <a data-toggle="collapse" href="#widget-1" role="button" aria-expanded="true" aria-controls="widget-1">
                              Category
                              </a>
                           </h3>
                           <div class="collapse show" id="widget-1">
                              <div class="widget-body">
                                 <div class="filter-items filter-items-count">
                                    <?php
                                    $i = 1;
                                    foreach ($filtered_counts['categories'] as $filter => $count) {
                                       $unique_id = 'cat-' . $i++;
                                    ?>
                                       <div class="filter-item">
                                          <div class="custom-control custom-checkbox">
                                             <input type="checkbox" class="custom-control-input filter-checkbox" data-type="category" value="<?= htmlspecialchars($filter) ?>" id="<?= $unique_id ?>" <?= ($category === $filter) ? 'checked' : '' ?>>
                                             <label class="custom-control-label" for="<?= $unique_id ?>"><?= htmlspecialchars($filter) ?></label>
                                          </div>
                                          <span class="item-count"><?= $count ?></span>
                                       </div>
                                    <?php } ?>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="widget widget-collapsible">
                           <h3 class="widget-title">
                              <a data-toggle="collapse" href="#widget-2" role="button" aria-expanded="true" aria-controls="widget-2">
                              Size
                              </a>
                           </h3>
                           <div class="collapse show" id="widget-2">
                              <div class="widget-body">
                                 <div class="filter-items">
                                    <?php
                                    $j = 1;
                                    foreach ($filtered_counts['sizes'] as $filter => $count) {
                                       $unique_id = 'size-' . $j++;
                                    ?>
                                    <div class="filter-item">
                                       <div class="custom-control custom-checkbox">
                                          <input type="checkbox" class="custom-control-input filter-checkbox" data-type="size" value="<?= htmlspecialchars($filter) ?>" id="<?= $unique_id ?>">
                                          <label class="custom-control-label" for="<?= $unique_id ?>"><?= htmlspecialchars($filter) ?></label>
                                       </div>
                                    </div>
                                    <?php } ?>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="widget widget-collapsible">
                           <h3 class="widget-title">
                              <a data-toggle="collapse" href="#widget-3" role="button" aria-expanded="true" aria-controls="widget-3">
                              Colour
                              </a>
                           </h3>
                           <div class="collapse show" id="widget-3">
                              <div class="widget-body">
                                 <div class="filter-colors">
                                    <?php
                                    $k = 1;
                                    foreach ($filtered_counts['colors'] as $filter => $count) {
                                       $unique_id = 'color-' . $k++;
                                    ?>
                                    <div class="filter-item">
                                       <div class="custom-control custom-checkbox">
                                          <input type="checkbox" class="custom-control-input filter-checkbox" data-type="color" value="<?= htmlspecialchars($filter) ?>" id="<?= $unique_id ?>">
                                          <label class="custom-control-label" for="<?= $unique_id ?>"><?= htmlspecialchars($filter) ?></label>
                                       </div>
                                    </div>
                                    <?php } ?>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="widget widget-collapsible">
                           <h3 class="widget-title">
                              <a data-toggle="collapse" href="#widget-4" role="button" aria-expanded="true" aria-controls="widget-4">
                              Brand
                              </a>
                           </h3>
                           <div class="collapse show" id="widget-4">
                              <div class="widget-body">
                                 <div class="filter-items">
                                    <?php
                                    $l = 1;
                                    foreach ($filtered_counts['brands'] as $filter => $count) {
                                       $unique_id = 'brand-' . $l++;
                                    ?>
                                    <div class="filter-item">
                                       <div class="custom-control custom-checkbox">
                                          <input type="checkbox" class="custom-control-input filter-checkbox" data-type="brand" value="<?= htmlspecialchars($filter) ?>" id="<?= $unique_id ?>">
                                          <label class="custom-control-label" for="<?= $unique_id ?>"><?= htmlspecialchars($filter) ?></label>
                                       </div>
                                    </div>
                                    <?php } ?>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="widget widget-collapsible">
                           <h3 class="widget-title">
                              <a data-toggle="collapse" href="#widget-5" role="button" aria-expanded="true" aria-controls="widget-5">
                              Price
                              </a>
                           </h3>
                           <div class="collapse show" id="widget-5">
                              <div class="widget-body">
                                 <div class="filter-price">
                                    <div class="filter-price-text">
                                       Price Range:
                                       <span id="filter-price-range"></span>
                                    </div>
                                    <div id="price-slider" data-min="<?= $filtered_counts['min_price'] ?>" data-max="<?= $filtered_counts['max_price'] ?>"></div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        
                     </div>
                  </aside>
                  <!-- End .col-lg-3 -->
               </div>
               <!-- End .row -->
            </div>
            <!-- End .container -->
         </div>
         <!-- End .page-content -->
          <style>
.product-label.label-new {
    color: #fff;
    background-color: #37475a; 
}
/* Uniform 4-Sided Gap for Product Grid (Left, Right, Top, Bottom equal) */
.products .row,
#product-list {
    margin-left: -10px !important;
    margin-right: -10px !important;
    margin-top: -10px !important;
    margin-bottom: -10px !important;
}

.products .row > [class*="col-"],
#product-list > [class*="col-"] {
    padding: 10px !important;
    margin-bottom: 0 !important;
    display: flex !important;
    align-items: stretch !important;
    align-content: stretch !important;
}

.products .product,
#product-list .product {
    width: 100% !important;
    height: 100% !important;
    margin: 0 !important;
    box-sizing: border-box !important;
}

@media screen and (min-width: 992px) {
    .products .row,
    #product-list {
        margin-left: -12px !important;
        margin-right: -12px !important;
        margin-top: -12px !important;
        margin-bottom: -12px !important;
    }

    .products .row > [class*="col-"],
    #product-list > [class*="col-"] {
        padding: 12px !important;
    }
}

/* Mobile adjustments for product cards to prevent horizontal overflow */
@media screen and (max-width: 576px) {
    .product .product-body {
        padding: 10px 6px !important;
    }
    .product-size-select {
        gap: 4px !important;
        padding: 4px 0 !important;
        flex-wrap: wrap !important;
    }
    .product-size-select .size-option {
        width: 28px !important;
        height: 28px !important;
        line-height: 28px !important;
        font-size: 11px !important;
    }
    .product-action-split .btn-product {
        padding: 10px 4px !important;
        font-size: 10px !important;
    }
}
          </style>
      </main>
      <!-- End .main -->

      <script src="<?= _BASEURL ?>assets/js/page/product-filter.js?v=5.2"></script>
      <?php include('include/backto-top.php');?> 
      <?php include('include/bottom.php');?>