$(function () {

    // ── Mobile OTP Verification for Guest Checkout ─────────────────────────
    var checkoutOtpTimer = null;

    function startCheckoutOtpTimer(seconds) {
        clearInterval(checkoutOtpTimer);
        var count = seconds;
        $('#checkout-timer-text').show();
        $('#checkout-otp-countdown').text(count);
        $('#btn-checkout-resend-otp').hide();

        checkoutOtpTimer = setInterval(function() {
            count--;
            $('#checkout-otp-countdown').text(count);
            if (count <= 0) {
                clearInterval(checkoutOtpTimer);
                $('#checkout-timer-text').hide();
                $('#btn-checkout-resend-otp').show();
            }
        }, 1000);
    }

    // Send OTP button click
    $(document).on('click', '#btn-checkout-send-otp, #btn-checkout-resend-otp', function (e) {
        e.preventDefault();
        var mobile = $('#phone').val().trim();
        var $phoneField = $('#phone');

        if (!/^[6-9]\d{9}$/.test(mobile)) {
            showFieldError($phoneField, 'Please enter a valid 10-digit mobile number starting with 6-9.');
            return;
        }
        clearFieldError($phoneField);

        var $btn = $('#btn-checkout-send-otp');
        $btn.prop('disabled', true).text('Sending...');

        $.ajax({
            url: _BASEURL + 'include/auth_api.php',
            type: 'POST',
            data: { action: 'send_otp', mobile: mobile },
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).text('Verify Mobile');
                if (res.success) {
                    $('#checkout-display-mobile').text(mobile);
                    if (res.debug_otp) {
                        $('#checkout-debug-otp-code').text(res.debug_otp);
                        $('#checkout-debug-otp-alert').show();
                    } else {
                        $('#checkout-debug-otp-alert').hide();
                    }
                    $('#checkout-otp-wrapper').slideDown();
                    $('#checkout-otp-input').val('').focus();
                    $('#checkout-otp-msg').html('');
                    startCheckoutOtpTimer(30);
                } else {
                    showFieldError($phoneField, res.message || 'Failed to send OTP.');
                }
            },
            error: function () {
                $btn.prop('disabled', false).text('Verify Mobile');
                showFieldError($phoneField, 'Error connecting to OTP server.');
            }
        });
    });

    // Verify OTP button click
    $(document).on('click', '#btn-checkout-verify-otp', function (e) {
        e.preventDefault();
        var mobile = $('#phone').val().trim();
        var otp = $('#checkout-otp-input').val().trim();
        var $btn = $(this);
        var $msg = $('#checkout-otp-msg');

        if (otp.length !== 6) {
            $msg.html('<div class="alert alert-danger py-1 px-2 mb-0 small">Please enter a valid 6-digit OTP.</div>');
            return;
        }

        $btn.prop('disabled', true).find('span').text('Verifying...');
        $msg.html('');

        $.ajax({
            url: _BASEURL + 'include/auth_api.php',
            type: 'POST',
            data: { action: 'verify_otp', mobile: mobile, otp: otp },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $msg.html('<div class="alert alert-success py-1 px-2 mb-0 small"><i class="icon-check"></i> Mobile Verified & Registered!</div>');
                    $('#phone').attr('data-verified', 'true').prop('readonly', true);
                    $('#phone-verified-badge').html('<i class="icon-check"></i> Verified').show();
                    $('#btn-checkout-send-otp').hide();

                    setTimeout(function () {
                        $('#checkout-otp-wrapper').slideUp();
                    }, 800);
                } else {
                    $btn.prop('disabled', false).find('span').text('VERIFY OTP & CONTINUE');
                    $msg.html('<div class="alert alert-danger py-1 px-2 mb-0 small">' + res.message + '</div>');
                }
            },
            error: function () {
                $btn.prop('disabled', false).find('span').text('VERIFY OTP & CONTINUE');
                $msg.html('<div class="alert alert-danger py-1 px-2 mb-0 small">Verification failed. Please try again.</div>');
            }
        });
    });

    $('#phone').on('input', function () {
        if ($(this).attr('data-verified') !== 'true') {
            $('#phone-verified-badge').hide();
        }
    });

    // ── Payment method accordion selection ──────────────────────────────
    $('[data-payment]').on('click', function () {
        $('#payment_method').val($(this).data('payment'));
    });

    // When an option is opened/selected, clear the error styling immediately
    $('#accordion-payment .collapse').on('shown.bs.collapse', function () {
        var $triggerLink = $('[href="#' + $(this).attr('id') + '"]');
        var selectedPayment = $triggerLink.data('payment');

        if (selectedPayment) {
            $('#payment_method').val(selectedPayment);
            
            // CLEAR THE VISUAL ERRORS MANUALLY HERE:
            $('#accordion-payment').css('border', 'none');
            $('#payment-error-msg').remove();
        }
    });

    // ── Validation helpers ───────────────────────────────────────────────
    function showFieldError($field, message) {
        $field.addClass('is-invalid');
        $field.siblings('.invalid-feedback').text(message);
    }

    function clearFieldError($field) {
        $field.removeClass('is-invalid');
    }

    function validateForm() {
        var isValid = true;

        var $firstName = $('#first_name');
        clearFieldError($firstName);
        if ($firstName.val().trim() === '') {
            showFieldError($firstName, 'Name is required.');
            isValid = false;
        }

        var $street = $('#street_address');
        clearFieldError($street);
        if ($street.val().trim() === '') {
            showFieldError($street, 'Street address is required.');
            isValid = false;
        }

        var $city = $('#city');
        clearFieldError($city);
        if ($city.val().trim() === '') {
            showFieldError($city, 'City is required.');
            isValid = false;
        }

        var $postcode = $('#postcode');
        clearFieldError($postcode);
        if (!/^[0-9]{4,10}$/.test($postcode.val().trim())) {
            showFieldError($postcode, 'Valid postcode is required.');
            isValid = false;
        }

        var $phone = $('#phone');
        clearFieldError($phone);
        var phoneVal = $phone.val().trim();
        if (!/^[6-9][0-9]{9}$/.test(phoneVal)) {
            showFieldError($phone, 'Valid 10-digit phone number is required.');
            isValid = false;
        } else if (window.ENABLE_MOBILE_VERIFICATION !== false && $phone.attr('data-verified') !== 'true') {
            showFieldError($phone, 'Please click "Verify Mobile" to verify your phone number via OTP.');
            if ($('#checkout-otp-wrapper').is(':hidden')) {
                $('#btn-checkout-send-otp').trigger('click');
            }
            isValid = false;
        }

        var $payment_method = $('#payment_method');
        var $accordion_container = $('#accordion-payment');
        
        $accordion_container.css('border', 'none');
        $('#payment-error-msg').remove(); 

        if ($payment_method.val().trim() === '') {
            $accordion_container.css({
                'border': '1px solid #b30000',
                'border-radius': '4px',
                'padding': '10px',
                'margin-bottom': '15px'
            });

            $accordion_container.after('<p id="payment-error-msg" style="color: #b30000; font-size: 13px; margin: 5px 0 15px 0;">Please select a payment method.</p>');
            
            isValid = false;
        }

        return isValid;
    }

    $('#checkout-form input, #checkout-form textarea').on('input', function () {
        clearFieldError($(this));
    });

    // ── Form submit ──────────────────────────────────────────────────────
    // ── Form submit ──────────────────────────────────────────────────────
    $('#checkout-form').on('submit', function (e) {
        e.preventDefault();

        if (!validateForm()) {
            var $firstInvalid = $('#checkout-form').find('.is-invalid:visible, #payment-error-msg, #accordion-payment').filter(function() {
                if ($(this).attr('id') === 'accordion-payment' || $(this).attr('id') === 'payment-error-msg') {
                    return $('#payment_method').val().trim() === '';
                }
                return $(this).is(':visible');
            }).first();

            if ($firstInvalid.length && ($firstInvalid.attr('id') === 'accordion-payment' || $firstInvalid.attr('id') === 'payment-error-msg')) {
                showAlert('danger', 'Please select a payment method.', false);
                scrollToElement($firstInvalid);
            } else {
                showAlert('danger', 'Please fix the highlighted fields before placing your order.', true);
            }
            return false;
        }

        var $btn = $('#btn-place-order');
        $btn.prop('disabled', true);

        // STEP 1: Save the order to the database as PENDING first
        $.ajax({
            url: _BASEURL + 'process-order.php',
            method: 'POST',
            data: $('#checkout-form').serialize(),
            dataType: 'json'
        })
        .done(function (orderResponse) {
            if (!orderResponse.success) {
                showAlert('danger', orderResponse.message || 'Could not register order.');
                $btn.prop('disabled', false);
                return;
            }

            // Target ID captured safely from database injection step
            var dbOrderId = orderResponse.order_id; 
            var paymentMethod = $('#payment_method').val();
            var requiresRazorpay = (paymentMethod === 'razorpay') || 
                                  (paymentMethod === 'cod' && window.ENABLE_COD_ONLINE_DEPOSIT && window.ENABLE_RAZORPAY);

            if (!requiresRazorpay) {
                showAlert('success', 'Order #' + dbOrderId + ' placed successfully!');
                $('#checkout-form')[0].reset();
                setTimeout(function () {
                    window.location.href = _BASEURL + 'order-confirmation.php?order_id=' + dbOrderId;
                }, 1200);
                return;
            }

            // STEP 2: Initiate your Razorpay payment transaction matching the order
            $.ajax({
                url: _BASEURL + 'create_razorpay_order.php',
                method: 'POST',
                data: { payment_method: paymentMethod, db_order_id: dbOrderId },
                dataType: 'json'
            })
            .done(function (rzpOrderData) {
                if (!rzpOrderData.success) {
                    showAlert('danger', rzpOrderData.message || 'Could not initiate payment window.');
                    $btn.prop('disabled', false);
                    return;
                }

                var options = {
                    key:         rzpOrderData.key,
                    amount:      rzpOrderData.amount,
                    currency:    'INR',
                    name:        'Bunny Boss',
                    description: $('#payment_method').val() === 'cod' ? 'Shipping & GST Deposit for Cash on Delivery' : 'Full Order Payment',
                    order_id:    rzpOrderData.order_id, 
                    handler: function (rzpResponse) {
                        // STEP 3: Pass verification tokens to verify-payment.php 
                        var verificationPayload = {
                            db_order_id:          dbOrderId,
                            razorpay_payment_id: rzpResponse.razorpay_payment_id,
                            razorpay_order_id:   rzpResponse.razorpay_order_id,
                            razorpay_signature:  rzpResponse.razorpay_signature
                        };

                        $.ajax({
                            url: _BASEURL + 'verify-payment.php',
                            method: 'POST',
                            data: verificationPayload,
                            dataType: 'json'
                        }).done(function(finalResponse) {
                            if (finalResponse.success) {
                                showAlert('success', 'Order #' + dbOrderId + ' processed successfully!');
                                $('#checkout-form')[0].reset();
                                setTimeout(function () {
                                    window.location.href = _BASEURL + 'order-confirmation.php?order_id=' + dbOrderId;
                                }, 1200);
                            } else {
                                showAlert('danger', finalResponse.message || 'Status upgrade mismatch verification error.');
                            }
                        });
                    },
                    prefill: {
                        name:    $('#first_name').val().trim(),
                        contact: $('#phone').val().trim()
                    },
                    theme: { color: '#19978c' },
                    modal: {
                        ondismiss: function () {
                            // If they close the modal, the order stays beautifully inside your DB as "pending"
                            showAlert('danger', 'Payment window closed. Order context recorded as pending.');
                            $btn.prop('disabled', false);
                        }
                    }
                };

                if (typeof Razorpay === 'undefined') {
                    showAlert('danger', 'Payment gateway SDK could not be loaded. Please check your network connection or ad-blocker settings.');
                    $btn.prop('disabled', false);
                    return;
                }

                try {
                    var rzp = new Razorpay(options);
                    rzp.on('payment.failed', function (response) {
                        var errDesc = response.error ? response.error.description : 'Payment failed.';
                        showAlert('danger', 'Payment failed: ' + errDesc);
                        $btn.prop('disabled', false);
                    });
                    rzp.open();
                } catch (err) {
                    showAlert('danger', 'Error opening payment window: ' + err.message);
                    $btn.prop('disabled', false);
                }
            });
        })
        .fail(function () {
            showAlert('danger', 'Could not establish connection to order registry platform.');
            $btn.prop('disabled', false);
        });
    });

    // ── Razorpay flow ────────────────────────────────────────────────────
    function handleRazorpay() {
        var $btn = $('#btn-place-order');
        $btn.prop('disabled', true);
        $('#checkout-alert').addClass('d-none');

        var paymentMethod = $('#payment_method').val();

        // Pass payment_method to your order initialization script
        $.ajax({
            url: _BASEURL + 'create_razorpay_order.php',
            method: 'POST',
            data: { payment_method: paymentMethod },
            dataType: 'json'
        })
        .done(function (data) {
            if (!data.success) {
                showAlert('danger', data.message || 'Could not initiate payment.');
                $btn.prop('disabled', false);
                return;
            }

            if (typeof Razorpay === 'undefined') {
                showAlert('danger', 'Payment gateway SDK could not be loaded. Please check your network connection or ad-blocker settings.');
                $btn.prop('disabled', false);
                return;
            }

            var options = {
                key:         data.key,
                amount:      data.amount,   // Populated by PHP (either total price or shipping only)
                currency:    'INR',
                name:        'Bunny Boss',
                description: paymentMethod === 'cod' ? 'Shipping Deposit for COD' : 'Order Payment',
                order_id:    data.order_id, 
                handler: function (response) {
                    submitRazorpayOrder(response);
                },
                prefill: {
                    name:    $('#first_name').val().trim(),
                    contact: $('#phone').val().trim()
                },
                theme: { color: '#19978c' },
                modal: {
                    ondismiss: function () {
                        showAlert('danger', 'Payment cancelled. Please try again.');
                        $btn.prop('disabled', false);
                    }
                }
            };

            try {
                var rzp = new Razorpay(options);
                rzp.on('payment.failed', function (response) {
                    showAlert('danger', 'Payment failed: ' + (response.error ? response.error.description : 'Payment failed.'));
                    $btn.prop('disabled', false);
                });
                rzp.open();
            } catch (err) {
                showAlert('danger', 'Error opening payment window: ' + err.message);
                $btn.prop('disabled', false);
            }
        })
        .fail(function () {
            showAlert('danger', 'Could not connect to payment server. Please try again.');
            $btn.prop('disabled', false);
        });
    }

    function submitRazorpayOrder(rzpResponse) {
        var $form = $('#checkout-form');

        // Merge form fields (including payment_method) with verification tokens
        var postData = $form.serialize()
            + '&razorpay_payment_id=' + encodeURIComponent(rzpResponse.razorpay_payment_id)
            + '&razorpay_order_id='   + encodeURIComponent(rzpResponse.razorpay_order_id)
            + '&razorpay_signature='  + encodeURIComponent(rzpResponse.razorpay_signature);

        $.ajax({
            url: _BASEURL + 'process-order.php',
            method: 'POST',
            data: postData,
            dataType: 'json'
        })
        .done(function (response) {
            if (response.success) {
                showAlert('success', 'Order #' + response.order_id + ' placed successfully!');
                $form[0].reset();
                setTimeout(function () {
                    window.location.href = _BASEURL + 'order-confirmation.php?order_id=' + response.order_id;
                }, 1200);
            } else {
                showAlert('danger', response.message || 'Payment verification failed.');
            }
        })
        .fail(function () {
            showAlert('danger', 'Payment received but order save failed. Please contact support.');
        })
        .always(function () {
            $('#btn-place-order').prop('disabled', false);
        });
    }

    // ── Helpers ──────────────────────────────────────────────────────────
    function getHeaderHeight() {
        var $deskHeader = $('.desktop-header-wrap');
        var $mobHeader = $('.mobile-header');
        var h = 0;
        if ($mobHeader.length && $mobHeader.is(':visible')) {
            h = $mobHeader.outerHeight();
            if (!h || h < 120) h = 200;
        } else if ($deskHeader.length && $deskHeader.is(':visible')) {
            h = $deskHeader.outerHeight();
            if (!h || h < 100) h = 160;
        } else {
            h = window.innerWidth < 768 ? 200 : 160;
        }
        return h;
    }

    function scrollToElement($el) {
        if (!$el || !$el.length) return;
        var headerHeight = getHeaderHeight();
        var targetOffset = $el.offset().top - (headerHeight + 35);
        if (targetOffset < 0) targetOffset = 0;
        $('html, body').stop(true, true).animate({ scrollTop: targetOffset }, 400);
    }

    function showAlert(type, message, doScroll) {
        var $alert = $('#checkout-alert');
        $alert
            .removeClass('d-none alert-success alert-danger')
            .addClass('alert-' + type)
            .text(message);
        if (doScroll !== false && $alert.length) {
            scrollToElement($alert);
        }
    }

    function getCartTotal() {
        return parseFloat($('#cart-total').data('total')) || 0;
    }

});