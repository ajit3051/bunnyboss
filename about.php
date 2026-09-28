<?php
$page_title = "About Us - Bunny Boss";
include_once("include/config.php");
include('include/top.php');
?>

<main class="main">
    <div class="page-header text-center" style="background-image: url('<?= _BASEURL ?>assets/images/page-header-bg.jpg');">
        <div class="container">
            <h1 class="page-title font-weight-bold">About Bunny Boss<span>Your Trusted Destination for Premium Footwear</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About Us</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Brand Story Card -->
                    <div class="card border-0 shadow-sm mb-5" style="border-radius: 14px; background: #ffffff;">
                        <div class="card-body p-4 p-md-5">
                            <span class="badge badge-primary px-3 py-2 mb-3" style="background-color: #19978c; font-size: 13px; border-radius: 20px;">
                                <i class="icon-star mr-1"></i> The Bunny Boss Story
                            </span>
                            <h2 class="font-weight-bold text-dark mb-3" style="font-size: 26px;">Step Into Style, Comfort & Authenticity</h2>
                            <p class="text-secondary" style="font-size: 15px; line-height: 1.8;">
                                <strong>Bunny Boss</strong> (operated by <strong>Kalika Impex</strong>) is a premier multi-brand footwear destination founded with a singular ambition: bringing authentic, stylish, and high-performance shoes to footwear enthusiasts across India.
                            </p>
                            <p class="text-secondary" style="font-size: 15px; line-height: 1.8;">
                                Located in Janakpuri, New Delhi, Bunny Boss showcases curated collections spanning streetwear sneakers, running shoes, casual lifestyle kicks, and formal footwear from premier brands. We bridge the gap between quality craftsmanship, trend-setting aesthetics, and affordable pricing.
                            </p>
                            <p class="text-secondary mb-0" style="font-size: 15px; line-height: 1.8;">
                                Whether shopping online through our platform with fast pan-India shipping or stepping into our Delhi retail showroom, customers enjoy transparent pricing, 100% genuine products, and genuine 7-day doorstep size exchange support.
                            </p>
                        </div>
                    </div>

                    <!-- Values / Core Strengths Grid -->
                    <h3 class="font-weight-bold text-dark mb-4 text-center" style="font-size: 22px;">Why Shoppers Choose Bunny Boss</h3>
                    <div class="row mb-5">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm p-4" style="border-radius: 12px; background: #ffffff; border-top: 4px solid #19978c !important;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #e6f5f4; border-radius: 10px; color: #19978c; font-size: 22px;">
                                        <i class="icon-check"></i>
                                    </div>
                                    <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 18px;">100% Genuine Footwear</h4>
                                </div>
                                <p class="text-secondary small mb-0" style="line-height: 1.7;">
                                    We stand firmly against counterfeit products. Every pair of shoes sold on Bunny Boss is verified and quality-checked before leaving our store.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm p-4" style="border-radius: 12px; background: #ffffff; border-top: 4px solid #2980b9 !important;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #ebf3fa; border-radius: 10px; color: #2980b9; font-size: 22px;">
                                        <i class="icon-truck"></i>
                                    </div>
                                    <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 18px;">Pan-India Express Logistics</h4>
                                </div>
                                <p class="text-secondary small mb-0" style="line-height: 1.7;">
                                    In partnership with Delhivery and Shadowfax, our parcels reach 27,000+ pin codes safely, backed by automated SMS tracking updates.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm p-4" style="border-radius: 12px; background: #ffffff; border-top: 4px solid #e67e22 !important;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fef4e8; border-radius: 10px; color: #e67e22; font-size: 22px;">
                                        <i class="icon-refresh"></i>
                                    </div>
                                    <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 18px;">7-Day Doorstep Size Exchange</h4>
                                </div>
                                <p class="text-secondary small mb-0" style="line-height: 1.7;">
                                    We know fit is everything in footwear. If your shoes don't fit just right, we'll swap them for your correct size right at your doorstep.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm p-4" style="border-radius: 12px; background: #ffffff; border-top: 4px solid #27ae60 !important;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #eafaf1; border-radius: 10px; color: #27ae60; font-size: 22px;">
                                        <i class="icon-phone"></i>
                                    </div>
                                    <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 18px;">Dedicated Support 7 Days a Week</h4>
                                </div>
                                <p class="text-secondary small mb-0" style="line-height: 1.7;">
                                    Our support associates are just a call or WhatsApp message away to resolve sizing queries, track orders, or answer payment questions.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Store Location & Visit Box -->
                    <div class="card border-0 shadow-sm mb-5 text-white" style="border-radius: 14px; background: #37475a;">
                        <div class="card-body p-4 p-md-5">
                            <div class="row align-items-center">
                                <div class="col-md-7 mb-3 mb-md-0">
                                    <h3 class="text-white font-weight-bold mb-2" style="font-size: 22px;">Visit Our Showroom in Janakpuri</h3>
                                    <p class="text-white-50 mb-3" style="font-size: 14px; line-height: 1.6;">
                                        A1-40, Chanakya Place Part-1, 25 Foota Road (C-1 Janak Puri), Opposite Mata Chanan Devi Hospital, Delhi - 110059.
                                    </p>
                                    <p class="text-white-50 small mb-0">
                                        Open Daily: 10:30 AM to 8:30 PM &bull; Call: +91 7428068439
                                    </p>
                                </div>
                                <div class="col-md-5 text-md-right">
                                    <a href="<?= _BASEURL ?>contact.php" class="btn btn-primary btn-round px-4 py-2" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                                        <i class="icon-map-marker mr-1"></i> Get Directions & Contact
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- End .col-lg-10 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
</main>

<?php include('include/bottom.php'); ?>