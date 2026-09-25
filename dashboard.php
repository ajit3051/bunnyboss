<?php
include_once("include/config.php");

$db = connect();

$is_logged_in = !empty($_SESSION['user_id']) || !empty($_SESSION['user_mobile']);
$user_id = $_SESSION['user_id'] ?? 0;
$user_mobile = $_SESSION['user_mobile'] ?? '';

$user = null;
if ($is_logged_in) {
    if (!empty($user_id)) {
        $u_stmt = $db->select("SELECT * FROM tbl_users WHERE id = ?", 'i', $user_id);
    } else {
        $u_stmt = $db->select("SELECT * FROM tbl_users WHERE mobile = ?", 's', $user_mobile);
    }
    if ($u_stmt && $u_res = $u_stmt->fetch_assoc()) {
        $user = $u_res;
        $user_id = (int)$user['id'];
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_mobile'] = $user['mobile'];
        if (!empty($user['name'])) {
            $_SESSION['user_name'] = $user['name'];
        }
    }
}

// Active tab selection via query parameter
$active_tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'dashboard';
$allowed_tabs = ['dashboard', 'orders', 'addresses', 'profile'];
if (!in_array($active_tab, $allowed_tabs, true)) {
    $active_tab = 'dashboard';
}

// Fetch user addresses if logged in
$saved_addresses = [];
$default_address = null;
if ($is_logged_in && $user_id > 0) {
    $addr_stmt = $db->select("SELECT * FROM tbl_user_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC", 'i', $user_id);
    if ($addr_stmt) {
        while ($a_row = $addr_stmt->fetch_assoc()) {
            $saved_addresses[] = $a_row;
            if ((int)$a_row['is_default'] === 1 && !$default_address) {
                $default_address = $a_row;
            }
        }
    }
}

// Fetch user orders if logged in
$user_orders = [];
if ($is_logged_in) {
    $phone_param = $user['mobile'] ?? $user_mobile;
    $ord_stmt = $db->select(
        "SELECT O.*, 
                (SELECT COUNT(*) FROM tbl_order_items WHERE order_id = O.order_id) as total_items
         FROM tbl_orders O 
         WHERE O.user_id = ? OR (O.phone = ? AND O.phone != '')
         ORDER BY O.order_id DESC",
        'is',
        $user_id,
        $phone_param
    );
    if ($ord_stmt) {
        while ($o_row = $ord_stmt->fetch_assoc()) {
            $user_orders[] = $o_row;
        }
    }
}

include('include/top.php');
?>

<style>
.dashboard-wrapper {
    background-color: #f8f9fa;
    padding: 30px 0;
    min-height: 70vh;
}
.dashboard-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    border: 1px solid #eef2f5;
    padding: 24px;
    margin-bottom: 25px;
    transition: all 0.3s ease;
}
.dashboard-card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
}
.dashboard-user-header {
    background: linear-gradient(135deg, #232f3e 0%, #37475a 100%);
    color: #ffffff;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}
.dashboard-avatar {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    background: #ff9800;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 700;
    box-shadow: 0 4px 10px rgba(255, 152, 0, 0.4);
}
.stat-box {
    background: #ffffff;
    border: 1px solid #eef2f5;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
}
.stat-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 12px;
}
.stat-icon.primary { background: rgba(55, 71, 90, 0.1); color: #37475a; }
.stat-icon.warning { background: rgba(255, 152, 0, 0.1); color: #ff9800; }
.stat-icon.success { background: rgba(40, 167, 69, 0.1); color: #28a745; }
.stat-icon.info { background: rgba(23, 162, 184, 0.1); color: #17a2b8; }

.stat-number {
    font-size: 26px;
    font-weight: 700;
    color: #222;
    margin-bottom: 4px;
}
.stat-label {
    font-size: 13px;
    color: #777;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.dashboard-nav-pills {
    border: none;
    background: #ffffff;
    border-radius: 12px;
    padding: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}
.dashboard-nav-pills .nav-link {
    border-radius: 8px;
    padding: 12px 18px;
    font-weight: 600;
    color: #444;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.2s ease;
    margin-bottom: 4px;
}
.dashboard-nav-pills .nav-link i {
    font-size: 18px;
    width: 20px;
    text-align: center;
}
.dashboard-nav-pills .nav-link:hover {
    background: #f1f5f9;
    color: #37475a;
}
.dashboard-nav-pills .nav-link.active {
    background: #37475a;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(55, 71, 90, 0.3);
}

.address-card {
    border: 2px solid #eef2f5;
    border-radius: 10px;
    padding: 20px;
    position: relative;
    transition: all 0.2s ease;
    height: 100%;
    background: #ffffff;
}
.address-card.default-address {
    border-color: #ff9800;
    background: #fffdf8;
}
.address-tag {
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    background: #eef2f5;
    color: #555;
    display: inline-block;
    margin-bottom: 10px;
}
.address-tag.tag-default {
    background: #ff9800;
    color: #ffffff;
}

.order-badge {
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: capitalize;
}
.badge-status-pending { background: #fff3cd; color: #856404; }
.badge-status-processing { background: #cce5ff; color: #004085; }
.badge-status-shipped { background: #d1ecf1; color: #0c5460; }
.badge-status-delivered { background: #d4edda; color: #155724; }
.badge-status-cancelled { background: #f8d7da; color: #721c24; }

.badge-pay-paid { background: #d4edda; color: #155724; }
.badge-pay-partial_paid { background: #d1ecf1; color: #0c5460; }
.badge-pay-cod { background: #cce5ff; color: #004085; }
.badge-pay-pending { background: #fff3cd; color: #856404; }
.badge-pay-failed { background: #f8d7da; color: #721c24; }

.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.modal-header {
    background: #37475a;
    color: #ffffff;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}
.modal-header .close {
    color: #ffffff;
    opacity: 0.8;
}
.modal-header .close:hover {
    opacity: 1;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
}
.empty-state-icon {
    font-size: 50px;
    color: #ccc;
    margin-bottom: 15px;
}
</style>

<main class="main">
    <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
        <div class="container">
            <h1 class="page-title">My Account Dashboard</h1>
        </div>
    </div>
    
    <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <div class="container">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </div>
    </nav>

    <div class="dashboard-wrapper">
        <div class="container">

            <?php if (!$is_logged_in): ?>
                <!-- GUEST / LOGGED-OUT VIEW -->
                <div class="row justify-content-center">
                    <div class="col-md-7 col-lg-5">
                        <div class="dashboard-card text-center py-5 px-4">
                            <div class="empty-state-icon text-warning mb-3">
                                <i class="icon-user" style="font-size: 64px;"></i>
                            </div>
                            <h3 class="font-weight-bold mb-2">Welcome to BunnyBoss</h3>
                            <p class="text-muted mb-4">Please sign in to access your personal dashboard, manage multiple delivery addresses, and track your orders.</p>
                            
                            <a href="#signin-modal" data-toggle="modal" class="btn btn-primary btn-round btn-lg btn-block shadow-sm">
                                <i class="icon-long-arrow-right mr-2"></i> SIGN IN / REGISTER WITH OTP
                            </a>
                            <a href="<?= _BASEURL ?>index.php" class="btn btn-link btn-sm mt-3 text-muted">Return to Store Homepage</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- LOGGED-IN DASHBOARD VIEW -->
                
                <!-- USER HEADER BANNER -->
                <div class="dashboard-user-header">
                    <div class="d-flex align-items-center gap-3" style="gap: 18px;">
                        <div class="dashboard-avatar">
                            <?= strtoupper(substr(!empty($user['name']) ? $user['name'] : 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <h3 class="mb-1 text-white font-weight-bold">
                                Hello, <?= htmlspecialchars(!empty($user['name']) ? $user['name'] : 'Valued Customer') ?>!
                            </h3>
                            <p class="mb-0 text-white-50 small">
                                <i class="icon-phone mr-1"></i> +91 <?= htmlspecialchars($user['mobile']) ?>
                                <?php if (!empty($user['email'])): ?>
                                    <span class="mx-2">•</span> <i class="icon-envelope mr-1"></i> <?= htmlspecialchars($user['email']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div>
                        <a href="<?= _BASEURL ?>logout.php" class="btn btn-outline-light btn-sm btn-round">
                            <i class="icon-long-arrow-right mr-1"></i> Logout Account
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- DASHBOARD SIDEBAR NAVIGATION -->
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="nav flex-column nav-pills dashboard-nav-pills" id="dashboard-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link <?= $active_tab === 'dashboard' ? 'active' : '' ?>" id="tab-dash-link" data-toggle="pill" href="#tab-dashboard" role="tab" aria-controls="tab-dashboard" aria-selected="<?= $active_tab === 'dashboard' ? 'true' : 'false' ?>">
                                <i class="icon-dashboard"></i> Overview
                            </a>
                            <a class="nav-link <?= $active_tab === 'orders' ? 'active' : '' ?>" id="tab-orders-link" data-toggle="pill" href="#tab-orders" role="tab" aria-controls="tab-orders" aria-selected="<?= $active_tab === 'orders' ? 'true' : 'false' ?>">
                                <i class="icon-shopping-cart"></i> My Orders (<?= count($user_orders) ?>)
                            </a>
                            <a class="nav-link <?= $active_tab === 'addresses' ? 'active' : '' ?>" id="tab-addresses-link" data-toggle="pill" href="#tab-addresses" role="tab" aria-controls="tab-addresses" aria-selected="<?= $active_tab === 'addresses' ? 'true' : 'false' ?>">
                                <i class="icon-map-marker"></i> Saved Addresses (<?= count($saved_addresses) ?>)
                            </a>
                            <a class="nav-link <?= $active_tab === 'profile' ? 'active' : '' ?>" id="tab-profile-link" data-toggle="pill" href="#tab-profile" role="tab" aria-controls="tab-profile" aria-selected="<?= $active_tab === 'profile' ? 'true' : 'false' ?>">
                                <i class="icon-user"></i> Basic Details
                            </a>
                            <a class="nav-link text-danger mt-2 border-top pt-3" href="<?= _BASEURL ?>logout.php">
                                <i class="icon-long-arrow-right"></i> Sign Out
                            </a>
                        </div>
                    </div>

                    <!-- DASHBOARD CONTENT PANES -->
                    <div class="col-lg-9 col-md-8">
                        <div class="tab-content" id="dashboard-tab-content">
                            
                            <!-- ------------------------------------------------------------- -->
                            <!-- TAB 1: OVERVIEW -->
                            <!-- ------------------------------------------------------------- -->
                            <div class="tab-pane fade <?= $active_tab === 'dashboard' ? 'show active' : '' ?>" id="tab-dashboard" role="tabpanel" aria-labelledby="tab-dash-link">
                                <div class="row mb-4">
                                    <div class="col-sm-4 mb-3">
                                        <div class="stat-box">
                                            <div class="stat-icon primary"><i class="icon-shopping-cart"></i></div>
                                            <div class="stat-number"><?= count($user_orders) ?></div>
                                            <div class="stat-label">Total Orders</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <div class="stat-box">
                                            <div class="stat-icon warning"><i class="icon-map-marker"></i></div>
                                            <div class="stat-number"><?= count($saved_addresses) ?></div>
                                            <div class="stat-label">Saved Addresses</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <div class="stat-box">
                                            <div class="stat-icon success"><i class="icon-check"></i></div>
                                            <div class="stat-number">Active</div>
                                            <div class="stat-label">Account Status</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- RECENT ORDERS PREVIEW -->
                                <div class="dashboard-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="mb-0 font-weight-bold"><i class="icon-shopping-cart mr-2 text-primary"></i> Recent Orders</h4>
                                        <a href="#" onclick="$('#tab-orders-link').tab('show'); return false;" class="btn btn-link btn-sm p-0">View All Orders &rarr;</a>
                                    </div>

                                    <?php if (empty($user_orders)): ?>
                                        <div class="empty-state">
                                            <i class="icon-shopping-cart empty-state-icon"></i>
                                            <h5>No orders placed yet</h5>
                                            <p class="text-muted small">Explore our latest footwear collection and place your first order!</p>
                                            <a href="<?= _BASEURL ?>product-list.php" class="btn btn-outline-primary btn-round">Start Shopping</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Order #</th>
                                                        <th>Date</th>
                                                        <th>Items</th>
                                                        <th>Total</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $recent_orders = array_slice($user_orders, 0, 3);
                                                    foreach ($recent_orders as $ord): 
                                                    ?>
                                                        <tr>
                                                            <td><strong>#<?= $ord['order_id'] ?></strong></td>
                                                            <td><?= date('d M Y, h:i A', strtotime($ord['created_at'])) ?></td>
                                                            <td><?= (int)$ord['total_items'] ?> item(s)</td>
                                                            <td class="font-weight-bold">₹<?= number_format($ord['grand_total'], 2) ?></td>
                                                            <td>
                                                                <span class="order-badge badge-status-<?= strtolower($ord['order_status']) ?>">
                                                                    <?= ucfirst($ord['order_status']) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-outline-primary btn-sm btn-view-order" data-order-id="<?= $ord['order_id'] ?>">
                                                                    Details
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- DEFAULT ADDRESS PREVIEW -->
                                <div class="dashboard-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="mb-0 font-weight-bold"><i class="icon-map-marker mr-2 text-warning"></i> Default Delivery Address</h4>
                                        <a href="#" onclick="$('#tab-addresses-link').tab('show'); return false;" class="btn btn-link btn-sm p-0">Manage Addresses &rarr;</a>
                                    </div>
                                    <?php if ($default_address): ?>
                                        <div class="bg-light p-3 border-radius-lg rounded" style="border-radius: 8px;">
                                            <span class="address-tag tag-default mb-2">Default Shipping Address</span>
                                            <h6 class="font-weight-bold mb-1"><?= htmlspecialchars($default_address['first_name'] . ' ' . $default_address['last_name']) ?> (<?= htmlspecialchars($default_address['title']) ?>)</h6>
                                            <p class="mb-1 text-dark small"><?= htmlspecialchars($default_address['street_address']) ?>, <?= htmlspecialchars($default_address['city']) ?><?= !empty($default_address['state']) ? ', ' . htmlspecialchars($default_address['state']) : '' ?> - <strong><?= htmlspecialchars($default_address['postcode']) ?></strong></p>
                                            <p class="mb-0 text-muted small"><i class="icon-phone"></i> +91 <?= htmlspecialchars($default_address['phone']) ?></p>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted mb-3">You haven't saved any default delivery address yet.</p>
                                        <button class="btn btn-outline-primary btn-sm btn-round btn-open-add-address">
                                            <i class="icon-plus mr-1"></i> Add New Address
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <!-- ------------------------------------------------------------- -->
                            <!-- TAB 2: MY ORDERS -->
                            <!-- ------------------------------------------------------------- -->
                            <div class="tab-pane fade <?= $active_tab === 'orders' ? 'show active' : '' ?>" id="tab-orders" role="tabpanel" aria-labelledby="tab-orders-link">
                                <div class="dashboard-card">
                                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                        <h4 class="mb-0 font-weight-bold"><i class="icon-shopping-cart mr-2 text-primary"></i> Order History</h4>
                                        <span class="text-muted small">Showing <?= count($user_orders) ?> order(s)</span>
                                    </div>

                                    <?php if (empty($user_orders)): ?>
                                        <div class="empty-state">
                                            <div class="empty-state-icon"><i class="icon-shopping-cart"></i></div>
                                            <h5>No orders placed yet</h5>
                                            <p class="text-muted">You haven't placed any orders yet. Discover high-quality footwear today!</p>
                                            <a href="<?= _BASEURL ?>product-list.php" class="btn btn-primary btn-round mt-2">Explore Shop</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Order ID</th>
                                                        <th>Date</th>
                                                        <th>Payment Method</th>
                                                        <th>Payment Status</th>
                                                        <th>Order Status</th>
                                                        <th>Grand Total</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($user_orders as $ord): ?>
                                                        <tr>
                                                            <td><strong class="text-dark">#<?= $ord['order_id'] ?></strong></td>
                                                            <td class="small"><?= date('d M Y, h:i A', strtotime($ord['created_at'])) ?></td>
                                                            <td><span class="text-uppercase small font-weight-bold"><?= htmlspecialchars($ord['payment_method']) ?></span></td>
                                                            <td>
                                                                <span class="order-badge badge-pay-<?= strtolower($ord['payment_status'] ?? '') ?>">
                                                                    <?= strtoupper($ord['payment_status'] ?? '') === 'COD' ? 'COD' : ucfirst($ord['payment_status'] ?? '') ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="order-badge badge-status-<?= strtolower($ord['order_status']) ?>">
                                                                    <?= ucfirst($ord['order_status']) ?>
                                                                </span>
                                                            </td>
                                                            <td class="font-weight-bold text-dark">₹<?= number_format($ord['grand_total'], 2) ?></td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button class="btn btn-outline-primary btn-view-order" data-order-id="<?= $ord['order_id'] ?>" title="View Items & Details">
                                                                        <i class="icon-eye"></i> Details
                                                                    </button>
                                                                    <button class="btn btn-outline-info btn-track-order" data-order-id="<?= $ord['order_id'] ?>" title="Track Shipment Live">
                                                                        <i class="icon-truck"></i> Track Order
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
                            </div>


                            <!-- ------------------------------------------------------------- -->
                            <!-- TAB 3: SAVED ADDRESSES -->
                            <!-- ------------------------------------------------------------- -->
                            <div class="tab-pane fade <?= $active_tab === 'addresses' ? 'show active' : '' ?>" id="tab-addresses" role="tabpanel" aria-labelledby="tab-addresses-link">
                                <div class="dashboard-card">
                                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                        <div>
                                            <h4 class="mb-1 font-weight-bold"><i class="icon-map-marker mr-2 text-warning"></i> My Saved Addresses</h4>
                                            <p class="text-muted small mb-0">Manage multiple shipping addresses for faster checkout.</p>
                                        </div>
                                        <button class="btn btn-primary btn-round btn-sm btn-open-add-address">
                                            <i class="icon-plus mr-1"></i> Add New Address
                                        </button>
                                    </div>

                                    <?php if (empty($saved_addresses)): ?>
                                        <div class="empty-state">
                                            <div class="empty-state-icon text-warning"><i class="icon-map-marker"></i></div>
                                            <h5>No addresses saved yet</h5>
                                            <p class="text-muted">Save your delivery addresses here to skip typing during checkout!</p>
                                            <button class="btn btn-outline-primary btn-round mt-2 btn-open-add-address">
                                                <i class="icon-plus mr-1"></i> Add First Address
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="row">
                                            <?php foreach ($saved_addresses as $addr): ?>
                                                <div class="col-md-6 mb-4">
                                                    <div class="address-card <?= (int)$addr['is_default'] === 1 ? 'default-address' : '' ?>">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="address-tag <?= (int)$addr['is_default'] === 1 ? 'tag-default' : '' ?>">
                                                                <?= htmlspecialchars($addr['title']) ?> <?= (int)$addr['is_default'] === 1 ? ' (Default)' : '' ?>
                                                            </span>
                                                            <?php if ((int)$addr['is_default'] !== 1): ?>
                                                                <button class="btn btn-link btn-sm p-0 text-warning btn-set-default" data-id="<?= $addr['id'] ?>" title="Make Default Address">
                                                                    Set as Default
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>

                                                        <h6 class="font-weight-bold mb-1">
                                                            <?= htmlspecialchars($addr['first_name'] . ' ' . $addr['last_name']) ?>
                                                        </h6>
                                                        <p class="text-dark mb-2 small">
                                                            <?= htmlspecialchars($addr['street_address']) ?><br>
                                                            <?= htmlspecialchars($addr['city']) ?><?= !empty($addr['state']) ? ', ' . htmlspecialchars($addr['state']) : '' ?> - <strong><?= htmlspecialchars($addr['postcode']) ?></strong>
                                                        </p>
                                                        <p class="text-muted small mb-3">
                                                            <i class="icon-phone"></i> +91 <?= htmlspecialchars($addr['phone']) ?>
                                                        </p>

                                                        <div class="border-top pt-2 d-flex justify-content-end gap-2" style="gap: 10px;">
                                                            <button class="btn btn-outline-primary btn-xs btn-edit-address" data-id="<?= $addr['id'] ?>">
                                                                <i class="icon-edit"></i> Edit
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-xs btn-delete-address" data-id="<?= $addr['id'] ?>">
                                                                <i class="icon-close"></i> Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <!-- ------------------------------------------------------------- -->
                            <!-- TAB 4: BASIC DETAILS / PROFILE -->
                            <!-- ------------------------------------------------------------- -->
                            <div class="tab-pane fade <?= $active_tab === 'profile' ? 'show active' : '' ?>" id="tab-profile" role="tabpanel" aria-labelledby="tab-profile-link">
                                <div class="dashboard-card">
                                    <h4 class="mb-1 font-weight-bold"><i class="icon-user mr-2 text-info"></i> Basic Account Details</h4>
                                    <p class="text-muted small mb-4">Keep your personal contact details up to date.</p>

                                    <div id="profile-alert" class="alert d-none" role="alert"></div>

                                    <form id="profile-form" style="max-width: 600px;">
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold">Registered Mobile Number</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-light">+91</span>
                                                </div>
                                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-light text-success font-weight-bold">
                                                        <i class="icon-check mr-1"></i> Verified
                                                    </span>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Mobile number is linked to your OTP login account.</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="profile_name" class="font-weight-bold">Full Name *</label>
                                            <input type="text" class="form-control" id="profile_name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" placeholder="Enter your full name" required>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label for="profile_email" class="font-weight-bold">Email Address</label>
                                            <input type="email" class="form-control" id="profile_email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="name@example.com">
                                            <small class="form-text text-muted">Used for sending digital invoices and tracking notifications.</small>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-round px-4" id="btn-save-profile">
                                            <i class="icon-check mr-1"></i> SAVE CHANGES
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div><!-- End .tab-content -->
                    </div><!-- End .col-lg-9 -->
                </div><!-- End .row -->

            <?php endif; ?>

        </div><!-- End .container -->
    </div><!-- End .dashboard-wrapper -->
</main>


<!-- ========================================================================= -->
<!-- ADDRESS MODAL (ADD / EDIT) -->
<!-- ========================================================================= -->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white font-weight-bold" id="addressModalLabel"><i class="icon-map-marker mr-2"></i> Add New Address</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="address-form">
                <div class="modal-body p-4">
                    <div id="address-modal-alert" class="alert d-none" role="alert"></div>

                    <input type="hidden" name="address_id" id="addr_id" value="0">

                    <div class="row">
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold">Address Tag / Title *</label>
                            <select class="form-control" name="title" id="addr_title" required>
                                <option value="Home">Home</option>
                                <option value="Office">Office / Work</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold">First Name *</label>
                            <input type="text" class="form-control" name="first_name" id="addr_first_name" required>
                        </div>
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="addr_last_name">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Street Address *</label>
                        <input type="text" class="form-control" name="street_address" id="addr_street_address" placeholder="House number, flat, building, street name" required>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold">Town / City *</label>
                            <input type="text" class="form-control" name="city" id="addr_city" required>
                        </div>
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold">State</label>
                            <input type="text" class="form-control" name="state" id="addr_state" placeholder="e.g. Delhi, Maharashtra">
                        </div>
                        <div class="col-sm-4 form-group mb-3">
                            <label class="font-weight-bold">Postcode / ZIP *</label>
                            <input type="text" class="form-control" name="postcode" id="addr_postcode" placeholder="6-digit pincode" required maxlength="10">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 form-group mb-3">
                            <label class="font-weight-bold">Contact Mobile Phone *</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">+91</span></div>
                                <input type="tel" class="form-control" name="phone" id="addr_phone" maxlength="16" required placeholder="10-digit phone number">
                            </div>
                        </div>
                        <div class="col-sm-6 d-flex align-items-center pt-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="addr_is_default" name="is_default" value="1">
                                <label class="custom-control-label font-weight-bold" for="addr_is_default">Set as default shipping address</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-address-submit">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ========================================================================= -->
<!-- ORDER DETAILS MODAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white font-weight-bold" id="orderDetailsModalLabel">
                    <i class="icon-shopping-cart mr-2"></i> Order Breakdown <span id="modal-order-id"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="modal-order-body">
                <div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading order details...</span></div></div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- LIVE ORDER TRACKING MODAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="orderTrackModal" tabindex="-1" role="dialog" aria-labelledby="orderTrackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #19978c;">
                <h5 class="modal-title text-white font-weight-bold" id="orderTrackModalLabel">
                    <i class="icon-truck mr-2"></i> Live Order Tracking #<span id="modal-track-order-id"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="modal-track-body">
                <div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Fetching shipment status...</span></div></div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document.body).ready(function() {

    // Preserve active tab state in URL hash/query
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        var tabId = $(e.target).attr('href').replace('#tab-', '');
        if (history.pushState) {
            history.pushState(null, null, '?tab=' + tabId);
        }
    });

    // -------------------------------------------------------------
    // PROFILE FORM SUBMISSION (BASIC DETAILS)
    // -------------------------------------------------------------
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn-save-profile');
        var $alert = $('#profile-alert');
        
        $btn.prop('disabled', true).html('<i class="icon-refresh icon-spin mr-1"></i> Saving...');
        $alert.addClass('d-none').removeClass('alert-success alert-danger');

        $.ajax({
            url: 'user-api.php',
            type: 'POST',
            data: $(this).serialize() + '&action=update_profile',
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="icon-check mr-1"></i> SAVE CHANGES');
                if (res.success) {
                    $alert.removeClass('d-none').addClass('alert-success').text(res.message);
                    setTimeout(function() { $alert.addClass('d-none'); }, 4000);
                } else {
                    $alert.removeClass('d-none').addClass('alert-danger').text(res.message || 'Error updating profile');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="icon-check mr-1"></i> SAVE CHANGES');
                $alert.removeClass('d-none').addClass('alert-danger').text('Network connection error.');
            }
        });
    });


    // -------------------------------------------------------------
    // ADDRESS MANAGEMENT (OPEN MODAL, SAVE, EDIT, DELETE, DEFAULT)
    // -------------------------------------------------------------
    $('.btn-open-add-address').on('click', function() {
        $('#address-form')[0].reset();
        $('#addr_id').val('0');
        $('#addressModalLabel').html('<i class="icon-map-marker mr-2"></i> Add New Address');
        $('#address-modal-alert').addClass('d-none');
        // Auto-fill phone from logged-in mobile if available
        $('#addr_phone').val('<?= htmlspecialchars($user['mobile'] ?? '') ?>');
        $('#addressModal').modal('show');
    });

    // Edit address handler
    $(document).on('click', '.btn-edit-address', function() {
        var addrId = $(this).data('id');
        $('#address-modal-alert').addClass('d-none');

        $.ajax({
            url: 'user-api.php',
            type: 'GET',
            data: { action: 'get_address', address_id: addrId },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.address) {
                    var a = res.address;
                    $('#addr_id').val(a.id);
                    $('#addr_title').val(a.title || 'Home');
                    $('#addr_first_name').val(a.first_name);
                    $('#addr_last_name').val(a.last_name);
                    $('#addr_street_address').val(a.street_address);
                    $('#addr_city').val(a.city);
                    $('#addr_state').val(a.state);
                    $('#addr_postcode').val(a.postcode);
                    $('#addr_phone').val(a.phone);
                    $('#addr_is_default').prop('checked', parseInt(a.is_default) === 1);

                    $('#addressModalLabel').html('<i class="icon-edit mr-2"></i> Edit Address');
                    $('#addressModal').modal('show');
                } else {
                    alert(res.message || 'Unable to fetch address details.');
                }
            }
        });
    });

    // Real-time mobile input formatting and validation for Address Form
    $('#addr_phone').on('input', function() {
        var raw = $(this).val();
        var cleaned = (typeof window.cleanIndianMobile === 'function') ? window.cleanIndianMobile(raw) : raw.replace(/\D/g, '').slice(0, 10);
        if (raw !== cleaned) {
            $(this).val(cleaned);
        }
        if (cleaned.length === 10) {
            var check = (typeof window.validateIndianMobile === 'function') ? window.validateIndianMobile(cleaned) : { valid: /^[6-9]\d{9}$/.test(cleaned), message: 'Please enter a valid 10-digit mobile number starting with 6-9.' };
            if (check.valid) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        } else {
            $(this).removeClass('is-valid is-invalid');
        }
    });

    $('#addr_phone').on('paste', function() {
        var $this = $(this);
        setTimeout(function() {
            var raw = $this.val();
            var cleaned = (typeof window.cleanIndianMobile === 'function') ? window.cleanIndianMobile(raw) : raw.replace(/\D/g, '').slice(0, 10);
            $this.val(cleaned).trigger('input');
        }, 10);
    });

    // Save Address form submit
    $('#address-form').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btn-save-address-submit');
        var $alert = $('#address-modal-alert');
        var $phone = $('#addr_phone');

        var rawPhone = $phone.val();
        var phoneCheck = (typeof window.validateIndianMobile === 'function')
            ? window.validateIndianMobile(rawPhone)
            : { valid: /^[6-9]\d{9}$/.test(rawPhone), clean: rawPhone, message: 'Please enter a valid 10-digit mobile number starting with 6-9.' };

        if (!phoneCheck.valid) {
            $phone.removeClass('is-valid').addClass('is-invalid').focus();
            $alert.removeClass('d-none alert-success').addClass('alert-danger').text(phoneCheck.message);
            return;
        }

        $phone.val(phoneCheck.clean).removeClass('is-invalid').addClass('is-valid');
        $btn.prop('disabled', true).html('Saving Address...');
        $alert.addClass('d-none').removeClass('alert-success alert-danger');

        $.ajax({
            url: 'user-api.php',
            type: 'POST',
            data: $(this).serialize() + '&action=save_address',
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).text('Save Address');
                if (res.success) {
                    $alert.removeClass('d-none').addClass('alert-success').text(res.message);
                    setTimeout(function() {
                        $('#addressModal').modal('hide');
                        window.location.href = 'dashboard.php?tab=addresses';
                    }, 800);
                } else {
                    $alert.removeClass('d-none').addClass('alert-danger').text(res.message || 'Error saving address');
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Save Address');
                $alert.removeClass('d-none').addClass('alert-danger').text('Network connection error.');
            }
        });
    });

    // Set Default Address
    $(document).on('click', '.btn-set-default', function() {
        var addrId = $(this).data('id');
        if (!addrId) return;

        $.ajax({
            url: 'user-api.php',
            type: 'POST',
            data: { action: 'set_default_address', address_id: addrId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    window.location.href = 'dashboard.php?tab=addresses';
                } else {
                    alert(res.message || 'Failed to set default address.');
                }
            }
        });
    });

    // Delete Address
    $(document).on('click', '.btn-delete-address', function() {
        var addrId = $(this).data('id');
        if (!confirm('Are you sure you want to delete this saved address?')) return;

        $.ajax({
            url: 'user-api.php',
            type: 'POST',
            data: { action: 'delete_address', address_id: addrId },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    window.location.href = 'dashboard.php?tab=addresses';
                } else {
                    alert(res.message || 'Failed to delete address.');
                }
            }
        });
    });


    // -------------------------------------------------------------
    // VIEW ORDER DETAILS MODAL
    // -------------------------------------------------------------
    $(document).on('click', '.btn-view-order', function() {
        var orderId = $(this).data('order-id');
        $('#modal-order-id').text('#' + orderId);
        $('#modal-order-body').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#orderDetailsModal').modal('show');

        $.ajax({
            url: 'user-api.php',
            type: 'GET',
            data: { action: 'get_order_details', order_id: orderId },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.order) {
                    var ord = res.order;
                    var items = res.items || [];
                    
                    var html = '<div class="row mb-3">' +
                                   '<div class="col-sm-6 mb-2">' +
                                       '<h6>Order Info</h6>' +
                                       '<p class="small text-muted mb-1"><strong>Order ID:</strong> #' + ord.order_id + '</p>' +
                                       '<p class="small text-muted mb-1"><strong>Date:</strong> ' + ord.created_at + '</p>' +
                                       '<p class="small text-muted mb-1"><strong>Payment:</strong> ' + ord.payment_method.toUpperCase() + ' (' + ord.payment_status + ')</p>' +
                                       '<p class="small text-muted mb-0"><strong>Status:</strong> <span class="badge badge-info">' + ord.order_status + '</span></p>' +
                                   '</div>' +
                                   '<div class="col-sm-6 mb-2">' +
                                       '<h6>Shipping Address</h6>' +
                                       '<p class="small text-dark mb-1"><strong>' + ord.first_name + ' ' + ord.last_name + '</strong></p>' +
                                       '<p class="small text-muted mb-1">' + ord.street_address + ', ' + ord.city + ' - ' + ord.postcode + '</p>' +
                                       '<p class="small text-muted mb-0">Phone: +91-' + ord.phone + '</p>' +
                                   '</div>' +
                               '</div>' +
                               '<h6 class="border-top pt-3 mb-2">Order Items (' + items.length + ')</h6>' +
                               '<div class="table-responsive"><table class="table table-sm align-middle">' +
                               '<thead><tr><th>Item</th><th>Size</th><th>Price</th><th>Qty</th><th class="text-right">Total</th></tr></thead><tbody>';

                    items.forEach(function(it) {
                        html += '<tr>' +
                                    '<td class="d-flex align-items-center">' +
                                        '<img src="' + it.image_url + '" style="width:40px; height:40px; object-fit:cover; border-radius:4px; margin-right:10px;" alt="">' +
                                        '<div><strong class="small text-dark">' + it.product_title + '</strong></div>' +
                                    '</td>' +
                                    '<td>' + (it.size ? it.size : '-') + '</td>' +
                                    '<td>₹' + parseFloat(it.price).toFixed(2) + '</td>' +
                                    '<td>' + it.qty + '</td>' +
                                    '<td class="text-right font-weight-bold">₹' + parseFloat(it.row_total).toFixed(2) + '</td>' +
                                '</tr>';
                    });

                    var subAmt = parseFloat(ord.subtotal) || 0;
                    var grandAmt = parseFloat(ord.grand_total) || 0;
                    var gstAmt = parseFloat(ord.gst_amount) || 0;
                    var gstPercent = 0;

                    items.forEach(function(it) {
                        if (gstAmt === 0 && it.gst_amount) {
                            gstAmt += parseFloat(it.gst_amount) || 0;
                        }
                        if (!gstPercent && it.gst_percent) {
                            gstPercent = parseFloat(it.gst_percent) || 0;
                        }
                    });

                    if (!gstPercent && subAmt > 0 && gstAmt > 0) {
                        gstPercent = Math.round((gstAmt / subAmt) * 100);
                    }

                    var shippingAmt = Math.max(0, grandAmt - (subAmt + gstAmt));

                    var gstLabel = gstPercent > 0 ? ('GST (' + gstPercent + '%):') : 'GST:';
                    var gstHtml = gstAmt > 0 ? '<p class="mb-1 small">' + gstLabel + ' <strong>₹' + gstAmt.toFixed(2) + '</strong></p>' : '';
                    var shippingHtml = shippingAmt > 0 ? '<p class="mb-1 small">Shipping Charges: <strong>₹' + shippingAmt.toFixed(2) + '</strong></p>' : '';

                    html += '</tbody></table></div>' +
                            '<div class="border-top pt-2 text-right">' +
                                '<p class="mb-1 small">Subtotal: <strong>₹' + subAmt.toFixed(2) + '</strong></p>' +
                                gstHtml +
                                shippingHtml +
                                '<h5 class="text-dark font-weight-bold mb-0 mt-1 pt-1 border-top">Grand Total: ₹' + grandAmt.toFixed(2) + '</h5>' +
                            '</div>';

                    var awb = ord.courier_awb || ord.delhivery_awb;
                    var courierName = ord.courier_name ? (ord.courier_name.charAt(0).toUpperCase() + ord.courier_name.slice(1)) : 'Delhivery';
                    if (awb) {
                        html += '<div class="alert alert-info mt-3 py-2 text-center mb-0">' +
                                    '<small><strong>Courier Partner:</strong> ' + courierName + ' &nbsp;|&nbsp; <strong>AWB:</strong> ' + awb + ' &nbsp;|&nbsp; ' +
                                    '<a href="javascript:void(0)" class="alert-link btn-track-order" data-order-id="' + ord.order_id + '" data-dismiss="modal">Track Package &rarr;</a></small>' +
                                '</div>';
                    }

                    $('#modal-order-body').html(html);
                } else {
                    $('#modal-order-body').html('<div class="alert alert-danger">' + (res.message || 'Failed to load order breakdown.') + '</div>');
                }
            },
            error: function() {
                $('#modal-order-body').html('<div class="alert alert-danger">Network error occurred while fetching order details.</div>');
            }
        });
    });

    // -------------------------------------------------------------
    // LIVE ORDER TRACKING MODAL HANDLER
    // -------------------------------------------------------------
    $(document).on('click', '.btn-track-order', function(e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');
        if (!orderId) return;

        $('#modal-track-order-id').text(orderId);
        $('#modal-track-body').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted small font-weight-bold">Fetching live shipment tracking...</p></div>');
        $('#orderTrackModal').modal('show');

        $.ajax({
            url: '<?= _BASEURL ?>track-order.php',
            type: 'GET',
            data: { query: orderId },
            dataType: 'json',
            success: function(data) {
                if (!data.success) {
                    $('#modal-track-body').html('<div class="alert alert-warning mb-0"><i class="icon-exclamation-circle mr-2"></i>' + (data.message || 'Tracking details are not yet available for this order.') + '</div>');
                    return;
                }

                var statusClass = 'badge-info';
                if (data.status_type === 'DL') statusClass = 'badge-success';
                else if (data.status_type === 'IT' || data.status_type === 'UD') statusClass = 'badge-warning';

                var courierTitle = data.courier_name ? (data.courier_name.charAt(0).toUpperCase() + data.courier_name.slice(1)) : 'Courier';
                var html = '<div class="card border-0 bg-light p-3 mb-3" style="border-radius: 8px;">' +
                               '<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">' +
                                   '<div>' +
                                       '<span class="badge badge-dark px-2 py-1 text-uppercase">' + escapeHtml(courierTitle) + '</span> ' +
                                       '<strong class="ml-2 text-dark">AWB: ' + escapeHtml(data.awb || '-') + '</strong>' +
                                   '</div>' +
                                   '<span class="badge ' + statusClass + ' px-3 py-2 font-weight-bold" style="font-size: 13px;">' + escapeHtml(data.status || 'IN TRANSIT') + '</span>' +
                               '</div>' +
                               '<div class="row mt-3 text-center small">' +
                                   '<div class="col-6 border-right"><strong>Origin:</strong> ' + escapeHtml(data.origin || 'Warehouse') + '</div>' +
                                   '<div class="col-6"><strong>Destination:</strong> ' + escapeHtml(data.destination || (data.city ? data.city : 'Customer Address')) + '</div>' +
                               '</div>' +
                           '</div>';

                if (data.scans && data.scans.length > 0) {
                    html += '<h6 class="font-weight-bold text-dark mb-3"><i class="icon-list mr-1"></i> Scan Checkpoints & Updates</h6>' +
                            '<div class="timeline-wrapper px-2 py-2" style="position:relative; padding-left: 20px; border-left: 2px solid #19978c;">';
                    var scansArr = data.scans.slice().reverse();
                    scansArr.forEach(function(scanItem) {
                        var detail = scanItem.ScanDetail || scanItem;
                        var scanText = detail.Scan || detail.status || 'Package Processed';
                        var location = detail.ScannedLocation || detail.location || '';
                        var dateTime = detail.ScanDateTime || detail.timestamp || detail.date || '';

                        html += '<div class="timeline-step mb-3" style="position:relative; padding-left:15px;">' +
                                    '<h6 class="font-weight-bold text-dark mb-0 small">' + escapeHtml(scanText) + '</h6>' +
                                    (location ? '<p class="small text-muted mb-0"><i class="icon-map-marker mr-1"></i>' + escapeHtml(location) + '</p>' : '') +
                                    (dateTime ? '<small class="text-secondary"><i class="icon-clock-o mr-1"></i>' + escapeHtml(dateTime) + '</small>' : '') +
                                '</div>';
                    });
                    html += '</div>';
                } else {
                    html += '<div class="alert alert-info small mb-0"><i class="icon-info-circle mr-1"></i> Order is registered with courier partner. Awaiting location scan updates.</div>';
                }

                $('#modal-track-body').html(html);
            },
            error: function() {
                $('#modal-track-body').html('<div class="alert alert-danger mb-0">Error connecting to courier tracking network.</div>');
            }
        });
    });

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

});
</script>

<?php include('include/bottom.php');?>
