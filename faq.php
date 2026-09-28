<?php
$page_title = "Frequently Asked Questions (FAQ) - Bunny Boss";
include_once("include/config.php");
include('include/top.php');
?>

<main class="main">
    <div class="page-header text-center" style="background-image: url('<?= _BASEURL ?>assets/images/page-header-bg.jpg');">
        <div class="container">
            <h1 class="page-title font-weight-bold">Frequently Asked Questions<span>Quick Answers to Your Most Common Queries</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">FAQ</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <!-- Category: Ordering & Accounts -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge badge-primary px-3 py-2 mr-2" style="background-color: #19978c; font-size: 13px; border-radius: 20px;">
                                <i class="icon-shopping-cart mr-1"></i> Ordering
                            </span>
                            <h3 class="font-weight-bold text-dark mb-0" style="font-size: 20px;">Ordering & Account</h3>
                        </div>

                        <div class="accordion accordion-rounded" id="accordion-ordering">
                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-ord-1">
                                    <h2 class="card-title mb-0">
                                        <a role="button" data-toggle="collapse" href="#collapse-ord-1" aria-expanded="true" aria-controls="collapse-ord-1" class="font-weight-bold text-dark" style="font-size: 15px;">
                                            How do I place an order on Bunny Boss?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-ord-1" class="collapse show" aria-labelledby="heading-ord-1" data-parent="#accordion-ordering">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Placing an order is super quick! Browse our footwear collection, choose your shoe size, and click "Add to Cart" or "Order Now". You will be asked for your mobile number to receive a 1-time OTP login. Enter your shipping address, select your preferred payment mode (Cash on Delivery or Online UPI/Card), and confirm your order.
                                    </div>
                                </div>
                            </div>

                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-ord-2">
                                    <h2 class="card-title mb-0">
                                        <a class="collapsed font-weight-bold text-dark" role="button" data-toggle="collapse" href="#collapse-ord-2" aria-expanded="false" aria-controls="collapse-ord-2" style="font-size: 15px;">
                                            Do I need to create a permanent password to shop?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-ord-2" class="collapse" aria-labelledby="heading-ord-2" data-parent="#accordion-ordering">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        No, you don't need to remember passwords! We use secure mobile OTP login. Whenever you want to check your order history, edit your addresses, or check out, simply enter your mobile number and verify via OTP.
                                    </div>
                                </div>
                            </div>

                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-ord-3">
                                    <h2 class="card-title mb-0">
                                        <a class="collapsed font-weight-bold text-dark" role="button" data-toggle="collapse" href="#collapse-ord-3" aria-expanded="false" aria-controls="collapse-ord-3" style="font-size: 15px;">
                                            Can I modify my delivery address after placing an order?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-ord-3" class="collapse" aria-labelledby="heading-ord-3" data-parent="#accordion-ordering">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Yes! If your order has not yet been handed over to the courier, you can update your address directly from your <a href="<?= _BASEURL ?>orders.php" style="color: #19978c; font-weight: bold;">My Orders</a> dashboard or call our support team at <a href="tel:7428068439" style="color: #19978c; font-weight: bold;">+91 7428068439</a>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category: Shipping & Delivery -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge badge-primary px-3 py-2 mr-2" style="background-color: #2980b9; font-size: 13px; border-radius: 20px;">
                                <i class="icon-truck mr-1"></i> Shipping
                            </span>
                            <h3 class="font-weight-bold text-dark mb-0" style="font-size: 20px;">Shipping & Delivery</h3>
                        </div>

                        <div class="accordion accordion-rounded" id="accordion-shipping">
                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-shp-1">
                                    <h2 class="card-title mb-0">
                                        <a role="button" data-toggle="collapse" href="#collapse-shp-1" aria-expanded="true" aria-controls="collapse-shp-1" class="font-weight-bold text-dark" style="font-size: 15px;">
                                            How many days will it take for my shoes to be delivered?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-shp-1" class="collapse show" aria-labelledby="heading-shp-1" data-parent="#accordion-shipping">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Delivery timelines depend on your location:<br>
                                        &bull; <strong>Delhi / NCR:</strong> 24 to 48 hours.<br>
                                        &bull; <strong>Metro Cities:</strong> 2 to 3 business days.<br>
                                        &bull; <strong>Rest of India:</strong> 3 to 5 business days.<br>
                                        You will receive real-time SMS updates with an active tracking link.
                                    </div>
                                </div>
                            </div>

                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-shp-2">
                                    <h2 class="card-title mb-0">
                                        <a class="collapsed font-weight-bold text-dark" role="button" data-toggle="collapse" href="#collapse-shp-2" aria-expanded="false" aria-controls="collapse-shp-2" style="font-size: 15px;">
                                            How can I track my shipment?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-shp-2" class="collapse" aria-labelledby="heading-shp-2" data-parent="#accordion-shipping">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        You can track your package anytime by visiting our <a href="<?= _BASEURL ?>track-order-page.php" style="color: #2980b9; font-weight: bold;">Order Tracking Page</a> and entering your AWB number or order ID. You can also click the tracking link sent in our automated SMS.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category: Payments & COD -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge badge-primary px-3 py-2 mr-2" style="background-color: #e67e22; font-size: 13px; border-radius: 20px;">
                                <i class="icon-credit-card mr-1"></i> Payments
                            </span>
                            <h3 class="font-weight-bold text-dark mb-0" style="font-size: 20px;">Payments & Cash on Delivery</h3>
                        </div>

                        <div class="accordion accordion-rounded" id="accordion-payment">
                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-pay-1">
                                    <h2 class="card-title mb-0">
                                        <a role="button" data-toggle="collapse" href="#collapse-pay-1" aria-expanded="true" aria-controls="collapse-pay-1" class="font-weight-bold text-dark" style="font-size: 15px;">
                                            Is Cash on Delivery (COD) available for my pincode?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-pay-1" class="collapse show" aria-labelledby="heading-pay-1" data-parent="#accordion-payment">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Yes! Cash on Delivery is available across 27,000+ PIN codes across India. When your order arrives, you can pay in cash or ask the delivery executive to display their UPI QR code to pay via Google Pay, PhonePe, or Paytm.
                                    </div>
                                </div>
                            </div>

                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-pay-2">
                                    <h2 class="card-title mb-0">
                                        <a class="collapsed font-weight-bold text-dark" role="button" data-toggle="collapse" href="#collapse-pay-2" aria-expanded="false" aria-controls="collapse-pay-2" style="font-size: 15px;">
                                            What happens if money was debited but the order didn't go through?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-pay-2" class="collapse" aria-labelledby="heading-pay-2" data-parent="#accordion-payment">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Due to banking network delays, payments occasionally face temporary confirmation lag. If the order is not confirmed, the debited amount is automatically reversed by your issuing bank within 24 to 48 banking hours. You can also reach our support team with your payment reference for immediate status verification.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category: Returns & Size Exchanges -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge badge-primary px-3 py-2 mr-2" style="background-color: #27ae60; font-size: 13px; border-radius: 20px;">
                                <i class="icon-refresh mr-1"></i> Exchanges
                            </span>
                            <h3 class="font-weight-bold text-dark mb-0" style="font-size: 20px;">Returns & Size Exchanges</h3>
                        </div>

                        <div class="accordion accordion-rounded" id="accordion-returns">
                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-faq-ret-1">
                                    <h2 class="card-title mb-0">
                                        <a role="button" data-toggle="collapse" href="#collapse-faq-ret-1" aria-expanded="true" aria-controls="collapse-faq-ret-1" class="font-weight-bold text-dark" style="font-size: 15px;">
                                            What should I do if the shoes do not fit my feet?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-faq-ret-1" class="collapse show" aria-labelledby="heading-faq-ret-1" data-parent="#accordion-returns">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        No worries at all! We offer a <strong>7-day doorstep size exchange</strong>. Just ensure the footwear is unused and in its original box with tags intact. Call or WhatsApp us at <a href="tel:7428068439" style="color: #27ae60; font-weight: bold;">+91 7428068439</a> with your order number, and we will schedule a reverse pickup for your size exchange.
                                    </div>
                                </div>
                            </div>

                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-faq-ret-2">
                                    <h2 class="card-title mb-0">
                                        <a class="collapsed font-weight-bold text-dark" role="button" data-toggle="collapse" href="#collapse-faq-ret-2" aria-expanded="false" aria-controls="collapse-faq-ret-2" style="font-size: 15px;">
                                            Are there any charges for size exchanges?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-faq-ret-2" class="collapse" aria-labelledby="heading-faq-ret-2" data-parent="#accordion-returns">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        We provide hassle-free size replacement service. Detailed exchange conditions can be reviewed on our <a href="<?= _BASEURL ?>returns.php" style="color: #27ae60; font-weight: bold;">Returns & Exchange Policy</a> page.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Still Have Questions Banner -->
                    <div class="text-center p-4 p-md-5 mb-5" style="background-color: #37475a; border-radius: 14px; color: #ffffff;">
                        <h3 class="text-white font-weight-bold mb-2">Can't find the answer you're looking for?</h3>
                        <p class="text-white-50 mb-4" style="max-width: 600px; margin: 0 auto; font-size: 15px;">Our support team is ready to answer any questions about our products, sizing, delivery, or orders.</p>
                        <a href="tel:7428068439" class="btn btn-primary btn-round px-4 py-2 mr-2" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                            <i class="icon-phone mr-1"></i> Call Support
                        </a>
                        <a href="<?= _BASEURL ?>contact.php" class="btn btn-outline-white btn-round px-4 py-2 text-white" style="border-color: #ffffff; font-weight: bold;">
                            Send a Message
                        </a>
                    </div>

                </div><!-- End .col-lg-10 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
</main>

<?php include('include/bottom.php'); ?>
