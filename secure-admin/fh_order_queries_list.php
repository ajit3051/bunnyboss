<?php
include('top.php');

// Fetch Summary Counts
$total_queries       = 0;
$open_queries        = 0;
$inprogress_queries  = 0;
$resolved_queries    = 0;

$count_res = $conn->query("
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) AS open_cnt,
        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS inprogress_cnt,
        SUM(CASE WHEN status = 'resolved' OR status = 'closed' THEN 1 ELSE 0 END) AS resolved_cnt
    FROM tbl_order_queries
");
if ($count_res && $row = $count_res->fetch_assoc()) {
    $total_queries       = (int)$row['total'];
    $open_queries        = (int)$row['open_cnt'];
    $inprogress_queries  = (int)$row['inprogress_cnt'];
    $resolved_queries    = (int)$row['resolved_cnt'];
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

// Status filter & Search query handling
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$search_query  = isset($_GET['search']) ? trim($_GET['search']) : (isset($_GET['q']) ? trim($_GET['q']) : '');

$where_clauses = [];
if (in_array($filter_status, ['open', 'in_progress', 'resolved', 'closed'], true)) {
    $where_clauses[] = "q.status = '" . $conn->real_escape_string($filter_status) . "'";
}

if (!empty($search_query)) {
    $safe_search = $conn->real_escape_string($search_query);
    $where_clauses[] = "(
        q.order_id = '{$safe_search}'
        OR q.customer_name LIKE '%{$safe_search}%' 
        OR q.customer_phone LIKE '%{$safe_search}%' 
        OR q.customer_email LIKE '%{$safe_search}%'
        OR q.issue_type LIKE '%{$safe_search}%'
        OR q.subject LIKE '%{$safe_search}%'
        OR q.message LIKE '%{$safe_search}%'
        OR q.id = '{$safe_search}'
    )";
}

$where_sql = !empty($where_clauses) ? " WHERE " . implode(' AND ', $where_clauses) : "";

// Count Total Matching Records for Pagination
$count_query = "SELECT COUNT(*) AS total_filtered FROM tbl_order_queries q {$where_sql}";
$count_filtered_res = $conn->query($count_query);
$totalRecords = ($count_filtered_res && $crow = $count_filtered_res->fetch_assoc()) ? (int)$crow['total_filtered'] : 0;

$maxPage = max(1, (int)ceil($totalRecords / $recordsPerPage));
if ($page > $maxPage) {
    $page = $maxPage;
}
$offset = ($page - 1) * $recordsPerPage;

$queries_sql = "
    SELECT q.*, o.grand_total, o.order_status, o.payment_method, o.courier_name, o.courier_awb, o.delhivery_awb
    FROM tbl_order_queries q
    LEFT JOIN tbl_orders o ON o.order_id = q.order_id
    {$where_sql} 
    ORDER BY q.id DESC 
    LIMIT {$offset}, {$recordsPerPage}
";
$queries_res = $conn->query($queries_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Order Queries & Issues || Bunny Boss Admin</title>
    <?php include('include/css.php'); ?>
    <style>
        .badge-status {
            display: inline-block;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 12px;
            letter-spacing: 0.3px;
        }
        .badge-status-open {
            background-color: #fce8e6;
            color: #d93025;
            border: 1px solid #fad2cf;
        }
        .badge-status-in_progress {
            background-color: #e8f0fe;
            color: #1a73e8;
            border: 1px solid #d2e3fc;
        }
        .badge-status-resolved {
            background-color: #e6f4ea;
            color: #137333;
            border: 1px solid #ceead6;
        }
        .badge-status-closed {
            background-color: #f1f3f4;
            color: #5f6368;
            border: 1px solid #dadce0;
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

        /* Order Query Modal Custom Styles */
        #queryModal .modal-dialog {
            max-width: 900px;
            width: 92%;
            margin: 30px auto;
        }
        #queryModal .modal-content {
            border-radius: 6px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        #queryModal .modal-header {
            background-color: #009688;
            color: #ffffff;
            padding: 14px 20px;
        }
        #queryModal .modal-header .close {
            color: #ffffff;
            opacity: 0.9;
            font-size: 24px;
            margin-top: -2px;
            text-shadow: none;
        }
        #queryModal .modal-body {
            padding: 20px 22px;
            background: #f8fafc;
            max-height: 80vh;
            overflow-y: auto;
        }
        .qmodal-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px 18px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .qmodal-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            margin: 0 0 12px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .qmodal-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            display: block;
            margin-bottom: 3px;
        }
        .qmodal-val {
            font-size: 13px;
            color: #1e293b;
            font-weight: 600;
            word-break: break-word;
        }
        .qmodal-issue-box {
            background: #fffdf5;
            border: 1px solid #fde68a;
            border-left: 4px solid #f59e0b;
            border-radius: 4px;
            padding: 14px 16px;
            font-size: 14px;
            line-height: 1.6;
            color: #1f2937;
            white-space: pre-wrap;
        }
        .qmodal-order-summary {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 10px;
        }
        .qmodal-table {
            margin-bottom: 0;
            background: #fff;
            font-size: 12px;
        }
        .qmodal-table th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
            padding: 6px 10px;
            border-bottom: 2px solid #e2e8f0 !important;
        }
        .qmodal-table td {
            padding: 6px 10px;
            vertical-align: middle !important;
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
                                <i class="fa fa-question-circle-o" style="margin-right: 8px;"></i> Customer Order Queries & Issues
                            </span>
                        </div>
                        <div class="panel-body">
                            <!-- Metrics Row -->
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #009688;"><i class="fa fa-question-circle"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($total_queries) ?></h4>
                                            <span>Total Queries</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #d93025;"><i class="fa fa-exclamation-triangle"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($open_queries) ?></h4>
                                            <span>Open / Pending</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #1a73e8;"><i class="fa fa-clock-o"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($inprogress_queries) ?></h4>
                                            <span>In Progress</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #137333;"><i class="fa fa-check-circle"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($resolved_queries) ?></h4>
                                            <span>Resolved / Closed</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter & Action Toolbar -->
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-7 col-sm-12" style="margin-bottom: 8px;">
                                    <div class="btn-group" role="group">
                                        <a href="fh_order_queries_list.php<?= !empty($search_query) ? '?search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= empty($filter_status) ? 'btn-primary' : 'btn-default' ?>" style="<?= empty($filter_status) ? 'background-color: #009688; border-color: #009688;' : '' ?>">
                                            All (<?= $total_queries ?>)
                                        </a>
                                        <a href="fh_order_queries_list.php?status=open<?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= ($filter_status === 'open') ? 'btn-danger' : 'btn-default' ?>">
                                            Open (<?= $open_queries ?>)
                                        </a>
                                        <a href="fh_order_queries_list.php?status=in_progress<?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= ($filter_status === 'in_progress') ? 'btn-info' : 'btn-default' ?>">
                                            In Progress (<?= $inprogress_queries ?>)
                                        </a>
                                        <a href="fh_order_queries_list.php?status=resolved<?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= ($filter_status === 'resolved') ? 'btn-success' : 'btn-default' ?>">
                                            Resolved (<?= $resolved_queries ?>)
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-5 col-sm-12 text-right">
                                    <form method="GET" action="fh_order_queries_list.php" class="form-inline" style="display: inline-block;">
                                        <?php if (!empty($filter_status)): ?>
                                            <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status) ?>">
                                        <?php endif; ?>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Search order ID, name, issue..." value="<?= htmlspecialchars($search_query) ?>" style="min-width: 190px; border-radius: 4px 0 0 4px;">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary btn-sm" type="submit" style="background-color: #009688; border-color: #009688; border-radius: 0 4px 4px 0;">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                                <?php if (!empty($search_query)): ?>
                                                    <a href="fh_order_queries_list.php<?= !empty($filter_status) ? '?status=' . urlencode($filter_status) : '' ?>" class="btn btn-default btn-sm" title="Clear Search">
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

                            <!-- Queries Table -->
                            <div class="table-responsive">
                                <table id="orderQueriesTable" class="table table-bordered table-striped table-hover mb-0">
                                    <thead>
                                        <tr class="info">
                                            <th width="40">#</th>
                                            <th width="110">Date & Time</th>
                                            <th width="90">Order #</th>
                                            <th width="95">Status</th>
                                            <th>Customer Details</th>
                                            <th>Issue Nature</th>
                                            <th>Subject & Message</th>
                                            <th width="90">Has Reply</th>
                                            <th width="110" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($queries_res && $queries_res->num_rows > 0):
                                            $sr = $offset + 1;
                                            while ($row = $queries_res->fetch_assoc()):
                                                $id          = (int)$row['id'];
                                                $ord_id      = (int)$row['order_id'];
                                                $name        = htmlspecialchars($row['customer_name']);
                                                $phone       = htmlspecialchars($row['customer_phone']);
                                                $email       = htmlspecialchars($row['customer_email'] ?? '');
                                                $issue_type  = htmlspecialchars($row['issue_type']);
                                                $subject     = htmlspecialchars($row['subject']);
                                                $message     = htmlspecialchars($row['message']);
                                                $status      = $row['status'] ?? 'open';
                                                $has_reply   = !empty(trim($row['admin_reply'] ?? ''));
                                                $created     = date('d M Y, h:i A', strtotime($row['created_at']));
                                                $msg_short   = mb_strlen($message) > 60 ? mb_substr($message, 0, 60) . '...' : $message;

                                                // Status badge class
                                                $badge_class = 'badge-status-open';
                                                if ($status === 'in_progress') $badge_class = 'badge-status-in_progress';
                                                elseif ($status === 'resolved') $badge_class = 'badge-status-resolved';
                                                elseif ($status === 'closed') $badge_class = 'badge-status-closed';
                                        ?>
                                        <tr id="query-row-<?= $id ?>">
                                            <td><?= $sr++ ?></td>
                                            <td style="font-size: 12px; color: #555;"><?= $created ?></td>
                                            <td>
                                                <a href="javascript:void(0)" class="view-order-popup font-weight-bold" data-id="<?= $ord_id ?>" style="color: #009688; font-size: 13px;">
                                                    <i class="fa fa-shopping-bag mr-1"></i> #<?= $ord_id ?>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge-status <?= $badge_class ?>" id="status-badge-<?= $id ?>">
                                                    <?= htmlspecialchars(str_replace('_', ' ', $status)) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong style="color: #222;"><?= $name ?></strong><br>
                                                <a href="tel:<?= $phone ?>" style="color: #009688; font-weight: 600; font-size: 12px;">
                                                    <i class="fa fa-phone mr-1"></i> <?= $phone ?>
                                                </a>
                                                &nbsp;
                                                <a href="https://wa.me/91<?= $phone ?>" target="_blank" title="WhatsApp Customer" style="color: #25D366; font-size: 13px;">
                                                    <i class="fa fa-whatsapp"></i>
                                                </a>
                                                <?php if (!empty($email)): ?>
                                                    <br><small class="text-muted"><a href="mailto:<?= $email ?>"><?= $email ?></a></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="label label-warning" style="font-size: 11px; background-color: #f39c12;">
                                                    <?= $issue_type ?>
                                                </span>
                                            </td>
                                            <td style="font-size: 13px; color: #444;" title="<?= $message ?>">
                                                <strong style="display: block; color: #333; margin-bottom: 2px;"><?= $subject ?></strong>
                                                <?= $msg_short ?>
                                            </td>
                                            <td>
                                                <?php if ($has_reply): ?>
                                                    <span class="label label-success" style="font-size: 10px;"><i class="fa fa-check"></i> Replied</span>
                                                <?php else: ?>
                                                    <span class="label label-default" style="font-size: 10px;">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="action-btn-group justify-content-center">
                                                    <!-- View & Respond Button -->
                                                    <button type="button" class="btn btn-info btn-xs btn-view-query" data-id="<?= $id ?>" title="View Query & Respond">
                                                        <i class="fa fa-pencil-square-o"></i> Respond
                                                    </button>
                                                    <!-- Delete Button -->
                                                    <button type="button" class="btn btn-danger btn-xs btn-delete-query" data-id="<?= $id ?>" title="Delete Query">
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
                                            <td colspan="9" class="text-center py-4" style="color: #888; padding: 35px 20px;">
                                                <i class="fa fa-question-circle fa-3x" style="display: block; margin-bottom: 12px; color: #ccc;"></i>
                                                <span style="font-size: 15px; font-weight: 500;">No customer order queries found matching the search or filter criteria.</span>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
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

<!-- View / Respond Query Modal -->
<div class="modal fade" id="queryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-comments" style="margin-right: 8px;"></i> Customer Order Query #<span id="m-query-id"></span> (Order #<span id="m-order-id-label"></span>)
                </h4>
            </div>
            <div class="modal-body">
                <div id="modal-loading" class="text-center" style="padding: 30px 15px;">
                    <i class="fa fa-spinner fa-spin fa-2x" style="color: #009688;"></i>
                    <p class="text-muted" style="margin-top: 10px; font-size: 13px;">Loading query and order details...</p>
                </div>

                <div id="modal-content" style="display: none;">
                    <!-- 1. Customer & Ticket Details -->
                    <div class="qmodal-card">
                        <div class="qmodal-title">
                            <span><i class="fa fa-user-circle" style="color: #009688; margin-right: 6px;"></i> Customer & Ticket Information</span>
                            <span id="m-current-status-badge"></span>
                        </div>
                        
                        <div class="row" style="margin-bottom: 12px;">
                            <div class="col-sm-4">
                                <div class="query-info-group">
                                    <span class="qmodal-label">Customer Name</span>
                                    <div class="qmodal-val" id="m-cust-name" style="font-size: 14px; font-weight: 700; color: #111;"></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="query-info-group">
                                    <span class="qmodal-label">Contact Phone</span>
                                    <div class="qmodal-val" id="m-phone-wrap"></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="query-info-group">
                                    <span class="qmodal-label">Email Address</span>
                                    <div class="qmodal-val" id="m-email-wrap"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="query-info-group" style="margin-bottom: 0;">
                                    <span class="qmodal-label">Date Submitted</span>
                                    <div class="qmodal-val" id="m-query-date" style="color: #555;"></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="query-info-group" style="margin-bottom: 0;">
                                    <span class="qmodal-label">Issue Category</span>
                                    <div class="qmodal-val">
                                        <span class="label label-warning" id="m-issue-type" style="font-size: 12px; font-weight: 600; padding: 4px 8px; display: inline-block;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="query-info-group" style="margin-bottom: 0;">
                                    <span class="qmodal-label">Customer User ID</span>
                                    <div class="qmodal-val" id="m-user-id-wrap" style="color: #555;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Customer Issue Message -->
                    <div class="qmodal-card">
                        <div class="qmodal-title">
                            <span><i class="fa fa-envelope-open" style="color: #f59e0b; margin-right: 6px;"></i> Reported Issue / Message</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong style="color: #1e293b; font-size: 13px;">Subject:</strong>
                            <span id="m-query-subject" style="color: #334155; font-weight: 600; margin-left: 4px;"></span>
                        </div>
                        <div class="qmodal-issue-box" id="m-query-msg"></div>
                    </div>

                    <!-- 3. Linked Order & Items Quick View -->
                    <div class="qmodal-card" id="m-order-card" style="display: none;">
                        <div class="qmodal-title">
                            <span>
                                <i class="fa fa-shopping-bag" style="color: #009688; margin-right: 6px;"></i> Linked Order #<span id="m-ord-num"></span>
                            </span>
                            <button type="button" class="btn btn-default btn-xs" id="m-btn-view-full-order" style="border-radius: 3px; font-size: 11px;">
                                <i class="fa fa-external-link"></i> View Full Order
                            </button>
                        </div>

                        <!-- Order summary strip -->
                        <div class="qmodal-order-summary">
                            <div class="row">
                                <div class="col-sm-3 col-xs-6" style="margin-bottom: 6px;">
                                    <span class="qmodal-label">Order Date</span>
                                    <span id="m-ord-date" style="font-weight: 600; font-size: 12px; color: #333;"></span>
                                </div>
                                <div class="col-sm-3 col-xs-6" style="margin-bottom: 6px;">
                                    <span class="qmodal-label">Order Status</span>
                                    <span id="m-ord-status"></span>
                                </div>
                                <div class="col-sm-3 col-xs-6" style="margin-bottom: 6px;">
                                    <span class="qmodal-label">Payment</span>
                                    <span id="m-ord-payment" style="font-weight: 600; font-size: 12px; color: #333;"></span>
                                </div>
                                <div class="col-sm-3 col-xs-6" style="margin-bottom: 6px;">
                                    <span class="qmodal-label">Grand Total</span>
                                    <strong id="m-ord-total" style="font-size: 14px; color: #009688;"></strong>
                                </div>
                            </div>
                            <div id="m-courier-row" style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #cbd5e1; display: none;">
                                <span style="font-size: 12px; color: #475569;">
                                    <i class="fa fa-truck" style="color: #009688; margin-right: 4px;"></i> <strong>Courier:</strong> <span id="m-courier-name"></span>
                                    &nbsp;|&nbsp; <strong>AWB / Tracking:</strong> <span class="label label-info" id="m-courier-awb" style="font-size: 11px;"></span>
                                    <span id="m-courier-track-link"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Mini items list -->
                        <div id="m-items-container" style="display: none;">
                            <span class="qmodal-label" style="margin-bottom: 6px;">Order Items:</span>
                            <div class="table-responsive" style="padding: 0; border: 1px solid #e2e8f0; border-radius: 4px; overflow: hidden;">
                                <table class="table table-bordered table-striped qmodal-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px; text-align: center;">Item</th>
                                            <th>Product Details</th>
                                            <th style="width: 60px; text-align: center;">Size</th>
                                            <th style="width: 50px; text-align: center;">Qty</th>
                                            <th style="width: 85px; text-align: right;">Price</th>
                                            <th style="width: 95px; text-align: right;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="m-items-tbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Admin Response & Status Form -->
                    <div class="qmodal-card" style="background: #fafbfc; border: 1px solid #cbd5e1; margin-bottom: 0;">
                        <div class="qmodal-title" style="border-bottom: 1px solid #e2e8f0;">
                            <span><i class="fa fa-pencil-square-o" style="color: #009688; margin-right: 6px;"></i> Staff Response & Status Management</span>
                        </div>
                        
                        <form id="update-query-form">
                            <input type="hidden" name="id" id="modal-query-id-input" value="">
                            <input type="hidden" name="action" value="update_status">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group" style="margin-bottom: 14px;">
                                        <label for="m-status-select" style="font-weight: 700; color: #333; margin-bottom: 5px; font-size: 13px;">
                                            Update Ticket Status: <span class="text-danger">*</span>
                                        </label>
                                        <select name="status" id="m-status-select" class="form-control" style="height: 38px; border-radius: 4px; font-weight: 600;">
                                            <option value="open">Open / Under Review</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="resolved">Resolved</option>
                                            <option value="closed">Closed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group" style="margin-bottom: 14px;">
                                        <label for="m-admin-notes" style="font-weight: 700; color: #333; margin-bottom: 5px; font-size: 13px;">
                                            Staff Internal Remarks (Private):
                                        </label>
                                        <input type="text" name="admin_notes" id="m-admin-notes" class="form-control" placeholder="Internal notes not visible to customer" style="height: 38px; border-radius: 4px;">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 12px;">
                                <label for="m-admin-reply" style="font-weight: 700; color: #333; margin-bottom: 5px; font-size: 13px;">
                                    <i class="fa fa-reply text-success" style="margin-right: 4px;"></i> Response Message to Customer:
                                </label>
                                <textarea name="admin_reply" id="m-admin-reply" rows="3" class="form-control" placeholder="Type your response/resolution here. The customer will see this directly when checking their queries in My Account..." style="border-radius: 4px; font-size: 13px; line-height: 1.5;"></textarea>
                                <p class="help-block" style="margin-top: 4px; font-size: 12px; color: #64748b; margin-bottom: 0;">
                                    <i class="fa fa-info-circle"></i> Once saved, this message is visible to the customer on their order details and tracking page.
                                </p>
                            </div>

                            <div id="modal-alert" class="alert" style="display: none; margin-top: 10px; margin-bottom: 10px; padding: 10px 15px;"></div>

                            <div style="text-align: right; margin-top: 16px;">
                                <button type="button" class="btn btn-default" data-dismiss="modal" style="margin-right: 8px;">Close</button>
                                <button type="submit" class="btn btn-primary" id="btn-submit-response" style="background-color: #009688; border-color: #009688; font-weight: 600; padding: 7px 20px;">
                                    <i class="fa fa-check-circle" style="margin-right: 4px;"></i> Save Reply & Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 10px 20px; background: #eef2f5;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Popup Container -->
<div class="modal fade" id="modal-view-order-details" tabindex="-1" role="dialog" aria-hidden="true"></div>

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

    // View Order Details Popup Modal
    $(document).on('click', '.view-order-popup', function() {
        var orderId = $(this).data('id');
        $('#modal-view-order-details').load('popup/view-order-details.php?id=' + orderId, function() {
            $('#modal-view-order-details').modal('show');
        });
    });

    function escapeHtmlSafe(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // View & Respond Query Details
    $(document).on('click', '.btn-view-query', function() {
        var queryId = $(this).data('id');
        var $modal = $('#queryModal');
        var $loading = $('#modal-loading');
        var $content = $('#modal-content');
        var $alert = $('#modal-alert');

        $alert.hide().removeClass('alert-success alert-danger').empty();
        $loading.show();
        $content.hide();
        $modal.modal('show');
        $('#m-query-id').text(queryId);
        $('#modal-query-id-input').val(queryId);

        $.ajax({
            url: 'ajax/order-query-results',
            type: 'POST',
            data: {
                action: 'get_details',
                id: queryId
            },
            dataType: 'json',
            success: function(res) {
                $loading.hide();
                if (res && res.success && res.data) {
                    var q = res.data;
                    var o = res.order;

                    $('#m-order-id-label').text(q.order_id);
                    $('#m-cust-name').text(q.customer_name);
                    $('#m-query-date').text(q.created_at);
                    
                    var phoneHtml = '<a href="tel:' + encodeURIComponent(q.customer_phone) + '" style="color: #009688; font-weight: 700; font-size: 13px;"><i class="fa fa-phone"></i> ' + escapeHtmlSafe(q.customer_phone) + '</a>' +
                                    ' &nbsp; <a href="https://wa.me/91' + encodeURIComponent(q.customer_phone.replace(/\D/g, '')) + '" target="_blank" class="btn btn-success btn-xs" style="padding: 1px 7px; border-radius: 10px; font-size: 11px;"><i class="fa fa-whatsapp"></i> Chat</a>';
                    $('#m-phone-wrap').html(phoneHtml);

                    if (q.customer_email) {
                        $('#m-email-wrap').html('<a href="mailto:' + encodeURIComponent(q.customer_email) + '" style="color: #337ab7;"><i class="fa fa-envelope"></i> ' + escapeHtmlSafe(q.customer_email) + '</a>');
                    } else {
                        $('#m-email-wrap').text('Not provided');
                    }

                    $('#m-user-id-wrap').text(q.user_id > 0 ? ('#' + q.user_id) : 'Guest Customer');
                    $('#m-issue-type').text(q.issue_type);
                    
                    // Render status badge
                    var st = q.status || 'open';
                    var stBadge = '';
                    if (st === 'open') {
                        stBadge = '<span class="badge-status badge-status-open">Open</span>';
                    } else if (st === 'in_progress') {
                        stBadge = '<span class="badge-status badge-status-in_progress">In Progress</span>';
                    } else if (st === 'resolved') {
                        stBadge = '<span class="badge-status badge-status-resolved">Resolved</span>';
                    } else {
                        stBadge = '<span class="badge-status badge-status-closed">' + escapeHtmlSafe(st.toUpperCase()) + '</span>';
                    }
                    $('#m-current-status-badge').html(stBadge);

                    // Query subject & message
                    $('#m-query-subject').text(q.subject || 'No subject');
                    $('#m-query-msg').text(q.message);

                    // Form values
                    $('#m-status-select').val(q.status || 'open');
                    $('#m-admin-reply').val(q.admin_reply || '');
                    $('#m-admin-notes').val(q.admin_notes || '');

                    // Order summary card
                    if (o) {
                        $('#m-ord-num').text(o.order_id);
                        $('#m-btn-view-full-order').data('id', o.order_id);
                        $('#m-ord-date').text((o.created_at || '').substring(0, 16));

                        var ordStatus = (o.order_status || 'pending').toLowerCase();
                        var ordBadgeClass = 'label-default';
                        if (ordStatus === 'paid' || ordStatus === 'completed') ordBadgeClass = 'label-success';
                        else if (ordStatus === 'cancelled') ordBadgeClass = 'label-danger';
                        else if (ordStatus === 'pending') ordBadgeClass = 'label-warning';
                        $('#m-ord-status').html('<span class="label ' + ordBadgeClass + '" style="font-size: 11px;">' + ordStatus.toUpperCase() + '</span>');

                        var payInfo = (o.payment_method || 'N/A').toUpperCase();
                        if (o.payment_status) payInfo += ' (' + o.payment_status.toUpperCase() + ')';
                        $('#m-ord-payment').text(payInfo);

                        $('#m-ord-total').text('₹' + parseFloat(o.grand_total || 0).toFixed(2));

                        var awb = o.courier_awb || o.delhivery_awb || '';
                        if (awb) {
                            $('#m-courier-row').show();
                            $('#m-courier-name').text(o.courier_name || 'Courier Partner');
                            $('#m-courier-awb').text(awb);
                            $('#m-courier-track-link').html('&nbsp; <a href="../track-order-page.php?waybill=' + encodeURIComponent(awb) + '" target="_blank" class="btn btn-default btn-xs" style="margin-left: 6px;"><i class="fa fa-search"></i> Track Parcel</a>');
                        } else {
                            $('#m-courier-row').hide();
                        }

                        // Order items
                        if (res.items && res.items.length > 0) {
                            var itemsHtml = '';
                            res.items.forEach(function(it) {
                                var imgPath = it.picture ? ('uploads/item-master/' + it.picture) : 'assets/dist/img/no-image.png';
                                itemsHtml += '<tr>' +
                                    '<td style="text-align: center;"><img src="' + imgPath + '" style="width: 36px; height: 36px; object-fit: contain; border-radius: 4px; border: 1px solid #eee;"></td>' +
                                    '<td><strong>' + escapeHtmlSafe(it.product_title || 'Item') + '</strong>' + (it.transaction_id ? ('<br><small class="text-muted">' + escapeHtmlSafe(it.transaction_id) + '</small>') : '') + '</td>' +
                                    '<td style="text-align: center;">' + (it.size || '-') + '</td>' +
                                    '<td style="text-align: center;">' + (it.qty || 1) + '</td>' +
                                    '<td style="text-align: right;">₹' + parseFloat(it.price || 0).toFixed(2) + '</td>' +
                                    '<td style="text-align: right;"><strong>₹' + parseFloat(it.row_total || 0).toFixed(2) + '</strong></td>' +
                                '</tr>';
                            });
                            $('#m-items-tbody').html(itemsHtml);
                            $('#m-items-container').show();
                        } else {
                            $('#m-items-container').hide();
                        }

                        $('#m-order-card').show();
                    } else {
                        $('#m-order-card').hide();
                    }

                    $content.fadeIn();
                } else {
                    $loading.html('<div class="alert alert-danger" style="margin: 15px 0;">' + ((res && res.message) ? res.message : 'Failed to load query details.') + '</div>').show();
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText);
                $loading.html('<div class="alert alert-danger" style="margin: 15px 0;"><i class="fa fa-exclamation-triangle"></i> Error communicating with server. (' + (error || status) + ')</div>').show();
            }
        });
    });

    // View full order popup trigger from within query modal
    $(document).on('click', '#m-btn-view-full-order', function() {
        var ordId = $(this).data('id');
        if (ordId) {
            $('#modal-view-order-details').load('popup/view-order-details.php?id=' + ordId, function() {
                $('#modal-view-order-details').modal('show');
            });
        }
    });

    // Save Query Response & Status Form
    $('#update-query-form').on('submit', function(e) {
        e.preventDefault();
        var $alert = $('#modal-alert');
        var $btn = $('#btn-submit-response');
        var queryId = $('#modal-query-id-input').val();
        var newStatus = $('#m-status-select').val();

        $alert.hide().removeClass('alert-success alert-danger').empty();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: 'ajax/order-query-results',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-check-circle" style="margin-right: 4px;"></i> Save Reply & Update Status');
                if (res && res.success) {
                    $alert.removeClass('alert-danger').addClass('alert-success')
                          .html('<i class="fa fa-check mr-1"></i> ' + res.message).slideDown();

                    // Update row badge in main table
                    var $rowBadge = $('#status-badge-' + queryId);
                    $rowBadge.removeClass('badge-status-open badge-status-in_progress badge-status-resolved badge-status-closed')
                             .addClass('badge-status-' + newStatus)
                             .text(newStatus.replace('_', ' '));

                    // Update badge inside open modal
                    var stBadge = '';
                    if (newStatus === 'open') {
                        stBadge = '<span class="badge-status badge-status-open">Open</span>';
                    } else if (newStatus === 'in_progress') {
                        stBadge = '<span class="badge-status badge-status-in_progress">In Progress</span>';
                    } else if (newStatus === 'resolved') {
                        stBadge = '<span class="badge-status badge-status-resolved">Resolved</span>';
                    } else {
                        stBadge = '<span class="badge-status badge-status-closed">' + escapeHtmlSafe(newStatus.toUpperCase()) + '</span>';
                    }
                    $('#m-current-status-badge').html(stBadge);

                    setTimeout(function() {
                        $('#queryModal').modal('hide');
                    }, 1000);
                } else {
                    $alert.removeClass('alert-success').addClass('alert-danger')
                          .html('<i class="fa fa-exclamation-triangle mr-1"></i> ' + (res.message || 'Update failed.')).slideDown();
                }
            },
            error: function(xhr, status, error) {
                $btn.prop('disabled', false).html('<i class="fa fa-check-circle" style="margin-right: 4px;"></i> Save Reply & Update Status');
                console.error("AJAX Error:", status, error, xhr.responseText);
                $alert.removeClass('alert-success').addClass('alert-danger')
                      .html('Network error occurred: ' + (error || status)).slideDown();
            }
        });
    });

    // Delete Query
    $(document).on('click', '.btn-delete-query', function() {
        var queryId = $(this).data('id');
        if (!confirm('Are you sure you want to delete this customer order query? This cannot be undone.')) {
            return;
        }

        $.ajax({
            url: 'ajax/order-query-results',
            type: 'POST',
            data: {
                action: 'delete_query',
                id: queryId
            },
            dataType: 'json',
            success: function(res) {
                if (res && res.success) {
                    $('#query-row-' + queryId).fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    alert(res.message || 'Failed to delete query.');
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
