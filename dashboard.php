<?php
include_once("include/config.php");

// Handle legacy tab query parameters with smooth redirects to dedicated pages
$tab = isset($_GET['tab']) ? trim($_GET['tab']) : '';
if ($tab === 'orders') {
    header("Location: " . _BASEURL . "orders.php");
    exit;
} elseif ($tab === 'addresses') {
    header("Location: " . _BASEURL . "addresses.php");
    exit;
} elseif ($tab === 'profile') {
    header("Location: " . _BASEURL . "profile.php");
    exit;
}

$current_account_page = 'dashboard';
$page_title = 'Dashboard Overview';
include("include/user_account_header.php");

if ($is_logged_in):
    // Fetch recent 4 orders for preview
    $phone_param = $user['mobile'] ?? $user_mobile;
    $recent_orders = [];
    $active_orders_count = 0;
    $ord_stmt = $db->select(
        "SELECT O.*, 
                (SELECT COUNT(*) FROM tbl_order_items WHERE order_id = O.order_id) as total_items
         FROM tbl_orders O 
         WHERE O.user_id = ? OR (O.phone = ? AND O.phone != '')
         ORDER BY O.order_id DESC LIMIT 4",
        'is',
        $user_id,
        $phone_param
    );
    if ($ord_stmt) {
        while ($o_row = $ord_stmt->fetch_assoc()) {
            // Fetch preview items with images
            $items_stmt = $db->select(
                "SELECT OI.product_title, OI.qty, OI.size, OI.price, OI.row_total,
                        (SELECT image_path FROM tbl_item_images WHERE item_id = OI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) as picture
                 FROM tbl_order_items OI 
                 WHERE OI.order_id = ? LIMIT 2",
                'i',
                $o_row['order_id']
            );
            $o_row['preview_items'] = [];
            if ($items_stmt) {
                while ($it = $items_stmt->fetch_assoc()) {
                    $o_row['preview_items'][] = $it;
                }
            }

            $recent_orders[] = $o_row;
            if (in_array(strtolower($o_row['order_status']), ['pending', 'placed', 'processing', 'shipped', 'dispatched'])) {
                $active_orders_count++;
            }
        }
    }

    // Fetch default shipping address
    $default_address = null;
    if ($user_id > 0) {
        $def_addr_stmt = $db->select("SELECT * FROM tbl_user_addresses WHERE user_id = ? AND is_default = 1 LIMIT 1", 'i', $user_id);
        if ($def_addr_stmt && $da_row = $def_addr_stmt->fetch_assoc()) {
            $default_address = $da_row;
        } elseif ($total_addresses_count > 0) {
            $first_addr_stmt = $db->select("SELECT * FROM tbl_user_addresses WHERE user_id = ? ORDER BY id DESC LIMIT 1", 'i', $user_id);
            if ($first_addr_stmt && $fa_row = $first_addr_stmt->fetch_assoc()) {
                $default_address = $fa_row;
            }
        }
    }
?>

    <!-- 4 KPI SUMMARY STATS -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3 mb-3">
            <a href="<?= _BASEURL ?>orders.php" style="text-decoration: none; color: inherit;">
                <div class="stat-box">
                    <div class="stat-icon primary"><i class="icon-shopping-cart"></i></div>
                    <div class="stat-number"><?= $total_orders_count ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3 mb-3">
            <a href="<?= _BASEURL ?>addresses.php" style="text-decoration: none; color: inherit;">
                <div class="stat-box">
                    <div class="stat-icon warning"><i class="icon-map-marker"></i></div>
                    <div class="stat-number"><?= $total_addresses_count ?></div>
                    <div class="stat-label">Saved Addresses</div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3 mb-3">
            <a href="<?= _BASEURL ?>orders.php" style="text-decoration: none; color: inherit;">
                <div class="stat-box">
                    <div class="stat-icon teal"><i class="icon-truck"></i></div>
                    <div class="stat-number"><?= $active_orders_count ?></div>
                    <div class="stat-label">In-Progress Orders</div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3 mb-3">
            <a href="<?= _BASEURL ?>profile.php" style="text-decoration: none; color: inherit;">
                <div class="stat-box">
                    <div class="stat-icon success"><i class="icon-check"></i></div>
                    <div class="stat-number" style="font-size: 20px; padding-top: 5px;">Active</div>
                    <div class="stat-label">Account Status</div>
                </div>
            </a>
        </div>
    </div>

    <!-- RECENT ORDERS SECTION -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h4 class="dashboard-card-title">
                <i class="icon-shopping-cart text-primary"></i> Recent Orders
            </h4>
            <a href="<?= _BASEURL ?>orders.php" class="btn btn-outline-primary btn-sm px-3" style="border-radius: 20px; font-weight: 600;">
                View All Orders &rarr;
            </a>
        </div>

        <?php if (empty($recent_orders)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="icon-shopping-cart"></i></div>
                <h5>No orders placed yet</h5>
                <p>Explore our premium footwear collection and place your first order!</p>
                <a href="<?= _BASEURL ?>product-list.php" class="btn btn-primary btn-round px-4" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                    Explore Shoes Collection
                </a>
            </div>
        <?php else: ?>
            <div class="recent-orders-list">
                <?php foreach ($recent_orders as $ord): 
                    $order_date_str = date('d M Y, h:i A', strtotime($ord['created_at']));
                    $has_awb = !empty($ord['courier_awb']) || !empty($ord['delhivery_awb']);
                    $awb_code = !empty($ord['courier_awb']) ? $ord['courier_awb'] : (!empty($ord['delhivery_awb']) ? $ord['delhivery_awb'] : '');
                    $courier_name = !empty($ord['courier_name']) ? ucfirst($ord['courier_name']) : 'Delhivery';

                    $pay_status_raw = strtolower(trim($ord['payment_status'] ?? 'pending'));
                    $order_status_raw = strtolower(trim($ord['order_status'] ?? 'pending'));
                    $pay_method_raw = strtolower(trim($ord['payment_method'] ?? 'cod'));

                    $grand_total_val = (float)($ord['grand_total'] ?? 0);
                    $paid_amount_val = (float)($ord['paid_amount'] ?? 0);
                    $cod_due_val = max(0, $grand_total_val - $paid_amount_val);
                    $is_delivered = ($order_status_raw === 'delivered' || $order_status_raw === 'completed');

                    // Cancellation eligibility
                    $is_cancellable_status = in_array($order_status_raw, ['pending', 'placed', 'processing', 'success']);
                    $is_already_shipped = in_array($order_status_raw, ['shipped', 'dispatched', 'delivered', 'completed', 'cancelled', 'rto', 'returned']);
                    $dispatch_raw = strtolower(trim($ord['dispatch_status'] ?? ''));
                    $is_dispatched = in_array($dispatch_raw, ['shipped', 'dispatched', 'in_transit', 'out_for_delivery']);
                    $can_cancel = ($is_cancellable_status && !$is_already_shipped && !$is_dispatched);
                ?>
                    <div class="order-card-box mb-3">
                        <!-- HEADER BAR -->
                        <div class="order-card-header-bar">
                            <div class="d-flex align-items-center flex-wrap gap-2" style="gap: 12px;">
                                <span class="order-id-title">Order #<?= $ord['order_id'] ?></span>
                                <span class="text-muted" style="font-size: 13px;">
                                    <i class="icon-calendar mr-1"></i> <?= $order_date_str ?>
                                </span>
                            </div>

                            <div class="d-flex align-items-center flex-wrap gap-2" style="gap: 8px;">
                                <span class="order-badge badge-pay-<?= $pay_status_raw ?>">
                                    <?php if ($pay_status_raw === 'cod'): ?>
                                        <i class="icon-money mr-1"></i> COD
                                    <?php elseif ($pay_status_raw === 'paid'): ?>
                                        <i class="icon-check mr-1"></i> PAID
                                    <?php elseif ($pay_status_raw === 'partial_paid'): ?>
                                        <i class="icon-credit-card mr-1"></i> PARTIAL PAID
                                    <?php else: ?>
                                        <i class="icon-clock-o mr-1"></i> <?= strtoupper($pay_status_raw) ?>
                                    <?php endif; ?>
                                </span>

                                <span class="order-badge badge-status-<?= $order_status_raw ?>">
                                    <?= strtoupper($order_status_raw) ?>
                                </span>

                                <span class="badge badge-light px-2 py-1 text-uppercase text-secondary" style="font-size: 11px; border: 1px solid #e2e8f0;">
                                    <?= htmlspecialchars($ord['payment_method']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- BODY -->
                        <div class="order-card-main-body">
                            <div class="row align-items-center">
                                <div class="col-lg-7 col-md-12 mb-3 mb-lg-0">
                                    <div class="d-flex flex-column gap-2" style="gap: 10px;">
                                        <?php foreach ($ord['preview_items'] as $item): 
                                            $img_src = !empty($item['picture']) ? (defined('_IMAGE_PATH') ? _IMAGE_PATH : _BASEURL . 'uploads/') . 'item-master/' . ltrim($item['picture'], '/') : _BASEURL . 'assets/images/no-image.jpg';
                                        ?>
                                            <div class="d-flex align-items-center" style="gap: 12px;">
                                                <img src="<?= $img_src ?>" alt="" class="order-thumb-img" onerror="this.src='<?= _BASEURL ?>assets/images/no-image.jpg';">
                                                <div>
                                                    <h6 class="text-dark mb-1 font-weight-bold" style="font-size: 14px; line-height: 1.3;">
                                                        <?= htmlspecialchars($item['product_title']) ?>
                                                    </h6>
                                                    <div class="d-flex align-items-center flex-wrap gap-2 text-muted small" style="gap: 8px;">
                                                        <?php if (!empty($item['size'])): ?>
                                                            <span class="badge badge-light" style="border: 1px solid #e2e8f0; font-weight: 600;">Size: <?= htmlspecialchars($item['size']) ?></span>
                                                        <?php endif; ?>
                                                        <span>Qty: <strong><?= $item['qty'] ?></strong></span>
                                                        <span>&bull;</span>
                                                        <span class="font-weight-bold text-dark">₹<?= number_format($item['price'], 2) ?> each</span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                        <?php if ((int)$ord['total_items'] > count($ord['preview_items'])): ?>
                                            <div class="small text-muted font-italic pl-2">
                                                + <?= (int)$ord['total_items'] - count($ord['preview_items']) ?> more item(s)
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-lg-5 col-md-12 text-lg-right">
                                    <div class="mb-3">
                                        <span class="text-muted small d-block">Grand Total</span>
                                        <span class="font-weight-bold text-dark" style="font-size: 20px; color: #19978c !important;">
                                            ₹<?= number_format($grand_total_val, 2) ?>
                                        </span>

                                        <?php if ($pay_method_raw === 'cod' && $cod_due_val > 0 && !$is_delivered): ?>
                                            <div class="d-inline-block text-left mt-2 p-2 rounded" style="background: #fef3c7; border: 1px solid #fde68a; font-size: 11px; color: #92400e;">
                                                <i class="icon-money mr-1"></i> To Pay on Delivery: <strong>₹<?= number_format($cod_due_val, 2) ?></strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex justify-content-lg-end align-items-center flex-wrap gap-2" style="gap: 8px;">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-view-order px-3" data-order-id="<?= $ord['order_id'] ?>" style="border-radius: 20px; font-weight: 600;">
                                            <i class="icon-eye mr-1"></i> Details
                                        </button>
                                        <?php if ($can_cancel): ?>
                                            <button type="button" class="btn btn-outline-secondary btn-sm btn-change-order-address px-3" 
                                                    data-order-id="<?= $ord['order_id'] ?>" 
                                                    data-first-name="<?= htmlspecialchars($ord['first_name'] ?? '') ?>"
                                                    data-last-name="<?= htmlspecialchars($ord['last_name'] ?? '') ?>"
                                                    data-phone="<?= htmlspecialchars($ord['phone'] ?? '') ?>"
                                                    data-street="<?= htmlspecialchars($ord['street_address'] ?? '') ?>"
                                                    data-city="<?= htmlspecialchars($ord['city'] ?? '') ?>"
                                                    data-state="<?= htmlspecialchars($ord['state'] ?? '') ?>"
                                                    data-postcode="<?= htmlspecialchars($ord['postcode'] ?? '') ?>"
                                                    style="border-radius: 20px; font-weight: 600;">
                                                <i class="icon-map-marker mr-1"></i> Address
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-cancel-order px-3" 
                                                    data-order-id="<?= $ord['order_id'] ?>" 
                                                    data-pay-status="<?= $pay_status_raw ?>"
                                                    data-paid-amt="<?= $paid_amount_val ?>"
                                                    style="border-radius: 20px; font-weight: 600;">
                                                <i class="icon-close mr-1"></i> Cancel
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($has_awb): ?>
                                            <button type="button" class="btn btn-primary btn-sm btn-track-order px-3" data-order-id="<?= $ord['order_id'] ?>" style="background-color: #19978c; border-color: #19978c; border-radius: 20px; font-weight: 600;">
                                                <i class="icon-truck mr-1"></i> Track Live
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- COURIER STRIP -->
                        <?php if ($has_awb): ?>
                            <div class="order-courier-strip">
                                <div>
                                    <i class="icon-truck mr-1"></i> Courier: <strong><?= $courier_name ?></strong> &nbsp;|&nbsp; 
                                    AWB: <strong class="text-dark"><?= htmlspecialchars($awb_code) ?></strong>
                                </div>
                                <a href="javascript:void(0)" class="btn-track-order font-weight-bold" data-order-id="<?= $ord['order_id'] ?>" style="color: #0f766e; text-decoration: underline;">
                                    Track Live &rarr;
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ROW: DEFAULT ADDRESS & PROFILE SNAPSHOT -->
    <div class="row">
        <!-- DEFAULT ADDRESS -->
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title" style="font-size: 16px;">
                        <i class="icon-map-marker text-warning"></i> Primary Delivery Address
                    </h5>
                    <a href="<?= _BASEURL ?>addresses.php" class="btn btn-link btn-sm p-0 text-primary font-weight-bold" style="font-size: 12px;">
                        Manage All (<?= $total_addresses_count ?>) &rarr;
                    </a>
                </div>

                <?php if ($default_address): ?>
                    <div class="p-3 bg-light rounded" style="border-radius: 10px; border: 1px solid #eef2f5;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="address-tag tag-default"><?= htmlspecialchars($default_address['title']) ?> (Default)</span>
                        </div>
                        <h6 class="font-weight-bold text-dark mb-1">
                            <?= htmlspecialchars($default_address['first_name'] . ' ' . $default_address['last_name']) ?>
                        </h6>
                        <p class="text-muted small mb-2">
                            <?= htmlspecialchars($default_address['street_address']) ?><br>
                            <?= htmlspecialchars($default_address['city']) ?><?= !empty($default_address['state']) ? ', ' . htmlspecialchars($default_address['state']) : '' ?> - <strong><?= htmlspecialchars($default_address['postcode']) ?></strong>
                        </p>
                        <p class="text-dark small mb-0 font-weight-bold">
                            <i class="icon-phone mr-1 text-primary"></i> +91 <?= htmlspecialchars($default_address['phone']) ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="icon-map-marker text-muted mb-2" style="font-size: 36px; display: inline-block;"></i>
                        <p class="text-muted small mb-3">No default address saved yet.</p>
                        <button class="btn btn-outline-primary btn-sm btn-round btn-open-add-address">
                            <i class="icon-plus mr-1"></i> Add Delivery Address
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- PROFILE SNAPSHOT -->
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title" style="font-size: 16px;">
                        <i class="icon-user text-info"></i> Account Profile Snapshot
                    </h5>
                    <a href="<?= _BASEURL ?>profile.php" class="btn btn-link btn-sm p-0 text-primary font-weight-bold" style="font-size: 12px;">
                        Edit Profile &rarr;
                    </a>
                </div>

                <div class="p-3 bg-light rounded" style="border-radius: 10px; border: 1px solid #eef2f5;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="dashboard-avatar mr-3" style="width: 48px; height: 48px; font-size: 20px;">
                            <?= strtoupper(substr(!empty($user['name']) ? $user['name'] : 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0"><?= htmlspecialchars(!empty($user['name']) ? $user['name'] : 'Valued Customer') ?></h6>
                            <small class="text-muted">Registered Customer</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-1">
                        <strong>Mobile:</strong> +91 <?= htmlspecialchars($user['mobile']) ?> <span class="badge badge-success ml-1" style="font-size: 10px;">VERIFIED</span>
                    </p>
                    <p class="small text-muted mb-1">
                        <strong>Email:</strong> <?= !empty($user['email']) ? htmlspecialchars($user['email']) : '<span class="text-muted font-italic">Not added yet</span>' ?>
                    </p>
                    <p class="small text-muted mb-0">
                        <strong>Member Since:</strong> <?= !empty($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : 'Recent Member' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK SHORTCUTS CARD -->
    <div class="dashboard-card" style="background: linear-gradient(135deg, #f0fdfa 0%, #e6fffa 100%); border-color: #99f6e4;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="font-weight-bold mb-1" style="color: #0f766e;">Looking for fresh footwear styles?</h5>
                <p class="mb-0 text-muted small">Explore trending shoes, slippers, and casuals with fast delivery and easy COD.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= _BASEURL ?>product-list.php" class="btn btn-primary btn-round btn-sm px-4" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                    <i class="icon-shopping-bag mr-1"></i> Start Shopping
                </a>
            </div>
        </div>
    </div>

<?php 
endif;

include("include/user_account_footer.php");
?>
