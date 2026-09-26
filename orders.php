<?php
include_once("include/config.php");

$current_account_page = 'orders';
$page_title = 'My Orders';
include("include/user_account_header.php");

if ($is_logged_in):
    $phone_param = $user['mobile'] ?? $user_mobile;
    $orders = [];
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
            // Fetch first 3 item images for preview
            $items_stmt = $db->select(
                "SELECT OI.product_title, OI.qty, OI.size, OI.price, OI.row_total,
                        (SELECT image_path FROM tbl_item_images WHERE item_id = OI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) as picture
                 FROM tbl_order_items OI 
                 WHERE OI.order_id = ? LIMIT 3",
                'i',
                $o_row['order_id']
            );
            $o_row['preview_items'] = [];
            if ($items_stmt) {
                while ($it = $items_stmt->fetch_assoc()) {
                    $o_row['preview_items'][] = $it;
                }
            }
            $orders[] = $o_row;
        }
    }
?>

    <div class="dashboard-card">
        <!-- HEADER WITH STATS & SEARCH -->
        <div class="dashboard-card-header">
            <div>
                <h4 class="dashboard-card-title">
                    <i class="icon-shopping-cart text-primary"></i> My Orders
                </h4>
                <p class="text-muted small mb-0">Review past orders, view invoices, and track live deliveries.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge badge-light px-3 py-2 text-dark font-weight-bold" style="font-size: 13px; border: 1px solid #e2e8f0; border-radius: 20px;">
                    Total: <?= count($orders) ?> Order(s)
                </span>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="icon-shopping-cart"></i></div>
                <h5>No Orders Placed Yet</h5>
                <p>You haven't placed any orders yet. Discover our latest trending footwear collection with fast shipping and cash on delivery!</p>
                <a href="<?= _BASEURL ?>product-list.php" class="btn btn-primary btn-round px-4" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                    <i class="icon-shopping-bag mr-1"></i> Start Shopping
                </a>
            </div>
        <?php else: ?>

            <!-- FILTER PILLS & SEARCH BAR -->
            <div class="row align-items-center mb-4">
                <div class="col-lg-7 col-md-12 mb-3 mb-lg-0">
                    <div class="order-filter-pills" id="orderFilterPills">
                        <button type="button" class="order-filter-btn active" data-filter="all">
                            All Orders (<?= count($orders) ?>)
                        </button>
                        <button type="button" class="order-filter-btn" data-filter="in_progress">
                            <i class="icon-clock-o"></i> In Progress
                        </button>
                        <button type="button" class="order-filter-btn" data-filter="delivered">
                            <i class="icon-check"></i> Delivered
                        </button>
                        <button type="button" class="order-filter-btn" data-filter="cod">
                            <i class="icon-money"></i> COD Orders
                        </button>
                        <button type="button" class="order-filter-btn" data-filter="paid">
                            <i class="icon-credit-card"></i> Paid Online
                        </button>
                        <button type="button" class="order-filter-btn" data-filter="cancelled">
                            <i class="icon-close"></i> Cancelled
                        </button>
                    </div>
                </div>

                <div class="col-lg-5 col-md-12">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white" style="border-right: none; border-radius: 20px 0 0 20px; border-color: #e2e8f0;">
                                <i class="icon-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" id="orderSearchInput" class="form-control" placeholder="Search by Order ID, Product, or Date..." style="border-left: none; border-radius: 0 20px 20px 0; border-color: #e2e8f0; font-size: 13px;">
                    </div>
                </div>
            </div>

            <!-- ORDERS LISTING -->
            <div class="orders-list-wrapper" id="ordersListContainer">
                <?php foreach ($orders as $ord): 
                    $order_date_str = date('d M Y, h:i A', strtotime($ord['created_at']));
                    $has_awb = !empty($ord['courier_awb']) || !empty($ord['delhivery_awb']);
                    $awb_code = !empty($ord['courier_awb']) ? $ord['courier_awb'] : (!empty($ord['delhivery_awb']) ? $ord['delhivery_awb'] : '');
                    $courier_name = !empty($ord['courier_name']) ? ucfirst($ord['courier_name']) : 'Delhivery';
                    
                    $pay_status_raw = strtolower(trim($ord['payment_status'] ?? 'pending'));
                    $order_status_raw = strtolower(trim($ord['order_status'] ?? 'pending'));
                    $pay_method_raw = strtolower(trim($ord['payment_method'] ?? 'cod'));

                    // Filter tags
                    $is_delivered = ($order_status_raw === 'delivered' || $order_status_raw === 'completed');
                    $is_in_progress = in_array($order_status_raw, ['pending', 'placed', 'processing', 'shipped', 'dispatched', 'shadowfax', 'delhivery']);
                    $is_cod_type = ($pay_method_raw === 'cod');
                    $is_paid_type = ($pay_status_raw === 'paid');
                    $is_cancelled_type = ($order_status_raw === 'cancelled');

                    // Cancellation eligibility
                    $is_cancellable_status = in_array($order_status_raw, ['pending', 'placed', 'processing', 'success']);
                    $is_already_shipped = in_array($order_status_raw, ['shipped', 'dispatched', 'delivered', 'completed', 'cancelled', 'rto', 'returned']);
                    $dispatch_raw = strtolower(trim($ord['dispatch_status'] ?? ''));
                    $is_dispatched = in_array($dispatch_raw, ['shipped', 'dispatched', 'in_transit', 'out_for_delivery']);
                    $can_cancel = ($is_cancellable_status && !$is_already_shipped && !$is_dispatched);

                    // Calculate COD balance due on delivery
                    $grand_total_val = (float)($ord['grand_total'] ?? 0);
                    $paid_amount_val = (float)($ord['paid_amount'] ?? 0);
                    $cod_due_val = max(0, $grand_total_val - $paid_amount_val);

                    // Search keywords
                    $item_titles = '';
                    foreach ($ord['preview_items'] as $it) {
                        $item_titles .= ' ' . $it['product_title'];
                    }
                    $search_text = strtolower($ord['order_id'] . ' ' . $order_date_str . ' ' . $pay_method_raw . ' ' . $pay_status_raw . ' ' . $order_status_raw . ' ' . $item_titles);
                ?>
                    <div class="order-card-box" 
                         data-search="<?= htmlspecialchars($search_text) ?>"
                         data-in-progress="<?= $is_in_progress ? 'true' : 'false' ?>"
                         data-delivered="<?= $is_delivered ? 'true' : 'false' ?>"
                         data-cod="<?= $is_cod_type ? 'true' : 'false' ?>"
                         data-paid="<?= $is_paid_type ? 'true' : 'false' ?>"
                         data-cancelled="<?= $is_cancelled_type ? 'true' : 'false' ?>">
                        
                        <!-- CARD HEADER BAR -->
                        <div class="order-card-header-bar">
                            <div class="d-flex align-items-center flex-wrap gap-2" style="gap: 12px;">
                                <span class="order-id-title">
                                    Order #<?= $ord['order_id'] ?>
                                </span>
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

                        <!-- CARD BODY -->
                        <div class="order-card-main-body">
                            <div class="row align-items-center">
                                <!-- ITEMS LISTING -->
                                <div class="col-lg-7 col-md-12 mb-3 mb-lg-0">
                                    <div class="d-flex flex-column gap-3" style="gap: 12px;">
                                        <?php foreach ($ord['preview_items'] as $item): 
                                            $img_src = !empty($item['picture']) ? (defined('_IMAGE_PATH') ? _IMAGE_PATH : _BASEURL . 'uploads/') . 'item-master/' . ltrim($item['picture'], '/') : _BASEURL . 'assets/images/no-image.jpg';
                                        ?>
                                            <div class="d-flex align-items-center" style="gap: 14px;">
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
                                                + <?= (int)$ord['total_items'] - count($ord['preview_items']) ?> additional item(s) in this order
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mt-3 pt-2 text-muted small border-top">
                                        <i class="icon-map-marker text-warning mr-1"></i> Delivery Address: 
                                        <strong class="text-dark"><?= htmlspecialchars($ord['first_name'] . ' ' . $ord['last_name']) ?></strong>, 
                                        <?= htmlspecialchars($ord['city']) ?> - <?= htmlspecialchars($ord['postcode']) ?>
                                    </div>
                                </div>

                                <!-- ORDER FINANCIALS & ACTIONS -->
                                <div class="col-lg-5 col-md-12 border-left-lg pl-lg-4 text-lg-right">
                                    <div class="mb-3">
                                        <span class="text-muted small d-block">Order Grand Total</span>
                                        <span class="font-weight-bold text-dark" style="font-size: 22px; color: #19978c !important;">
                                            ₹<?= number_format($grand_total_val, 2) ?>
                                        </span>
                                        
                                        <?php if ($pay_method_raw === 'cod' && $cod_due_val > 0 && !$is_delivered): ?>
                                            <div class="d-inline-block text-left mt-2 p-2 rounded" style="background: #fef3c7; border: 1px solid #fde68a; font-size: 12px; color: #92400e;">
                                                <i class="icon-money mr-1"></i> Cash to collect on delivery: <strong>₹<?= number_format($cod_due_val, 2) ?></strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex justify-content-lg-end align-items-center flex-wrap gap-2" style="gap: 8px;">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-view-order px-3" data-order-id="<?= $ord['order_id'] ?>" style="border-radius: 20px; font-weight: 600;">
                                            <i class="icon-eye mr-1"></i> View Order Details
                                        </button>

                                        <?php if ($can_cancel): ?>
                                            <button type="button" class="btn btn-outline-danger btn-sm btn-cancel-order px-3" 
                                                    data-order-id="<?= $ord['order_id'] ?>" 
                                                    data-pay-status="<?= $pay_status_raw ?>"
                                                    data-paid-amt="<?= $paid_amount_val ?>"
                                                    style="border-radius: 20px; font-weight: 600;">
                                                <i class="icon-close mr-1"></i> Cancel Order
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

                        <!-- COURIER TRACKING STRIP -->
                        <?php if ($has_awb): ?>
                            <div class="order-courier-strip">
                                <div>
                                    <i class="icon-truck mr-1"></i> Courier Partner: <strong><?= $courier_name ?></strong> &nbsp;|&nbsp; 
                                    AWB Number: <strong class="text-dark"><?= htmlspecialchars($awb_code) ?></strong>
                                </div>
                                <a href="javascript:void(0)" class="btn-track-order font-weight-bold" data-order-id="<?= $ord['order_id'] ?>" style="color: #0f766e; text-decoration: underline;">
                                    Track Live Shipment Status &rarr;
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Instant Client-Side Filter & Search Script -->
            <script>
            $(document.body).ready(function() {
                var currentFilter = 'all';

                // Tab Filter Buttons
                $('#orderFilterPills').on('click', '.order-filter-btn', function() {
                    $('#orderFilterPills .order-filter-btn').removeClass('active');
                    $(this).addClass('active');
                    currentFilter = $(this).data('filter');
                    applyOrderFilters();
                });

                // Search Input Filter
                $('#orderSearchInput').on('input', function() {
                    applyOrderFilters();
                });

                function applyOrderFilters() {
                    var q = $('#orderSearchInput').val().toLowerCase().trim();

                    $('.order-card-box').each(function() {
                        var $card = $(this);
                        var searchStr = $card.data('search') || '';
                        var matchSearch = !q || (searchStr.indexOf(q) !== -1);

                        var matchTab = false;
                        if (currentFilter === 'all') {
                            matchTab = true;
                        } else if (currentFilter === 'in_progress') {
                            matchTab = ($card.data('in-progress') === true || $card.data('in-progress') === 'true');
                        } else if (currentFilter === 'delivered') {
                            matchTab = ($card.data('delivered') === true || $card.data('delivered') === 'true');
                        } else if (currentFilter === 'cod') {
                            matchTab = ($card.data('cod') === true || $card.data('cod') === 'true');
                        } else if (currentFilter === 'paid') {
                            matchTab = ($card.data('paid') === true || $card.data('paid') === 'true');
                        } else if (currentFilter === 'cancelled') {
                            matchTab = ($card.data('cancelled') === true || $card.data('cancelled') === 'true');
                        }

                        if (matchSearch && matchTab) {
                            $card.fadeIn(150);
                        } else {
                            $card.fadeOut(150);
                        }
                    });
                }
            });
            </script>

        <?php endif; ?>
    </div>

<?php 
endif;

include("include/user_account_footer.php");
?>
