<!-- Delivery Location Modal -->
<div class="modal fade" id="location-modal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true" style="z-index: 100000;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px; margin: 1.75rem auto;">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.25);">
            <!-- Modal Header -->
            <div class="modal-header py-3 px-4" style="background-color: #f8f9fa; border-bottom: 1px solid #eaeaea;">
                <h5 class="modal-title font-weight-bold text-dark d-flex align-items-center mb-0" id="locationModalLabel" style="font-size: 15px; letter-spacing: -0.2px;">
                    <i class="icon-map-marker text-primary mr-2" style="font-size: 18px;"></i> Choose your delivery location
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 22px; padding: 12px 16px; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <p class="text-muted small mb-3" style="line-height: 1.4;">
                    Select a delivery location to check product availability, shipping speeds, and serviceable PIN codes.
                </p>

                <!-- Current Active Location Badge -->
                <div class="p-2 px-3 mb-3 d-flex align-items-center justify-content-between" style="background-color: #f1f5f9; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <span class="small text-muted">Current location:</span>
                    <span class="font-weight-bold text-dark small current-active-loc-badge">
                        <i class="icon-map-marker text-success mr-1"></i> <span class="header-loc-city">Loading...</span>
                    </span>
                </div>

                <!-- Saved Addresses Section (Logged-in users) -->
                <div id="modal-saved-addresses-wrapper" style="display: none;">
                    <label class="font-weight-bold text-dark small text-uppercase mb-2" style="letter-spacing: 0.5px; font-size: 11px;">Your Saved Addresses</label>
                    <div id="modal-saved-addresses-list" class="mb-3" style="max-height: 180px; overflow-y: auto;">
                        <!-- Dynamically populated -->
                    </div>
                    <div class="text-center position-relative my-3">
                        <hr style="margin: 0; border-color: #e5e7eb;">
                        <span class="bg-white px-2 small text-muted text-uppercase" style="position: relative; top: -10px; font-weight: 600; font-size: 10px;">OR ENTER A PINCODE</span>
                    </div>
                </div>

                <!-- Sign-in Prompt for Guest Visitors -->
                <div id="modal-signin-prompt-wrapper" class="card border-0 p-3 mb-3 text-center" style="display: none; background-color: #fcf8e3; border-radius: 8px; border: 1px solid #faebcc !important;">
                    <p class="small text-dark mb-2" style="font-size: 12px;">Sign in to view your saved addresses and 1-click delivery.</p>
                    <a href="#signin-modal" data-toggle="modal" data-dismiss="modal" class="btn btn-outline-primary-2 btn-sm py-1 mx-auto" style="min-width: 140px; font-size: 12px; height: auto;">
                        <span>Sign In / Register</span>
                    </a>
                </div>

                <!-- Pincode Form -->
                <label class="font-weight-bold text-dark small text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 11px;">Enter an Indian PIN code</label>
                <form id="form-update-delivery-location">
                    <div class="input-group">
                        <input type="tel" 
                               id="input-delivery-pincode" 
                               class="form-control" 
                               placeholder="6-digit PIN code (e.g. 110059)" 
                               maxlength="6" 
                               pattern="[0-9]{6}" 
                               style="height: 42px; font-size: 14px; border-radius: 6px 0 0 6px;"
                               required>
                        <div class="input-group-append">
                            <button class="btn btn-primary px-4" type="submit" id="btn-submit-pincode" style="height: 42px; border-radius: 0 6px 6px 0; font-size: 13px;">
                                <span>Apply</span>
                            </button>
                        </div>
                    </div>
                    <div id="pincode-feedback-msg" class="mt-2" style="font-size: 12px;"></div>
                </form>

                <!-- Current Location Auto-Detect Option -->
                <div class="mt-3 pt-2 border-top text-center">
                    <button type="button" class="btn btn-link btn-sm text-primary p-0 d-inline-flex align-items-center" id="btn-auto-detect-loc" style="font-weight: 600; font-size: 12px; text-decoration: none;">
                        <i class="icon-location-arrow mr-1"></i> Use my current location
                    </button>
                    <div id="detect-loc-spinner" class="small text-muted mt-1" style="display: none; font-size: 11px;">
                        <i class="icon-refresh icon-spin mr-1"></i> Detecting your location...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.saved-addr-card {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 9px 12px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #ffffff;
}
.saved-addr-card:hover {
    border-color: #fcb941;
    background: #fffdf9;
    box-shadow: 0 2px 8px rgba(252, 185, 65, 0.15);
}
.saved-addr-card.active-addr {
    border-color: #28a745;
    background: #f0fdf4;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') return;
    var $ = jQuery;

    // Load initial text into modal badge
    function syncModalBadge() {
        var currentText = $('.header-loc-city').first().text().trim();
        if (currentText) {
            $('.current-active-loc-badge .header-loc-city').text(currentText);
        }
    }

    // When Location modal opens, fetch saved addresses if user is logged in
    $('#location-modal').on('show.bs.modal', function() {
        syncModalBadge();
        $('#pincode-feedback-msg').html('');
        $('#detect-loc-spinner').hide();

        $.ajax({
            url: '<?= _BASEURL ?>api/set-location.php',
            type: 'GET',
            data: { action: 'get_saved_addresses' },
            dataType: 'json',
            success: function(res) {
                if (res.logged_in && res.addresses && res.addresses.length > 0) {
                    var html = '';
                    var currentPin = res.current_pincode || '';
                    res.addresses.forEach(function(addr) {
                        var isActive = (currentPin && String(addr.postcode).trim() === String(currentPin).trim());
                        html += '<div class="saved-addr-card d-flex align-items-center justify-content-between ' + (isActive ? 'active-addr' : '') + '" data-id="' + addr.id + '">' +
                                    '<div style="overflow: hidden; padding-right: 8px;">' +
                                        '<strong class="d-block small text-dark">' + (addr.title ? escapeHtml(addr.title) + ': ' : '') + escapeHtml(addr.first_name || '') + ' ' + escapeHtml(addr.last_name || '') + (isActive ? ' <span class="badge badge-success small py-0" style="font-size: 9px;">Selected</span>' : '') + '</strong>' +
                                        '<span class="text-muted" style="font-size: 11px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' + escapeHtml(addr.street_address || '') + ', ' + escapeHtml(addr.city || '') + ' - ' + escapeHtml(addr.postcode || '') + '</span>' +
                                    '</div>' +
                                    '<button type="button" class="btn btn-outline-primary btn-sm py-1 px-2 flex-shrink-0 btn-select-addr" style="font-size: 11px; height: auto;">' +
                                        (isActive ? 'Selected' : 'Deliver Here') +
                                    '</button>' +
                                '</div>';
                    });
                    $('#modal-saved-addresses-list').html(html);
                    $('#modal-saved-addresses-wrapper').show();
                    $('#modal-signin-prompt-wrapper').hide();
                } else if (!res.logged_in) {
                    $('#modal-saved-addresses-wrapper').hide();
                    $('#modal-signin-prompt-wrapper').show();
                } else {
                    $('#modal-saved-addresses-wrapper').hide();
                    $('#modal-signin-prompt-wrapper').hide();
                }
            },
            error: function() {
                $('#modal-saved-addresses-wrapper').hide();
            }
        });
    });

    // Selecting a saved address card
    $(document).on('click', '.saved-addr-card, .btn-select-addr', function(e) {
        e.stopPropagation();
        var $card = $(this).closest('.saved-addr-card');
        var addrId = $card.data('id');
        if (!addrId) return;

        var $btn = $card.find('.btn-select-addr');
        $btn.prop('disabled', true).text('Updating...');

        $.ajax({
            url: '<?= _BASEURL ?>api/set-location.php',
            type: 'POST',
            data: { action: 'select_address', address_id: addrId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    updateHeaderLocationDisplay(res.displayText);
                    $('#pincode-feedback-msg').html('<div class="alert alert-success py-1 px-2 mb-0">' + escapeHtml(res.message) + '</div>');
                    setTimeout(function() {
                        $('#location-modal').modal('hide');
                    }, 700);
                } else {
                    $btn.prop('disabled', false).text('Deliver Here');
                    $('#pincode-feedback-msg').html('<div class="alert alert-danger py-1 px-2 mb-0">' + escapeHtml(res.message || 'Error updating address.') + '</div>');
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Deliver Here');
                $('#pincode-feedback-msg').html('<div class="alert alert-danger py-1 px-2 mb-0">Connection error. Please try again.</div>');
            }
        });
    });

    // Form Submission: Manual Pincode Entry
    $('#form-update-delivery-location').on('submit', function(e) {
        e.preventDefault();
        var pin = $('#input-delivery-pincode').val().trim();
        var $btn = $('#btn-submit-pincode');
        var $msg = $('#pincode-feedback-msg');

        if (!/^[0-9]{6}$/.test(pin)) {
            $msg.html('<div class="alert alert-danger py-1 px-2 mb-0">Please enter a valid 6-digit PIN code.</div>');
            return;
        }

        $btn.prop('disabled', true).find('span').text('Applying...');
        $msg.html('');

        $.ajax({
            url: '<?= _BASEURL ?>api/set-location.php',
            type: 'POST',
            data: { action: 'set_location', pincode: pin },
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).find('span').text('Apply');
                if (res.success) {
                    updateHeaderLocationDisplay(res.displayText);
                    var alertClass = res.serviceable ? 'alert-success' : 'alert-warning';
                    $msg.html('<div class="alert ' + alertClass + ' py-1 px-2 mb-0">' + escapeHtml(res.message) + '</div>');
                    setTimeout(function() {
                        $('#location-modal').modal('hide');
                    }, 800);
                } else {
                    $msg.html('<div class="alert alert-danger py-1 px-2 mb-0">' + escapeHtml(res.message || 'Invalid PIN code.') + '</div>');
                }
            },
            error: function() {
                $btn.prop('disabled', false).find('span').text('Apply');
                $msg.html('<div class="alert alert-danger py-1 px-2 mb-0">Network error. Please try again.</div>');
            }
        });
    });

    // Auto-detect location button via browser Geolocation
    $('#btn-auto-detect-loc').on('click', function(e) {
        e.preventDefault();
        var $spinner = $('#detect-loc-spinner');
        var $msg = $('#pincode-feedback-msg');

        if (!navigator.geolocation) {
            $msg.html('<div class="alert alert-warning py-1 px-2 mb-0">Geolocation is not supported by your browser. Please enter PIN code manually.</div>');
            return;
        }

        $spinner.show();
        $msg.html('');

        navigator.geolocation.getCurrentPosition(
            function(position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;

                // Call client-side reverse geocoding
                var geoUrl = 'https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=' + lat + '&longitude=' + lon + '&localityLanguage=en';
                fetch(geoUrl)
                    .then(function(response) { return response.json(); })
                    .then(function(data) {
                        $spinner.hide();
                        var detectedPin = (data.postcode || '').replace(/\D/g, '');
                        var detectedCity = data.city || data.locality || data.principalSubdivision || '';

                        if (detectedPin && detectedPin.length === 6) {
                            $('#input-delivery-pincode').val(detectedPin);
                            // Auto submit to save
                            $.ajax({
                                url: '<?= _BASEURL ?>api/set-location.php',
                                type: 'POST',
                                data: { action: 'set_location', pincode: detectedPin, city: detectedCity },
                                dataType: 'json',
                                success: function(res) {
                                    if (res.success) {
                                        updateHeaderLocationDisplay(res.displayText);
                                        $msg.html('<div class="alert alert-success py-1 px-2 mb-0"><i class="icon-check mr-1"></i> Detected: ' + escapeHtml(res.displayText) + '</div>');
                                        setTimeout(function() {
                                            $('#location-modal').modal('hide');
                                        }, 900);
                                    }
                                }
                            });
                        } else {
                            if (detectedCity) {
                                $msg.html('<div class="alert alert-info py-1 px-2 mb-0">Location detected: ' + escapeHtml(detectedCity) + '. Please enter your 6-digit PIN code to confirm.</div>');
                            } else {
                                $msg.html('<div class="alert alert-warning py-1 px-2 mb-0">Could not determine 6-digit PIN code. Please type it manually.</div>');
                            }
                        }
                    })
                    .catch(function(err) {
                        $spinner.hide();
                        $msg.html('<div class="alert alert-warning py-1 px-2 mb-0">Could not lookup PIN code. Please type your 6-digit PIN code manually.</div>');
                    });
            },
            function(error) {
                $spinner.hide();
                var errMsg = 'Unable to retrieve location. Please enter PIN code manually.';
                if (error.code === error.PERMISSION_DENIED) {
                    errMsg = 'Location permission denied. Please enter PIN code manually.';
                }
                $msg.html('<div class="alert alert-warning py-1 px-2 mb-0">' + errMsg + '</div>');
            },
            { timeout: 8000, maximumAge: 60000 }
        );
    });

    // Helper: Update all location text DOM elements across the page
    function updateHeaderLocationDisplay(displayText) {
        if (!displayText) return;
        $('.header-loc-city').text(displayText);
        syncModalBadge();
        // Trigger event in case other components listen
        $(document).trigger('deliveryLocationChanged', [displayText]);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }
});
</script>
