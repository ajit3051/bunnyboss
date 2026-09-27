<?php
$page_title = "Contact Us - Bunny Boss";
include_once("include/config.php");
include('include/top.php');
?>

<style>
    .contact-form .form-control {
        border-radius: 8px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .contact-form .form-control:focus {
        border-color: #19978c;
        box-shadow: 0 0 0 3px rgba(25, 151, 140, 0.15);
    }
    .contact-form .invalid-feedback {
        font-size: 12px;
        font-weight: 500;
        margin-top: 4px;
        display: none;
    }
    .contact-form .form-control.is-invalid ~ .invalid-feedback {
        display: block;
    }
    .btn-submit-contact {
        background-color: #19978c;
        border-color: #19978c;
        font-weight: 700;
        letter-spacing: 0.5px;
        transition: all 0.2s ease-in-out;
    }
    .btn-submit-contact:hover, .btn-submit-contact:focus {
        background-color: #147970 !important;
        border-color: #147970 !important;
        color: #fff !important;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .icon-spin {
        display: inline-block;
        animation: spin 1s infinite linear;
    }
</style>

<main class="main">
    <div class="page-header text-center" style="background-image: url('<?= _BASEURL ?>assets/images/page-header-bg.jpg');">
        <div class="container">
            <h1 class="page-title font-weight-bold">Contact Us<span>Get in Touch With Our Footwear Specialists</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="title mb-2 font-weight-bold text-dark" style="font-size: 24px;">Store Information</h2>
                    <p class="mb-4 text-secondary" style="font-size: 15px; line-height: 1.7;">
                        Have questions about footwear sizes, live tracking, exchanges, or store visits? We’re always eager to assist you.
                    </p>

                    <div class="row">
                        <div class="col-sm-7 mb-4">
                            <div class="contact-info">
                                <h3 class="font-weight-bold text-dark mb-3" style="font-size: 18px;">Showroom & Office</h3>
                                <ul class="contact-list list-unstyled text-secondary" style="line-height: 1.8;">
                                    <li class="mb-2">
                                        <i class="icon-map-marker text-primary mr-2" style="color: #19978c !important;"></i>
                                        A1-40, Chanakya Place Part-1, 25 Foota Road (C-1 Janak Puri), Opp. Mata Chanan Devi Hospital, Delhi - 110059.
                                    </li>
                                    <li class="mb-2">
                                        <i class="icon-phone text-primary mr-2" style="color: #19978c !important;"></i>
                                        <a href="tel:7428068439" style="color: #19978c; font-weight: bold;">+91 7428068439</a><br>
                                        <a href="tel:7838384314" style="color: #19978c; font-weight: bold;">+91 7838384314</a>
                                    </li>
                                    <li>
                                        <i class="icon-envelope text-primary mr-2" style="color: #19978c !important;"></i>
                                        <a href="mailto:support@bunnyboss.in" style="color: #19978c;">support@bunnyboss.in</a>
                                    </li>
                                </ul><!-- End .contact-list -->
                            </div><!-- End .contact-info -->
                        </div><!-- End .col-sm-7 -->

                        <div class="col-sm-5 mb-4">
                            <div class="contact-info">
                                <h3 class="font-weight-bold text-dark mb-3" style="font-size: 18px;">Working Hours</h3>
                                <ul class="contact-list list-unstyled text-secondary" style="line-height: 1.8;">
                                    <li class="mb-2">
                                        <i class="icon-clock-o text-primary mr-2" style="color: #19978c !important;"></i>
                                        <span class="text-dark font-weight-bold">Monday &ndash; Saturday</span><br>
                                        10:30 AM &ndash; 8:30 PM
                                    </li>
                                    <li>
                                        <i class="icon-calendar text-primary mr-2" style="color: #19978c !important;"></i>
                                        <span class="text-dark font-weight-bold">Sunday</span><br>
                                        11:00 AM &ndash; 8:00 PM
                                    </li>
                                </ul><!-- End .contact-list -->
                            </div><!-- End .contact-info -->
                        </div><!-- End .col-sm-5 -->
                    </div><!-- End .row -->
                </div><!-- End .col-lg-6 -->

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 14px; background: #ffffff;">
                        <h2 class="title mb-1 font-weight-bold text-dark" style="font-size: 22px;">Send Us a Message</h2>
                        <p class="mb-3 text-secondary small">Fill out the form below and our customer support team will get back to you shortly.</p>

                        <!-- Dynamic Alert Message -->
                        <div id="contact-alert" class="alert d-none" role="alert" style="border-radius: 8px; font-size: 14px;"></div>

                        <form id="contact-form" class="contact-form mb-0" novalidate>
                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label for="cname" class="font-weight-bold small text-dark">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="cname" placeholder="Enter your full name" required>
                                    <div class="invalid-feedback" id="cname-error">Please enter your name (minimum 2 characters).</div>
                                </div><!-- End .col-sm-6 -->

                                <div class="col-sm-6 mb-3">
                                    <label for="cphone" class="font-weight-bold small text-dark">Mobile Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light text-muted small" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">+91</span>
                                        </div>
                                        <input type="tel" class="form-control" name="phone" id="cphone" placeholder="10-digit mobile" maxlength="10" required style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                                    </div>
                                    <div class="invalid-feedback" id="cphone-error">Please enter a valid 10-digit Indian mobile number.</div>
                                </div><!-- End .col-sm-6 -->
                            </div><!-- End .row -->

                            <div class="row">
                                <div class="col-sm-6 mb-3">
                                    <label for="cemail" class="font-weight-bold small text-dark">Email Address</label>
                                    <input type="email" class="form-control" name="email" id="cemail" placeholder="name@example.com">
                                    <div class="invalid-feedback" id="cemail-error">Please enter a valid email address.</div>
                                </div><!-- End .col-sm-6 -->

                                <div class="col-sm-6 mb-3">
                                    <label for="csubject" class="font-weight-bold small text-dark">Subject / Query Type</label>
                                    <select class="form-control" name="subject" id="csubject" style="border-radius: 8px;">
                                        <option value="General Inquiry">General Inquiry</option>
                                        <option value="Size Exchange Request">Size Exchange Request</option>
                                        <option value="Order Tracking Help">Order Tracking Help</option>
                                        <option value="Payment / Refund Issue">Payment / Refund Issue</option>
                                        <option value="Product Availability">Product Availability</option>
                                        <option value="Store Visit Question">Store Visit Question</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div><!-- End .col-sm-6 -->
                            </div><!-- End .row -->

                            <div class="mb-4">
                                <label for="cmessage" class="font-weight-bold small text-dark">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="message" id="cmessage" cols="30" rows="4" placeholder="How can we assist you today? (Please include Order ID if relevant)" required></textarea>
                                <div class="invalid-feedback" id="cmessage-error">Please enter your message (at least 5 characters).</div>
                            </div>

                            <button type="submit" id="btn-submit-contact" class="btn btn-primary btn-round btn-submit-contact px-4 py-3">
                                <span id="btn-text">SUBMIT INQUIRY</span>
                                <i class="icon-long-arrow-right ml-1" id="btn-icon"></i>
                            </button>
                        </form><!-- End .contact-form -->
                    </div>
                </div><!-- End .col-lg-6 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
</main>

<script>
$(document).ready(function() {
    var $form = $('#contact-form');
    var $alert = $('#contact-alert');
    var $btn = $('#btn-submit-contact');
    var $btnText = $('#btn-text');
    var $btnIcon = $('#btn-icon');

    // Validation helper functions
    function validateName() {
        var val = $('#cname').val().trim();
        if (val.length < 2) {
            $('#cname').removeClass('is-valid').addClass('is-invalid');
            return false;
        } else {
            $('#cname').removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }

    function validatePhone() {
        var val = $('#cphone').val().trim();
        var phoneRegex = /^[6-9]\d{9}$/;
        if (!phoneRegex.test(val)) {
            $('#cphone').removeClass('is-valid').addClass('is-invalid');
            return false;
        } else {
            $('#cphone').removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }

    function validateEmail() {
        var val = $('#cemail').val().trim();
        if (val.length > 0) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(val)) {
                $('#cemail').removeClass('is-valid').addClass('is-invalid');
                return false;
            } else {
                $('#cemail').removeClass('is-invalid').addClass('is-valid');
                return true;
            }
        } else {
            $('#cemail').removeClass('is-invalid is-valid');
            return true; // email is optional
        }
    }

    function validateMessage() {
        var val = $('#cmessage').val().trim();
        if (val.length < 5) {
            $('#cmessage').removeClass('is-valid').addClass('is-invalid');
            return false;
        } else {
            $('#cmessage').removeClass('is-invalid').addClass('is-valid');
            return true;
        }
    }

    // Real-time input validation triggers
    $('#cname').on('input blur', validateName);
    $('#cphone').on('input blur', function() {
        // Only allow numbers
        this.value = this.value.replace(/[^0-9]/g, '');
        validatePhone();
    });
    $('#cemail').on('input blur', validateEmail);
    $('#cmessage').on('input blur', validateMessage);

    // Form submission
    $form.on('submit', function(e) {
        e.preventDefault();

        // Run all validations
        var isNameValid = validateName();
        var isPhoneValid = validatePhone();
        var isEmailValid = validateEmail();
        var isMessageValid = validateMessage();

        if (!isNameValid || !isPhoneValid || !isEmailValid || !isMessageValid) {
            $alert.removeClass('d-none alert-success').addClass('alert-danger')
                  .html('<i class="icon-info-circle mr-1"></i> Please fix the errors highlighted in red before submitting.');
            return false;
        }

        // Disable button & show spinner
        $btn.prop('disabled', true);
        $btnText.text('SENDING INQUIRY...');
        $btnIcon.removeClass('icon-long-arrow-right').addClass('icon-refresh icon-spin');
        $alert.addClass('d-none').removeClass('alert-success alert-danger');

        $.ajax({
            url: '<?= _BASEURL ?>contact-submit.php',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false);
                $btnText.text('SUBMIT INQUIRY');
                $btnIcon.removeClass('icon-refresh icon-spin').addClass('icon-long-arrow-right');

                if (response && response.success) {
                    $alert.removeClass('d-none alert-danger').addClass('alert-success')
                          .html('<i class="icon-check mr-2"></i> <strong>Success!</strong> ' + response.message);
                    $form[0].reset();
                    $('.form-control').removeClass('is-valid is-invalid');
                    $('html, body').animate({
                        scrollTop: $alert.offset().top - 120
                    }, 400);
                } else {
                    var errorMsg = (response && response.message) ? response.message : 'Failed to submit inquiry. Please try again.';
                    $alert.removeClass('d-none alert-success').addClass('alert-danger')
                          .html('<i class="icon-info-circle mr-2"></i> ' + errorMsg);
                }
            },
            error: function(xhr, status, error) {
                $btn.prop('disabled', false);
                $btnText.text('SUBMIT INQUIRY');
                $btnIcon.removeClass('icon-refresh icon-spin').addClass('icon-long-arrow-right');

                $alert.removeClass('d-none alert-success').addClass('alert-danger')
                      .html('<i class="icon-info-circle mr-2"></i> An unexpected network error occurred. Please try again or call us directly.');
            }
        });
    });
});
</script>

<?php include('include/bottom.php'); ?>