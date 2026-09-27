<?php
$page_title = "Help & Customer Support - Bunny Boss";
include_once("include/config.php");
include('include/top.php');
?>

<main class="main">
    <div class="page-header text-center" style="background-image: url('<?= _BASEURL ?>assets/images/page-header-bg.jpg');">
        <div class="container">
            <h1 class="page-title font-weight-bold">Help & Support<span>We're Here to Assist You Every Step of the Way</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Help & Support</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Quick Assist Cards -->
                    <div class="row mb-5">
                        <div class="col-md-3 col-sm-6 mb-4">
                            <a href="<?= _BASEURL ?>track-order-page.php" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm text-center p-4" style="border-radius: 12px; transition: transform 0.2s; background: #ffffff;">
                                    <div class="mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background-color: #e6f5f4; border-radius: 50%; color: #19978c; font-size: 24px;">
                                        <i class="icon-truck"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">Track Order</h5>
                                    <p class="text-secondary small mb-0">Check live parcel status</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6 mb-4">
                            <a href="<?= _BASEURL ?>returns.php" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm text-center p-4" style="border-radius: 12px; transition: transform 0.2s; background: #ffffff;">
                                    <div class="mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background-color: #ebf3fa; border-radius: 50%; color: #2980b9; font-size: 24px;">
                                        <i class="icon-refresh"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">Exchanges</h5>
                                    <p class="text-secondary small mb-0">7-Day size replacement</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6 mb-4">
                            <a href="<?= _BASEURL ?>payment-methods.php" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm text-center p-4" style="border-radius: 12px; transition: transform 0.2s; background: #ffffff;">
                                    <div class="mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background-color: #fef4e8; border-radius: 50%; color: #e67e22; font-size: 24px;">
                                        <i class="icon-credit-card"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">Payments</h5>
                                    <p class="text-secondary small mb-0">COD & UPI options</p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6 mb-4">
                            <a href="<?= _BASEURL ?>faq.php" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm text-center p-4" style="border-radius: 12px; transition: transform 0.2s; background: #ffffff;">
                                    <div class="mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background-color: #f3ebf9; border-radius: 50%; color: #8e44ad; font-size: 24px;">
                                        <i class="icon-info-circle"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">FAQs</h5>
                                    <p class="text-secondary small mb-0">Common answers</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Direct Support Channels -->
                    <div class="card border-0 shadow-sm mb-5" style="border-radius: 14px; background: #ffffff;">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="font-weight-bold text-dark mb-4" style="font-size: 22px;">Contact Customer Care</h3>
                            <div class="row">
                                <div class="col-md-4 mb-4 mb-md-0">
                                    <div class="d-flex align-items-start">
                                        <div class="mr-3 text-primary" style="color: #19978c !important; font-size: 24px;"><i class="icon-phone"></i></div>
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-1">Phone Numbers</h6>
                                            <p class="small text-secondary mb-1">Mon - Sun: 10:00 AM - 8:00 PM</p>
                                            <p class="mb-0">
                                                <a href="tel:7428068439" class="font-weight-bold d-block" style="color: #19978c;">+91 7428068439</a>
                                                <a href="tel:7838384314" class="font-weight-bold d-block" style="color: #19978c;">+91 7838384314</a>
                                                <a href="tel:7838384318" class="font-weight-bold d-block" style="color: #19978c;">+91 7838384318</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-4 mb-md-0">
                                    <div class="d-flex align-items-start">
                                        <div class="mr-3 text-primary" style="color: #19978c !important; font-size: 24px;"><i class="icon-envelope"></i></div>
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-1">Email Support</h6>
                                            <p class="small text-secondary mb-1">We respond within 24 hours</p>
                                            <a href="mailto:support@bunnyboss.in" class="font-weight-bold" style="color: #19978c;">support@bunnyboss.in</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="mr-3 text-primary" style="color: #19978c !important; font-size: 24px;"><i class="icon-map-marker"></i></div>
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-1">Retail Flagship Store</h6>
                                            <p class="small text-secondary mb-0" style="line-height: 1.6;">
                                                A1-40, Chanakya Place Part-1, 25 Foota Road, Janak Puri, Opp. Mata Chanan Devi Hospital, Delhi - 110059.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Common Help Topics -->
                    <div class="mb-5">
                        <h3 class="font-weight-bold text-dark mb-3" style="font-size: 22px;">Popular Help Topics</h3>
                        <div class="accordion accordion-rounded" id="help-accordion">
                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-help-1">
                                    <h2 class="card-title mb-0">
                                        <a role="button" data-toggle="collapse" href="#collapse-help-1" aria-expanded="true" aria-controls="collapse-help-1" class="font-weight-bold text-dark" style="font-size: 15px;">
                                            How do I know my shoe size before placing an order?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-help-1" class="collapse show" aria-labelledby="heading-help-1" data-parent="#help-accordion">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        All footwear sizes on Bunny Boss follow standard Indian / UK shoe sizing. We recommend checking the size chart on each product page. If you are between two sizes, we generally recommend choosing the larger size for the most comfortable fit. In case the size isn't right, our 7-day doorstep size exchange has you covered!
                                    </div>
                                </div>
                            </div>

                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-help-2">
                                    <h2 class="card-title mb-0">
                                        <a class="collapsed font-weight-bold text-dark" role="button" data-toggle="collapse" href="#collapse-help-2" aria-expanded="false" aria-controls="collapse-help-2" style="font-size: 15px;">
                                            Can I modify or cancel my order after placing it?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-help-2" class="collapse" aria-labelledby="heading-help-2" data-parent="#help-accordion">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Yes! You can modify your shipping address or cancel your order before it gets dispatched by calling our customer care team at <a href="tel:7428068439" style="color: #19978c; font-weight: bold;">+91 7428068439</a> or reaching out via WhatsApp. Once dispatched, orders can easily be exchanged or returned upon arrival.
                                    </div>
                                </div>
                            </div>

                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-help-3">
                                    <h2 class="card-title mb-0">
                                        <a class="collapsed font-weight-bold text-dark" role="button" data-toggle="collapse" href="#collapse-help-3" aria-expanded="false" aria-controls="collapse-help-3" style="font-size: 15px;">
                                            How do I claim a refund for a returned product?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-help-3" class="collapse" aria-labelledby="heading-help-3" data-parent="#help-accordion">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Once your return package arrives at our warehouse and passes our quality check, refunds for online payments are credited to the source account within 24 to 48 hours. For COD orders, our team will request your UPI ID or bank account details to transfer the refund directly.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Contact Form CTA -->
                    <div class="text-center p-4 p-md-5 mb-5" style="background-color: #37475a; border-radius: 12px; color: #ffffff;">
                        <h4 class="text-white font-weight-bold mb-2">Still need help with your inquiry?</h4>
                        <p class="text-white-50 mb-4" style="max-width: 600px; margin: 0 auto; font-size: 14px;">Fill out our online contact form or drop us an email, and an associate will assist you shortly.</p>
                        <a href="<?= _BASEURL ?>contact.php" class="btn btn-primary btn-round px-4 py-2" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                            Go to Contact Us Form
                        </a>
                    </div>
                </div><!-- End .col-lg-10 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
</main>

<?php include('include/bottom.php'); ?>
