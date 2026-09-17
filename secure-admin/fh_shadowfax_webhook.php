<?php include_once("include/config.php"); ?>
<?php include('include/header.php'); ?>
<link rel="stylesheet" href="<?= _BASEURL ?>assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
<style>
   .datepicker { z-index: 9999 !important; }
   .webhook-url-box {
       background: #f8f9fa;
       border: 1px solid #e2e8f0;
       border-radius: 6px;
       padding: 12px 15px;
       margin-bottom: 15px;
       position: relative;
   }
   .webhook-url-text {
       font-family: monospace;
       font-weight: bold;
       font-size: 13px;
       color: #009688;
       word-break: break-all;
   }
   .copy-btn {
       padding: 3px 10px;
       font-size: 12px;
       font-weight: 600;
       border-radius: 4px;
   }
   .shadowfax-card {
       background: #fff;
       border: 1px solid #e4e5e7;
       border-radius: 6px;
       box-shadow: 0 2px 8px rgba(0,0,0,0.04);
       margin-bottom: 20px;
       overflow: hidden;
   }
   .shadowfax-card-header {
       background: #fdfdfd;
       border-bottom: 1px solid #eee;
       padding: 12px 20px;
       display: flex;
       align-items: center;
       justify-content: space-between;
   }
   .shadowfax-card-body {
       padding: 20px;
   }
   .section-subtitle {
       font-size: 11px;
       font-weight: 700;
       text-transform: uppercase;
       letter-spacing: 0.8px;
       color: #718096;
       margin-bottom: 12px;
   }
</style>

<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading">
               <div class="panel-title" style="display: flex; align-items: center; justify-content: space-between;">
                  <span style="font-size: 16px; font-weight: 600; color: #333;">
                     <i class="fa fa-truck text-primary" style="margin-right: 8px;"></i>
                     <span>Shadowfax Courier Webhook Integration</span>
                  </span>
                  <span class="label label-success" style="padding: 6px 12px; font-size: 12px; font-weight: bold;">
                     <i class="fa fa-check-circle" style="margin-right: 4px;"></i> Listener Active
                  </span>
               </div>
            </div>
            <div class="panel-body">
               
               <!-- Shadowfax MAX360 Webhook Configuration Details Card (Matching Shadowfax Portal Setup) -->
               <div class="shadowfax-card">
                  <div class="shadowfax-card-header">
                     <h4 style="margin: 0; font-size: 15px; font-weight: bold; color: #2d3748;">
                        <i class="fa fa-cogs" style="margin-right: 6px; color: #009688;"></i> Shadowfax Webhook Push URLs (MAX360 / Dale Engine)
                     </h4>
                     <div>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#simulateWebhookModal" style="background-color: #009688; border-color: #009688; font-weight: 600;">
                           <i class="fa fa-paper-plane" style="margin-right: 5px;"></i> Simulate / Test Webhook
                        </button>
                     </div>
                  </div>
                  <div class="shadowfax-card-body">
                     <div class="row">
                        <!-- Staging Details -->
                        <div class="col-md-6">
                           <div class="section-subtitle"><i class="fa fa-flask" style="margin-right: 4px;"></i> Staging Details</div>
                           <div class="webhook-url-box">
                              <label style="font-weight: 600; font-size: 12px; margin-bottom: 4px; color: #4a5568;">Client Push URL (Local / Test):</label>
                              <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                                 <span class="webhook-url-text" id="stagingPushUrl"><?= _BASEURL ?>api/shadowfax_webhook.php</span>
                                 <button type="button" class="btn btn-default btn-xs copy-btn" onclick="copyToClipboard('stagingPushUrl')">
                                    <i class="fa fa-copy"></i> Copy
                                 </button>
                              </div>
                              <div style="margin-top: 8px; font-size: 12px; color: #718096;">
                                 <strong>Authorisation Present:</strong> <span class="label label-default">Optional (Bearer Token / None)</span>
                              </div>
                           </div>
                        </div>

                        <!-- Production Details -->
                        <div class="col-md-6">
                           <div class="section-subtitle"><i class="fa fa-rocket" style="margin-right: 4px;"></i> Production Details</div>
                           <div class="webhook-url-box" style="background: #f0fff4; border-color: #c6f6d5;">
                              <label style="font-weight: 600; font-size: 12px; margin-bottom: 4px; color: #22543d;">Client Push URL (Production Live):</label>
                              <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                                 <span class="webhook-url-text" id="prodPushUrl" style="color: #276749;">https://bunnyboss.in/api/shadowfax_webhook.php</span>
                                 <button type="button" class="btn btn-success btn-xs copy-btn" onclick="copyToClipboard('prodPushUrl')">
                                    <i class="fa fa-copy"></i> Copy
                                 </button>
                              </div>
                              <div style="margin-top: 8px; font-size: 12px; color: #2f855a;">
                                 <strong>Authorisation Present:</strong> <span class="label label-success">Configured & Ready</span>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="alert alert-info" style="margin-bottom: 0; margin-top: 5px; padding: 10px 15px; font-size: 13px;">
                        <i class="fa fa-info-circle" style="margin-right: 6px; font-size: 15px;"></i>
                        <strong>Shadowfax Setup Note:</strong> Paste the <strong>Production Client Push URL</strong> into your Shadowfax MAX360 / Dale Merchant Dashboard under <em>Webhooks > Add New Webhook</em>. Status updates (Delivered, In Transit, Out for Delivery, Cancelled) will automatically update your order list in real-time.
                     </div>
                  </div>
               </div>

               <!-- Webhook Logs Table Section -->
               <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                  <h4 style="margin: 0; font-weight: bold; font-size: 16px;">
                     <i class="fa fa-list-alt" style="margin-right: 6px; color: #009688;"></i> Shadowfax Webhook Audit Logs (<span id="totalLogCount">0</span>)
                  </h4>
                  <button type="button" class="btn btn-danger btn-sm" id="btnClearLogs" style="font-weight: 600;">
                     <i class="fa fa-trash" style="margin-right: 5px;"></i> Clear Webhook Logs
                  </button>
               </div>

               <!-- Search & Filter Controls -->
               <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; background: #f8f9fa; padding: 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                  <div style="min-width: 150px;">
                     <select class="form-control" name="status_filter" id="status_filter" style="height: 34px;">
                        <option value="">All Statuses</option>
                        <option value="delivered">Delivered (DL)</option>
                        <option value="out_for_delivery">Out for Delivery (OFD)</option>
                        <option value="in_transit">In Transit / Picked Up (IT)</option>
                        <option value="rto">RTO / Return (RTO)</option>
                        <option value="cancelled">Cancelled (CAN)</option>
                     </select>
                  </div>
                  <div style="flex: 1; min-width: 200px;">
                     <input type="text" class="form-control" name="search" id="full_search" placeholder="Search by Order ID, AWB, Location, Remarks..." style="height: 34px;">
                  </div>
                  <div style="min-width: 120px;">
                     <input type="text" class="form-control" name="from_date" id="from_date" placeholder="From Date" autocomplete="off" style="height: 34px;">
                  </div>
                  <div style="min-width: 120px;">
                     <input type="text" class="form-control" name="to_date" id="to_date" placeholder="To Date" autocomplete="off" style="height: 34px;">
                  </div>
                  <button type="button" class="btn btn-primary btn-sm" id="btnFilter" style="background-color: #009688; border-color: #009688; height: 34px; padding: 6px 16px; font-weight: bold;"><i class="fa fa-search"></i> Search</button>
                  <button type="button" class="btn btn-default btn-sm" id="btnResetFilter" style="height: 34px; padding: 6px 14px;"><i class="fa fa-refresh"></i> Reset</button>
               </div>

               <form name="searchForm" id="searchForm" method="post" class="search-form" action="" autocomplete="off">
                  <input type="hidden" name="page" value="1" />
                  <input type="hidden" name="sortOrder" value="DESC" />
                  <input type="hidden" name="sortField" value="id" />
                  <input type="hidden" name="status_filter" id="hidden_status_filter" value="" />
                  <input type="hidden" name="search" id="hidden_search" value="" />
                  <input type="hidden" name="from_date" id="hidden_from_date" value="" />
                  <input type="hidden" name="to_date" id="hidden_to_date" value="" />
               </form>

               <!-- Table View -->
               <div class="table-responsive" style="position: relative; min-height: 200px;">
                  <div id="overlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 99; text-align: center;">
                     <div style="position: absolute; top: 80px; left: 50%; transform: translateX(-50%); text-align: center;">
                        <i class="fa fa-spinner fa-spin fa-3x fa-fw" style="color: #009688;"></i>
                        <p style="margin-top: 10px; font-weight: bold; color: #333; font-size: 14px;">Loading Webhook Logs...</p>
                     </div>
                  </div>
                  <table class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th style="width: 50px;">SrNo</th>
                           <th>Order ID</th>
                           <th>AWB Number</th>
                           <th>Event Status</th>
                           <th>Code</th>
                           <th>Location</th>
                           <th>Remarks / Details</th>
                           <th>IP Address</th>
                           <th>Received Date</th>
                           <th class="text-center">Payload</th>
                        </tr>
                     </thead>
                     <tbody id="latestRecord">
                        
                     </tbody>
                  </table>
               </div>

               <div id="pagination-result"></div>

            </div>
         </div>
      </div>
   </div>
</section>

<!-- Webhook Simulator Modal -->
<div class="modal fade" id="simulateWebhookModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
   <div class="modal-dialog">
      <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.5);">
         <div class="modal-header" style="background-color: #009688; color: white; padding: 12px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.9; font-size: 24px;">&times;</button>
            <h4 class="modal-title" style="margin: 0; font-size: 16px; font-weight: 600;"><i class="fa fa-paper-plane" style="margin-right: 8px;"></i>Simulate Shadowfax Webhook Update</h4>
         </div>
         <div class="modal-body" style="padding: 20px;">
            <form id="simulateWebhookForm">
               <div class="form-group">
                  <label style="font-weight: 600;">Order ID (Client Order ID):</label>
                  <input type="text" class="form-control" id="sim_order_id" placeholder="e.g. 756 or 755" value="">
               </div>
               <div class="form-group">
                  <label style="font-weight: 600;">AWB Number:</label>
                  <input type="text" class="form-control" id="sim_awb_number" placeholder="e.g. SF123456789" value="">
               </div>
               <div class="form-group">
                  <label style="font-weight: 600;">Event Status:</label>
                  <select class="form-control" id="sim_status">
                     <option value="delivered">Delivered (DL)</option>
                     <option value="out_for_delivery">Out for Delivery (OFD)</option>
                     <option value="in_transit">In Transit (IT)</option>
                     <option value="rto">Return to Origin (RTO)</option>
                     <option value="cancelled">Cancelled (CAN)</option>
                  </select>
               </div>
               <div class="form-group">
                  <label style="font-weight: 600;">Hub / Location:</label>
                  <input type="text" class="form-control" id="sim_location" value="Delhi Hub / Customer Address">
               </div>
               <div class="form-group">
                  <label style="font-weight: 600;">Remarks / Comment:</label>
                  <input type="text" class="form-control" id="sim_remarks" value="Simulated webhook update from admin panel">
               </div>
            </form>
         </div>
         <div class="modal-footer" style="background-color: #f8f9fa; padding: 10px 20px;">
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" id="btnSendSimulateWebhook" style="background-color: #009688; border-color: #009688;"><i class="fa fa-send" style="margin-right: 5px;"></i> Send Test Webhook</button>
         </div>
      </div>
   </div>
</div>

<!-- Raw Payload Viewer Modal -->
<div class="modal fade" id="payloadViewerModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
   <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.5);">
         <div class="modal-header" style="background-color: #2d3748; color: white; padding: 12px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.9; font-size: 24px;">&times;</button>
            <h4 class="modal-title" style="margin: 0; font-size: 16px; font-weight: 600;"><i class="fa fa-code" style="margin-right: 8px;"></i>Raw Shadowfax Webhook Payload</h4>
         </div>
         <div class="modal-body" style="background-color: #1a202c; color: #63b3ed; padding: 20px; font-family: monospace; font-size: 13px;">
            <pre id="payloadContent" style="background: transparent; color: #63b3ed; border: none; font-family: monospace; white-space: pre-wrap; word-break: break-all; margin: 0;"></pre>
         </div>
         <div class="modal-footer" style="background-color: #f8f9fa; padding: 10px 20px;">
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>

<?php include('include/footer-2.php'); ?>
<script src="<?= _BASEURL ?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script>
(function ($) {
    $("#from_date, #to_date").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    // Copy to clipboard helper
    window.copyToClipboard = function(elementId) {
        var text = $("#" + elementId).text();
        var tempInput = $("<input>");
        $("body").append(tempInput);
        tempInput.val(text).select();
        document.execCommand("copy");
        tempInput.remove();
        alert("Copied URL to clipboard: " + text);
    };

    // Filter Trigger
    $("body").on("click", "#btnFilter", function (e) {
        e.preventDefault();
        $("#searchForm").find('input[name=status_filter]').val($("#status_filter").val());
        $("#searchForm").find('input[name=search]').val($("#full_search").val());
        $("#searchForm").find('input[name=from_date]').val($("#from_date").val());
        $("#searchForm").find('input[name=to_date]').val($("#to_date").val());
        $("#searchForm").find('input[name=page]').val(1);
        getAjaxResults();
    });

    // Reset Filter
    $("body").on("click", "#btnResetFilter", function (e) {
        e.preventDefault();
        $("#status_filter").val('');
        $("#full_search").val('');
        $("#from_date").val('');
        $("#to_date").val('');
        $("#searchForm").find('input[name=status_filter]').val('');
        $("#searchForm").find('input[name=search]').val('');
        $("#searchForm").find('input[name=from_date]').val('');
        $("#searchForm").find('input[name=to_date]').val('');
        $("#searchForm").find('input[name=page]').val(1);
        getAjaxResults();
    });

    // Pagination link
    $("body").on("click", ".page-link-v2", function (e) {
        e.preventDefault();
        var pageNo = $(this).attr("href");
        $("#searchForm").find('input[name=page]').val(pageNo);
        getAjaxResults();
    });

    // View Payload Modal
    $("body").on("click", ".btn-view-payload", function (e) {
        e.preventDefault();
        var rawPayload = $(this).attr("data-payload");
        try {
            var parsed = JSON.parse(rawPayload);
            $("#payloadContent").text(JSON.stringify(parsed, null, 4));
        } catch (err) {
            $("#payloadContent").text(rawPayload);
        }
        $("#payloadViewerModal").modal('show');
    });

    // Simulate Webhook Submit
    $("body").on("click", "#btnSendSimulateWebhook", function (e) {
        e.preventDefault();
        var orderId   = $("#sim_order_id").val();
        var awbNumber = $("#sim_awb_number").val();
        var status    = $("#sim_status").val();
        var location  = $("#sim_location").val();
        var remarks   = $("#sim_remarks").val();

        if (!orderId && !awbNumber) {
            alert("Please enter Order ID or AWB Number to test simulation.");
            return;
        }

        var $btn = $(this);
        $btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

        $.ajax({
            type: 'POST',
            url: 'ajax/shadowfax-webhook-results.php',
            data: {
                action: 'simulate_webhook',
                order_id: orderId,
                awb_number: awbNumber,
                status: status,
                location: location,
                remarks: remarks
            },
            dataType: 'json',
            success: function (res) {
                $btn.prop("disabled", false).html('<i class="fa fa-send" style="margin-right: 5px;"></i> Send Test Webhook');
                $("#simulateWebhookModal").modal('hide');
                if (res.status === 'success') {
                    alert(res.message || "Simulated webhook executed successfully.");
                    getAjaxResults();
                } else {
                    alert(res.message || "Simulation failed.");
                }
            },
            error: function () {
                $btn.prop("disabled", false).html('<i class="fa fa-send" style="margin-right: 5px;"></i> Send Test Webhook');
                $("#simulateWebhookModal").modal('hide');
                alert("An error occurred during webhook simulation.");
            }
        });
    });

    // Clear Logs Action
    $("body").on("click", "#btnClearLogs", function (e) {
        e.preventDefault();
        if (!confirm("Are you sure you want to clear all Shadowfax webhook logs?")) {
            return;
        }
        $.ajax({
            type: 'POST',
            url: 'ajax/shadowfax-webhook-results.php',
            data: { action: 'clear_logs' },
            dataType: 'json',
            success: function (res) {
                if (res.status === 'success') {
                    getAjaxResults();
                } else {
                    alert(res.message || "Failed to clear logs.");
                }
            }
        });
    });

    getAjaxResults();

    function getAjaxResults() {
        var formData = $("#searchForm").serialize();
        $("#overlay").fadeIn(100);
        $.ajax({
            type: 'POST',
            url: 'ajax/shadowfax-webhook-results.php',
            data: formData,
            success: function (response) {
                var res = JSON.parse(response);
                $("#latestRecord").html(res.htmlData);
                $("#pagination-result").html(res.pagination);
                $("#totalLogCount").text(res.totalLogs || 0);
                $("#overlay").fadeOut(150);
            },
            error: function () {
                $("#overlay").fadeOut(150);
            }
        });
    }

}(jQuery));
</script>
