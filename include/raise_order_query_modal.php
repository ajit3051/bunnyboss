<!-- ========================================================================= -->
<!-- RAISE ORDER QUERY MODAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="raiseOrderQueryModal" tabindex="-1" role="dialog" aria-labelledby="raiseOrderQueryModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 14px; overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 16px 24px;">
                <h5 class="modal-title text-white font-weight-bold" id="raiseOrderQueryModalLabel" style="font-size: 17px; margin: 0;">
                    <i class="icon-question-circle mr-2"></i> Raise Query for Order #<span id="query-modal-order-id-title"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div id="query-modal-alert" class="alert d-none" role="alert"></div>

                <!-- Existing Queries for this order (if any) -->
                <div id="previous-queries-container" class="mb-4" style="display: none;">
                    <h6 class="font-weight-bold text-dark mb-2" style="font-size: 14px;">
                        <i class="icon-comments text-warning mr-1"></i> Previously Raised Queries for this Order:
                    </h6>
                    <div id="previous-queries-list" class="d-flex flex-column gap-2" style="max-height: 220px; overflow-y: auto; gap: 10px;"></div>
                    <hr class="my-3">
                </div>

                <!-- Query Submission Form -->
                <form id="raise-order-query-form">
                    <input type="hidden" name="order_id" id="query_order_id" value="">
                    <input type="hidden" name="action" value="raise_order_query">

                    <h6 class="font-weight-bold text-dark mb-3" style="font-size: 14px;">
                        <i class="icon-pencil text-primary mr-1"></i> Submit a New Query / Complaint:
                    </h6>

                    <div class="row">
                        <div class="col-sm-6 form-group mb-3">
                            <label class="font-weight-bold small text-dark mb-1">Issue Category <span class="text-danger">*</span></label>
                            <select name="issue_type" id="query_issue_type" class="form-control" required style="border-radius: 8px; height: 40px; font-size: 13px;">
                                <option value="">-- Select Nature of Issue --</option>
                                <option value="Delivery Delay / Tracking Issue">Delivery Delay / Tracking Issue</option>
                                <option value="Damaged or Defective Item Received">Damaged or Defective Item Received</option>
                                <option value="Wrong Item or Size Received">Wrong Item or Size Received</option>
                                <option value="Payment / Refund Status Query">Payment / Refund Status Query</option>
                                <option value="Cancel or Return Request">Cancel or Return Request</option>
                                <option value="Size / Exchange Query">Size / Exchange Query</option>
                                <option value="Other Order Issue">Other Order Issue</option>
                            </select>
                        </div>
                        <div class="col-sm-6 form-group mb-3">
                            <label class="font-weight-bold small text-dark mb-1">Subject (Optional)</label>
                            <input type="text" name="subject" id="query_subject" class="form-control" placeholder="Brief summary of your query" style="border-radius: 8px; height: 40px; font-size: 13px;">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark mb-1">Describe your query / problem in detail <span class="text-danger">*</span></label>
                        <textarea name="message" id="query_message" rows="3" class="form-control" required placeholder="Please provide specific details so our support team can assist you as quickly as possible..." style="border-radius: 8px; font-size: 13px;"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group mb-2">
                            <label class="font-weight-bold small text-dark mb-1">Your Name</label>
                            <input type="text" name="customer_name" id="query_cust_name" class="form-control form-control-sm" placeholder="Your full name" style="border-radius: 8px;">
                        </div>
                        <div class="col-sm-4 form-group mb-2">
                            <label class="font-weight-bold small text-dark mb-1">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="customer_phone" id="query_cust_phone" class="form-control form-control-sm" placeholder="10-digit mobile number" required style="border-radius: 8px;">
                        </div>
                        <div class="col-sm-4 form-group mb-2">
                            <label class="font-weight-bold small text-dark mb-1">Email Address</label>
                            <input type="email" name="customer_email" id="query_cust_email" class="form-control form-control-sm" placeholder="Email address" style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="p-2 rounded mt-2 small text-muted" style="background: #fef3c7; border: 1px solid #fde68a; color: #92400e !important;">
                        <i class="icon-info-circle mr-1"></i> Our customer support team typically reviews and responds to order queries within 2–4 business hours.
                    </div>

                    <div class="modal-footer bg-light px-0 pb-0 pt-3 mt-3 border-top" style="border-top: 1px solid #eef2f5;">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" style="border-radius: 20px;">Cancel</button>
                        <button type="submit" class="btn btn-warning btn-sm px-4" id="btn-submit-order-query" style="background-color: #f59e0b; border-color: #f59e0b; color: white; border-radius: 20px; font-weight: 600;">
                            <i class="icon-send mr-1"></i> Submit Query to Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function escapeHtmlSafe(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // -------------------------------------------------------------
    // OPEN RAISE ORDER QUERY MODAL & LOAD PREVIOUS QUERIES
    // -------------------------------------------------------------
    $(document).on('click', '.btn-raise-order-query', function(e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');
        var custName = $(this).data('name') || '';
        var custPhone = $(this).data('phone') || '';
        var custEmail = $(this).data('email') || '';

        $('#query-modal-order-id-title').text(orderId);
        $('#query_order_id').val(orderId);
        if (custName) $('#query_cust_name').val(custName);
        if (custPhone) $('#query_cust_phone').val(custPhone);
        if (custEmail) $('#query_cust_email').val(custEmail);
        $('#query_issue_type').val('');
        $('#query_subject').val('');
        $('#query_message').val('');
        $('#query-modal-alert').addClass('d-none').removeClass('alert-success alert-danger');
        
        // Load existing queries for this order
        var $prevContainer = $('#previous-queries-container');
        var $prevList = $('#previous-queries-list');
        $prevContainer.hide();
        $prevList.empty();

        $.ajax({
            url: '<?= defined("_BASEURL") ? _BASEURL : "" ?>user-api.php',
            type: 'GET',
            data: { action: 'get_order_queries', order_id: orderId },
            dataType: 'json',
            success: function(res) {
                if (res && res.success && res.queries && res.queries.length > 0) {
                    var qHtml = '';
                    res.queries.forEach(function(q) {
                        var statusBadge = '';
                        if (q.status === 'open') {
                            statusBadge = '<span class="badge badge-warning text-dark font-weight-bold" style="font-size: 11px;">OPEN / UNDER REVIEW</span>';
                        } else if (q.status === 'in_progress') {
                            statusBadge = '<span class="badge badge-info text-white font-weight-bold" style="font-size: 11px;">IN PROGRESS</span>';
                        } else if (q.status === 'resolved') {
                            statusBadge = '<span class="badge badge-success text-white font-weight-bold" style="font-size: 11px;">RESOLVED</span>';
                        } else {
                            statusBadge = '<span class="badge badge-secondary text-white font-weight-bold" style="font-size: 11px;">' + q.status.toUpperCase() + '</span>';
                        }

                        qHtml += '<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 13px;">' +
                                    '<div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">' +
                                        '<strong>' + escapeHtmlSafe(q.issue_type) + '</strong>' +
                                        '<div>' + statusBadge + ' <small class="text-muted ml-2">' + q.created_at.substring(0, 16) + '</small></div>' +
                                    '</div>' +
                                    '<p class="mb-1 text-dark" style="white-space: pre-wrap;">' + escapeHtmlSafe(q.message) + '</p>' +
                                    (q.admin_reply ? (
                                        '<div class="mt-2 p-2 rounded" style="background: #e6f4ea; border: 1px solid #ceead6; color: #137333; font-size: 12px;">' +
                                            '<strong><i class="icon-reply mr-1"></i> Admin Response:</strong> ' + escapeHtmlSafe(q.admin_reply) +
                                        '</div>'
                                    ) : '<div class="small text-muted font-italic mt-1"><i class="icon-clock-o mr-1"></i> Awaiting response from customer care.</div>') +
                                 '</div>';
                    });
                    $prevList.html(qHtml);
                    $prevContainer.slideDown(200);
                }
            }
        });

        $('#raiseOrderQueryModal').modal('show');
    });

    // Auto-update subject when issue type changes
    $('#query_issue_type').on('change', function() {
        var issue = $(this).val();
        var ordId = $('#query_order_id').val();
        if (issue && !$('#query_subject').val()) {
            $('#query_subject').val(issue + ' (Order #' + ordId + ')');
        }
    });

    // Submit Query Form
    $('#raise-order-query-form').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn-submit-order-query');
        var $alert = $('#query-modal-alert');
        var orderId = $('#query_order_id').val();

        $btn.prop('disabled', true).html('<i class="icon-refresh icon-spin mr-1"></i> Submitting Query...');
        $alert.addClass('d-none').removeClass('alert-success alert-danger');

        $.ajax({
            url: '<?= defined("_BASEURL") ? _BASEURL : "" ?>user-api.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="icon-send mr-1"></i> Submit Query to Admin');
                if (res && res.success) {
                    $alert.removeClass('d-none alert-danger').addClass('alert-success')
                          .html('<i class="icon-check mr-1 font-weight-bold"></i> ' + res.message);
                    $('#query_message').val('');
                    $('#query_subject').val('');
                    $('#query_issue_type').val('');

                    // Reload query history inside modal
                    setTimeout(function() {
                        $('.btn-raise-order-query[data-order-id="' + orderId + '"]').first().trigger('click');
                    }, 1200);
                } else {
                    $alert.removeClass('d-none alert-success').addClass('alert-danger')
                          .html('<i class="icon-warning mr-1"></i> ' + ((res && res.message) ? res.message : 'Failed to submit query.'));
                }
            },
            error: function(xhr, status, error) {
                $btn.prop('disabled', false).html('<i class="icon-send mr-1"></i> Submit Query to Admin');
                $alert.removeClass('d-none alert-success').addClass('alert-danger')
                      .html('Network error occurred. Please try again.');
            }
        });
    });
});
</script>
