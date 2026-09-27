<?php
$page_title = "How to Shop on BunnyBoss - Shopping Guide";
include_once("include/config.php");
include('include/top.php');
?>

<main class="main">
    <div class="page-header text-center" style="background-image: url('<?= _BASEURL ?>assets/images/page-header-bg.jpg');">
        <div class="container">
            <h1 class="page-title font-weight-bold">How to Shop on BunnyBoss<span>Your Quick 4-Step Guide to Buying Premium Shoes</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">How to Shop</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Intro Hero Banner -->
                    <div class="card border-0 shadow-sm mb-5 text-center" style="border-radius: 14px; background: linear-gradient(135deg, #37475a 0%, #202b38 100%); color: #ffffff;">
                        <div class="card-body p-4 p-md-5">
                            <h2 class="text-white font-weight-bold mb-3" style="font-size: 26px;">Shopping for Shoes Made Effortless</h2>
                            <p class="text-white-50 mx-auto mb-0" style="max-width: 650px; font-size: 15px; line-height: 1.7;">
                                Welcome to <strong>Bunny Boss</strong>, your ultimate destination for trend-setting footwear, sneakers, and casual shoes. We’ve streamlined the entire shopping experience from finding the right fit to speedy doorstep delivery.
                            </p>
                        </div>
                    </div>

                    <!-- 4 Steps Visual Timeline -->
                    <div class="steps-container mb-5">
                        <!-- Step 1 -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: #ffffff; border-left: 5px solid #19978c !important;">
                            <div class="card-body p-4 p-md-5">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <div class="d-inline-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 64px; height: 64px; background-color: #19978c; border-radius: 50%; font-size: 24px;">
                                            1
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <h4 class="font-weight-bold text-dark mb-2" style="font-size: 20px;">
                                            Explore & Select Your Favorite Shoes
                                        </h4>
                                        <p class="text-secondary mb-2" style="font-size: 14px; line-height: 1.7;">
                                            Browse our extensive footwear catalog by visiting <a href="<?= _BASEURL ?>product-list.php" style="color: #19978c; font-weight: bold;">Shop Shoes</a>. Use our handy search bar or filter by brand, color, category, and size to quickly discover styles that match your taste.
                                        </p>
                                        <span class="badge badge-light px-3 py-1 text-dark" style="border: 1px solid #e2e8f0; border-radius: 12px;">
                                            <i class="icon-check text-success mr-1"></i> Multi-angle high-resolution images & detailed specifications
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: #ffffff; border-left: 5px solid #2980b9 !important;">
                            <div class="card-body p-4 p-md-5">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <div class="d-inline-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 64px; height: 64px; background-color: #2980b9; border-radius: 50%; font-size: 24px;">
                                            2
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <h4 class="font-weight-bold text-dark mb-2" style="font-size: 20px;">
                                            Select Your Shoe Size (UK / India)
                                        </h4>
                                        <p class="text-secondary mb-2" style="font-size: 14px; line-height: 1.7;">
                                            On the product details page, pick your required size (e.g. UK 7, UK 8, UK 9, UK 10). If you are uncertain about fit, select your regular Indian/UK standard size. Click <strong>"Add to Cart"</strong> or <strong>"Order Now"</strong> to proceed immediately.
                                        </p>
                                        <span class="badge badge-light px-3 py-1 text-dark" style="border: 1px solid #e2e8f0; border-radius: 12px;">
                                            <i class="icon-refresh text-primary mr-1"></i> Don't worry! We offer a 7-day doorstep size exchange guarantee
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: #ffffff; border-left: 5px solid #e67e22 !important;">
                            <div class="card-body p-4 p-md-5">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <div class="d-inline-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 64px; height: 64px; background-color: #e67e22; border-radius: 50%; font-size: 24px;">
                                            3
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <h4 class="font-weight-bold text-dark mb-2" style="font-size: 20px;">
                                            Seamless Checkout & Secure Payment
                                        </h4>
                                        <p class="text-secondary mb-2" style="font-size: 14px; line-height: 1.7;">
                                            No long sign-up forms required! Simply log in with your mobile number via OTP, enter your shipping delivery address, and choose your preferred payment method: <strong>Cash on Delivery (COD)</strong>, <strong>Instant UPI (Google Pay, PhonePe, Paytm)</strong>, or <strong>Credit/Debit Cards</strong>.
                                        </p>
                                        <span class="badge badge-light px-3 py-1 text-dark" style="border: 1px solid #e2e8f0; border-radius: 12px;">
                                            <i class="icon-shield text-success mr-1"></i> Bank-grade 256-bit SSL secured payments with Razorpay
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: #ffffff; border-left: 5px solid #27ae60 !important;">
                            <div class="card-body p-4 p-md-5">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <div class="d-inline-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 64px; height: 64px; background-color: #27ae60; border-radius: 50%; font-size: 24px;">
                                            4
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <h4 class="font-weight-bold text-dark mb-2" style="font-size: 20px;">
                                            Real-Time Tracking & Fast Doorstep Delivery
                                        </h4>
                                        <p class="text-secondary mb-2" style="font-size: 14px; line-height: 1.7;">
                                            As soon as your order is packaged, you will receive an SMS and WhatsApp message containing your AWB tracking link. You can track your courier journey live through our <a href="<?= _BASEURL ?>track-order-page.php" style="color: #27ae60; font-weight: bold;">Order Tracking Page</a> until it arrives at your doorstep!
                                        </p>
                                        <span class="badge badge-light px-3 py-1 text-dark" style="border: 1px solid #e2e8f0; border-radius: 12px;">
                                            <i class="icon-truck text-success mr-1"></i> Pan-India express delivery by Delhivery & Shadowfax
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pro Shopping Tips -->
                    <div class="card border-0 shadow-sm mb-5" style="border-radius: 14px; background: #fbfbfc; border: 1px solid #e2e8f0;">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="font-weight-bold text-dark mb-3" style="font-size: 20px;">
                                <i class="icon-lightbulb text-warning mr-2"></i> Pro Tips for the Best Shopping Experience
                            </h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="font-weight-bold text-dark mb-1">Verify Your Delivery Pincode</h6>
                                    <p class="text-secondary small mb-0">Double-check your 6-digit postal pincode and contact number during checkout for speedy routing.</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="font-weight-bold text-dark mb-1">Check Ongoing Deals</h6>
                                    <p class="text-secondary small mb-0">Keep an eye on the top announcement bar for festive discounts and promotional coupon codes.</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="font-weight-bold text-dark mb-1">Save Favorite Shoes</h6>
                                    <p class="text-secondary small mb-0">Use the wishlist feature to save shoes and easily compare different styles before deciding.</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="font-weight-bold text-dark mb-1">Store Pickup Available</h6>
                                    <p class="text-secondary small mb-0">Living in Delhi? You can also visit our flagship Janakpuri store to try shoes in person!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ready to Shop CTA -->
                    <div class="text-center p-4 p-md-5 mb-5" style="background-color: #19978c; border-radius: 14px; color: #ffffff;">
                        <h3 class="text-white font-weight-bold mb-2">Ready to Upgrade Your Footwear?</h3>
                        <p class="text-white-50 mb-4" style="max-width: 550px; margin: 0 auto; font-size: 15px;">Discover our collection of sneakers, casual shoes, sports footwear, and formal styles at unbeatable prices.</p>
                        <a href="<?= _BASEURL ?>product-list.php" class="btn btn-white btn-round px-5 py-3" style="background-color: #ffffff; color: #19978c; font-weight: bold; font-size: 15px;">
                            <i class="icon-shopping-cart mr-2"></i> Start Shopping Now
                        </a>
                    </div>
                </div><!-- End .col-lg-10 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
</main>

<?php include('include/bottom.php'); ?>
