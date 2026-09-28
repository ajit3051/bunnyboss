<?php
include('top.php');

// Handle AJAX actions (Status update or delete)
if (isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    $action = $_POST['ajax_action'];

    if ($action === 'update_status') {
        $inquiry_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';
        $admin_notes = isset($_POST['admin_notes']) ? trim($_POST['admin_notes']) : null;

        if ($inquiry_id > 0 && in_array($status, ['unread', 'read', 'replied'], true)) {
            if ($admin_notes !== null) {
                $stmt = $conn->prepare("UPDATE tbl_contact_inquiries SET status = ?, admin_notes = ? WHERE id = ?");
                $stmt->bind_param("ssi", $status, $admin_notes, $inquiry_id);
            } else {
                $stmt = $conn->prepare("UPDATE tbl_contact_inquiries SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $inquiry_id);
            }
            $success = $stmt->execute();
            echo json_encode(['success' => $success, 'message' => $success ? 'Status updated successfully.' : 'Failed to update status.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        }
        exit;
    }

    if ($action === 'delete_inquiry') {
        $inquiry_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($inquiry_id > 0) {
            $stmt = $conn->prepare("DELETE FROM tbl_contact_inquiries WHERE id = ?");
            $stmt->bind_param("i", $inquiry_id);
            $success = $stmt->execute();
            echo json_encode(['success' => $success, 'message' => $success ? 'Inquiry deleted successfully.' : 'Failed to delete inquiry.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid inquiry ID.']);
        }
        exit;
    }

    if ($action === 'get_details') {
        $inquiry_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($inquiry_id > 0) {
            $stmt = $conn->prepare("SELECT * FROM tbl_contact_inquiries WHERE id = ?");
            $stmt->bind_param("i", $inquiry_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                // If it was unread, mark it as read automatically
                if ($row['status'] === 'unread') {
                    $conn->query("UPDATE tbl_contact_inquiries SET status = 'read' WHERE id = " . $inquiry_id);
                    $row['status'] = 'read';
                }
                echo json_encode(['success' => true, 'data' => $row]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Record not found.']);
            }
        }
        exit;
    }
}

// Fallback GET delete
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM tbl_contact_inquiries WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    header("Location: fh_contact_inquiries_list.php?msg=deleted");
    exit();
}

// Fetch Summary Counts
$total_inquiries = 0;
$unread_inquiries = 0;
$today_inquiries = 0;
$replied_inquiries = 0;

$count_res = $conn->query("
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'unread' THEN 1 ELSE 0 END) AS unread_count,
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) AS today_count,
        SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) AS replied_count
    FROM tbl_contact_inquiries
");
if ($count_res && $row = $count_res->fetch_assoc()) {
    $total_inquiries   = (int)$row['total'];
    $unread_inquiries  = (int)$row['unread_count'];
    $today_inquiries   = (int)$row['today_count'];
    $replied_inquiries = (int)$row['replied_count'];
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
if (in_array($filter_status, ['unread', 'read', 'replied'], true)) {
    $where_clauses[] = "status = '" . $conn->real_escape_string($filter_status) . "'";
}

if (!empty($search_query)) {
    $safe_search = $conn->real_escape_string($search_query);
    $where_clauses[] = "(
        name LIKE '%{$safe_search}%' 
        OR phone LIKE '%{$safe_search}%' 
        OR email LIKE '%{$safe_search}%'
        OR subject LIKE '%{$safe_search}%'
        OR message LIKE '%{$safe_search}%'
        OR id = '{$safe_search}'
    )";
}

$where_sql = !empty($where_clauses) ? " WHERE " . implode(' AND ', $where_clauses) : "";

// Count Total Matching Records for Pagination
$count_query = "SELECT COUNT(*) AS total_filtered FROM tbl_contact_inquiries {$where_sql}";
$count_filtered_res = $conn->query($count_query);
$totalRecords = ($count_filtered_res && $crow = $count_filtered_res->fetch_assoc()) ? (int)$crow['total_filtered'] : 0;

$maxPage = max(1, (int)ceil($totalRecords / $recordsPerPage));
if ($page > $maxPage) {
    $page = $maxPage;
}
$offset = ($page - 1) * $recordsPerPage;

$inquiries_query = "SELECT * FROM tbl_contact_inquiries {$where_sql} ORDER BY id DESC LIMIT {$offset}, {$recordsPerPage}";
$inquiries_res   = $conn->query($inquiries_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Inquiries Management || Bunny Boss Admin</title>
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
        .badge-status-unread {
            background-color: #fce8e6;
            color: #d93025;
            border: 1px solid #fad2cf;
        }
        .badge-status-read {
            background-color: #e8f0fe;
            color: #1a73e8;
            border: 1px solid #d2e3fc;
        }
        .badge-status-replied {
            background-color: #e6f4ea;
            color: #137333;
            border: 1px solid #ceead6;
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
                                <i class="fa fa-envelope-o" style="margin-right: 8px;"></i> Customer Contact Inquiries
                            </span>
                        </div>
                        <div class="panel-body">
                            <!-- Metrics Row -->
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #009688;"><i class="fa fa-envelope"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($total_inquiries) ?></h4>
                                            <span>Total Inquiries</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #d93025;"><i class="fa fa-bell"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($unread_inquiries) ?></h4>
                                            <span>Unread Inquiries</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #1a73e8;"><i class="fa fa-calendar-check-o"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($today_inquiries) ?></h4>
                                            <span>Received Today</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="metric-card">
                                        <div class="metric-icon" style="background: #137333;"><i class="fa fa-check-circle"></i></div>
                                        <div class="metric-content">
                                            <h4><?= number_format($replied_inquiries) ?></h4>
                                            <span>Resolved / Replied</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter & Action Toolbar -->
                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-md-7 col-sm-12" style="margin-bottom: 8px;">
                                    <div class="btn-group" role="group">
                                        <a href="fh_contact_inquiries_list.php<?= !empty($search_query) ? '?search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= empty($filter_status) ? 'btn-primary' : 'btn-default' ?>" style="<?= empty($filter_status) ? 'background-color: #009688; border-color: #009688;' : '' ?>">
                                            All (<?= $total_inquiries ?>)
                                        </a>
                                        <a href="fh_contact_inquiries_list.php?status=unread<?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= ($filter_status === 'unread') ? 'btn-danger' : 'btn-default' ?>">
                                            Unread (<?= $unread_inquiries ?>)
                                        </a>
                                        <a href="fh_contact_inquiries_list.php?status=read<?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= ($filter_status === 'read') ? 'btn-info' : 'btn-default' ?>">
                                            Read
                                        </a>
                                        <a href="fh_contact_inquiries_list.php?status=replied<?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>" class="btn btn-sm <?= ($filter_status === 'replied') ? 'btn-success' : 'btn-default' ?>">
                                            Replied (<?= $replied_inquiries ?>)
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-5 col-sm-12 text-right">
                                    <form method="GET" action="fh_contact_inquiries_list.php" class="form-inline" style="display: inline-block;">
                                        <?php if (!empty($filter_status)): ?>
                                            <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status) ?>">
                                        <?php endif; ?>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="search" class="form-control" placeholder="Search inquiries..." value="<?= htmlspecialchars($search_query) ?>" style="min-width: 190px; border-radius: 4px 0 0 4px;">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary btn-sm" type="submit" style="background-color: #009688; border-color: #009688; border-radius: 0 4px 4px 0;">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                                <?php if (!empty($search_query)): ?>
                                                    <a href="fh_contact_inquiries_list.php<?= !empty($filter_status) ? '?status=' . urlencode($filter_status) : '' ?>" class="btn btn-default btn-sm" title="Clear Search">
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

                            <!-- Inquiries Table -->
                            <div class="table-responsive">
                                <table id="inquiriesTable" class="table table-bordered table-striped table-hover mb-0">
                                    <thead>
                                        <tr class="info">
                                            <th width="40">#</th>
                                            <th width="120">Date & Time</th>
                                            <th width="90">Status</th>
                                            <th>Customer Name</th>
                                            <th>Mobile Phone</th>
                                            <th>Email</th>
                                            <th>Subject</th>
                                            <th>Message</th>
                                            <th width="110" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($inquiries_res && $inquiries_res->num_rows > 0):
                                            $sr = $offset + 1;
                                            while ($row = $inquiries_res->fetch_assoc()):
                                                $id       = (int)$row['id'];
                                                $name     = htmlspecialchars($row['name']);
                                                $phone    = htmlspecialchars($row['phone']);
                                                $email    = htmlspecialchars($row['email'] ?? '');
                                                $subject  = htmlspecialchars($row['subject'] ?? 'General Inquiry');
                                                $message  = htmlspecialchars($row['message']);
                                                $status   = $row['status'] ?? 'unread';
                                                $created  = date('d M Y, h:i A', strtotime($row['created_at']));
                                                $msg_short= mb_strlen($message) > 60 ? mb_substr($message, 0, 60) . '...' : $message;

                                                // Status badge class
                                                $badge_class = 'badge-status-unread';
                                                if ($status === 'read') $badge_class = 'badge-status-read';
                                                elseif ($status === 'replied') $badge_class = 'badge-status-replied';
                                        ?>
                                        <tr id="inquiry-row-<?= $id ?>">
                                            <td><?= $sr++ ?></td>
                                            <td style="font-size: 12px; color: #555;"><?= $created ?></td>
                                            <td>
                                                <span class="badge-status <?= $badge_class ?>" id="status-badge-<?= $id ?>">
                                                    <?= htmlspecialchars($status) ?>
                                                </span>
                                            </td>
                                            <td><strong><?= $name ?></strong></td>
                                            <td>
                                                <a href="tel:<?= $phone ?>" style="color: #009688; font-weight: 600;">
                                                    <i class="fa fa-phone mr-1"></i> <?= $phone ?>
                                                </a>
                                                &nbsp;
                                                <a href="https://wa.me/91<?= $phone ?>" target="_blank" title="WhatsApp Customer" style="color: #25D366; font-size: 14px;">
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
                                                <span class="label label-default" style="font-size: 11px;"><?= $subject ?></span>
                                            </td>
                                            <td style="font-size: 13px; color: #444;" title="<?= $message ?>">
                                                <?= $msg_short ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="action-btn-group justify-content-center">
                                                    <!-- View Details Button -->
                                                    <button type="button" class="btn btn-info btn-xs btn-view-inquiry" data-id="<?= $id ?>" title="View Inquiry Details">
                                                        <i class="fa fa-eye"></i> View
                                                    </button>
                                                    <!-- Delete Button -->
                                                    <button type="button" class="btn btn-danger btn-xs btn-delete-inquiry" data-id="<?= $id ?>" title="Delete Inquiry">
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
                                                <i class="fa fa-inbox fa-3x" style="display: block; margin-bottom: 12px; color: #ccc;"></i>
                                                <span style="font-size: 15px; font-weight: 500;">No contact inquiries found matching the search or filter criteria.</span>
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

<!-- View / Update Inquiry Modal -->
<div class="modal fade" id="inquiryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background-color: #009688; color: #ffffff; padding: 14px 20px;">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" style="margin: 0; font-weight: 600; font-size: 16px;">
                    <i class="fa fa-envelope-open mr-2"></i> Inquiry Details #<span id="modal-inquiry-id"></span>
                </h4>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div id="modal-loading" class="text-center py-4">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="text-muted small mt-2">Loading details...</p>
                </div>

                <div id="modal-content" style="display: none;">
                    <div class="row mb-3" style="border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <div class="col-sm-6 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">Customer Name</label>
                            <h4 id="m-name" style="margin: 0; font-weight: 700; color: #222;"></h4>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">Submitted On</label>
                            <span id="m-date" style="font-weight: 600; color: #555;"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">Phone Number</label>
                            <span id="m-phone-wrap"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">Email Address</label>
                            <span id="m-email-wrap"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">Subject / Query Type</label>
                            <span class="label label-primary" id="m-subject" style="font-size: 13px; font-weight: 600;"></span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="text-muted small text-uppercase" style="display: block; margin-bottom: 2px;">IP Address</label>
                            <span id="m-ip" class="text-muted" style="font-size: 13px;"></span>
                        </div>
                    </div>

                    <!-- Message Body -->
                    <div class="form-group mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold">Inquiry Message</label>
                        <div id="m-message" style="background: #f9fbfb; border: 1px solid #e1e7ec; border-radius: 6px; padding: 15px; font-size: 14px; line-height: 1.7; color: #333; white-space: pre-wrap;"></div>
                    </div>

                    <!-- Status & Admin Notes -->
                    <form id="update-status-form">
                        <input type="hidden" name="id" id="modal-input-id" value="">
                        <input type="hidden" name="ajax_action" value="update_status">

                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label for="m-status-select" class="font-weight-bold text-dark">Update Status:</label>
                                <select name="status" id="m-status-select" class="form-control" style="height: 38px; border-radius: 4px;">
                                    <option value="unread">Unread</option>
                                    <option value="read">Read</option>
                                    <option value="replied">Replied / Resolved</option>
                                </select>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="m-admin-notes" class="font-weight-bold text-dark">Admin Notes (Optional):</label>
                                <textarea name="admin_notes" id="m-admin-notes" rows="2" class="form-control" placeholder="Add internal notes about this inquiry..." style="border-radius: 4px;"></textarea>
                            </div>
                        </div>

                        <div id="modal-alert" class="alert d-none" style="margin-top: 10px;"></div>

                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-primary" style="background-color: #009688; border-color: #009688; font-weight: 600;">
                                <i class="fa fa-save mr-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
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

    // View Inquiry Details
    $(document).on('click', '.btn-view-inquiry', function() {
        var inquiryId = $(this).data('id');
        var $modal = $('#inquiryModal');
        var $loading = $('#modal-loading');
        var $content = $('#modal-content');
        var $alert = $('#modal-alert');

        $alert.addClass('d-none').removeClass('alert-success alert-danger');
        $loading.show();
        $content.hide();
        $modal.modal('show');
        $('#modal-inquiry-id').text(inquiryId);
        $('#modal-input-id').val(inquiryId);

        $.ajax({
            url: 'ajax/contact-inquiry-results',
            type: 'POST',
            data: {
                action: 'get_details',
                id: inquiryId
            },
            dataType: 'json',
            success: function(res) {
                $loading.hide();
                if (res && res.success && res.data) {
                    var d = res.data;
                    $('#m-name').text(d.name);
                    $('#m-date').text(d.created_at);
                    
                    var phoneHtml = '<a href="tel:' + d.phone + '" class="font-weight-bold" style="color: #009688; font-size: 15px;"><i class="fa fa-phone mr-1"></i> ' + d.phone + '</a>' +
                                    ' &nbsp; <a href="https://wa.me/91' + d.phone + '" target="_blank" class="btn btn-success btn-xs" style="padding: 2px 8px; border-radius: 10px;"><i class="fa fa-whatsapp"></i> Chat</a>';
                    $('#m-phone-wrap').html(phoneHtml);

                    if (d.email) {
                        $('#m-email-wrap').html('<a href="mailto:' + d.email + '" style="color: #337ab7; font-size: 14px;"><i class="fa fa-envelope mr-1"></i> ' + d.email + '</a>');
                    } else {
                        $('#m-email-wrap').html('<span class="text-muted">Not provided</span>');
                    }

                    $('#m-subject').text(d.subject || 'General Inquiry');
                    $('#m-ip').text(d.ip_address || 'N/A');
                    $('#m-message').text(d.message);
                    $('#m-status-select').val(d.status || 'read');
                    $('#m-admin-notes').val(d.admin_notes || '');

                    // Update row badge on page
                    var $rowBadge = $('#status-badge-' + inquiryId);
                    $rowBadge.removeClass('badge-status-unread badge-status-read badge-status-replied')
                             .addClass('badge-status-' + d.status)
                             .text(d.status);

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

    // Save Status / Notes from Modal
    $('#update-status-form').on('submit', function(e) {
        e.preventDefault();
        var $alert = $('#modal-alert');
        var inquiryId = $('#modal-input-id').val();
        var newStatus = $('#m-status-select').val();

        $alert.addClass('d-none').removeClass('alert-success alert-danger');

        $.ajax({
            url: 'ajax/contact-inquiry-results',
            type: 'POST',
            data: $(this).serialize() + '&action=update_status',
            dataType: 'json',
            success: function(res) {
                if (res && res.success) {
                    $alert.removeClass('d-none alert-danger').addClass('alert-success')
                          .html('<i class="fa fa-check mr-1"></i> ' + res.message);

                    // Update row badge on main table
                    var $rowBadge = $('#status-badge-' + inquiryId);
                    $rowBadge.removeClass('badge-status-unread badge-status-read badge-status-replied')
                             .addClass('badge-status-' + newStatus)
                             .text(newStatus);

                    setTimeout(function() {
                        $('#inquiryModal').modal('hide');
                    }, 800);
                } else {
                    $alert.removeClass('d-none alert-success').addClass('alert-danger')
                          .html('<i class="fa fa-exclamation-triangle mr-1"></i> ' + (res.message || 'Update failed.'));
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText);
                $alert.removeClass('d-none alert-success').addClass('alert-danger')
                      .html('Network error occurred: ' + (error || status));
            }
        });
    });

    // Delete Inquiry
    $(document).on('click', '.btn-delete-inquiry', function() {
        var inquiryId = $(this).data('id');
        if (!confirm('Are you sure you want to delete this contact inquiry? This cannot be undone.')) {
            return;
        }

        $.ajax({
            url: 'ajax/contact-inquiry-results',
            type: 'POST',
            data: {
                action: 'delete_inquiry',
                id: inquiryId
            },
            dataType: 'json',
            success: function(res) {
                if (res && res.success) {
                    $('#inquiry-row-' + inquiryId).fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    alert(res.message || 'Failed to delete inquiry.');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error, xhr.responseText);
                alert('Network error occurred while deleting: ' + (error || status));
            }
        });
    });
});
</script>
</body>
</html>
