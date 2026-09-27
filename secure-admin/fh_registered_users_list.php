<?php
include('top.php');

// Fetch Metrics
$total_users = 0;
$active_users = 0;
$inactive_users = 0;
$users_with_orders = 0;
$total_revenue = 0.00;

$metrics_res = $conn->query("
    SELECT 
        COUNT(DISTINCT u.id) AS total,
        COUNT(DISTINCT CASE WHEN u.status = 'active' THEN u.id END) AS active_cnt,
        COUNT(DISTINCT CASE WHEN u.status = 'inactive' OR u.status = 'blocked' THEN u.id END) AS inactive_cnt,
        COUNT(DISTINCT CASE WHEN o.order_id IS NOT NULL THEN u.id END) AS with_orders_cnt,
        (SELECT COALESCE(SUM(grand_total), 0) FROM tbl_orders WHERE user_id > 0) AS revenue_sum
    FROM tbl_users u
    LEFT JOIN tbl_orders o ON o.user_id = u.id
");
if ($metrics_res && $mrow = $metrics_res->fetch_assoc()) {
    $total_users        = (int)$mrow['total'];
    $active_users       = (int)$mrow['active_cnt'];
    $inactive_users     = (int)$mrow['inactive_cnt'];
    $users_with_orders  = (int)$mrow['with_orders_cnt'];
    $total_revenue      = (float)$mrow['revenue_sum'];
}

// Configurable Pager Configuration (like fh_order_list.php)
$recordsPerPage = isset($_SESSION["_RECORDPERPAGE_"]) ? (int)$_SESSION["_RECORDPERPAGE_"] : (defined('_RECORDPERPAGE_') ? (int)_RECORDPERPAGE_ : 20);
if ($recordsPerPage <= 0) {
    $recordsPerPage = 20;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// Filter & Search handling
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : (isset($_GET['q']) ? trim($_GET['q']) : '');

$where_clauses = [];
$having_clause = "";

if ($filter === 'active') {
    $where_clauses[] = "u.status = 'active'";
} elseif ($filter === 'inactive') {
    $where_clauses[] = "u.status = 'inactive'";
} elseif ($filter === 'with_orders') {
    $having_clause = " HAVING total_orders > 0 ";
}

if (!empty($search_query)) {
    $safe_search = $conn->real_escape_string($search_query);
    $where_clauses[] = "(
        u.name LIKE '%{$safe_search}%' 
        OR u.mobile LIKE '%{$safe_search}%' 
        OR u.email LIKE '%{$safe_search}%'
        OR u.id = '{$safe_search}'
    )";
}

$where_sql = !empty($where_clauses) ? " WHERE " . implode(' AND ', $where_clauses) : "";

// Count Total Matching Records for Pagination
$count_sql = "
    SELECT COUNT(*) AS total_filtered FROM (
        SELECT u.id, COUNT(DISTINCT o.order_id) AS total_orders
        FROM tbl_users u
        LEFT JOIN tbl_orders o ON o.user_id = u.id
        {$where_sql}
        GROUP BY u.id
        {$having_clause}
    ) AS sub
";
$count_res = $conn->query($count_sql);
$totalRecords = ($count_res && $crow = $count_res->fetch_assoc()) ? (int)$crow['total_filtered'] : 0;

$maxPage = max(1, (int)ceil($totalRecords / $recordsPerPage));
if ($page > $maxPage) {
    $page = $maxPage;
}
$offset = ($page - 1) * $recordsPerPage;

$query = "
    SELECT 
        u.id,
        u.name,
        COALESCE(
            NULLIF(TRIM(u.name), ''),
            (SELECT CONCAT(first_name, ' ', last_name) FROM tbl_orders WHERE user_id = u.id ORDER BY order_id DESC LIMIT 1),
            (SELECT CONCAT(first_name, ' ', last_name) FROM tbl_user_addresses WHERE user_id = u.id ORDER BY id DESC LIMIT 1),
            'Customer'
        ) AS display_name,
        u.mobile,
        u.email,
        u.role,
        u.status,
        u.created_at,
        COUNT(DISTINCT o.order_id) AS total_orders,
        COALESCE(SUM(o.grand_total), 0) AS total_spent
    FROM tbl_users u
    LEFT JOIN tbl_orders o ON o.user_id = u.id
    {$where_sql}
    GROUP BY u.id
    {$having_clause}
    ORDER BY u.id DESC
    LIMIT {$offset}, {$recordsPerPage}
";

$users_res = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registered User List || Bunny Boss Admin</title>
    <?php include('include/css.php'); ?>
    <style>
        @media (min-width: 992px) {
            .col-md-5th {
                width: 20%;
                float: left;
                position: relative;
                min-height: 1px;
                padding-left: 10px;
                padding-right: 10px;
            }
        }
        .metric-card {
            background: #fff;
            border: 1px solid #e4e5e7;
            border-radius: 6px;
            padding: 12px 18px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        .metric-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
        }
        .metric-content h4 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #333;
        }
        .metric-content span {
            font-size: 12px;
            color: #777;
            text-transform: uppercase;
            font-weight: 600;
        }
        .badge-user-status {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 12px;
            letter-spacing: 0.3px;
            cursor: pointer;
        }
        .badge-status-active {
            background-color: #e6f4ea;
            color: #137333;
            border: 1px solid #ceead6;
        }
        .badge-status-inactive {
            background-color: #fce8e6;
            color: #d93025;
            border: 1px solid #fad2cf;
        }
        .action-btn-group {
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }
        .table-responsive {
            border: 1px solid #e4e5e7;
            border-radius: 4px;
            background: #fff;
            padding: 10px;
        }
        /* Pager & Pagination Component Styling (like fh_order_list.php) */
        .select-pagination {
            margin: 5px 0;
        }
        .select-pagination .d-flex {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .select-pagination .recordsPerPage {
            width: 80px;
            display: inline-block;
            height: 32px;
            padding: 4px 8px;
            font-size: 13px;
            margin-right: 8px;
        }
        .select-pagination span {
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }
        #pagination-result .float-right {
            float: right !important;
        }
        #pagination-result .pagination {
            margin: 0;
        }
        #pagination-result .pagination > li > a {
            color: #009688;
            border-color: #e4e5e7;
            padding: 5px 12px;
            font-size: 13px;
            cursor: pointer;
        }
        #pagination-result .pagination > .active > a,
        #pagination-result .pagination > .active > a:focus,
        #pagination-result .pagination > .active > a:hover {
            background-color: #009688 !important;
            border-color: #009688 !important;
            color: #fff !important;
        }
        #pagination-result .pagination > li > a:hover {
            background-color: #e0f2f1;
            border-color: #b2dfdb;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Left side column -->
    <aside class="main-sidebar">
        <?php include('include/sidebar-left.php'); ?>
        <?php include('include/toggle_switch_list.php'); ?>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <?php include('include/menu-header.php'); ?>
            </div>
        </div>

        <section class="content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-bd lobidisable">
                        <div class="panel-heading" style="background-color: #009688; color: #ffffff;">
                            <span style="font-size: 16px; font-weight: 600;">
                                <i class="fa fa-users" style="margin-right: 8px;"></i> Registered User List
                            </span>
                        </div>
                        <div class="panel-body">
                            <!-- Metrics Row -->
                            <div class="row">
                                <div class="col-md-5th col-sm-6 col-xs-12">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #009688;"><i class="fa fa-users"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($total_users) ?></h4>
                                            <span>Total Customers</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5th col-sm-6 col-xs-12">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #137333;"><i class="fa fa-user-plus"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($active_users) ?></h4>
                                            <span>Active Customers</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5th col-sm-6 col-xs-12">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #d93025;"><i class="fa fa-user-times"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($inactive_users) ?></h4>
                                            <span>Inactive Customers</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5th col-sm-6 col-xs-12">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #1a73e8;"><i class="fa fa-shopping-bag"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($users_with_orders) ?></h4>
                                            <span>With Orders</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5th col-sm-6 col-xs-12">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #f2994a;"><i class="fa fa-inr"></i></div>
                                        <div class="metric-content">
                                            <h4>₹<?= number_format($total_revenue, 2) ?></h4>
                                            <span>Orders Revenue</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter & Action Toolbar -->
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-7 col-sm-12" style="margin-bottom: 8px;">
                                    <div class="btn-group" role="group">
                                        <a href="fh_registered_users_list.php<?= !empty($search_query) ? '?search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= ($filter === 'all') ? 'btn-primary' : 'btn-default' ?>" style="<?= ($filter === 'all') ? 'background-color: #009688; border-color: #009688;' : '' ?>">
                                            All (<?= $total_users ?>)
                                        </a>
                                        <a href="fh_registered_users_list.php?filter=with_orders<?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= ($filter === 'with_orders') ? 'btn-info' : 'btn-default' ?>">
                                            With Orders (<?= $users_with_orders ?>)
                                        </a>
                                        <a href="fh_registered_users_list.php?filter=active<?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= ($filter === 'active') ? 'btn-success' : 'btn-default' ?>">
                                            Active (<?= $active_users ?>)
                                        </a>
                                        <a href="fh_registered_users_list.php?filter=inactive<?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= ($filter === 'inactive') ? 'btn-danger' : 'btn-default' ?>">
                                            Inactive (<?= $inactive_users ?>)
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-5 col-sm-12 text-right">
                                    <form method="GET" action="fh_registered_users_list.php" class="form-inline" style="display: inline-block;">
                                        <?php if ($filter !== 'all'): ?>
                                            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                                        <?php endif; ?>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Search name, phone, email, ID..." value="<?= htmlspecialchars($search_query) ?>" style="min-width: 190px; border-radius: 4px 0 0 4px;">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary btn-sm" type="submit" style="background-color: #009688; border-color: #009688; border-radius: 0 4px 4px 0;">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                                <?php if (!empty($search_query)): ?>
                                                    <a href="fh_registered_users_list.php<?= ($filter !== 'all') ? '?filter=' . $filter : '' ?>" class="btn btn-default btn-sm" title="Clear Search">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </form>
                                    <button class="btn btn-default btn-sm" onclick="window.print();" style="margin-left: 5px;">
                                        <i class="fa fa-print" style="margin-right: 4px;"></i> Print
                                    </button>
                                </div>
                            </div>

                            <!-- Users Table -->
                            <div class="table-responsive">
                                <table id="usersTable" class="table table-bordered table-striped table-hover mb-0">
                                    <thead>
                                        <tr class="info">
                                            <th width="40">ID</th>
                                            <th width="120">Registered Date</th>
                                            <th>Customer Name</th>
                                            <th>Mobile Number</th>
                                            <th>Email</th>
                                            <th width="80">Role</th>
                                            <th width="80" class="text-center">Status</th>
                                            <th width="90" class="text-center">Orders</th>
                                            <th width="110" class="text-right">Total Spent</th>
                                            <th width="90" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($users_res && $users_res->num_rows > 0):
                                            while ($row = $users_res->fetch_assoc()):
                                                $id          = (int)$row['id'];
                                                $name        = htmlspecialchars($row['display_name']);
                                                $mobile      = htmlspecialchars($row['mobile']);
                                                $email       = htmlspecialchars($row['email'] ?? '');
                                                $role        = htmlspecialchars($row['role']);
                                                $status      = htmlspecialchars($row['status']);
                                                $joined      = date('d M Y, h:i A', strtotime($row['created_at']));
                                                $orders_cnt  = (int)$row['total_orders'];
                                                $spent_total = (float)$row['total_spent'];

                                                $status_class = ($status === 'active') ? 'badge-status-active' : 'badge-status-inactive';
                                        ?>
                                        <tr id="user-row-<?= $id ?>">
                                            <td><strong>#<?= $id ?></strong></td>
                                            <td style="font-size: 12px; color: #555;"><?= $joined ?></td>
                                            <td>
                                                <strong style="color: #222;"><?= $name ?></strong>
                                            </td>
                                            <td>
                                                <a href="tel:<?= $mobile ?>" style="color: #009688; font-weight: 600;">
                                                    <i class="fa fa-phone mr-1"></i> <?= $mobile ?>
                                                </a>
                                                &nbsp;
                                                <a href="https://wa.me/91<?= $mobile ?>" target="_blank" title="WhatsApp Customer" style="color: #25D366; font-size: 14px;">
                                                    <i class="fa fa-whatsapp"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if (!empty($email)): ?>
                                                    <a href="mailto:<?= $email ?>" style="color: #337ab7; font-size: 13px;">
                                                        <?= $email ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size: 12px;">--</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="label <?= ($role === 'admin') ? 'label-danger' : (($role === 'staff') ? 'label-warning' : 'label-default') ?>" style="font-size: 11px;">
                                                    <?= ucfirst($role) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-user-status <?= $status_class ?> btn-toggle-status" data-id="<?= $id ?>" data-status="<?= $status ?>" title="Click to toggle status">
                                                    <?= ucfirst($status) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($orders_cnt > 0): ?>
                                                    <span class="badge" style="background-color: #1a73e8; font-size: 12px; font-weight: 700;">
                                                        <?= $orders_cnt ?> Orders
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted" style="font-size: 12px;">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right" style="font-weight: 600; color: #333;">
                                                ₹<?= number_format($spent_total, 2) ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="action-btn-group justify-content-center">
                                                    <!-- View Profile Button -->
                                                    <button type="button" class="btn btn-info btn-xs btn-view-user" data-id="<?= $id ?>" title="View Customer Details, Addresses & Orders">
                                                        <i class="fa fa-eye"></i> Details
                                                    </button>
                                                    <!-- Delete Button -->
                                                    <button type="button" class="btn btn-danger btn-xs btn-delete-user" data-id="<?= $id ?>" title="Delete User">
                                                        <i class="fa fa-trash-o"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                            endwhile;
                                        else:
                                        ?>
                                        <tr>
                                            <td colspan="10" class="text-center py-4" style="color: #888; padding: 35px 20px;">
                                                <i class="fa fa-users fa-3x" style="display: block; margin-bottom: 12px; color: #ccc;"></i>
                                                <span style="font-size: 15px; font-weight: 500;">No customers found matching the search or filter criteria.</span>
                                            </td>
                                        </tr>
                                        <?php
                                        endif;
                                        ?>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive -->

                            <!-- Configurable Pager Component (like fh_order_list.php) -->
                            <div id="pagination-result" style="margin-top: 15px;">
                                <?= include_pagination_component($page, $recordsPerPage, $totalRecords) ?>
                            </div>

                        </div><!-- /.panel-body -->
                    </div><!-- /.panel -->
                </div><!-- /.col-sm-12 -->
            </div><!-- /.row -->
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <?php include('include/footer.php'); ?>
</div><!-- /.wrapper -->

<!-- Customer Details Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background-color: #009688; color: #ffffff; padding: 14px 20px;">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" style="margin: 0; font-weight: 600; font-size: 16px;">
                    <i class="fa fa-user-circle mr-2"></i> Customer Profile #<span id="cust-modal-id"></span>
                </h4>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div id="cust-modal-loading" class="text-center py-4">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="text-muted small mt-2">Loading customer profile & order history...</p>
                </div>

                <div id="cust-modal-content" style="display: none;">
                    <!-- Customer Overview -->
                    <div class="row mb-3" style="border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <div class="col-sm-4 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">Customer Name</label>
                            <h4 id="cust-name" style="margin: 0; font-weight: 700; color: #222;"></h4>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">Mobile Phone</label>
                            <span id="cust-phone-wrap"></span>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">Email Address</label>
                            <span id="cust-email" style="font-weight: 600; color: #555;"></span>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">Registered On</label>
                            <span id="cust-date" class="text-dark" style="font-size: 13px;"></span>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">Total Orders</label>
                            <span id="cust-orders-cnt" class="badge" style="background-color: #1a73e8; font-size: 13px;"></span>
                        </div>
                        <div class="col-sm-4 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">Total Spent</label>
                            <strong id="cust-spent-sum" style="color: #009688; font-size: 16px;"></strong>
                        </div>
                    </div>

                    <!-- Saved Delivery Addresses Section -->
                    <h5 class="font-weight-bold text-dark mt-3 mb-2" style="font-size: 15px;">
                        <i class="fa fa-map-marker text-danger mr-1"></i> Saved Delivery Addresses (<span id="cust-addr-count">0</span>)
                    </h5>
                    <div id="cust-addresses-list" class="row mb-3"></div>

                    <!-- Recent Orders Section -->
                    <h5 class="font-weight-bold text-dark mt-4 mb-2" style="font-size: 15px;">
                        <i class="fa fa-shopping-cart text-primary mr-1"></i> Order History (<span id="cust-orders-count">0</span>)
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr class="active" style="font-size: 12px;">
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Order Status</th>
                                    <th>Dispatch</th>
                                    <th>Courier / AWB</th>
                                </tr>
                            </thead>
                            <tbody id="cust-orders-list"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light" style="padding: 12px 20px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include('include/js.php'); ?>
<script>
$(document).ready(function() {
    // Configurable Pager - Page navigation (like fh_order_list.php)
    $(document).on("click", ".page-link-v2", function(e) {
        e.preventDefault();
        var pageNo = $(this).attr("href");
        if (!pageNo || pageNo === "javascript:void(0);" || isNaN(pageNo)) return;
        var url = new URL(window.location.href);
        url.searchParams.set('page', pageNo);
        window.location.href = url.toString();
    });

    // Configurable Pager - Records per page change (reset to page 1)
    $(document).on("change", ".recordsPerPage", function(e) {
        var selectedval = $(this).val();
        $.ajax({
            type: 'GET',
            url: 'get_ajaxdata.php?function=setPerpageValue&selectedval=' + selectedval,
            success: function() {
                var url = new URL(window.location.href);
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            }
        });
        e.stopImmediatePropagation();
    });

    // View Customer Profile Details
    $(document).on('click', '.btn-view-user', function() {
        var userId = $(this).data('id');
        var $modal = $('#customerModal');
        var $loading = $('#cust-modal-loading');
        var $content = $('#cust-modal-content');

        $loading.show();
        $content.hide();
        $modal.modal('show');
        $('#cust-modal-id').text(userId);

        $.ajax({
            url: 'ajax/registered-users-results',
            type: 'POST',
            data: {
                action: 'get_user_details',
                id: userId
            },
            dataType: 'json',
            success: function(res) {
                $loading.hide();
                if (res && res.success && res.user) {
                    var u = res.user;
                    $('#cust-name').text(u.display_name || 'Customer');
                    $('#cust-date').text(u.created_at);
                    
                    var phoneHtml = '<a href="tel:' + u.mobile + '" class="font-weight-bold" style="color: #009688; font-size: 15px;"><i class="fa fa-phone mr-1"></i> ' + u.mobile + '</a>' +
                                    ' &nbsp; <a href="https://wa.me/91' + u.mobile + '" target="_blank" class="btn btn-success btn-xs" style="padding: 2px 8px; border-radius: 10px;"><i class="fa fa-whatsapp"></i> Chat</a>';
                    $('#cust-phone-wrap').html(phoneHtml);
                    $('#cust-email').text(u.email || '--');
                    $('#cust-orders-cnt').text((u.total_orders || 0) + ' Orders');
                    $('#cust-spent-sum').text('₹' + parseFloat(u.total_spent || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}));

                    // Addresses
                    var addrs = res.addresses || [];
                    $('#cust-addr-count').text(addrs.length);
                    var addrHtml = '';
                    if (addrs.length > 0) {
                        addrs.forEach(function(a) {
                            addrHtml += '<div class="col-sm-6 mb-2">';
                            addrHtml += '<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; font-size: 13px;">';
                            addrHtml += '<strong>' + (a.title || 'Address') + '</strong> ' + (a.is_default == 1 ? '<span class="label label-success" style="font-size: 10px;">Default</span>' : '') + '<br>';
                            addrHtml += '<span>' + a.first_name + ' ' + (a.last_name || '') + ' (' + a.phone + ')</span><br>';
                            addrHtml += '<span class="text-muted">' + a.street_address + ', ' + a.city + ', ' + (a.state || '') + ' - ' + a.postcode + '</span>';
                            addrHtml += '</div></div>';
                        });
                    } else {
                        addrHtml = '<div class="col-sm-12"><p class="text-muted small">No saved addresses on file.</p></div>';
                    }
                    $('#cust-addresses-list').html(addrHtml);

                    // Orders
                    var ords = res.orders || [];
                    $('#cust-orders-count').text(ords.length);
                    var ordHtml = '';
                    if (ords.length > 0) {
                        ords.forEach(function(o) {
                            var awb = o.courier_awb || o.delhivery_awb || '--';
                            ordHtml += '<tr style="font-size: 13px;">';
                            ordHtml += '<td><strong>#' + o.order_id + '</strong></td>';
                            ordHtml += '<td>' + o.created_at.substring(0, 16) + '</td>';
                            ordHtml += '<td><strong>₹' + parseFloat(o.grand_total).toFixed(2) + '</strong></td>';
                            ordHtml += '<td><span class="label label-default">' + (o.payment_method || 'cod').toUpperCase() + '</span></td>';
                            ordHtml += '<td><span class="label label-info">' + o.order_status + '</span></td>';
                            ordHtml += '<td><span class="label label-warning">' + (o.dispatch_status || 'pending') + '</span></td>';
                            ordHtml += '<td>' + (o.courier_name ? '<strong>' + o.courier_name + ':</strong> ' : '') + awb + '</td>';
                            ordHtml += '</tr>';
                        });
                    } else {
                        ordHtml = '<tr><td colspan="7" class="text-center text-muted py-3">No orders placed yet.</td></tr>';
                    }
                    $('#cust-orders-list').html(ordHtml);

                    $content.fadeIn();
                } else {
                    $loading.html('<div class="alert alert-danger">' + ((res && res.message) ? res.message : 'Failed to load details.') + '</div>').show();
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText);
                $loading.html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Error communicating with server. (' + (error || status) + ')</div>').show();
            }
        });
    });

    // Toggle User Status (Active / Inactive) - Delegated
    $(document).on('click', '.btn-toggle-status', function() {
        var $badge = $(this);
        var userId = $badge.data('id');
        var currentStatus = $badge.data('status');
        var newStatus = (currentStatus === 'active') ? 'inactive' : 'active';

        $.ajax({
            url: 'ajax/registered-users-results',
            type: 'POST',
            data: {
                action: 'toggle_status',
                id: userId,
                status: newStatus
            },
            dataType: 'json',
            success: function(res) {
                if (res && res.success) {
                    $badge.data('status', newStatus);
                    $badge.removeClass('badge-status-active badge-status-inactive')
                          .addClass((newStatus === 'active') ? 'badge-status-active' : 'badge-status-inactive')
                          .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                } else {
                    alert((res && res.message) ? res.message : 'Failed to update status.');
                }
            },
            error: function() {
                alert('Network error while updating status.');
            }
        });
    });

    // Delete User
    $(document).on('click', '.btn-delete-user', function() {
        var userId = $(this).data('id');
        if (!confirm('Are you sure you want to permanently delete this customer account? This cannot be undone.')) {
            return;
        }

        $.ajax({
            url: 'ajax/registered-users-results',
            type: 'POST',
            data: {
                action: 'delete_user',
                id: userId
            },
            dataType: 'json',
            success: function(res) {
                if (res && res.success) {
                    $('#user-row-' + userId).fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    alert(res.message || 'Failed to delete user.');
                }
            },
            error: function(xhr, status, error) {
                alert('Network error occurred while deleting: ' + (error || status));
            }
        });
    });
});
</script>
</body>
</html>
