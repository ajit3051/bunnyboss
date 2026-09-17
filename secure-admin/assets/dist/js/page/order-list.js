(function ($) {

    $("#from_date, #to_date").on('keydown', function(e) {
        e.preventDefault();
    });

    // Initialize datepickers
    $("#from_date, #to_date").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto'
    });

    // Restrict "to_date" to not be before "from_date"
    $("#from_date").on('changeDate', function (e) {
        var fromDate = $(this).val();
        $("#to_date").datepicker('setStartDate', fromDate);
    });

    $("#to_date").on('changeDate', function (e) {
        var toDate = $(this).val();
        $("#from_date").datepicker('setEndDate', toDate);
    });

    $("#checkAll").change(function () {
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });

    // Pagination event
    $("body").on("click", ".page-link-v2", function (e) {
        e.preventDefault();
        var pageNo = $(this).attr("href");
        $("#searchForm").find('input[name=page]').val(pageNo);
        $("#searchForm").find('input[name=export]').val('');
        getAjaxResults();
    });

    // Search / Date filter - Search button click
    $("body").on("click", "#btnFilter", function (e) {
        e.preventDefault();

        var statusVal   = $("#payment_status_filter").val();
        var searchVal   = $("#full_search").val();
        var fromDateVal = $("#from_date").val();
        var toDateVal   = $("#to_date").val();

        $("#searchForm").find('input[name=payment_status_filter]').val(statusVal);
        $("#searchForm").find('input[name=search]').val(searchVal);
        $("#searchForm").find('input[name=from_date]').val(fromDateVal);
        $("#searchForm").find('input[name=to_date]').val(toDateVal);
        $("#searchForm").find('input[name=page]').val(1);
        $("#searchForm").find('input[name=export]').val('');

        getAjaxResults();
    });

    // Search / Date filter - Reset button click
    $("body").on("click", "#btnResetFilter", function (e) {
        e.preventDefault();

        $("#payment_status_filter").val('');
        $("#full_search").val('');
        $("#from_date").val('');
        $("#to_date").val('');

        $("#searchForm").find('input[name=payment_status_filter]').val('');
        $("#searchForm").find('input[name=search]').val('');
        $("#searchForm").find('input[name=from_date]').val('');
        $("#searchForm").find('input[name=to_date]').val('');
        $("#searchForm").find('input[name=page]').val(1);
        $("#searchForm").find('input[name=export]').val('');

        getAjaxResults();
    });

    // Auto-trigger filter on payment status change
    $("body").on("change", "#payment_status_filter", function (e) {
        $("#btnFilter").trigger('click');
    });

    // Handle Select All / master checkbox
    $("body").on("change", "#checkAllOrders", function () {
        $(".order-checkbox").prop('checked', $(this).prop("checked"));
        updateDispatchButtonState();
    });

    // Handle individual row checkbox change
    $("body").on("change", ".order-checkbox", function () {
        var allChecked = $(".order-checkbox").length > 0 && $(".order-checkbox:checked").length === $(".order-checkbox").length;
        $("#checkAllOrders").prop('checked', allChecked);
        updateDispatchButtonState();
    });

    // Open Dispatch Confirmation Modal
    $("body").on("click", "#btnDispatchSelected", function (e) {
        e.preventDefault();
        var selectedCount = $(".order-checkbox:checked").length;
        if (selectedCount === 0) return;
        $("#dispatchModalCount").text(selectedCount);
        $("#dispatchConfirmModal").modal('show');
    });

    // Confirm Bulk Dispatch Action
    $("body").on("click", "#btnConfirmDispatchAction", function (e) {
        e.preventDefault();
        var selectedIds = [];
        $(".order-checkbox:checked").each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) return;

        var $btn = $(this);
        $btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            type: 'POST',
            url: 'ajax/order-results',
            data: {
                action: 'bulk_dispatch',
                order_ids: selectedIds
            },
            dataType: 'json',
            success: function (res) {
                $btn.prop("disabled", false).html('<i class="fa fa-check" style="margin-right: 5px;"></i> Yes, Mark Dispatched');
                $("#dispatchConfirmModal").modal('hide');

                if (res.status === 'success') {
                    $("#checkAllOrders").prop('checked', false);
                    updateDispatchButtonState();
                    getAjaxResults();
                } else {
                    alert(res.message || 'Failed to dispatch orders.');
                }
            },
            error: function () {
                $btn.prop("disabled", false).html('<i class="fa fa-check" style="margin-right: 5px;"></i> Yes, Mark Dispatched');
                $("#dispatchConfirmModal").modal('hide');
                alert('An error occurred while updating dispatch status.');
            }
        });
    });

    function updateDispatchButtonState() {
        var selectedCount = $(".order-checkbox:checked").length;
        $("#selectedCount").text(selectedCount);
        if (selectedCount > 0) {
            $("#btnDispatchSelected").prop("disabled", false);
        } else {
            $("#btnDispatchSelected").prop("disabled", true);
        }
    }

    // Open Single Dispatch Status Update Modal
    window.openDispatchModal = function (el) {
        var $el = $(el);
        var orderId = $el.attr("data-order-id") || $el.data("order-id");
        var currentStatus = $el.attr("data-status") || $el.data("status") || "pending";

        currentStatus = $.trim(currentStatus).toLowerCase();
        if (currentStatus === "dispatch") currentStatus = "dispatched";
        if (currentStatus === "out of stock") currentStatus = "out_of_stock";

        $("#singleModalOrderIdDisplay").text(orderId);
        $("#singleModalOrderId").val(orderId);
        $("#singleModalStatusSelect").val(currentStatus);
        $("#updateDispatchStatusModal").modal('show');
    };

    $("body").on("click", ".dispatch-status-trigger", function (e) {
        if (e && e.stopPropagation) e.stopPropagation();
        openDispatchModal(this);
    });

    // Save Single Dispatch Status Action
    $("body").on("click", "#btnSaveSingleDispatchStatus", function (e) {
        e.preventDefault();
        var orderId = $("#singleModalOrderId").val();
        var dispatchStatus = $("#singleModalStatusSelect").val();

        if (!orderId) return;

        var $btn = $(this);
        $btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            type: 'POST',
            url: 'ajax/order-results',
            data: {
                action: 'update_single_dispatch_status',
                order_id: orderId,
                dispatch_status: dispatchStatus
            },
            dataType: 'json',
            success: function (res) {
                $btn.prop("disabled", false).html('<i class="fa fa-save" style="margin-right: 5px;"></i> Update Status');
                $("#updateDispatchStatusModal").modal('hide');

                if (res.status === 'success') {
                    getAjaxResults();
                } else {
                    alert(res.message || 'Failed to update dispatch status.');
                }
            },
            error: function () {
                $btn.prop("disabled", false).html('<i class="fa fa-save" style="margin-right: 5px;"></i> Update Status');
                $("#updateDispatchStatusModal").modal('hide');
                alert('An error occurred while updating dispatch status.');
            }
        });
    });


    // Optional: trigger search on Enter key inside search/date fields
    $("body").on("keypress", "#full_search, #from_date, #to_date", function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#btnFilter").trigger('click');
        }
    });

}(jQuery));

function view_details(id) {
    $('#common-popup').load('popup/view-order-details.php?id=' + id,
        function() {
            $('#common-popup').modal('show');
        });
}

function deleterecord(id) {
    $('#common-popup').load('popup/deleterecord.php',
        function() {
            $('#common-popup').modal('show');
            $('#common-popup').modal('show').one('click', '#confirm', function() {
                $('#deleteid').val(id);
                $('#deleteform').submit();
            });
        });
}

function exportExcel(element, type) {

    $('#common-popup').load('popup/export?type='+type,
       function() {
          $('#common-popup').modal('show');
          $('#common-popup').modal('show').one('click', '#confirm', function() {
             $("#searchForm").find('input[name=export]').val(type);

            getAjaxResults();
             $('#common-popup').modal('hide');
          });
       });
 }

getAjaxResults();
function getAjaxResults() {

    var formData = $("#searchForm").serialize();

    var exp = $("#searchForm").find('input[name=export]').val();

    if(exp){

      const url = 'ajax/order-results?function=get_results&' + formData;
      window.location.href = url; // Redirect to the PHP script to trigger the download
    }

    $("#overlay").fadeIn(100);
    $.ajax({
        type: 'POST',
        url: 'ajax/order-results?function=get_results',
        data: formData,
        success: function (response) {

            let result = response.match(/!DOCTYPE html/);
            if (result == '!DOCTYPE html') {
                location.reload();
            }

            var res = JSON.parse(response);

            if (res.isLogged == 'false') {
                location.reload();
            }
            $("#latestRecord").html(res.htmlData);
            $("#pagination-result").html(res.pagination);
            $("#totalQty").text(res.totalQty);
            $("#currentQty").text(res.currentQty);
            if (res.pendingBalance) {
                $("#pendingBalance").text(res.pendingBalance);
            }

            // Reset checkboxes and button state
            $("#checkAllOrders").prop('checked', false);
            var selectedCount = $(".order-checkbox:checked").length;
            $("#selectedCount").text(selectedCount);
            if (selectedCount > 0) {
                $("#btnDispatchSelected").prop("disabled", false);
            } else {
                $("#btnDispatchSelected").prop("disabled", true);
            }

            $("#overlay").fadeOut(150);

        },
        error: function () {
            $("#overlay").fadeOut(150);
        }
    });
}

function showImageModal(src, title, e) {
    if (e && e.stopPropagation) {
        e.stopPropagation();
    }
    if (!src || src.indexOf('no-image.png') !== -1) {
        return;
    }
    $("#imagePreviewTitle").html('<i class="fa fa-picture-o" style="margin-right: 8px;"></i>' + (title ? title : 'Product Image Preview'));
    $("#imagePreviewSrc").attr('src', src);
    $("#imagePreviewLink").attr('href', src);
    $("#imagePreviewModal").modal('show');
}