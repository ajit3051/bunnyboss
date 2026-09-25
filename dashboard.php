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
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $ord): ?>
                            <tr>
                                <td>
                                    <strong class="text-dark">#<?= $ord['order_id'] ?></strong>
                                    <div class="small text-muted"><?= (int)$ord['total_items'] ?> item(s)</div>
                                </td>
                                <td class="small text-muted">
                                    <?= date('d M Y', strtotime($ord['created_at'])) ?><br>
                                    <?= date('h:i A', strtotime($ord['created_at'])) ?>
                                </td>
                                <td>
                                    <span class="order-badge badge-pay-<?= strtolower($ord['payment_status'] ?? 'pending') ?>">
                                        <?= strtoupper($ord['payment_status'] ?? 'PENDING') ?>
                                    </span>
                                    <div class="small text-muted mt-1 font-weight-bold"><?= strtoupper($ord['payment_method']) ?></div>
                                </td>
                                <td>
                                    <span class="order-badge badge-status-<?= strtolower($ord['order_status'] ?? 'pending') ?>">
                                        <?= strtoupper($ord['order_status'] ?? 'PENDING') ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark font-weight-bold" style="font-size: 15px;">₹<?= number_format($ord['grand_total'], 2) ?></strong>
                                </td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary btn-view-order btn-sm px-2" data-order-id="<?= $ord['order_id'] ?>" title="View Breakdown">
                                            <i class="icon-eye"></i> Details
                                        </button>
                                        <button class="btn btn-outline-info btn-track-order btn-sm px-2" data-order-id="<?= $ord['order_id'] ?>" title="Track Shipment Live">
                                            <i class="icon-truck"></i> Track
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
