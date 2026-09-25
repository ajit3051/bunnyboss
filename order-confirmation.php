<?php
include_once("include/config.php");

// Helper for product image URL
if (!function_exists('get_product_image_url')) {
    function get_product_image_url($image_file = null)
    {
        if (empty($image_file)) {
            return _BASEURL . 'assets/images/no-image.jpg';
        }
        if (preg_match('/^https?:\/\//i', $image_file)) {
            return $image_file;
        }
        $cleaned = ltrim($image_file, '/');
        if (strpos($cleaned, 'item-master/') === 0) {
            return (defined('_IMAGE_PATH') ? _IMAGE_PATH : _BASEURL . 'assets/images/') . $cleaned;
        }
        if (strpos($cleaned, 'uploads/') === 0) {
            return (defined('_ADMIN_URL') ? _ADMIN_URL : _BASEURL) . $cleaned;
        }
        return (defined('_IMAGE_PATH') ? _IMAGE_PATH : _BASEURL . 'assets/images/') . 'item-master/' . $cleaned;
    }
}

$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;
$order = null;
$order_items = [];
$error_message = '';

if ($order_id <= 0) {
    $error_message = 'Invalid order reference number.';
} else {
    $db = connect();

    // 1. Fetch order details
    $order_stmt = $db->select("SELECT * FROM tbl_orders WHERE order_id = ? LIMIT 1", 'i', $order_id);
    if ($order_stmt && ($row = $order_stmt->fetch_assoc())) {
        $order = $row;

        // 2. Fetch order items with pictures
        $items_result = $db->select(
            "SELECT OI.*, 
                    (SELECT image_path FROM tbl_item_images WHERE item_id = OI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture 
             FROM tbl_order_items OI 
             WHERE OI.order_id = ? 
             ORDER BY OI.item_id ASC",
            'i',
            $order_id
        );

        if ($items_result) {
            while ($i_row = $items_result->fetch_assoc()) {
                $order_items[] = $i_row;
            }
        }

        // Clear cart items from database & session once order confirmation is reached
        if (function_exists('get_cart_session')) {
            $cart_session = get_cart_session();
            if (!empty($cart_session)) {
                $db->delete("DELETE FROM tbl_cart_items WHERE cart_session = ?", 's', $cart_session);
            }
        }
        unset(
            $_SESSION['checkout_products'],
            $_SESSION['checkout_summary'],
            $_SESSION['cart_items'],
            $_SESSION['rzp_order_id'],
            $_SESSION['rzp_amount']
        );
    } else {
        $error_message = 'We could not find the specified order.';
    }
}

// Calculate financial details accurately from database values
$subtotal = 0.00;
$shipping_amount = 0.00;
$gst_amount = 0.00;
$grand_total = 0.00;
$paid_amount = 0.00;
$cod_remaining = 0.00;
$is_cod = false;

if ($order) {
    $subtotal = (float)($order['subtotal'] ?? 0);
    $gst_amount = (float)($order['gst_amount'] ?? 0);
    $grand_total = (float)($order['grand_total'] ?? 0);
    $paid_amount = (float)($order['paid_amount'] ?? 0);

    // Sum shipping from items or calculate difference
    foreach ($order_items as $oi) {
        if (isset($oi['shipping']) && (float)$oi['shipping'] > 0) {
            $shipping_amount += (float)$oi['shipping'];
        }
    }
    if ($shipping_amount <= 0 && $grand_total > ($subtotal + $gst_amount)) {
        $shipping_amount = max(0, $grand_total - $subtotal - $gst_amount);
    }

    $is_cod = (strtolower($order['payment_method'] ?? 'cod') === 'cod');
    $cod_remaining = max(0, $grand_total - $paid_amount);
}

include('include/top.php');
?>

<style>
/* Order Confirmation Scoped Modern Styling */
.order-conf-wrapper {
    background-color: #f8fafc;
    padding: 35px 0 60px;
    font-family: inherit;
}

.conf-hero-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    padding: 35px 30px;
    margin-bottom: 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.conf-hero-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
}

.conf-icon-pulse {
    width: 74px;
    height: 74px;
    margin: 0 auto 18px;
    background: #ecfdf5;
    color: #10b981;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 34px;
    border: 3px solid #d1fae5;
    box-shadow: 0 0 0 8px rgba(16, 185, 129, 0.12);
}

.conf-title {
    font-size: 26px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
    letter-spacing: -0.3px;
}

.conf-subtitle {
    font-size: 15px;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto 20px;
    line-height: 1.5;
}

.conf-meta-bar {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 12px 20px;
    background: #f1f5f9;
    border-radius: 40px;
    font-size: 13px;
    color: #334155;
}

.conf-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}

.conf-meta-item strong {
    color: #0f172a;
    font-weight: 700;
}

.conf-meta-divider {
    color: #cbd5e1;
    font-weight: 300;
}

.conf-sms-alert {
    margin-top: 18px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 500;
}

/* Section Cards */
.conf-box {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
    margin-bottom: 25px;
    overflow: hidden;
}

.conf-box-header {
    background: #ffffff;
    padding: 18px 22px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.conf-box-title {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.conf-box-title i {
    color: #c96;
    font-size: 18px;
}

.conf-box-body {
    padding: 22px;
}

/* Info Grid Rows */
.info-row {
    display: flex;
    padding: 11px 0;
    border-bottom: 1px solid #f8fafc;
    font-size: 14px;
}

.info-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.info-row:first-child {
    padding-top: 0;
}

.info-label {
    width: 38%;
    color: #64748b;
    font-weight: 500;
}

.info-val {
    width: 62%;
    color: #1e293b;
    font-weight: 600;
    word-break: break-word;
}

/* Badge Styles */
.badge-status-success {
    background: #dcfce7;
    color: #15803d;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
    text-transform: capitalize;
}

.badge-status-pending {
    background: #fef9c3;
    color: #854d0e;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
    text-transform: capitalize;
}

.badge-tag-cod {
    background: #eff6ff;
    color: #1d4ed8;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
}

/* Step Progress Tracker */
.tracking-steps {
    display: flex;
    justify-content: space-between;
    margin: 15px 0 10px;
    position: relative;
}

.tracking-steps::before {
    content: '';
    position: absolute;
    top: 18px;
    left: 40px;
    right: 40px;
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
}

.tracking-step {
    position: relative;
    z-index: 2;
    text-align: center;
    flex: 1;
}

.tracking-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-size: 14px;
    color: #94a3b8;
    transition: all 0.3s;
}

.tracking-step.done .tracking-icon {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
}

.tracking-step.active .tracking-icon {
    background: #3b82f6;
    border-color: #3b82f6;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
}

.tracking-label {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
}

.tracking-step.done .tracking-label,
.tracking-step.active .tracking-label {
    color: #0f172a;
}

/* Product Item Rows */
.conf-item-row {
    display: flex;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
}

.conf-item-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.conf-item-row:first-child {
    padding-top: 0;
}

.conf-item-thumb {
    width: 58px;
    height: 58px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    flex-shrink: 0;
    margin-right: 14px;
}

.conf-item-info {
    flex-grow: 1;
    min-width: 0;
}

.conf-item-title {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 4px;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.conf-item-meta {
    font-size: 12px;
    color: #64748b;
    display: flex;
    gap: 8px;
}

.conf-item-badge {
    background: #f1f5f9;
    padding: 1px 6px;
    border-radius: 4px;
    font-weight: 500;
}

.conf-item-price {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    text-align: right;
    margin-left: 12px;
    white-space: nowrap;
}

/* Order Summary Calculations */
.summary-calc-table {
    width: 100%;
    margin-top: 15px;
    border-top: 1px solid #f1f5f9;
    padding-top: 12px;
}

.summary-calc-row {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    padding: 6px 0;
    color: #64748b;
}

.summary-calc-row strong,
.summary-calc-row span.val {
    color: #1e293b;
    font-weight: 600;
}

.summary-calc-total {
    display: flex;
    justify-content: space-between;
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    border-top: 2px dashed #e2e8f0;
    margin-top: 10px;
    padding-top: 12px;
}

.summary-calc-total .total-amount {
    color: #c96;
    font-size: 20px;
}

.cod-due-box {
    margin-top: 15px;
    background: #fefce8;
    border: 1px solid #fef08a;
    border-radius: 8px;
    padding: 12px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cod-due-box .due-title {
    font-size: 13px;
    font-weight: 600;
    color: #854d0e;
}

.cod-due-box .due-val {
    font-size: 16px;
    font-weight: 700;
    color: #a16207;
}

/* Action Buttons */
.conf-actions-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-top: 35px;
}

.conf-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s ease;
    text-decoration: none !important;
}

.conf-btn-primary {
    background: #c96;
    color: #ffffff !important;
    border: 1px solid #c96;
}

.conf-btn-primary:hover {
    background: #b88655;
    border-color: #b88655;
    box-shadow: 0 4px 12px rgba(204, 153, 102, 0.25);
}

.conf-btn-secondary {
    background: #1e293b;
    color: #ffffff !important;
    border: 1px solid #1e293b;
}

.conf-btn-secondary:hover {
    background: #0f172a;
    border-color: #0f172a;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
}

.conf-btn-outline {
    background: #ffffff;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
}

.conf-btn-outline:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a !important;
}

/* Print Styles */
@media print {
    body * {
        visibility: hidden;
    }
    .order-conf-printable, .order-conf-printable * {
        visibility: visible;
    }
    .order-conf-printable {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: #fff !important;
    }
    .header, .footer, .breadcrumb-nav, .conf-actions-bar, .page-header {
        display: none !important;
    }
    .conf-hero-card {
        border: none !important;
        box-shadow: none !important;
        padding: 10px 0 !important;
    }
    .conf-box {
        border: 1px solid #ccc !important;
        box-shadow: none !important;
    }
}

@media (max-width: 767px) {
    .conf-hero-card {
        padding: 25px 15px;
    }
    .conf-title {
        font-size: 22px;
    }
    .info-label {
        width: 45%;
    }
    .info-val {
        width: 55%;
    }
    .conf-actions-bar {
        flex-direction: column;
        width: 100%;
    }
    .conf-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<main class="main">
    <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
        <div class="container">
            <h1 class="page-title">Order Confirmation<span>Shop</span></h1>
        </div><!-- End .container -->
    </div><!-- End .page-header -->

    <nav aria-label="breadcrumb" class="breadcrumb-nav mb-0">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>product-list.php">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">Order Confirmation</li>
            </ol>
        </div><!-- End .container -->
    </nav><!-- End .breadcrumb-nav -->

    <div class="order-conf-wrapper">
        <div class="container order-conf-printable">

        <?php if (!empty($error_message)): ?>

            <div class="conf-hero-card" style="padding: 50px 20px;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 32px;">
                    &times;
                </div>
                <h2 class="conf-title" style="color: #991b1b;">Order Not Found</h2>
                <p class="conf-subtitle"><?= htmlspecialchars($error_message) ?></p>
                <div class="conf-actions-bar">
                    <a href="<?= _BASEURL ?>product-list.php" class="conf-btn conf-btn-primary">
                        <i class="icon-shopping-cart"></i> Browse Products
                    </a>
                    <a href="<?= _BASEURL ?>index.php" class="conf-btn conf-btn-outline">
                        Back to Home
                    </a>
                </div>
            </div>

        <?php else: ?>

            <!-- Success Hero Banner -->
            <div class="conf-hero-card">
                <div class="conf-icon-pulse">
                    &#10003;
                </div>
                <h1 class="conf-title">Order Confirmed!</h1>
                <p class="conf-subtitle">
                    Thank you, <strong><?= htmlspecialchars($order['first_name'] . (!empty($order['last_name']) ? ' ' . $order['last_name'] : '')) ?></strong>! 
                    Your order has been received and is being prepared with care.
                </p>

                <div class="conf-meta-bar">
                    <span class="conf-meta-item">
                        Order Reference: <strong>#<?= htmlspecialchars($order['order_id']) ?></strong>
                    </span>
                    <span class="conf-meta-divider">&bull;</span>
                    <span class="conf-meta-item">
                        Date: <strong><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></strong>
                    </span>
                    <span class="conf-meta-divider">&bull;</span>
                    <span class="conf-meta-item">
                        Status: <span class="badge-status-success"><?= htmlspecialchars($order['order_status']) ?></span>
                    </span>
                </div>

                <?php if (!empty($order['phone'])): ?>
                <div>
                    <div class="conf-sms-alert">
                        <span>&#9993;</span>
                        <span>Confirmation SMS dispatched to <strong>+91 <?= htmlspecialchars($order['phone']) ?></strong></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Content Row -->
            <div class="row">
                <!-- Left Column: Shipping & Payment Details -->
                <div class="col-lg-7">

                    <!-- Delivery Details Card -->
                    <div class="conf-box">
                        <div class="conf-box-header">
                            <h3 class="conf-box-title">
                                <i class="icon-map-marker"></i> Delivery Details
                            </h3>
                        </div>
                        <div class="conf-box-body">
                            <div class="info-row">
                                <span class="info-label">Customer Name</span>
                                <span class="info-val"><?= htmlspecialchars(trim(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''))) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Mobile Number</span>
                                <span class="info-val">+91 <?= htmlspecialchars($order['phone'] ?? 'N/A') ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Delivery Address</span>
                                <span class="info-val">
                                    <?= htmlspecialchars($order['street_address'] ?? '') ?><br>
                                    <?= htmlspecialchars($order['city'] ?? '') ?>, Pincode: <?= htmlspecialchars($order['postcode'] ?? '') ?>
                                </span>
                            </div>
                            <?php if (!empty($order['order_notes'])): ?>
                            <div class="info-row">
                                <span class="info-label">Order Notes</span>
                                <span class="info-val" style="color: #64748b; font-weight: normal;">
                                    <?= nl2br(htmlspecialchars($order['order_notes'])) ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Shipment & Tracking Card -->
                    <div class="conf-box">
                        <div class="conf-box-header">
                            <h3 class="conf-box-title">
                                <i class="icon-truck"></i> Shipment Status
                            </h3>
                            <?php if (!empty($order['courier_awb'])): ?>
                                <span class="badge badge-info" style="font-size: 12px; padding: 4px 8px;">
                                    AWB: <?= htmlspecialchars($order['courier_awb']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="conf-box-body">
                            <!-- Visual Progress Steps -->
                            <div class="tracking-steps">
                                <div class="tracking-step done">
                                    <div class="tracking-icon">&#10003;</div>
                                    <div class="tracking-label">Order Placed</div>
                                </div>
                                <div class="tracking-step <?= (!empty($order['courier_awb']) || ($order['dispatch_status'] ?? '') === 'dispatched') ? 'done' : 'active' ?>">
                                    <div class="tracking-icon">&#9679;</div>
                                    <div class="tracking-label">Processing</div>
                                </div>
                                <div class="tracking-step <?= (($order['dispatch_status'] ?? '') === 'dispatched') ? 'active' : '' ?>">
                                    <div class="tracking-icon">&#9951;</div>
                                    <div class="tracking-label">In Transit</div>
                                </div>
                                <div class="tracking-step">
                                    <div class="tracking-icon">&#9733;</div>
                                    <div class="tracking-label">Delivered</div>
                                </div>
                            </div>

                            <div style="background: #f8fafc; border-radius: 8px; padding: 12px 15px; margin-top: 15px; font-size: 13px; color: #475569; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                <div>
                                    <?php if (!empty($order['courier_name'])): ?>
                                        <strong>Carrier:</strong> <?= htmlspecialchars(ucfirst($order['courier_name'])) ?> &bull; 
                                        <strong>AWB:</strong> <?= htmlspecialchars($order['courier_awb']) ?>
                                    <?php else: ?>
                                        <span>Your package is being securely packed at our fulfillment warehouse.</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($_SESSION['user_id']) || !empty($_SESSION['user_mobile'])): ?>
                                    <a href="<?= _BASEURL ?>dashboard.php?tab=orders" class="text-primary font-weight-bold" style="font-size: 13px;">
                                        Track in Dashboard &rarr;
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information Card -->
                    <div class="conf-box">
                        <div class="conf-box-header">
                            <h3 class="conf-box-title">
                                <i class="icon-credit-card"></i> Payment Summary
                            </h3>
                            <span class="<?= $is_cod ? 'badge-tag-cod' : 'badge-status-success' ?>">
                                <?= $is_cod ? 'Cash on Delivery' : 'Prepaid (Online)' ?>
                            </span>
                        </div>
                        <div class="conf-box-body">
                            <div class="info-row">
                                <span class="info-label">Payment Method</span>
                                <span class="info-val">
                                    <?php
                                    if ($is_cod) {
                                        $cod_includes_gst_val = defined('_COD_INCLUDES_GST_') ? (bool)_COD_INCLUDES_GST_ : true;
                                        echo $cod_includes_gst_val 
                                            ? 'Cash on Delivery (Shipping Charge & GST Paid Online)' 
                                            : 'Cash on Delivery (Shipping Charge Paid Online)';
                                    } else {
                                        echo 'Online Payment (Prepaid)';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Payment Status</span>
                                <span class="info-val">
                                    <?php if ($is_cod): ?>
                                        <span class="badge-status-pending">Partial Online / Balance on COD</span>
                                    <?php else: ?>
                                        <span class="badge-status-success">Paid in Full</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php if (!empty($order['razorpay_payment_id'])): ?>
                            <div class="info-row">
                                <span class="info-label">Transaction ID</span>
                                <span class="info-val"><code><?= htmlspecialchars($order['razorpay_payment_id']) ?></code></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div><!-- End .col-lg-7 -->

                <!-- Right Column: Order Items & Financial Summary -->
                <div class="col-lg-5">
                    <div class="conf-box">
                        <div class="conf-box-header">
                            <h3 class="conf-box-title">
                                <i class="icon-shopping-cart"></i> Order Items (<?= count($order_items) ?>)
                            </h3>
                        </div>
                        <div class="conf-box-body">

                            <!-- Product List -->
                            <div class="conf-items-list">
                                <?php if (!empty($order_items)): ?>
                                    <?php foreach ($order_items as $item): ?>
                                        <?php 
                                            $item_img = get_product_image_url($item['picture'] ?? null); 
                                        ?>
                                        <div class="conf-item-row">
                                            <img src="<?= htmlspecialchars($item_img) ?>" 
                                                 alt="<?= htmlspecialchars($item['product_title']) ?>" 
                                                 class="conf-item-thumb"
                                                 onerror="this.src='<?= _BASEURL ?>assets/images/no-image.jpg';">
                                            <div class="conf-item-info">
                                                <div class="conf-item-title" title="<?= htmlspecialchars($item['product_title']) ?>">
                                                    <?= htmlspecialchars($item['product_title']) ?>
                                                </div>
                                                <div class="conf-item-meta">
                                                    <?php if (!empty($item['size'])): ?>
                                                        <span class="conf-item-badge">Size: <?= htmlspecialchars($item['size']) ?></span>
                                                    <?php endif; ?>
                                                    <span>Qty: <strong><?= (int)$item['qty'] ?></strong> &times; ₹<?= number_format($item['price'], 2) ?></span>
                                                </div>
                                            </div>
                                            <div class="conf-item-price">
                                                ₹<?= number_format($item['row_total'], 2) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center py-3">No individual items found.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Cost Breakdown -->
                            <div class="summary-calc-table">
                                <div class="summary-calc-row">
                                    <span>Items Subtotal</span>
                                    <span class="val">₹<?= number_format($subtotal, 2) ?></span>
                                </div>
                                <div class="summary-calc-row">
                                    <span>Shipping Charge</span>
                                    <span class="val"><?= ($shipping_amount > 0) ? '₹' . number_format($shipping_amount, 2) : '<span style="color:#10b981; font-weight:700;">FREE</span>' ?></span>
                                </div>
                                <div class="summary-calc-row">
                                    <span>GST (<?= defined('_GST_') ? _GST_ : '5' ?>%)</span>
                                    <span class="val">₹<?= number_format($gst_amount, 2) ?></span>
                                </div>

                                <div class="summary-calc-total">
                                    <span>Total (GST Included)</span>
                                    <span class="total-amount">₹<?= number_format($grand_total, 2) ?></span>
                                </div>

                                <?php if ($is_cod): ?>
                                    <?php if ($paid_amount > 0): ?>
                                        <div class="summary-calc-row" style="margin-top: 6px; color: #166534;">
                                            <span>Paid Online (Advance)</span>
                                            <span class="val" style="color: #166534;">- ₹<?= number_format($paid_amount, 2) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="cod-due-box">
                                        <div class="due-title">
                                            <span>&#128181;</span> Due on Cash on Delivery:
                                        </div>
                                        <div class="due-val">
                                            ₹<?= number_format($cod_remaining, 2) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div><!-- End .col-lg-5 -->
            </div><!-- End .row -->

            <!-- Action Buttons Footer -->
            <div class="conf-actions-bar">
                <a href="<?= _BASEURL ?>product-list.php" class="conf-btn conf-btn-primary">
                    <i class="icon-shopping-cart"></i> Continue Shopping
                </a>

                <?php if (!empty($_SESSION['user_id']) || !empty($_SESSION['user_mobile'])): ?>
                <a href="<?= _BASEURL ?>dashboard.php?tab=orders" class="conf-btn conf-btn-secondary">
                    <i class="icon-user"></i> View in Order History
                </a>
                <?php endif; ?>

                <button onclick="window.print()" class="conf-btn conf-btn-outline">
                    <i class="icon-print"></i> Print Receipt
                </button>
            </div>

        <?php endif; ?>

        </div><!-- End .container -->
    </div><!-- End .order-conf-wrapper -->
</main><!-- End .main -->

<?php include('include/bottom.php'); ?>