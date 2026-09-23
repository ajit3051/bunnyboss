<?php include_once("include/config.php"); ?>
<?php include('include/top.php');?>  

<?php
$validationHelper = new validation();
$category = $_GET['category'] ?? '';

?>
      <main class="main">
         <div class="category-header-wrap">
             <?php include('include/category-name.php');?> 
         </div>
         
         <div class="container trust-badges-container">
             <div class="row trust-badges-row justify-content-center">
                <div class="col-4 col-md-4">
                   <div class="icon-box text-center">
                      <span class="icon-box-icon text-dark">
                      <i class="icon-truck"></i>
                      </span>
                      <div class="icon-box-content">
                         <h3 class="icon-box-title">Payment & Delivery</h3>
                         <p>Free shipping on orders</p>
                      </div>
                   </div>
                </div>

                <div class="col-4 col-md-4">
                   <div class="icon-box text-center">
                      <span class="icon-box-icon text-dark">
                      <i class="icon-rotate-left"></i>
                      </span>
                      <div class="icon-box-content">
                         <h3 class="icon-box-title">Secure Payment</h3>
                         <p>100% secure checkout</p>
                      </div>
                   </div>
                </div>

                <div class="col-4 col-md-4">
                   <div class="icon-box text-center">
                      <span class="icon-box-icon text-dark">
                      <i class="icon-headphones"></i>
                      </span>
                      <div class="icon-box-content">
                         <h3 class="icon-box-title">Quality Support</h3>
                         <p>Online assistance 24/7</p>
                      </div>
                   </div>
                </div>
             </div>
         </div>
         <!-- End .row -->
         
         <div class="page-content">
            <div class="container">
               <div class="row">
                  <div class="col-lg-10">
                     <!-- Mobile Filter Bar -->
                     <div class="mobile-filter-bar d-flex align-items-center justify-content-between d-lg-none mb-3">
                        <div class="mobile-filter-info d-flex align-items-center">
                           <i class="icon-sliders mr-2 text-dark" style="font-size: 15px;"></i>
                           <span class="font-weight-bold text-dark" style="font-size: 14px;">Filter & Refine</span>
                        </div>
                        <button type="button" class="btn btn-outline-dark mobile-filter-btn" id="open-mobile-filter">
                           <i class="icon-filter mr-1"></i>
                           <span>Filters</span>
                           <span class="active-filter-badge" id="active-filter-count" style="display: none;">0</span>
                        </button>
                     </div>

                     <div class="products mb-1">
                        <div class="row justify-content-center" id="product-list">
                           
                        
                        </div>
                        <!-- End .row -->
                     </div>
                     <!-- End .products -->

                     <nav aria-label="Page navigation" id="pagination-container" class="d-flex justify-content-center mt-4 mb-5">
                     </nav>
                  </div>
                  <!-- End .col-lg-10 -->
                  <?php $filtered_counts = getFilterCounts(); ?>
                  <aside class="col-lg-2 order-lg-first" id="shop-sidebar-aside">
                     <div class="sidebar-filter-overlay d-lg-none <?= isset($_GET['open_filter']) ? 'open' : '' ?>" id="mobile-filter-backdrop"></div>
                     <div class="sidebar sidebar-shop <?= isset($_GET['open_filter']) ? 'open' : '' ?>" id="shop-sidebar">
                        <div class="mobile-filter-header d-flex align-items-center justify-content-between d-lg-none">
                           <span class="mobile-filter-title"><i class="icon-filter mr-2"></i>Filters</span>
                           <div class="d-flex align-items-center" style="gap: 14px;">
                              <a href="javascript:void(0);" class="mobile-clear-btn" id="mobile-clear-all">Clear All</a>
                              <button type="button" class="mobile-close-btn" id="close-mobile-filter" aria-label="Close filters">
                                 <i class="icon-close"></i>
                              </button>
                           </div>
                        </div>

                        <div class="mobile-filter-scroll-body">
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
                        <!-- End .widget widget-collapsible (Price) -->

                        </div>
                        <!-- End .mobile-filter-scroll-body -->

                        <div class="mobile-filter-footer d-lg-none">
                           <button type="button" class="btn btn-primary btn-block mobile-apply-btn" id="apply-mobile-filter">
                              Apply Filters
                           </button>
                        </div>
                     </div>
                     <!-- End .sidebar-shop -->
                  </aside>
                  <!-- End .col-lg-2 -->
               </div>
               <!-- End .row -->
            </div>
            <!-- End .container -->
         </div>
         <!-- End .page-content -->

         <!-- Sticky Floating Filter Button on Mobile -->
         <div class="mobile-floating-filter-wrap d-lg-none" id="mobile-floating-filter-wrap">
            <button type="button" class="mobile-floating-filter-btn" id="floating-filter-btn">
               <i class="icon-filter mr-1"></i>
               <span>Filters</span>
               <span class="active-filter-badge" id="floating-filter-count" style="display: none;">0</span>
            </button>
         </div>

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

/* ===================================================
   Mobile Filter Bar & Off-Canvas Drawer Styles
   =================================================== */
.mobile-filter-bar {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    padding: 10px 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.mobile-filter-btn {
    border-radius: 50px !important;
    padding: 7px 18px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    letter-spacing: 0.3px;
    display: inline-flex !important;
    align-items: center;
    gap: 6px;
    border: 1.5px solid #222 !important;
    background: #fff !important;
    color: #222 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    transition: all 0.2s ease;
}

.mobile-filter-btn:active,
.mobile-filter-btn:hover {
    background: #222 !important;
    color: #fff !important;
}

.active-filter-badge {
    background: #37475a;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    line-height: 18px;
    border-radius: 50%;
    text-align: center;
    padding: 0 5px;
    display: inline-block;
}

/* Floating Filter Button */
.mobile-floating-filter-wrap {
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%) translateY(100px);
    z-index: 9999;
    transition: transform 0.3s cubic-bezier(0.2, 0.8, 0.2, 1), opacity 0.3s ease;
    opacity: 0;
    pointer-events: none;
}

.mobile-floating-filter-wrap.visible {
    transform: translateX(-50%) translateY(0);
    opacity: 1;
    pointer-events: auto;
}

.mobile-floating-filter-btn {
    background: #222222;
    color: #ffffff;
    border: 1.5px solid rgba(255,255,255,0.25);
    border-radius: 50px;
    padding: 10px 22px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.mobile-floating-filter-btn:hover,
.mobile-floating-filter-btn:active {
    background: #000000;
}

/* Mobile Off-Canvas Drawer (<= 991px) */
@media screen and (max-width: 991px) {
    #shop-sidebar-aside {
        padding: 0 !important;
        margin: 0 !important;
        width: 0 !important;
        max-width: 0 !important;
        flex: 0 0 0 !important;
        border: none !important;
    }

    .sidebar-filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
        z-index: 105000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    body.mobile-filter-open .sidebar-filter-overlay,
    .sidebar-filter-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    #shop-sidebar {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 330px !important;
        max-width: 88vw !important;
        height: 100vh !important;
        background: #ffffff !important;
        z-index: 105001 !important;
        box-shadow: -6px 0 35px rgba(0, 0, 0, 0.25) !important;
        transform: translateX(100%) !important;
        transition: transform 0.32s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
        display: flex !important;
        flex-direction: column !important;
        visibility: hidden;
    }

    body.mobile-filter-open #shop-sidebar,
    #shop-sidebar.open {
        transform: translateX(0) !important;
        visibility: visible !important;
    }

    body.mobile-filter-open {
        overflow: hidden !important;
    }

    .mobile-filter-header {
        padding: 16px 20px;
        background: #fafafa;
        border-bottom: 1px solid #ebebeb;
        flex-shrink: 0;
    }

    .mobile-filter-title {
        font-size: 16px;
        font-weight: 700;
        color: #222;
    }

    .mobile-clear-btn {
        font-size: 12px;
        font-weight: 600;
        color: #cc9966;
        text-decoration: underline;
    }

    .mobile-close-btn {
        background: transparent;
        border: none;
        font-size: 20px;
        color: #555;
        padding: 4px;
        cursor: pointer;
        line-height: 1;
    }

    .mobile-filter-scroll-body {
        flex: 1 1 auto;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 15px 20px 20px;
    }

    .mobile-filter-scroll-body .widget {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }

    .mobile-filter-footer {
        padding: 14px 20px;
        border-top: 1px solid #ebebeb;
        background: #ffffff;
        flex-shrink: 0;
        box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.05);
    }

    .mobile-apply-btn {
        border-radius: 50px !important;
        padding: 12px !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        background-color: #37475a !important;
        border-color: #37475a !important;
        color: #fff !important;
    }
}

.category-header-wrap {
    margin-top: 0;
    margin-bottom: 25px;
}

    .trust-badges-container {
        margin-bottom: 25px;
    }

    .trust-badges-row .icon-box {
        margin-bottom: 0;
        padding: 10px 15px;
    }

    @media (max-width: 768px) {
        .category-header-wrap {
            margin-top: 0;
            margin-bottom: 12px;
        }

        .trust-badges-container {
            margin-bottom: 14px;
            padding: 0 10px;
        }

        .trust-badges-row .icon-box {
            padding: 4px 2px;
            margin-bottom: 0;
        }

        .trust-badges-row .icon-box-icon {
            font-size: 20px !important;
            margin-bottom: 4px !important;
        }

        .trust-badges-row .icon-box-title {
            font-size: 11px !important;
            font-weight: 700 !important;
            margin-bottom: 2px !important;
            line-height: 1.2 !important;
        }

        .trust-badges-row .icon-box p {
            font-size: 9.5px !important;
            line-height: 1.15 !important;
            color: #777;
            margin-bottom: 0;
        }
    }
</style>
      </main>
      <!-- End .main -->

      <script src="<?= _BASEURL ?>assets/js/page/product-filter.js?v=5.3"></script>
      <?php include('include/backto-top.php');?> 
      <?php include('include/bottom.php');?>