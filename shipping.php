<?php
$page_title = "Shipping & Delivery Policy - Bunny Boss";
include_once("include/config.php");
include('include/top.php');
?>

<main class="main">
    <div class="page-header text-center" style="background-image: url('<?= _BASEURL ?>assets/images/page-header-bg.jpg');">
        <div class="container">
            <h1 class="page-title font-weight-bold">Shipping & Delivery<span>Fast, Safe & Reliable Pan-India Dispatch</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Shipping & Delivery</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Delivery Network Banner -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #f8fbfb 0%, #eef7f6 100%); border-left: 5px solid #19978c !important;">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge badge-primary px-3 py-2 mr-3" style="background-color: #19978c; font-size: 13px; border-radius: 20px;">
                                    <i class="icon-truck mr-1"></i> Pan-India Coverage
                                </span>
                                <span class="text-muted small">Over 27,000+ Pin Codes Serviced</span>
                            </div>
                            <h3 class="font-weight-bold mb-2 text-dark" style="font-size: 24px;">Fast & Reliable Footwear Delivery</h3>
                            <p class="text-secondary mb-0" style="font-size: 15px; line-height: 1.7;">
                                Bunny Boss partners with premier logistics providers including <strong>Delhivery</strong>, <strong>Shadowfax</strong>, <strong>BlueDart</strong>, and <strong>DTDC</strong> to ensure your favorite shoes reach your doorstep quickly, safely, and in mint condition.
                            </p>
                        </div>
                    </div>

                    <!-- Delivery Timelines Table / Cards -->
                    <h4 class="font-weight-bold text-dark mb-3" style="font-size: 20px;">Estimated Delivery Timelines</h4>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm text-center p-3" style="border-radius: 12px; background: #ffffff;">
                                <div class="card-body">
                                    <div class="mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #e6f5f4; border-radius: 50%; color: #19978c; font-size: 22px;">
                                        <i class="icon-map-marker"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">Delhi / NCR</h5>
                                    <p class="text-primary font-weight-bold mb-2" style="color: #19978c !important; font-size: 18px;">24 - 48 Hours</p>
                                    <p class="text-secondary small mb-0">Express local dispatch from our Delhi fulfillment center.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm text-center p-3" style="border-radius: 12px; background: #ffffff;">
                                <div class="card-body">
                                    <div class="mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #ebf3fa; border-radius: 50%; color: #2980b9; font-size: 22px;">
                                        <i class="icon-truck"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">Metro Cities</h5>
                                    <p class="text-primary font-weight-bold mb-2" style="color: #2980b9 !important; font-size: 18px;">2 - 3 Days</p>
                                    <p class="text-secondary small mb-0">Mumbai, Bengaluru, Kolkata, Chennai, Hyderabad, Pune, Ahmedabad.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm text-center p-3" style="border-radius: 12px; background: #ffffff;">
                                <div class="card-body">
                                    <div class="mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #fef4e8; border-radius: 50%; color: #e67e22; font-size: 22px;">
                                        <i class="icon-clock"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">Rest of India</h5>
                                    <p class="text-primary font-weight-bold mb-2" style="color: #e67e22 !important; font-size: 18px;">3 - 5 Days</p>
                                    <p class="text-secondary small mb-0">Tier-2, Tier-3 cities and regional zones across all states.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Features -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #ffffff;">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="font-weight-bold text-dark mb-4" style="font-size: 20px;">Shipping Features & Policies</h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="mr-3" style="color: #19978c; font-size: 24px;"><i class="icon-mobile"></i></div>
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-1">Live SMS & WhatsApp Notifications</h6>
                                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                                Receive milestone alerts with AWB tracking numbers as soon as your shoes are packed, dispatched, in-transit, and out for delivery.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="mr-3" style="color: #19978c; font-size: 24px;"><i class="icon-shield"></i></div>
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-1">Tamper-Proof Packaging</h6>
                                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                                Every shoe box is securely double-boxed and sealed with tamper-evident security tape to ensure safe handling during transit.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="mr-3" style="color: #19978c; font-size: 24px;"><i class="icon-check"></i></div>
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-1">Doorstep Inspection Allowed</h6>
                                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                                Verify the package seals and details with the delivery partner upon arrival. For COD orders, you can pay via cash or UPI QR scan.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="d-flex">
                                        <div class="mr-3" style="color: #19978c; font-size: 24px;"><i class="icon-clock"></i></div>
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-1">Same-Day Dispatch</h6>
                                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                                Orders placed before 2:00 PM IST on business days are processed and dispatched on the very same day.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Track Order CTA -->
                    <div class="p-4 p-md-5 mb-5 text-center text-md-left d-md-flex align-items-center justify-content-between" style="background-color: #37475a; border-radius: 12px; color: #ffffff;">
                        <div class="mb-3 mb-md-0">
                            <h4 class="text-white font-weight-bold mb-1">Already have a tracking number?</h4>
                            <p class="text-white-50 mb-0" style="font-size: 14px;">Enter your AWB or Order ID to see real-time scan updates from Delhivery & Shadowfax.</p>
                        </div>
                        <div>
                            <a href="<?= _BASEURL ?>track-order-page.php" class="btn btn-primary btn-round px-4 py-2" style="background-color: #19978c; border-color: #19978c; font-weight: bold; white-space: nowrap;">
                                <i class="icon-truck mr-1"></i> Track My Order Now
                            </a>
                        </div>
                    </div>
                </div><!-- End .col-lg-10 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
</main>

<?php include('include/bottom.php'); ?>
