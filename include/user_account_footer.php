<?php
if ($is_logged_in): ?>
                    </div><!-- End .col-lg-9 -->
                </div><!-- End .row -->
            <?php endif; ?>

        </div><!-- End .container -->
    </div><!-- End .dashboard-wrapper -->
</main>

<!-- ========================================================================= -->
<!-- ADDRESS MODAL (ADD / EDIT) -->
<!-- ========================================================================= -->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 14px; overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #19978c 0%, #0d6e65 100%); padding: 18px 24px;">
                <h5 class="modal-title text-white font-weight-bold" id="addressModalLabel" style="font-size: 17px;">
                    <i class="icon-map-marker mr-2"></i> Add New Address
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="address-form">
                <div class="modal-body p-4">
                    <div id="address-modal-alert" class="alert d-none" role="alert"></div>

                    <input type="hidden" name="address_id" id="addr_id" value="0">

                    <div class="row">
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold small text-dark">Address Tag / Title *</label>
                            <select class="form-control" name="title" id="addr_title" required style="border-radius: 8px;">
                                <option value="Home">Home</option>
                                <option value="Office">Office / Work</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold small text-dark">First Name *</label>
                            <input type="text" class="form-control" name="first_name" id="addr_first_name" required style="border-radius: 8px;" placeholder="First name">
                        </div>
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold small text-dark">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="addr_last_name" style="border-radius: 8px;" placeholder="Last name">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">Street Address *</label>
                        <input type="text" class="form-control" name="street_address" id="addr_street_address" placeholder="House number, flat, building, street, landmark" required style="border-radius: 8px;">
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold small text-dark">Town / City *</label>
                            <input type="text" class="form-control" name="city" id="addr_city" required style="border-radius: 8px;" placeholder="City">
                        </div>
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold small text-dark">State</label>
                            <input type="text" class="form-control" name="state" id="addr_state" placeholder="e.g. Delhi, Maharashtra" style="border-radius: 8px;">
                        </div>
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold small text-dark">Postcode / PIN *</label>
                            <input type="text" class="form-control" name="postcode" id="addr_postcode" placeholder="6-digit pincode" required maxlength="10" style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 form-group mb-3">
                            <label class="font-weight-bold small text-dark">Contact Mobile Phone *</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text bg-light" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">+91</span></div>
                                <input type="tel" class="form-control" name="phone" id="addr_phone" maxlength="16" required placeholder="10-digit mobile number" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                            </div>
                        </div>
                        <div class="col-sm-6 d-flex align-items-center pt-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="addr_is_default" name="is_default" value="1">
                                <label class="custom-control-label font-weight-bold text-dark small" for="addr_is_default">Set as default delivery address</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="padding: 14px 24px; border-top: 1px solid #eef2f5;">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-dismiss="modal" style="border-radius: 20px;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" id="btn-save-address-submit" style="background-color: #19978c; border-color: #19978c; font-weight: bold; border-radius: 20px;">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- ORDER DETAILS MODAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 14px; overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 18px 24px;">
                <h5 class="modal-title text-white font-weight-bold" id="orderDetailsModalLabel" style="font-size: 17px;">
                    <i class="icon-shopping-cart mr-2"></i> Order Breakdown <span id="modal-order-id"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="modal-order-body">
                <div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading order details...</span></div></div>
            </div>
            <div class="modal-footer bg-light" style="padding: 12px 24px; border-top: 1px solid #eef2f5;">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" style="border-radius: 20px;">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- LIVE ORDER TRACKING MODAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="orderTrackModal" tabindex="-1" role="dialog" aria-labelledby="orderTrackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #19978c 0%, #0d6e65 100%); padding: 18px 24px;">
                <h5 class="modal-title text-white font-weight-bold" id="orderTrackModalLabel" style="font-size: 17px;">
                    <i class="icon-truck mr-2"></i> Live Order Tracking #<span id="modal-track-order-id"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="modal-track-body">
                <div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Fetching shipment status...</span></div></div>
            </div>
            <div class="modal-footer bg-light" style="padding: 12px 24px; border-top: 1px solid #eef2f5;">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" style="border-radius: 20px;">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document.body).ready(function() {

    // -------------------------------------------------------------
    // PROFILE FORM SUBMISSION (BASIC DETAILS)
    // -------------------------------------------------------------
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn-save-profile');
        var $alert = $('#profile-alert');
        
        $btn.prop('disabled', true).html('<i class="icon-refresh icon-spin mr-1"></i> Saving...');
        $alert.addClass('d-none').removeClass('alert-success alert-danger');

        $.ajax({
            url: 'user-api.php',
            type: 'POST',
            data: $(this).serialize() + '&action=update_profile',
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="icon-check mr-1"></i> SAVE CHANGES');
                if (res.success) {
                    $alert.removeClass('d-none').addClass('alert-success').text(res.message);
                    setTimeout(function() { $alert.addClass('d-none'); }, 4000);
                } else {
                    $alert.removeClass('d-none').addClass('alert-danger').text(res.message || 'Error updating profile');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="icon-check mr-1"></i> SAVE CHANGES');
                $alert.removeClass('d-none').addClass('alert-danger').text('Network connection error.');
            }
        });
    });

    // -------------------------------------------------------------
    // ADDRESS MANAGEMENT
    // -------------------------------------------------------------
    $('.btn-open-add-address').on('click', function() {
        $('#address-form')[0].reset();
        $('#addr_id').val('0');
        $('#addressModalLabel').html('<i class="icon-map-marker mr-2"></i> Add New Address');
        $('#address-modal-alert').addClass('d-none');
        $('#addr_phone').val('<?= htmlspecialchars($user['mobile'] ?? '') ?>');
        $('#addressModal').modal('show');
    });

    $(document).on('click', '.btn-edit-address', function() {
        var addrId = $(this).data('id');
        $('#address-modal-alert').addClass('d-none');

        $.ajax({
            url: 'user-api.php',
            type: 'GET',
            data: { action: 'get_address', address_id: addrId },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.address) {
                    var a = res.address;
                    $('#addr_id').val(a.id);
                    $('#addr_title').val(a.title || 'Home');
                    $('#addr_first_name').val(a.first_name);
                    $('#addr_last_name').val(a.last_name);
                    $('#addr_street_address').val(a.street_address);
                    $('#addr_city').val(a.city);
                    $('#addr_state').val(a.state);
                    $('#addr_postcode').val(a.postcode);
                    $('#addr_phone').val(a.phone);
                    $('#addr_is_default').prop('checked', parseInt(a.is_default) === 1);

                    $('#addressModalLabel').html('<i class="icon-edit mr-2"></i> Edit Address');
                    $('#addressModal').modal('show');
                } else {
                    alert(res.message || 'Unable to fetch address details.');
                }
            }
        });
    });

    $('#addr_phone').on('input', function() {
        var raw = $(this).val();
        var cleaned = (typeof window.cleanIndianMobile === 'function') ? window.cleanIndianMobile(raw) : raw.replace(/\D/g, '').slice(0, 10);
        if (raw !== cleaned) {
            $(this).val(cleaned);
        }
        if (cleaned.length === 10) {
            var check = (typeof window.validateIndianMobile === 'function') ? window.validateIndianMobile(cleaned) : { valid: /^[6-9]\d{9}$/.test(cleaned), message: 'Please enter a valid 10-digit mobile number starting with 6-9.' };
            if (check.valid) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        } else {
            $(this).removeClass('is-valid is-invalid');
        }
    });

    $('#addr_phone').on('paste', function() {
        var $this = $(this);
        setTimeout(function() {
            var raw = $this.val();
            var cleaned = (typeof window.cleanIndianMobile === 'function') ? window.cleanIndianMobile(raw) : raw.replace(/\D/g, '').slice(0, 10);
            $this.val(cleaned).trigger('input');
        }, 10);
    });

    $('#address-form').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn-save-address-submit');
        var $alert = $('#address-modal-alert');
        var $phone = $('#addr_phone');

        var rawPhone = $phone.val();
        var phoneCheck = (typeof window.validateIndianMobile === 'function')
            ? window.validateIndianMobile(rawPhone)
            : { valid: /^[6-9]\d{9}$/.test(rawPhone), clean: rawPhone, message: 'Please enter a valid 10-digit mobile number starting with 6-9.' };

        if (!phoneCheck.valid) {
            $phone.removeClass('is-valid').addClass('is-invalid').focus();
            $alert.removeClass('d-none alert-success').addClass('alert-danger').text(phoneCheck.message);
            return;
        }

        $phone.val(phoneCheck.clean).removeClass('is-invalid').addClass('is-valid');
        $btn.prop('disabled', true).html('Saving Address...');
        $alert.addClass('d-none').removeClass('alert-success alert-danger');

        $.ajax({
            url: 'user-api.php',
            type: 'POST',
            data: $(this).serialize() + '&action=save_address',
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).text('Save Address');
                if (res.success) {
                    $alert.removeClass('d-none').addClass('alert-success').text(res.message);
                    setTimeout(function() {
                        $('#addressModal').modal('hide');
                        window.location.reload();
                    }, 700);
                } else {
                    $alert.removeClass('d-none').addClass('alert-danger').text(res.message || 'Error saving address');
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Save Address');
                $alert.removeClass('d-none').addClass('alert-danger').text('Network connection error.');
            }
        });
    });

    $(document).on('click', '.btn-set-default', function() {
        var addrId = $(this).data('id');
        if (!addrId) return;

        $.ajax({
            url: 'user-api.php',
            type: 'POST',
            data: { action: 'set_default_address', address_id: addrId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    window.location.reload();
                } else {
                    alert(res.message || 'Failed to set default address.');
                }
            }
        });
    });

    $(document).on('click', '.btn-delete-address', function() {
        var addrId = $(this).data('id');
        if (!confirm('Are you sure you want to delete this saved address?')) return;

        $.ajax({
            url: 'user-api.php',
            type: 'POST',
            data: { action: 'delete_address', address_id: addrId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    window.location.reload();
                } else {
                    alert(res.message || 'Failed to delete address.');
                }
            }
        });
    });

    // -------------------------------------------------------------
    // VIEW ORDER DETAILS MODAL
    // -------------------------------------------------------------
    $(document).on('click', '.btn-view-order', function() {
        var orderId = $(this).data('order-id');
        $('#modal-order-id').text('#' + orderId);
        $('#modal-order-body').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#orderDetailsModal').modal('show');

        $.ajax({
            url: 'user-api.php',
            type: 'GET',
            data: { action: 'get_order_details', order_id: orderId },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.order) {
                    var ord = res.order;
                    var items = res.items || [];
                    
                    var html = '<div class="row mb-3">' +
                                   '<div class="col-sm-6 mb-2">' +
                                       '<h6 class="font-weight-bold text-dark mb-2">Order Information</h6>' +
                                       '<p class="small text-muted mb-1"><strong>Order ID:</strong> #' + ord.order_id + '</p>' +
                                       '<p class="small text-muted mb-1"><strong>Date:</strong> ' + ord.created_at + '</p>' +
                                       '<p class="small text-muted mb-1"><strong>Payment Method:</strong> ' + ord.payment_method.toUpperCase() + '</p>' +
                                       '<p class="small text-muted mb-1"><strong>Payment Status:</strong> <span class="order-badge badge-pay-' + (ord.payment_status ? ord.payment_status.toLowerCase() : 'pending') + '">' + (ord.payment_status ? ord.payment_status.toUpperCase() : 'PENDING') + '</span></p>' +
                                       '<p class="small text-muted mb-0"><strong>Order Status:</strong> <span class="order-badge badge-status-' + (ord.order_status ? ord.order_status.toLowerCase() : 'pending') + '">' + (ord.order_status ? ord.order_status.toUpperCase() : 'PENDING') + '</span></p>' +
                                   '</div>' +
                                   '<div class="col-sm-6 mb-2">' +
                                       '<h6 class="font-weight-bold text-dark mb-2">Shipping Address</h6>' +
                                       '<p class="small text-dark mb-1 font-weight-bold">' + ord.first_name + ' ' + ord.last_name + '</p>' +
                                       '<p class="small text-muted mb-1">' + ord.street_address + '<br>' + ord.city + ' - ' + ord.postcode + '</p>' +
                                       '<p class="small text-muted mb-0"><i class="icon-phone mr-1"></i>+91-' + ord.phone + '</p>' +
                                   '</div>' +
                               '</div>' +
                               '<h6 class="border-top pt-3 mb-2 font-weight-bold text-dark">Order Items (' + items.length + ')</h6>' +
                               '<div class="table-responsive"><table class="table table-sm align-middle">' +
                               '<thead class="thead-light"><tr><th>Item</th><th>Size</th><th>Price</th><th>Qty</th><th class="text-right">Total</th></tr></thead><tbody>';

                    items.forEach(function(it) {
                        html += '<tr>' +
                                    '<td class="d-flex align-items-center">' +
                                        '<img src="' + it.image_url + '" style="width:40px; height:40px; object-fit:cover; border-radius:6px; margin-right:10px; border: 1px solid #eee;" alt="">' +
                                        '<div><strong class="small text-dark">' + it.product_title + '</strong></div>' +
                                    '</td>' +
                                    '<td>' + (it.size ? it.size : '-') + '</td>' +
                                    '<td>₹' + parseFloat(it.price).toFixed(2) + '</td>' +
                                    '<td>' + it.qty + '</td>' +
                                    '<td class="text-right font-weight-bold">₹' + parseFloat(it.row_total).toFixed(2) + '</td>' +
                                '</tr>';
                    });

                    var subAmt = parseFloat(ord.subtotal) || 0;
                    var grandAmt = parseFloat(ord.grand_total) || 0;
                    var gstAmt = parseFloat(ord.gst_amount) || 0;
                    var gstPercent = 0;

                    items.forEach(function(it) {
                        if (gstAmt === 0 && it.gst_amount) {
                            gstAmt += parseFloat(it.gst_amount) || 0;
                        }
                        if (!gstPercent && it.gst_percent) {
                            gstPercent = parseFloat(it.gst_percent) || 0;
                        }
                    });

                    if (!gstPercent && subAmt > 0 && gstAmt > 0) {
                        gstPercent = Math.round((gstAmt / subAmt) * 100);
                    }

                    var shippingAmt = Math.max(0, grandAmt - (subAmt + gstAmt));
                    var gstLabel = gstPercent > 0 ? ('GST (' + gstPercent + '%):') : 'GST:';
                    var gstHtml = gstAmt > 0 ? '<p class="mb-1 small"><span>' + gstLabel + '</span> <strong>₹' + gstAmt.toFixed(2) + '</strong></p>' : '';
                    var shippingHtml = shippingAmt > 0 ? '<p class="mb-1 small"><span>Shipping:</span> <strong>₹' + shippingAmt.toFixed(2) + '</strong></p>' : '';

                    html += '</tbody></table></div>' +
                            '<div class="border-top pt-2 text-right">' +
                                '<p class="mb-1 small">Subtotal: <strong>₹' + subAmt.toFixed(2) + '</strong></p>' +
                                gstHtml +
                                shippingHtml +
                                '<h5 class="text-dark font-weight-bold mb-0 mt-1 pt-1 border-top" style="color: #19978c !important;">Grand Total: ₹' + grandAmt.toFixed(2) + '</h5>' +
                            '</div>';

                    var awb = ord.courier_awb || ord.delhivery_awb;
                    var courierName = ord.courier_name ? (ord.courier_name.charAt(0).toUpperCase() + ord.courier_name.slice(1)) : 'Courier';
                    if (awb) {
                        html += '<div class="alert alert-info mt-3 py-2 text-center mb-0" style="border-radius: 8px;">' +
                                    '<small><strong>Courier Partner:</strong> ' + courierName + ' &nbsp;|&nbsp; <strong>AWB:</strong> ' + awb + ' &nbsp;|&nbsp; ' +
                                    '<a href="javascript:void(0)" class="alert-link btn-track-order" data-order-id="' + ord.order_id + '" data-dismiss="modal">Track Package &rarr;</a></small>' +
                                '</div>';
                    }

                    $('#modal-order-body').html(html);
                } else {
                    $('#modal-order-body').html('<div class="alert alert-danger">' + (res.message || 'Failed to load order breakdown.') + '</div>');
                }
            },
            error: function() {
                $('#modal-order-body').html('<div class="alert alert-danger">Network error occurred while fetching order details.</div>');
            }
        });
    });

    // -------------------------------------------------------------
    // LIVE ORDER TRACKING MODAL HANDLER
    // -------------------------------------------------------------
    $(document).on('click', '.btn-track-order', function(e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');
        if (!orderId) return;

        $('#modal-track-order-id').text(orderId);
        $('#modal-track-body').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted small font-weight-bold">Fetching live shipment tracking...</p></div>');
        $('#orderTrackModal').modal('show');

        $.ajax({
            url: '<?= _BASEURL ?>track-order.php',
            type: 'GET',
            data: { query: orderId },
            dataType: 'json',
            success: function(data) {
                if (!data.success) {
                    $('#modal-track-body').html('<div class="alert alert-warning mb-0"><i class="icon-exclamation-circle mr-2"></i>' + (data.message || 'Tracking details are not yet available for this order.') + '</div>');
                    return;
                }

                var statusClass = 'badge-info';
                if (data.status_type === 'DL') statusClass = 'badge-success';
                else if (data.status_type === 'IT' || data.status_type === 'UD') statusClass = 'badge-warning';

                var courierTitle = data.courier_name ? (data.courier_name.charAt(0).toUpperCase() + data.courier_name.slice(1)) : 'Courier';
                var html = '<div class="card border-0 bg-light p-3 mb-3" style="border-radius: 8px;">' +
                               '<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">' +
                                   '<div>' +
                                       '<span class="badge badge-dark px-2 py-1 text-uppercase">' + escapeHtml(courierTitle) + '</span> ' +
                                       '<strong class="ml-2 text-dark">AWB: ' + escapeHtml(data.awb || '-') + '</strong>' +
                                   '</div>' +
                                   '<span class="badge ' + statusClass + ' px-3 py-2 font-weight-bold" style="font-size: 13px;">' + escapeHtml(data.status || 'IN TRANSIT') + '</span>' +
                               '</div>' +
                               '<div class="row mt-3 text-center small">' +
                                   '<div class="col-6 border-right"><strong>Origin:</strong> ' + escapeHtml(data.origin || 'Warehouse') + '</div>' +
                                   '<div class="col-6"><strong>Destination:</strong> ' + escapeHtml(data.destination || (data.city ? data.city : 'Customer Address')) + '</div>' +
                               '</div>' +
                           '</div>';

                if (data.scans && data.scans.length > 0) {
                    html += '<h6 class="font-weight-bold text-dark mb-3"><i class="icon-list mr-1"></i> Scan Checkpoints & Updates</h6>' +
                            '<div class="timeline-wrapper px-2 py-2" style="position:relative; padding-left: 20px; border-left: 2px solid #19978c;">';
                    var scansArr = data.scans.slice().reverse();
                    scansArr.forEach(function(scanItem) {
                        var detail = scanItem.ScanDetail || scanItem;
                        var scanText = detail.Scan || detail.status || 'Package Processed';
                        var location = detail.ScannedLocation || detail.location || '';
                        var dateTime = detail.ScanDateTime || detail.timestamp || detail.date || '';

                        html += '<div class="timeline-step mb-3" style="position:relative; padding-left:15px;">' +
                                    '<h6 class="font-weight-bold text-dark mb-0 small">' + escapeHtml(scanText) + '</h6>' +
                                    (location ? '<p class="small text-muted mb-0"><i class="icon-map-marker mr-1"></i>' + escapeHtml(location) + '</p>' : '') +
                                    (dateTime ? '<small class="text-secondary"><i class="icon-clock-o mr-1"></i>' + escapeHtml(dateTime) + '</small>' : '') +
                                '</div>';
                    });
                    html += '</div>';
                } else {
                    html += '<div class="alert alert-info small mb-0"><i class="icon-info-circle mr-1"></i> Order is registered with courier partner. Awaiting location scan updates.</div>';
                }

                $('#modal-track-body').html(html);
            },
            error: function() {
                $('#modal-track-body').html('<div class="alert alert-danger mb-0">Error connecting to courier tracking network.</div>');
            }
        });
    });

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

});
</script>

<?php include(__DIR__ . '/bottom.php'); ?>
