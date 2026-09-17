<?php
include_once("include/config.php");

function trackShipment($waybill, $courier = null) {
    return trackCourierShipment($waybill, $courier);
}

// Handle AJAX request from frontend
if (isset($_GET['waybill'])) {
    header('Content-Type: application/json');
    $courier = isset($_GET['courier']) ? trim($_GET['courier']) : null;
    echo json_encode(trackShipment(trim($_GET['waybill']), $courier));
    exit;
}