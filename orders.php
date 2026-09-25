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
            // Fetch first 2 item images for preview
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
            $orders[] = $o_row;
        }
    }
?>

    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <div>
                <h4 class="dashboard-card-title">
                    <i class="icon-shopping-cart text-primary"></i> My Orders
                </h4>
                <p class="text-muted small mb-0">Track shipments, view detailed invoices, and check order statuses.</p>
            </div>
            <div>
                <span class="badge badge-light px-3 py-2 text-dark font-weight-bold" style="font-size: 13px; border: 1px solid #e2e8f0;">
                    Total: <?= count($orders) ?> Order(s)
                </span>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="icon-shopping-cart"></i></div>
                <h5>No Orders Found</h5>
                <p>You haven't placed any orders yet. Explore our latest footwear collection and enjoy cash on delivery with easy tracking!</p>
                <a href="<?= _BASEURL ?>product-list.php" class="btn btn-primary btn-round px-4" style="background-color: #19978c; border-color: #19978c; font-weight: bold;">
                    <i class="icon-shopping-bag mr-1"></i> Start Shopping
                </a>
            </div>
        <?php else: ?>

            <!-- Real-time Filter Search Input -->
            <div class="mb-4">
                <div class="input-group" style="max-width: 350px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white" style="border-right: none; border-radius: 8px 0 0 8px;">
                            <i class="icon-search text-muted"></i>
                        </span>
                    </div>
                    <input type="text" id="orderSearchInput" class="form-control" placeholder="Search by Order ID or Date..." style="border-left: none; border-radius: 0 8px 8px 0;">
                </div>
            </div>

            <div class="orders-list-wrapper" id="ordersListContainer">
                <?php foreach ($orders as $ord): 
                    $order_date_str = date('d M Y, h:i A', strtotime($ord['created_at']));
                    $has_awb = !empty($ord['courier_awb']) || !empty($ord['delhivery_awb']);
                ?>
                    <div class="order-card-item mb-4 p-3 p-md-4 rounded" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); transition: all 0.2s;" data-search="<?= strtolower($ord['order_id'] . ' ' . $order_date_str . ' ' . $ord['payment_method'] . ' ' . ($ord['payment_status'] ?? '')) ?>">
                        <!-- Order Top Header -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-3 mb-3 border-bottom">
                            <div>
                                <h5 class="mb-1 text-dark font-weight-bold" style="font-size: 16px;">
                                    Order #<?= $ord['order_id'] ?>
                                </h5>
                                <div class="small text-muted">
                                    <i class="icon-calendar mr-1"></i> Placed on <?= $order_date_str ?>
                                </div>
                            </div>

                            <div class="d-flex align-items-center flex-wrap gap-2" style="gap: 8px;">
                                <span class="order-badge badge-pay-<?= strtolower($ord['payment_status'] ?? 'pending') ?>">
                                    PAYMENT: <?= strtoupper($ord['payment_status'] ?? 'PENDING') ?>
                                </span>
                                <span class="order-badge badge-status-<?= strtolower($ord['order_status'] ?? 'pending') ?>">
                                    ORDER: <?= strtoupper($ord['order_status'] ?? 'PENDING') ?>
                                </span>
                                <span class="badge badge-secondary px-2 py-1" style="font-size: 10px; text-transform: uppercase;">
                                    <?= htmlspecialchars($ord['payment_method']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Order Body -->
                        <div class="row align-items-center">
                            <div class="col-md-7 mb-3 mb-md-0">
                                <div class="d-flex flex-column gap-2" style="gap: 8px;">
                                    <?php foreach ($ord['preview_items'] as $item): 
                                        $img_src = !empty($item['picture']) ? (defined('_IMAGE_PATH') ? _IMAGE_PATH : _BASEURL . 'uploads/') . 'item-master/' . ltrim($item['picture'], '/') : _BASEURL . 'assets/images/no-image.jpg';
                                    ?>
                                        <div class="d-flex align-items-center" style="gap: 12px;">
                                            <img src="<?= $img_src ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #eef2f5;" onerror="this.src='<?= _BASEURL ?>assets/images/no-image.jpg';">
                                            <div>
                                                <strong class="text-dark small d-block"><?= htmlspecialchars($item['product_title']) ?></strong>
                                                <div class="small text-muted">
                                                    Qty: <?= $item['qty'] ?><?= !empty($item['size']) ? ' | Size: ' . $item['size'] : '' ?> &bull; ₹<?= number_format($item['price'], 2) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if ((int)$ord['total_items'] > count($ord['preview_items'])): ?>
                                        <small class="text-muted font-italic">+ <?= (int)$ord['total_items'] - count($ord['preview_items']) ?> more item(s)</small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-5 text-md-right">
                                <div class="mb-3">
                                    <span class="text-muted small d-block">Grand Total</span>
                                    <strong class="text-dark" style="font-size: 19px; color: #19978c !important;">₹<?= number_format($ord['grand_total'], 2) ?></strong>
                                </div>

                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary btn-sm btn-view-order px-3" data-order-id="<?= $ord['order_id'] ?>" style="border-radius: 20px; font-weight: 600;">
                                        <i class="icon-eye mr-1"></i> View Details
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm btn-track-order px-3 ml-2" data-order-id="<?= $ord['order_id'] ?>" style="border-radius: 20px; font-weight: 600;">
                                        <i class="icon-truck mr-1"></i> Track Order
                                    </button>
                                </div>
                            </div>
                        </div>

                        <?php if ($has_awb): ?>
                            <div class="mt-3 pt-2 border-top small text-muted d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <i class="icon-truck text-primary mr-1"></i> Shipment AWB: <strong class="text-dark"><?= htmlspecialchars($ord['courier_awb'] ?? $ord['delhivery_awb']) ?></strong> (<?= ucfirst($ord['courier_name'] ?? 'Delhivery') ?>)
                                </div>
                                <a href="javascript:void(0)" class="btn-track-order text-primary font-weight-bold" data-order-id="<?= $ord['order_id'] ?>">
                                    Check Live Tracking Status &rarr;
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <script>
            $(document.body).ready(function() {
                $('#orderSearchInput').on('input', function() {
                    var q = $(this).val().toLowerCase().trim();
                    $('.order-card-item').each(function() {
                        var searchStr = $(this).data('search');
                        if (!q || (searchStr && searchStr.indexOf(q) !== -1)) {
                            $(this).fadeIn(100);
                        } else {
                            $(this).fadeOut(100);
                        }
                    });
                });
            });
            </script>

        <?php endif; ?>
    </div>

<?php 
endif;

include("include/user_account_footer.php");
?>
