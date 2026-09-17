<?php
include_once("include/config.php");

$awb_from_db = ''; 
$courier_from_db = '';
if (isset($_GET['order_id'])) {
    $order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;

    if ($order_id > 0) {
        $db = connect(); 
        $stmt = $db->select("SELECT courier_name, COALESCE(NULLIF(courier_awb, ''), delhivery_awb) AS awb FROM tbl_orders WHERE order_id = ? OR id = ?", 'ii', $order_id, $order_id);
        if ($stmt && $row = $stmt->fetch_assoc()) {
            $awb_from_db = $row['awb'] ?? '';
            $courier_from_db = $row['courier_name'] ?? '';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Track Your Order - Bunny Boss</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 40px; }
        .track-box { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        h2 { color: #19978c; }
        input[type=text] { width: 70%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 20px; background: #19978c; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .status-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-weight: bold; margin: 15px 0; }
        .status-delivered { background: #d4f4dd; color: #1a7f37; }
        .status-intransit { background: #fff3cd; color: #997404; }
        .status-pending { background: #f0f0f0; color: #666; }
        .timeline { margin-top: 20px; border-left: 2px solid #19978c; padding-left: 20px; }
        .timeline-item { margin-bottom: 18px; position: relative; }
        .timeline-item::before { content: ''; position: absolute; left: -26px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: #19978c; }
        .timeline-date { font-size: 12px; color: #888; }
        .error-msg { color: #c0392b; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="track-box">
        <h2>Track Your Order</h2>
        <input type="text" id="waybill_input" placeholder="Enter AWB / Tracking Number" value="<?php echo htmlspecialchars($awb_from_db); ?>">
        <button onclick="trackOrder()">Track</button>

        <div id="result_area"></div>
    </div>

    <script>
        function trackOrder() {
            var waybill = document.getElementById('waybill_input').value.trim();
            var resultArea = document.getElementById('result_area');

            if (!waybill) {
                resultArea.innerHTML = '<p class="error-msg">Please enter a tracking number.</p>';
                return;
            }

            resultArea.innerHTML = '<p>Loading...</p>';

            fetch('<?= _BASEURL ?>track-order.php?waybill=' + encodeURIComponent(waybill))
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        resultArea.innerHTML = '<p class="error-msg">' + data.message + '</p>';
                        return;
                    }

                    var statusClass = 'status-pending';
                    if (data.status_type === 'DL') statusClass = 'status-delivered';
                    else if (data.status_type === 'IT' || data.status_type === 'UD') statusClass = 'status-intransit';

                    var html = '<p><strong>Courier Partner:</strong> <span style="text-transform: capitalize; font-weight: bold; color: #19978c;">' + (data.courier_name || 'Delhivery') + '</span></p>';
                    html += '<p><strong>AWB / Tracking No:</strong> ' + data.awb + '</p>';
                    html += '<div class="status-badge ' + statusClass + '">' + data.status + '</div>';
                    html += '<p><strong>From:</strong> ' + data.origin + ' &rarr; <strong>To:</strong> ' + data.destination + '</p>';
                    if (data.expected_date) {
                        html += '<p><strong>Expected Delivery:</strong> ' + data.expected_date + '</p>';
                    }

                    html += '<div class="timeline">';
                    data.scans.slice().reverse().forEach(function(scan) {
                        var detail = scan.ScanDetail || {};
                        html += '<div class="timeline-item">';
                        html += '<div>' + (detail.Scan || '') + ' - ' + (detail.ScannedLocation || '') + '</div>';
                        html += '<div class="timeline-date">' + (detail.ScanDateTime || '') + '</div>';
                        html += '</div>';
                    });
                    html += '</div>';

                    resultArea.innerHTML = html;
                })
                .catch(err => {
                    resultArea.innerHTML = '<p class="error-msg">Something went wrong. Try again.</p>';
                });
        }

        <?php if ($awb_from_db): ?>
        window.onload = trackOrder;
        <?php endif; ?>
    </script>
</body>
</html>