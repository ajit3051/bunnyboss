<?php
$page_title = "Track Your Order - Bunny Boss";
include_once("include/config.php");

$awb_from_db = ''; 
$courier_from_db = '';
$order_id = 0;
if (isset($_GET['order_id'])) {
    $order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;

    if ($order_id > 0) {
        $db = connect(); 
        $stmt = $db->select("SELECT courier_name, COALESCE(NULLIF(courier_awb, ''), delhivery_awb) AS awb FROM tbl_orders WHERE order_id = ?", 'i', $order_id);
        if ($stmt && $row = $stmt->fetch_assoc()) {
            $awb_from_db = $row['awb'] ?? '';
            $courier_from_db = $row['courier_name'] ?? '';
        }
    }
}
if (empty($awb_from_db) && isset($_GET['waybill'])) {
    $awb_from_db = trim($_GET['waybill']);
}

if ($order_id === 0 && !empty($awb_from_db)) {
    $db = connect();
    $stmt_o = $db->select("SELECT order_id FROM tbl_orders WHERE courier_awb = ? OR delhivery_awb = ? LIMIT 1", 'ss', $awb_from_db, $awb_from_db);
    if ($stmt_o && $ro = $stmt_o->fetch_assoc()) {
        $order_id = (int) $ro['order_id'];
    }
}

include('include/top.php');
?>

<style>
    .track-card {
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        border: none;
        overflow: hidden;
    }
    .track-input-group {
        display: flex;
        gap: 10px;
        max-width: 650px;
        margin: 0 auto;
    }
    .track-input-group input {
        border-radius: 30px;
        padding: 14px 24px;
        font-size: 15px;
        border: 2px solid #e2e8f0;
        transition: border-color 0.2s;
    }
    .track-input-group input:focus {
        border-color: #19978c;
        outline: none;
    }
    .btn-track-submit {
        background: #19978c;
        border-color: #19978c;
        color: #fff;
        border-radius: 30px;
        padding: 12px 30px;
        font-weight: 700;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .btn-track-submit:hover {
        background: #147970;
        border-color: #147970;
        color: #fff;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 16px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.3px;
    }
    .status-delivered { background: #d4f4dd; color: #1a7f37; }
    .status-intransit { background: #fff3cd; color: #997404; }
    .status-pending { background: #eef2f6; color: #475569; }
    .status-failed { background: #fee2e2; color: #b91c1c; }
    
    .timeline {
        position: relative;
        padding-left: 28px;
        margin-top: 24px;
        border-left: 3px solid #19978c;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 22px;
    }
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -35px;
        top: 3px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #19978c;
        border: 3px solid #ffffff;
        box-shadow: 0 0 0 2px #19978c;
    }
    .timeline-date {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }
    .timeline-status {
        font-size: 14px;
        color: #1e293b;
        font-weight: 600;
    }
</style>

<main class="main">
    <div class="page-header text-center" style="background-image: url('<?= _BASEURL ?>assets/images/page-header-bg.jpg');">
        <div class="container">
            <h1 class="page-title font-weight-bold">Track Your Order<span>Real-Time Shipment & Delivery Updates</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Track Order</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="page-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <!-- Tracking Box Card -->
                    <div class="card track-card bg-white p-4 p-md-5 mb-5">
                        <div class="text-center mb-4">
                            <span class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background-color: #e6f5f4; border-radius: 50%; color: #19978c; font-size: 26px;">
                                <i class="icon-truck"></i>
                            </span>
                            <h3 class="font-weight-bold text-dark mb-1" style="font-size: 22px;">Check Order Status</h3>
                            <p class="text-secondary small mb-0">Enter your AWB tracking number or Waybill provided via SMS</p>
                        </div>

                        <form id="track-form" onsubmit="event.preventDefault(); trackOrder();" class="mb-4">
                            <div class="track-input-group flex-column flex-sm-row">
                                <input type="text" id="waybill_input" class="form-control flex-grow-1" placeholder="Enter AWB / Tracking Number (e.g. 1438...)" value="<?php echo htmlspecialchars($awb_from_db); ?>" required>
                                <button type="submit" class="btn btn-track-submit">
                                    <i class="icon-search mr-1"></i> Track Parcel
                                </button>
                            </div>
                        </form>

                        <!-- Result Area -->
                        <div id="result_area" class="mt-2"></div>

                        <?php if ($order_id > 0): ?>
                        <div class="mt-4 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px; background: #fffdf5; padding: 14px 18px; border-radius: 10px; border: 1px dashed #f59e0b;">
                            <div>
                                <strong class="text-dark d-block" style="font-size: 14px;"><i class="icon-help mr-1 text-warning"></i> Experiencing any issue with Order #<?= $order_id ?>?</strong>
                                <span class="text-muted small">Report delayed delivery, damaged parcel, or wrong item directly to support.</span>
                            </div>
                            <button type="button" class="btn btn-warning btn-sm btn-round btn-raise-order-query px-3 py-2 font-weight-bold" style="background-color: #f59e0b; border-color: #f59e0b; color: #fff;" data-order-id="<?= $order_id ?>">
                                <i class="icon-question-circle mr-1"></i> Raise Query to Admin
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Informational Guide -->
                    <div class="row mb-5">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm p-4 text-center" style="border-radius: 12px; background: #ffffff;">
                                <div class="text-primary mb-2" style="color: #19978c !important; font-size: 24px;"><i class="icon-mobile"></i></div>
                                <h6 class="font-weight-bold text-dark mb-1">Where is my AWB?</h6>
                                <p class="text-secondary small mb-0">Your AWB tracking code is sent via SMS and WhatsApp as soon as your shoes leave our warehouse.</p>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm p-4 text-center" style="border-radius: 12px; background: #ffffff;">
                                <div class="text-primary mb-2" style="color: #19978c !important; font-size: 24px;"><i class="icon-clock"></i></div>
                                <h6 class="font-weight-bold text-dark mb-1">Tracking Not Updating?</h6>
                                <p class="text-secondary small mb-0">Tracking details typically activate within 6 to 12 hours after package handover to the courier.</p>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm p-4 text-center" style="border-radius: 12px; background: #ffffff;">
                                <div class="text-primary mb-2" style="color: #19978c !important; font-size: 24px;"><i class="icon-phone"></i></div>
                                <h6 class="font-weight-bold text-dark mb-1">Need Urgent Help?</h6>
                                <p class="text-secondary small mb-0">Call our customer support at <a href="tel:7428068439" style="color: #19978c; font-weight: bold;">+91 7428068439</a> for immediate shipment assistance.</p>
                            </div>
                        </div>
                    </div>

                </div><!-- End .col-lg-9 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .page-content -->
</main>

<script>
function trackOrder() {
    var waybill = document.getElementById('waybill_input').value.trim();
    var resultArea = document.getElementById('result_area');

    if (!waybill) {
        resultArea.innerHTML = '<div class="alert alert-warning text-center" style="border-radius: 8px;">Please enter an AWB / tracking number.</div>';
        return;
    }

    resultArea.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status" style="color: #19978c !important;"><span class="sr-only">Loading shipment details...</span></div><p class="text-muted small mt-2">Fetching real-time tracking updates...</p></div>';

    fetch('<?= _BASEURL ?>track-order.php?waybill=' + encodeURIComponent(waybill))
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                resultArea.innerHTML = '<div class="alert alert-danger" style="border-radius: 8px;"><i class="icon-info-circle mr-2"></i> ' + (data.message || 'Tracking details not found. Please verify the AWB number.') + '</div>';
                return;
            }

            var statusClass = 'status-pending';
            if (data.status_type === 'DL') statusClass = 'status-delivered';
            else if (data.status_type === 'IT' || data.status_type === 'UD') statusClass = 'status-intransit';
            else if (data.status_type === 'RT' || data.status_type === 'CAN') statusClass = 'status-failed';

            var html = '<div class="p-4 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">';
            html += '<div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-3 border-bottom">';
            html += '<div>';
            html += '<span class="text-muted small d-block">Courier Partner</span>';
            html += '<strong class="text-dark" style="font-size: 16px; text-transform: capitalize; color: #19978c !important;">' + (data.courier_name || 'Delhivery') + '</strong>';
            html += '</div>';
            html += '<div class="text-right">';
            html += '<span class="status-badge ' + statusClass + '">' + (data.status || 'Active') + '</span>';
            html += '</div>';
            html += '</div>';

            html += '<div class="row mb-3 text-secondary small">';
            html += '<div class="col-sm-6 mb-2"><strong>AWB Number:</strong> <span class="text-dark font-weight-bold">' + data.awb + '</span></div>';
            if (data.origin && data.destination) {
                html += '<div class="col-sm-6 mb-2"><strong>Route:</strong> ' + data.origin + ' &rarr; ' + data.destination + '</div>';
            }
            if (data.expected_date) {
                html += '<div class="col-sm-12"><strong>Expected Delivery Date:</strong> <span class="text-success font-weight-bold">' + data.expected_date + '</span></div>';
            }
            html += '</div>';

            if (data.scans && data.scans.length > 0) {
                html += '<h5 class="font-weight-bold text-dark mt-4 mb-2" style="font-size: 16px;">Shipment Activity</h5>';
                html += '<div class="timeline">';
                data.scans.slice().reverse().forEach(function(scan) {
                    var detail = scan.ScanDetail || {};
                    html += '<div class="timeline-item">';
                    html += '<div class="timeline-status">' + (detail.Scan || 'In Transit') + ' ' + (detail.ScannedLocation ? '(' + detail.ScannedLocation + ')' : '') + '</div>';
                    html += '<div class="timeline-date">' + (detail.ScanDateTime || '') + '</div>';
                    html += '</div>';
                });
                html += '</div>';
            } else {
                html += '<div class="alert alert-info small mb-0 mt-3" style="border-radius: 8px;">Order has been booked with the carrier. Live transit scans will appear shortly.</div>';
            }

            html += '</div>';
            resultArea.innerHTML = html;
        })
        .catch(err => {
            resultArea.innerHTML = '<div class="alert alert-danger" style="border-radius: 8px;">Unable to fetch tracking data at this time. Please check your internet connection or try again shortly.</div>';
        });
}

<?php if ($awb_from_db): ?>
window.addEventListener('DOMContentLoaded', trackOrder);
<?php endif; ?>
</script>

<?php 
include('include/raise_order_query_modal.php');
include('include/bottom.php'); 
?>