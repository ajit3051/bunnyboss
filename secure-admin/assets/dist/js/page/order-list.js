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
    // Multicheck Filters Tracking
    var lastAppliedPaymentStatus  = $("#hidden_payment_status_filter").val() || '';
    var lastAppliedDispatchStatus = $("#hidden_dispatch_status_filter").val() || '';
    var lastAppliedPaymentMethod  = $("#hidden_payment_method_filter").val() || '';

    // 1. Payment Status Filter Helper
    function updatePaymentStatusFilter(triggerSearch) {
        var selected = [];
        var selectedLabels = [];
        var total = $(".payment-status-check").length;

        $(".payment-status-check:checked").each(function () {
            selected.push($(this).val());
            var lbl = $(this).closest('label').find('span:last').text().trim();
            selectedLabels.push(lbl);
        });

        $("#checkAllPaymentStatus").prop("checked", selected.length === total && total > 0);

        var $badge = $("#paymentStatusBadge");
        if ($badge.length) {
            $badge.text(selected.length);
            if (selected.length > 0 && selected.length < total) {
                $badge.show().css("background-color", "#009688");
                $("#paymentStatusFilterIcon").css("color", "#009688");
                $("#paymentStatusDropdown").css("border-color", "#009688");
                $("#paymentStatusDropdown").attr("title", "Filter active: " + selectedLabels.join(", "));
            } else {
                $badge.hide();
                $("#paymentStatusFilterIcon").css("color", "#777");
                $("#paymentStatusDropdown").css("border-color", "#ccc");
                $("#paymentStatusDropdown").attr("title", "Filter by Payment Status (All)");
            }
        }

        var commaSeparated = selected.join(",");
        $("#hidden_payment_status_filter").val(commaSeparated);

        if (triggerSearch) {
            lastAppliedPaymentStatus = commaSeparated;
            $("#searchForm").find('input[name=page]').val(1);
            getAjaxResults();
        }
    }

    // 2. Dispatch Status Filter Helper
    function updateDispatchStatusFilter(triggerSearch) {
        var selected = [];
        var selectedLabels = [];
        var total = $(".dispatch-status-check").length;

        $(".dispatch-status-check:checked").each(function () {
            selected.push($(this).val());
            var lbl = $(this).closest('label').find('span:last').text().trim();
            selectedLabels.push(lbl);
        });

        $("#checkAllDispatchStatus").prop("checked", selected.length === total && total > 0);

        var $badge = $("#dispatchStatusBadge");
        if ($badge.length) {
            $badge.text(selected.length);
            if (selected.length > 0 && selected.length < total) {
                $badge.show().css("background-color", "#009688");
                $("#dispatchStatusFilterIcon").css("color", "#009688");
                $("#dispatchStatusDropdown").css("border-color", "#009688");
                $("#dispatchStatusDropdown").attr("title", "Filter active: " + selectedLabels.join(", "));
            } else {
                $badge.hide();
                $("#dispatchStatusFilterIcon").css("color", "#777");
                $("#dispatchStatusDropdown").css("border-color", "#ccc");
                $("#dispatchStatusDropdown").attr("title", "Filter by Dispatch Status (All)");
            }
        }

        var commaSeparated = selected.join(",");
        $("#hidden_dispatch_status_filter").val(commaSeparated);

        if (triggerSearch) {
            lastAppliedDispatchStatus = commaSeparated;
            $("#searchForm").find('input[name=page]').val(1);
            getAjaxResults();
        }
    }

    // 3. Payment Method Filter Helper
    function updatePaymentMethodFilter(triggerSearch) {
        var selected = [];
        var selectedLabels = [];
        var total = $(".payment-method-check").length;

        $(".payment-method-check:checked").each(function () {
            selected.push($(this).val());
            var lbl = $(this).closest('label').find('span:last').text().trim();
            selectedLabels.push(lbl);
        });

        $("#checkAllPaymentMethod").prop("checked", selected.length === total && total > 0);

        var $badge = $("#paymentMethodBadge");
        if ($badge.length) {
            $badge.text(selected.length);
            if (selected.length > 0 && selected.length < total) {
                $badge.show().css("background-color", "#009688");
                $("#paymentMethodFilterIcon").css("color", "#009688");
                $("#paymentMethodDropdown").css("border-color", "#009688");
                $("#paymentMethodDropdown").attr("title", "Filter active: " + selectedLabels.join(", "));
            } else {
                $badge.hide();
                $("#paymentMethodFilterIcon").css("color", "#777");
                $("#paymentMethodDropdown").css("border-color", "#ccc");
                $("#paymentMethodDropdown").attr("title", "Filter by Payment Method (All)");
            }
        }

        var commaSeparated = selected.join(",");
        $("#hidden_payment_method_filter").val(commaSeparated);

        if (triggerSearch) {
            lastAppliedPaymentMethod = commaSeparated;
            $("#searchForm").find('input[name=page]').val(1);
            getAjaxResults();
        }
    }

    function revertPaymentStatusCheckboxes() {
        var applied = (lastAppliedPaymentStatus || '').split(',').filter(Boolean);
        $(".payment-status-check").each(function () {
            $(this).prop("checked", applied.indexOf($(this).val()) !== -1);
        });
        var total = $(".payment-status-check").length;
        var checked = $(".payment-status-check:checked").length;
        $("#checkAllPaymentStatus").prop("checked", checked === total && total > 0);
    }

    function revertDispatchStatusCheckboxes() {
        var applied = (lastAppliedDispatchStatus || '').split(',').filter(Boolean);
        $(".dispatch-status-check").each(function () {
            $(this).prop("checked", applied.indexOf($(this).val()) !== -1);
        });
        var total = $(".dispatch-status-check").length;
        var checked = $(".dispatch-status-check:checked").length;
        $("#checkAllDispatchStatus").prop("checked", checked === total && total > 0);
    }

    function revertPaymentMethodCheckboxes() {
        var applied = (lastAppliedPaymentMethod || '').split(',').filter(Boolean);
        $(".payment-method-check").each(function () {
            $(this).prop("checked", applied.indexOf($(this).val()) !== -1);
        });
        var total = $(".payment-method-check").length;
        var checked = $(".payment-method-check:checked").length;
        $("#checkAllPaymentMethod").prop("checked", checked === total && total > 0);
    }

    function closeAllDropdownsAndRevert() {
        if ($("#paymentStatusMenu").closest(".multicheck-dropdown").hasClass("open")) {
            revertPaymentStatusCheckboxes();
        }
        if ($("#dispatchStatusMenu").closest(".multicheck-dropdown").hasClass("open")) {
            revertDispatchStatusCheckboxes();
        }
        if ($("#paymentMethodMenu").closest(".multicheck-dropdown").hasClass("open")) {
            revertPaymentMethodCheckboxes();
        }
        $(".multicheck-dropdown").removeClass("open");
    }

    // Common Dropdown Toggle
    $("body").on("click", "#paymentStatusDropdown, #dispatchStatusDropdown, #paymentMethodDropdown", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $dd = $(this).closest(".multicheck-dropdown");
        var isOpen = $dd.hasClass("open");
        closeAllDropdownsAndRevert();
        if (!isOpen) {
            $dd.addClass("open");
        }
    });

    // Prevent clicks inside any filter menu from closing it
    $("body").on("click", ".multicheck-dropdown .dropdown-menu", function (e) {
        e.stopPropagation();
    });

    // Close on click outside without applying (revert changes)
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".multicheck-dropdown").length) {
            closeAllDropdownsAndRevert();
        }
    });

    // Close buttons inside menus without applying (revert changes)
    $("body").on("click", "#btnClosePaymentStatusMenu, #btnCloseDispatchStatusMenu, #btnClosePaymentMethodMenu", function (e) {
        e.preventDefault();
        e.stopPropagation();
        closeAllDropdownsAndRevert();
    });

    // Apply Filter buttons - Only here is search triggered!
    $("body").on("click", "#btnApplyPaymentStatusFilter", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".multicheck-dropdown").removeClass("open");
        updatePaymentStatusFilter(true);
    });

    $("body").on("click", "#btnApplyDispatchStatusFilter", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".multicheck-dropdown").removeClass("open");
        updateDispatchStatusFilter(true);
    });

    $("body").on("click", "#btnApplyPaymentMethodFilter", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".multicheck-dropdown").removeClass("open");
        updatePaymentMethodFilter(true);
    });

    // Payment Status Checkbox Events (Local selection only, search triggers on Apply)
    $("body").on("change", "#checkAllPaymentStatus", function () {
        var isChecked = $(this).prop("checked");
        $(".payment-status-check").prop("checked", isChecked);
    });

    $("body").on("click", "#clearPaymentStatus", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".payment-status-check, #checkAllPaymentStatus").prop("checked", false);
    });

    $("body").on("change", ".payment-status-check", function () {
        var total = $(".payment-status-check").length;
        var checked = $(".payment-status-check:checked").length;
        $("#checkAllPaymentStatus").prop("checked", checked === total && total > 0);
    });

    // Dispatch Status Checkbox Events (Local selection only, search triggers on Apply)
    $("body").on("change", "#checkAllDispatchStatus", function () {
        var isChecked = $(this).prop("checked");
        $(".dispatch-status-check").prop("checked", isChecked);
    });

    $("body").on("click", "#clearDispatchStatus", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".dispatch-status-check, #checkAllDispatchStatus").prop("checked", false);
    });

    $("body").on("change", ".dispatch-status-check", function () {
        var total = $(".dispatch-status-check").length;
        var checked = $(".dispatch-status-check:checked").length;
        $("#checkAllDispatchStatus").prop("checked", checked === total && total > 0);
    });

    // Payment Method Checkbox Events (Local selection only, search triggers on Apply)
    $("body").on("change", "#checkAllPaymentMethod", function () {
        var isChecked = $(this).prop("checked");
        $(".payment-method-check").prop("checked", isChecked);
    });

    $("body").on("click", "#clearPaymentMethod", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".payment-method-check, #checkAllPaymentMethod").prop("checked", false);
    });

    $("body").on("change", ".payment-method-check", function () {
        var total = $(".payment-method-check").length;
        var checked = $(".payment-method-check:checked").length;
        $("#checkAllPaymentMethod").prop("checked", checked === total && total > 0);
    });

    // Initial sync on page load
    updatePaymentStatusFilter(false);
    updateDispatchStatusFilter(false);
    updatePaymentMethodFilter(false);

    // Search / Date filter - Search button click
    $("body").on("click", "#btnFilter", function (e) {
        e.preventDefault();

        updatePaymentStatusFilter(false);
        updateDispatchStatusFilter(false);
        updatePaymentMethodFilter(false);

        var payStatusVal   = $("#hidden_payment_status_filter").val();
        var dispStatusVal  = $("#hidden_dispatch_status_filter").val();
        var payMethodVal   = $("#hidden_payment_method_filter").val();

        lastAppliedPaymentStatus  = payStatusVal;
        lastAppliedDispatchStatus = dispStatusVal;
        lastAppliedPaymentMethod  = payMethodVal;

        var searchVal   = $("#full_search").val();
        var fromDateVal = $("#from_date").val();
        var toDateVal   = $("#to_date").val();

        $("#searchForm").find('input[name=payment_status_filter]').val(payStatusVal);
        $("#searchForm").find('input[name=dispatch_status_filter]').val(dispStatusVal);
        $("#searchForm").find('input[name=payment_method_filter]').val(payMethodVal);
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

        // Reset Payment Status (all unchecked -> All)
        $(".payment-status-check, #checkAllPaymentStatus").prop("checked", false);
        updatePaymentStatusFilter(false);
        lastAppliedPaymentStatus = "";

        // Reset Dispatch Status (all unchecked -> All)
        $(".dispatch-status-check, #checkAllDispatchStatus").prop("checked", false);
        updateDispatchStatusFilter(false);
        lastAppliedDispatchStatus = "";

        // Reset Payment Method (all unchecked -> All)
        $(".payment-method-check, #checkAllPaymentMethod").prop("checked", false);
        updatePaymentMethodFilter(false);
        lastAppliedPaymentMethod = "";

        $("#full_search").val('');
        $("#from_date").val('');
        $("#to_date").val('');

        $("#searchForm").find('input[name=search]').val('');
        $("#searchForm").find('input[name=from_date]').val('');
        $("#searchForm").find('input[name=to_date]').val('');
        $("#searchForm").find('input[name=payment_status_filter]').val('');
        $("#searchForm").find('input[name=dispatch_status_filter]').val('');
        $("#searchForm").find('input[name=payment_method_filter]').val('');
        $("#searchForm").find('input[name=page]').val(1);
        $("#searchForm").find('input[name=export]').val('');

        getAjaxResults();
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
        var itemId = $el.attr("data-item-id") || $el.data("item-id") || "";
        var currentStatus = $el.attr("data-status") || $el.data("status") || "pending";

        currentStatus = $.trim(currentStatus).toLowerCase();
        if (currentStatus === "dispatch") currentStatus = "dispatched";
        if (currentStatus === "out of stock") currentStatus = "out_of_stock";

        $("#singleModalOrderIdDisplay").text(orderId);
        $("#singleModalOrderId").val(orderId);
        $("#singleModalItemId").val(itemId);
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
        var itemId = $("#singleModalItemId").val();
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
                item_id: itemId,
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