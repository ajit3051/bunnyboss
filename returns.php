<?php
$page_title = "Returns & Exchanges - Bunny Boss";
include_once("include/config.php");
include('include/top.php');
?>

<main class="main">
    <div class="page-header text-center" style="background-image: url('<?= _BASEURL ?>assets/images/page-header-bg.jpg');">
        <div class="container">
            <h1 class="page-title font-weight-bold">Returns & Exchanges<span>Simple, Fast & Doorstep Size Exchanges</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Returns & Exchanges</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Policy Highlights Box -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #f8fbfb 0%, #eef7f6 100%); border-left: 5px solid #19978c !important;">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge badge-primary px-3 py-2 mr-3" style="background-color: #19978c; font-size: 13px; border-radius: 20px;">
                                    <i class="icon-refresh mr-1"></i> 7-Day Exchange Policy
                                </span>
                                <span class="text-muted small">Worry-Free Footwear Shopping</span>
                            </div>
                            <h3 class="font-weight-bold mb-2 text-dark" style="font-size: 24px;">Hassle-Free Returns & Size Replacements</h3>
                            <p class="text-secondary mb-0" style="font-size: 15px; line-height: 1.7;">
                                We want you to love your shoes! If your pair doesn’t fit perfectly or if you notice any manufacturing defect, you can exchange your footwear within <strong>7 days</strong> of delivery. Our goal is to make the exchange process effortless.
                            </p>
                        </div>
                    </div>

                    <!-- Policy Guidelines & Conditions -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #ffffff;">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="font-weight-bold text-dark mb-4" style="font-size: 20px;">
                                <i class="icon-info-circle text-primary mr-2" style="color: #19978c !important;"></i> Guidelines for Exchange & Returns
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 rounded h-100" style="background: #fbfbfc; border: 1px solid #edf0f2;">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="icon-check text-success mr-2" style="font-size: 18px;"></i>
                                            <h6 class="font-weight-bold text-dark mb-0">Eligible for Exchange</h6>
                                        </div>
                                        <ul class="text-secondary small mb-0 pl-3" style="line-height: 1.8;">
                                            <li>Footwear is brand new, unworn, and unwashed.</li>
                                            <li>Original shoe box, brand tags, and labels are intact.</li>
                                            <li>Original purchase invoice / digital bill is provided.</li>
                                            <li>Request raised within 7 days from the delivery date.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="p-3 rounded h-100" style="background: #fff8f8; border: 1px solid #f9e2e2;">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="icon-close text-danger mr-2" style="font-size: 18px;"></i>
                                            <h6 class="font-weight-bold text-danger mb-0">Not Eligible for Exchange</h6>
                                        </div>
                                        <ul class="text-secondary small mb-0 pl-3" style="line-height: 1.8;">
                                            <li>Shoes showing visible signs of outdoor wear, dirt, or creased soles.</li>
                                            <li>Missing shoe box, torn packaging, or missing brand accessories.</li>
                                            <li>Products without valid purchase proof or invoice.</li>
                                            <li>Clearance/flash sale items marked non-returnable.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3 Easy Steps to Exchange -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #ffffff;">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="font-weight-bold text-dark mb-4" style="font-size: 20px;">How to Initiate an Exchange</h4>
                            <div class="row">
                                <div class="col-md-4 text-center mb-3 mb-md-0">
                                    <div class="d-inline-flex align-items-center justify-content-center text-white font-weight-bold mb-3" style="width: 50px; height: 50px; background-color: #19978c; border-radius: 50%; font-size: 20px;">1</div>
                                    <h5 class="font-weight-bold text-dark mb-2" style="font-size: 16px;">Request Exchange</h5>
                                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                        WhatsApp or call our team at <a href="tel:7428068439" style="color: #19978c; font-weight: bold;">+91 7428068439</a> with your Order ID and the new size you need.
                                    </p>
                                </div>
                                <div class="col-md-4 text-center mb-3 mb-md-0">
                                    <div class="d-inline-flex align-items-center justify-content-center text-white font-weight-bold mb-3" style="width: 50px; height: 50px; background-color: #19978c; border-radius: 50%; font-size: 20px;">2</div>
                                    <h5 class="font-weight-bold text-dark mb-2" style="font-size: 16px;">Doorstep Reverse Pickup</h5>
                                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                        Our logistics executive will arrive at your address to inspect and securely pack the return parcel.
                                    </p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="d-inline-flex align-items-center justify-content-center text-white font-weight-bold mb-3" style="width: 50px; height: 50px; background-color: #19978c; border-radius: 50%; font-size: 20px;">3</div>
                                    <h5 class="font-weight-bold text-dark mb-2" style="font-size: 16px;">New Size Dispatched</h5>
                                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                        Your replacement pair is immediately shipped out with real-time tracking updates sent via SMS.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQs on Returns -->
                    <div class="mb-5">
                        <h4 class="font-weight-bold text-dark mb-3" style="font-size: 20px;">Frequently Asked Questions</h4>
                        <div class="accordion accordion-rounded" id="returns-accordion">
                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-ret-1">
                                    <h2 class="card-title mb-0">
                                        <a role="button" data-toggle="collapse" href="#collapse-ret-1" aria-expanded="true" aria-controls="collapse-ret-1" class="font-weight-bold text-dark" style="font-size: 15px;">
                                            What if my required replacement size is out of stock?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-ret-1" class="collapse show" aria-labelledby="heading-ret-1" data-parent="#returns-accordion">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        If the exact size or color is unavailable in inventory, you can choose another shoe model of equivalent value or request store credit / a full refund back to your account.
                                    </div>
                                </div>
                            </div>

                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-ret-2">
                                    <h2 class="card-title mb-0">
                                        <a class="collapsed font-weight-bold text-dark" role="button" data-toggle="collapse" href="#collapse-ret-2" aria-expanded="false" aria-controls="collapse-ret-2" style="font-size: 15px;">
                                            How long does the entire exchange cycle take?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-ret-2" class="collapse" aria-labelledby="heading-ret-2" data-parent="#returns-accordion">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Pickup usually occurs within 48 to 72 hours of your request. For Delhi-NCR, replacements often arrive within 2 business days; for other cities, 3 to 5 business days.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Support Contact Card -->
                    <div class="text-center p-4 p-md-5 mb-5" style="background-color: #37475a; border-radius: 12px; color: #ffffff;">
                        <h4 class="text-white font-weight-bold mb-2">Need to initiate an exchange now?</h4>
                        <p class="text-white-50 mb-4" style="max-width: 600px; margin: 0 auto; font-size: 14px;">Our support specialists are here to guide you through a quick and effortless exchange.</p>
                        <a href="tel:7428068439" class="btn btn-primary btn-round px-4 py-2 mr-2" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                            <i class="icon-phone mr-1"></i> Call +91 7428068439
                        </a>
                        <a href="<?= _BASEURL ?>contact.php" class="btn btn-outline-white btn-round px-4 py-2 text-white" style="border-color: #ffffff; font-weight: bold;">
                            Write to Us
                        </a>
                    </div>
                </div><!-- End .col-lg-10 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
</main>

<?php include('include/bottom.php'); ?>
