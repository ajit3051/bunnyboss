<?php
$page_title = "My Wishlist - Bunny Boss";
include_once("include/config.php");
include('include/top.php');
?>

<main class="main">
    <div class="page-header text-center" style="background-image: url('<?= _BASEURL ?>assets/images/page-header-bg.jpg');">
        <div class="container">
            <h1 class="page-title font-weight-bold">My Wishlist<span>Your Favorite Shoes & Saved Styles</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <!-- Empty State Card (or populated if saved items exist) -->
            <div id="wishlist-empty-box" class="card border-0 shadow-sm text-center py-5 px-4 mb-5" style="border-radius: 14px; background: #ffffff;">
                <div class="card-body">
                    <div class="mb-4 mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background-color: #fcebeb; border-radius: 50%; color: #e74c3c; font-size: 36px;">
                        <i class="icon-heart"></i>
                    </div>
                    <h3 class="font-weight-bold text-dark mb-2" style="font-size: 24px;">Your Wishlist is Empty</h3>
                    <p class="text-secondary mx-auto mb-4" style="max-width: 500px; font-size: 15px; line-height: 1.6;">
                        Explore our latest shoe drops, trendy sneakers, and casual collections. Save your favorite pairs here to buy them whenever you're ready!
                    </p>
                    <a href="<?= _BASEURL ?>product-list.php" class="btn btn-primary btn-round px-5 py-3" style="background-color: #19978c; border-color: #19978c; font-weight: bold; font-size: 15px;">
                        <i class="icon-shopping-cart mr-2"></i> Explore Collection
                    </a>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="row mb-5">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center" style="border-radius: 12px; background: #fbfbfc;">
                        <div class="text-primary mb-3" style="color: #19978c !important; font-size: 28px;">
                            <i class="icon-heart-o"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1">Save Favorite Shoes</h5>
                        <p class="text-secondary small mb-0">Bookmark footwear you love and easily compare colors, sizes, and prices.</p>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center" style="border-radius: 12px; background: #fbfbfc;">
                        <div class="text-primary mb-3" style="color: #19978c !important; font-size: 28px;">
                            <i class="icon-refresh"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1">Stock & Price Alerts</h5>
                        <p class="text-secondary small mb-0">Stay updated on exclusive price drops and size re-stocks for your favorite shoes.</p>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center" style="border-radius: 12px; background: #fbfbfc;">
                        <div class="text-primary mb-3" style="color: #19978c !important; font-size: 28px;">
                            <i class="icon-truck"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark mb-1">Fast 1-Click Checkout</h5>
                        <p class="text-secondary small mb-0">Move items seamlessly to your shopping cart and complete payment with Cash on Delivery or UPI.</p>
                    </div>
                </div>
            </div>

            <!-- Trending Products Section -->
            <?php if (function_exists('getHTMLProductList')): ?>
            <div class="trending-section mb-5">
                <div class="heading heading-flex mb-3">
                    <div class="heading-left">
                        <h2 class="title font-weight-bold text-dark mb-0" style="font-size: 22px;">Trending Styles You May Like</h2>
                    </div>
                    <div class="heading-right">
                        <a href="<?= _BASEURL ?>product-list.php" class="title-link font-weight-bold" style="color: #19978c;">View More Shoes <i class="icon-long-arrow-right"></i></a>
                    </div>
                </div>
                <div class="products">
                    <div class="row justify-content-center">
                        <?php echo getHTMLProductList(0, 4); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div><!-- End .container -->
    </div><!-- End .page-content -->
</main>

<?php include('include/bottom.php'); ?>
