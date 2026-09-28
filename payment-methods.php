<?php
$page_title = "Payment Methods - Bunny Boss";
include_once("include/config.php");
include('include/top.php');
?>

<main class="main">
    <div class="page-header text-center" style="background-image: url('<?= _BASEURL ?>assets/images/page-header-bg.jpg');">
        <div class="container">
            <h1 class="page-title font-weight-bold">Payment Methods<span>Safe, Secure & Flexible Payment Options</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Payment Methods</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Intro Card -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #f8fbfb 0%, #eef7f6 100%); border-left: 5px solid #19978c !important;">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge badge-primary px-3 py-2 mr-3" style="background-color: #19978c; font-size: 13px; border-radius: 20px;">
                                    <i class="icon-shield mr-1"></i> 100% Secure Payments
                                </span>
                                <span class="text-muted small">RBI & PCI-DSS Compliant</span>
                            </div>
                            <h3 class="font-weight-bold mb-2 text-dark" style="font-size: 24px;">Convenient & Trusted Ways to Pay</h3>
                            <p class="text-secondary mb-0" style="font-size: 15px; line-height: 1.7;">
                                At <strong>Bunny Boss</strong>, we strive to make your shopping experience as seamless and reliable as possible. We offer a comprehensive suite of payment options, from popular UPI applications and credit/debit cards to hassle-free Cash on Delivery (COD) across India.
                            </p>
                        </div>
                    </div>

                    <!-- Payment Options Grid -->
                    <div class="row mb-4">
                        <!-- UPI -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-wrap mr-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #e6f5f4; border-radius: 50%; color: #19978c; font-size: 24px;">
                                            <i class="icon-mobile"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 18px;">UPI (Instant & Free)</h4>
                                            <span class="text-muted small">Google Pay, PhonePe, Paytm, BHIM, CRED</span>
                                        </div>
                                    </div>
                                    <p class="text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Enjoy zero-fee instant checkout using any UPI application. Pay directly from your bank account by scanning the QR code or confirming the payment request on your mobile app.
                                    </p>
                                    <ul class="list-unstyled text-secondary small mb-0">
                                        <li class="mb-1"><i class="icon-check text-success mr-2"></i> Zero transaction charges</li>
                                        <li class="mb-1"><i class="icon-check text-success mr-2"></i> Instant order confirmation</li>
                                        <li><i class="icon-check text-success mr-2"></i> High success rate with UPI Auto-Intent</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Cash on Delivery -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-0" style="border-radius: 12px;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-wrap mr-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #fef4e8; border-radius: 50%; color: #e67e22; font-size: 24px;">
                                            <i class="icon-truck"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 18px;">Cash on Delivery (COD)</h4>
                                            <span class="text-muted small">Pay at your doorstep</span>
                                        </div>
                                    </div>
                                    <p class="text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Prefer inspecting the package before paying? Select Cash on Delivery at checkout and pay the delivery partner in cash or through QR/UPI upon arrival.
                                    </p>
                                    <ul class="list-unstyled text-secondary small mb-0">
                                        <li class="mb-1"><i class="icon-check text-success mr-2"></i> Available across 27,000+ PIN codes</li>
                                        <li class="mb-1"><i class="icon-check text-success mr-2"></i> Doorstep digital UPI payment also supported by delivery agents</li>
                                        <li><i class="icon-check text-success mr-2"></i> SMS OTP verification for quick delivery</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Credit & Debit Cards -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-0" style="border-radius: 12px;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-wrap mr-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #ebf3fa; border-radius: 50%; color: #2980b9; font-size: 24px;">
                                            <i class="icon-credit-card"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 18px;">Credit & Debit Cards</h4>
                                            <span class="text-muted small">Visa, MasterCard, RuPay, Maestro, Amex</span>
                                        </div>
                                    </div>
                                    <p class="text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        All major domestic and international debit and credit cards are supported. Every card payment is authenticated using 3D Secure OTP sent to your registered mobile phone.
                                    </p>
                                    <ul class="list-unstyled text-secondary small mb-0">
                                        <li class="mb-1"><i class="icon-check text-success mr-2"></i> Bank-grade 256-bit SSL encryption</li>
                                        <li class="mb-1"><i class="icon-check text-success mr-2"></i> We never save your card number or CVV</li>
                                        <li><i class="icon-check text-success mr-2"></i> Instant refund in case of cancellation</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Net Banking & Wallets -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-0" style="border-radius: 12px;">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-wrap mr-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #f3ebf9; border-radius: 50%; color: #8e44ad; font-size: 24px;">
                                            <i class="icon-shopping-cart"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-weight-bold mb-0 text-dark" style="font-size: 18px;">Net Banking & Wallets</h4>
                                            <span class="text-muted small">50+ Banks & Top Digital Wallets</span>
                                        </div>
                                    </div>
                                    <p class="text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Pay directly from your bank account via Net Banking covering SBI, HDFC, ICICI, Axis, PNB, Kotak, and more, as well as digital wallets like Paytm, Mobikwik, and Amazon Pay.
                                    </p>
                                    <ul class="list-unstyled text-secondary small mb-0">
                                        <li class="mb-1"><i class="icon-check text-success mr-2"></i> Direct bank gateway authentication</li>
                                        <li class="mb-1"><i class="icon-check text-success mr-2"></i> Instant debit and real-time confirmation</li>
                                        <li><i class="icon-check text-success mr-2"></i> Seamless mobile banking checkout</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security & Protection Section -->
                    <div class="card border-0 shadow-sm mb-5" style="border-radius: 12px; background: #ffffff;">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="font-weight-bold mb-4 text-dark" style="font-size: 20px;">
                                <i class="icon-shield text-primary mr-2" style="color: #19978c !important;"></i> How We Protect Your Transactions
                            </h3>
                            <div class="row">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <h5 class="font-weight-bold text-dark mb-2" style="font-size: 16px;">256-Bit SSL Encryption</h5>
                                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                        Your payment information is transferred over an industry-standard encrypted connection ensuring maximum protection.
                                    </p>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <h5 class="font-weight-bold text-dark mb-2" style="font-size: 16px;">PCI-DSS Level 1 Partner</h5>
                                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                        Payments are processed through India's leading certified gateway, Razorpay, adhering to the highest global payment standards.
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <h5 class="font-weight-bold text-dark mb-2" style="font-size: 16px;">Zero Card Storing</h5>
                                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                        Bunny Boss never stores your credit card, debit card, or CVV credentials on our servers.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="mb-5">
                        <h3 class="font-weight-bold mb-3 text-dark" style="font-size: 22px;">Payment FAQs</h3>
                        <div class="accordion accordion-rounded" id="payment-accordion">
                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-1">
                                    <h2 class="card-title mb-0">
                                        <a role="button" data-toggle="collapse" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1" class="font-weight-bold text-dark" style="font-size: 15px;">
                                            What should I do if money is debited from my account but order is not confirmed?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-1" class="collapse show" aria-labelledby="heading-1" data-parent="#payment-accordion">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        If money was debited but you didn't receive an order confirmation within 15 minutes, please don't worry. This typically happens due to banking network latency. If the payment gateway does not confirm the order, your issuing bank will automatically reverse the transaction within 24 to 48 banking hours. You can also contact our support at <a href="tel:7428068439" class="font-weight-bold" style="color: #19978c;">+91 7428068439</a> with your transaction reference.
                                    </div>
                                </div>
                            </div>

                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-2">
                                    <h2 class="card-title mb-0">
                                        <a class="collapsed font-weight-bold text-dark" role="button" data-toggle="collapse" href="#collapse-2" aria-expanded="false" aria-controls="collapse-2" style="font-size: 15px;">
                                            Are there any additional fees for Cash on Delivery (COD)?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-2" class="collapse" aria-labelledby="heading-2" data-parent="#payment-accordion">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        We do not charge any hidden fees for COD orders. Any applicable nominal shipping or handling fee (if applicable) is clearly displayed during checkout before you place your order.
                                    </div>
                                </div>
                            </div>

                            <div class="card card-box card-sm bg-light mb-2 border-0" style="border-radius: 8px;">
                                <div class="card-header" id="heading-3">
                                    <h2 class="card-title mb-0">
                                        <a class="collapsed font-weight-bold text-dark" role="button" data-toggle="collapse" href="#collapse-3" aria-expanded="false" aria-controls="collapse-3" style="font-size: 15px;">
                                            Can I pay the delivery agent via UPI on delivery?
                                        </a>
                                    </h2>
                                </div>
                                <div id="collapse-3" class="collapse" aria-labelledby="heading-3" data-parent="#payment-accordion">
                                    <div class="card-body text-secondary" style="font-size: 14px; line-height: 1.6;">
                                        Yes! Most of our logistics partners (Delhivery, Shadowfax) provide a dynamic UPI QR code on the delivery partner's mobile device. You can scan and pay with Google Pay, PhonePe, or Paytm when the parcel arrives at your doorstep.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Support Banner -->
                    <div class="text-center p-4 p-md-5 mb-5" style="background-color: #37475a; border-radius: 12px; color: #ffffff;">
                        <h4 class="text-white font-weight-bold mb-2">Have questions about your payment?</h4>
                        <p class="text-white-50 mb-4" style="max-width: 600px; margin: 0 auto; font-size: 14px;">Our customer support team is available every day to assist you with payment status, refunds, or invoice inquiries.</p>
                        <a href="<?= _BASEURL ?>contact.php" class="btn btn-primary btn-round px-4 py-2" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                            <i class="icon-phone mr-1"></i> Contact Support
                        </a>
                    </div>
                </div><!-- End .col-lg-10 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
</main>

<?php include('include/bottom.php'); ?>
