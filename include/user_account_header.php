<?php
include_once(__DIR__ . "/config.php");

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

// Counts for badges
$total_orders_count = 0;
$total_addresses_count = 0;
if ($is_logged_in) {
    $phone_param = $user['mobile'] ?? $user_mobile;
    $cnt_ord_stmt = $db->select(
        "SELECT COUNT(*) AS total FROM tbl_orders WHERE user_id = ? OR (phone = ? AND phone != '')",
        'is',
        $user_id,
        $phone_param
    );
    if ($cnt_ord_stmt && $c_row = $cnt_ord_stmt->fetch_assoc()) {
        $total_orders_count = (int)$c_row['total'];
    }

    if ($user_id > 0) {
        $cnt_addr_stmt = $db->select("SELECT COUNT(*) AS total FROM tbl_user_addresses WHERE user_id = ?", 'i', $user_id);
        if ($cnt_addr_stmt && $ca_row = $cnt_addr_stmt->fetch_assoc()) {
            $total_addresses_count = (int)$ca_row['total'];
        }
    }
}

$current_page_title = $page_title ?? 'My Account';
include(__DIR__ . '/top.php');
?>

<style>
/* Modern User Account Design System */
:root {
    --dash-primary: #19978c;
    --dash-primary-hover: #147970;
    --dash-dark: #0f172a;
    --dash-card-bg: #ffffff;
    --dash-border: #e2e8f0;
    --dash-muted: #64748b;
}

.dashboard-wrapper {
    background-color: #f8fafc;
    padding: 35px 0 60px;
    min-height: 75vh;
}

.account-breadcrumb {
    margin-bottom: 20px;
    font-size: 13px;
    color: #64748b;
}
.account-breadcrumb a {
    color: #64748b;
    text-decoration: none;
    transition: color 0.15s;
}
.account-breadcrumb a:hover {
    color: var(--dash-primary);
}
.account-breadcrumb .active {
    color: #0f172a;
    font-weight: 600;
}

/* User Header Banner */
.dashboard-user-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
    color: #ffffff;
    border-radius: 16px;
    padding: 28px 30px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
    position: relative;
    overflow: hidden;
}
.dashboard-user-header::after {
    content: '';
    position: absolute;
    right: -40px;
    top: -40px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(25, 151, 140, 0.25) 0%, rgba(25, 151, 140, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.dashboard-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #19978c 0%, #0d6e65 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    font-weight: 700;
    box-shadow: 0 6px 16px rgba(25, 151, 140, 0.35);
    border: 3px solid rgba(255, 255, 255, 0.2);
    flex-shrink: 0;
}

.dashboard-user-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
    font-size: 13px;
    color: #cbd5e1;
    margin-top: 4px;
}
.dashboard-badge-verified {
    background: rgba(16, 185, 129, 0.2);
    color: #34d399;
    border: 1px solid rgba(16, 185, 129, 0.4);
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Sidebar Navigation */
.account-nav-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
    padding: 12px;
    margin-bottom: 25px;
}

.account-nav-pills {
    display: flex;
    flex-direction: column;
    gap: 6px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.account-nav-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border-radius: 10px;
    color: #334155;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid transparent;
}
.account-nav-link-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.account-nav-link i {
    font-size: 18px;
    width: 22px;
    text-align: center;
    color: #64748b;
    transition: color 0.2s ease;
}
.account-nav-link:hover {
    background: #f1f5f9;
    color: var(--dash-primary);
}
.account-nav-link:hover i {
    color: var(--dash-primary);
}
.account-nav-link.active {
    background: linear-gradient(135deg, #19978c 0%, #11786f 100%);
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(25, 151, 140, 0.35);
}
.account-nav-link.active i {
    color: #ffffff !important;
}
.account-nav-badge {
    background: #e2e8f0;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
    transition: all 0.2s ease;
}
.account-nav-link.active .account-nav-badge {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

.account-nav-signout {
    color: #ef4444 !important;
    border-top: 1px solid #f1f5f9;
    margin-top: 6px;
    padding-top: 12px;
}
.account-nav-signout i {
    color: #ef4444 !important;
}
.account-nav-signout:hover {
    background: #fef2f2 !important;
    color: #dc2626 !important;
}

/* Common Dashboard Cards */
.dashboard-card {
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04), 0 2px 4px -2px rgba(0, 0, 0, 0.02);
    border: 1px solid #e2e8f0;
    padding: 26px;
    margin-bottom: 25px;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}
.dashboard-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.06), 0 4px 6px -4px rgba(0, 0, 0, 0.03);
}

.dashboard-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid #f1f5f9;
}
.dashboard-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Stat Box Cards */
.stat-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px 18px;
    text-align: center;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
}
.stat-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08);
}
.stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 14px;
}
.stat-icon.primary { background: #e0f2fe; color: #0284c7; }
.stat-icon.teal { background: #ccfbf1; color: #0d9488; }
.stat-icon.warning { background: #fef3c7; color: #d97706; }
.stat-icon.success { background: #dcfce7; color: #16a34a; }

.stat-number {
    font-size: 28px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
    line-height: 1.1;
}
.stat-label {
    font-size: 13px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

/* Address Cards */
.address-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 22px;
    position: relative;
    transition: all 0.2s ease;
    height: 100%;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.address-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 8px 16px rgba(0,0,0,0.06);
}
.address-card.default-address {
    border-color: #19978c;
    background: #f0fdfa;
}
.address-tag {
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    background: #e2e8f0;
    color: #475569;
    display: inline-block;
}
.address-tag.tag-default {
    background: #19978c;
    color: #ffffff;
}

/* Order Badges */
.order-badge {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 700;
    letter-spacing: 0.3px;
    display: inline-block;
    text-transform: uppercase;
}
.badge-status-pending { background: #fef3c7; color: #92400e; }
.badge-status-processing { background: #e0f2fe; color: #075985; }
.badge-status-shipped { background: #dbeafe; color: #1e40af; }
.badge-status-delivered { background: #dcfce7; color: #166534; }
.badge-status-success { background: #dcfce7; color: #166534; }
.badge-status-cancelled { background: #fee2e2; color: #991b1b; }

.badge-pay-paid { background: #dcfce7; color: #166534; }
.badge-pay-partial_paid { background: #e0f2fe; color: #0369a1; }
.badge-pay-cod { background: #f3e8ff; color: #6b21a8; font-weight: 800; }
.badge-pay-pending { background: #fef3c7; color: #92400e; }
.badge-pay-failed { background: #fee2e2; color: #991b1b; }

/* Empty States */
.empty-state {
    text-align: center;
    padding: 50px 20px;
}
.empty-state-icon {
    font-size: 54px;
    color: #cbd5e1;
    margin-bottom: 16px;
}
.empty-state h5 {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
}
.empty-state p {
    color: #64748b;
    max-width: 420px;
    margin: 0 auto 20px;
    font-size: 14px;
}

/* Order Listing & Detail Modal Styling */
.order-filter-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.order-filter-btn {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    border-radius: 20px;
    padding: 6px 16px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.order-filter-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
}
.order-filter-btn.active {
    background: #19978c;
    border-color: #19978c;
    color: #ffffff;
    box-shadow: 0 3px 8px rgba(25, 151, 140, 0.3);
}

.order-card-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    margin-bottom: 22px;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.order-card-box:hover {
    box-shadow: 0 10px 20px -3px rgba(0,0,0,0.06);
    border-color: #cbd5e1;
}

.order-card-header-bar {
    background: #f8fafc;
    padding: 14px 20px;
    border-bottom: 1px solid #eef2f5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.order-card-header-bar .order-id-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.order-card-main-body {
    padding: 20px;
}

.order-thumb-img {
    width: 68px;
    height: 68px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
    background: #f8fafc;
}

.order-courier-strip {
    background: #f0fdfa;
    border-top: 1px solid #ccfbf1;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 13px;
    color: #0f766e;
}

/* Modal Stepper */
.order-stepper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    margin: 15px 0 25px;
    padding: 0 10px;
}
.order-stepper::before {
    content: '';
    position: absolute;
    top: 18px;
    left: 40px;
    right: 40px;
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
}
.stepper-step {
    position: relative;
    z-index: 2;
    text-align: center;
    flex: 1;
}
.stepper-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ffffff;
    border: 3px solid #cbd5e1;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-size: 14px;
    font-weight: bold;
    transition: all 0.3s ease;
}
.stepper-label {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
}
.stepper-step.completed .stepper-icon {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
}
.stepper-step.completed .stepper-label {
    color: #10b981;
    font-weight: 700;
}
.stepper-step.active .stepper-icon {
    background: #19978c;
    border-color: #19978c;
    color: #ffffff;
    box-shadow: 0 0 0 6px rgba(25, 151, 140, 0.2);
}
.stepper-step.active .stepper-label {
    color: #19978c;
    font-weight: 700;
}

/* Modal Info Summary Cards */
.modal-info-card {
    background: #f8fafc;
    border: 1px solid #eef2f5;
    border-radius: 12px;
    padding: 16px;
    height: 100%;
}
.modal-info-card-title {
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Financial Box */
.financial-summary-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 22px;
}
.cod-due-callout {
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 8px;
    padding: 12px 16px;
    margin-top: 12px;
    color: #92400e;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Print Invoice Styling */
@media print {
    body * {
        visibility: hidden;
    }
    #orderDetailsModal, #orderDetailsModal * {
        visibility: visible;
    }
    #orderDetailsModal {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    .modal-header .close, .modal-footer {
        display: none !important;
    }
}

/* Mobile responsive tabs */
@media (max-width: 767px) {
    .account-nav-card {
        padding: 8px;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }
    .account-nav-pills {
        flex-direction: row;
        gap: 8px;
    }
    .account-nav-link {
        padding: 10px 14px;
        font-size: 13px;
    }
    .dashboard-user-header {
        padding: 20px;
        text-align: center;
        justify-content: center;
    }
    .dashboard-user-meta {
        justify-content: center;
    }
    .order-stepper {
        display: none;
    }
}
</style>

<main class="main">
    <div class="dashboard-wrapper">
        <div class="container">

            <div class="account-breadcrumb">
                <a href="<?= _BASEURL ?>index.php"><i class="icon-home mr-1"></i> Home</a>
                <span class="mx-2">/</span>
                <a href="<?= _BASEURL ?>dashboard.php">My Account</a>
                <?php if (!empty($current_account_page) && $current_account_page !== 'dashboard'): ?>
                    <span class="mx-2">/</span>
                    <span class="active"><?= htmlspecialchars($current_page_title) ?></span>
                <?php endif; ?>
            </div>

            <?php if (!$is_logged_in): ?>
                <!-- UNAUTHENTICATED STATE -->
                <div class="row justify-content-center py-5">
                    <div class="col-md-7 col-lg-5">
                        <div class="dashboard-card text-center py-5">
                            <div class="empty-state-icon text-primary mb-3">
                                <i class="icon-user" style="font-size: 64px;"></i>
                            </div>
                            <h3 class="font-weight-bold mb-2 text-dark">Access Your Customer Portal</h3>
                            <p class="text-muted mb-4 small">
                                Sign in with your registered mobile number using instant secure OTP verification to view your orders, live tracking, and saved delivery addresses.
                            </p>
                            <a href="#signin-modal" data-toggle="modal" class="btn btn-primary btn-round btn-lg btn-block shadow-sm" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                                <i class="icon-long-arrow-right mr-2"></i> SIGN IN / REGISTER WITH OTP
                            </a>
                            <a href="<?= _BASEURL ?>index.php" class="btn btn-link btn-sm mt-3 text-muted">Return to Store Homepage</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- USER HEADER BANNER -->
                <div class="dashboard-user-header">
                    <div class="d-flex align-items-center" style="gap: 18px;">
                        <div class="dashboard-avatar">
                            <?= strtoupper(substr(!empty($user['name']) ? $user['name'] : 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <h3 class="mb-1 text-white font-weight-bold" style="font-size: 22px;">
                                Hello, <?= htmlspecialchars(!empty($user['name']) ? $user['name'] : 'Valued Customer') ?>!
                            </h3>
                            <div class="dashboard-user-meta">
                                <span><i class="icon-phone mr-1"></i> +91 <?= htmlspecialchars($user['mobile']) ?></span>
                                <span class="dashboard-badge-verified"><i class="icon-check"></i> Verified Mobile</span>
                                <?php if (!empty($user['email'])): ?>
                                    <span>•</span>
                                    <span><i class="icon-envelope mr-1"></i> <?= htmlspecialchars($user['email']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="<?= _BASEURL ?>logout.php" class="btn btn-outline-light btn-sm btn-round px-3" style="border-radius: 20px;">
                            <i class="icon-long-arrow-right mr-1"></i> Sign Out
                        </a>
                    </div>
                </div>

                <div class="row">
                    <!-- SIDEBAR NAVIGATION -->
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="account-nav-card">
                            <ul class="account-nav-pills">
                                <li>
                                    <a href="<?= _BASEURL ?>dashboard.php" class="account-nav-link <?= ($current_account_page ?? '') === 'dashboard' ? 'active' : '' ?>">
                                        <div class="account-nav-link-left">
                                            <i class="icon-dashboard"></i>
                                            <span>Overview</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= _BASEURL ?>orders.php" class="account-nav-link <?= ($current_account_page ?? '') === 'orders' ? 'active' : '' ?>">
                                        <div class="account-nav-link-left">
                                            <i class="icon-shopping-cart"></i>
                                            <span>My Orders</span>
                                        </div>
                                        <span class="account-nav-badge"><?= $total_orders_count ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= _BASEURL ?>addresses.php" class="account-nav-link <?= ($current_account_page ?? '') === 'addresses' ? 'active' : '' ?>">
                                        <div class="account-nav-link-left">
                                            <i class="icon-map-marker"></i>
                                            <span>Saved Addresses</span>
                                        </div>
                                        <span class="account-nav-badge"><?= $total_addresses_count ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= _BASEURL ?>profile.php" class="account-nav-link <?= ($current_account_page ?? '') === 'profile' ? 'active' : '' ?>">
                                        <div class="account-nav-link-left">
                                            <i class="icon-user"></i>
                                            <span>Basic Details</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= _BASEURL ?>logout.php" class="account-nav-link account-nav-signout">
                                        <div class="account-nav-link-left">
                                            <i class="icon-long-arrow-right"></i>
                                            <span>Sign Out</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- MAIN CONTENT CONTAINER -->
                    <div class="col-lg-9 col-md-8">
            <?php endif; ?>
