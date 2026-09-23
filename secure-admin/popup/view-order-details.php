<?php include_once("../include/config.php"); ?>
<?php
$validationHelper = new validation();
$db = connect();

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$order_stmt = $db->select("SELECT * FROM tbl_orders WHERE order_id=?", 'i', $order_id);
if (!$order_stmt || $order_stmt->num_rows == 0) {
    echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-body"><p class="text-danger">Order not found.</p></div></div></div>';
    exit();
}

$order = $order_stmt->fetch_assoc();
$order_stmt->close();

$items_stmt = $db->select("SELECT OI.*, (SELECT image_path FROM tbl_item_images WHERE item_id = OI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture FROM tbl_order_items AS OI WHERE OI.order_id=?", 'i', $order_id);
?>
<style>
.order-detail-well {
	height: auto !important;
	min-height: 160px;
	margin-bottom: 15px;
	padding: 12px 15px;
	background-color: #f7f9fa;
	border: 1px solid #e4e5e7;
	border-radius: 4px;
}
.order-detail-well p {
	margin-bottom: 6px;
	font-size: 13px;
	word-wrap: break-word;
}
.order-detail-well h5 {
	margin-top: 0;
	font-weight: bold;
	border-bottom: 1px solid #d0d0d0;
	padding-bottom: 8px;
	margin-bottom: 10px;
	color: #333;
}
</style>
<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header modal-header-primary" style="background-color: #009688; color: white;">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.8;">×</button>
			<h4 class="modal-title" style="margin: 0; text-align: left; font-size: 18px;"><i class="fa fa-shopping-bag" style="margin-right: 8px;"></i>Order Details - #<?= htmlspecialchars($order['order_id']) ?></h4>
		</div>
		<div class="modal-body" style="padding: 20px;">
			<div class="row" style="display: flex; flex-wrap: wrap;">
				<!-- Order Summary -->
				<div class="col-md-6">
					<div class="order-detail-well">
						<h5><i class="fa fa-info-circle"></i> Order Information</h5>
						<p><strong>Order ID:</strong> #<?= htmlspecialchars($order['order_id']) ?></p>
						<p><strong>Order Date:</strong> <?= (!empty($order['created_at']) && $order['created_at'] !== '0000-00-00 00:00:00') ? date('d/m/Y h:i A', strtotime($order['created_at'])) : '' ?></p>
						<p><strong>Order Status:</strong> 
							<span class="label label-<?= ($order['order_status'] == 'paid' || $order['order_status'] == 'completed') ? 'success' : 'warning' ?>">
								<?= htmlspecialchars(strtoupper($order['order_status'])) ?>
							</span>
						</p>
						<p><strong>Payment Method:</strong> <?= htmlspecialchars(strtoupper($order['payment_method'] ?? 'N/A')) ?></p>
						<p><strong>Payment Status:</strong> <?= htmlspecialchars(strtoupper($order['payment_status'] ?? 'N/A')) ?></p>
						<?php if (!empty($order['razorpay_payment_id'])): ?>
							<p><strong>Razorpay Payment ID:</strong> <?= htmlspecialchars($order['razorpay_payment_id']) ?></p>
						<?php endif; ?>
						<?php 
						$awb_val = !empty($order['courier_awb']) ? $order['courier_awb'] : (!empty($order['delhivery_awb']) ? $order['delhivery_awb'] : '');
						$courier_provider = !empty($order['courier_name']) ? ucfirst($order['courier_name']) : 'Delhivery';
						if (!empty($awb_val)): 
						?>
							<p><strong>Courier Partner:</strong> <span class="label label-primary"><?= htmlspecialchars($courier_provider) ?></span></p>
							<p><strong>AWB / Tracking No:</strong> <span class="label label-info"><?= htmlspecialchars($awb_val) ?></span></p>
						<?php endif; ?>
					</div>
				</div>

				<!-- Customer Info -->
				<div class="col-md-6">
					<div class="order-detail-well">
						<h5><i class="fa fa-user"></i> Customer & Shipping Details</h5>
						<p><strong>Name:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
						<p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
						<p><strong>Address:</strong> <?= htmlspecialchars($order['street_address']) ?></p>
						<p><strong>City / Pincode:</strong> <?= htmlspecialchars($order['city']) ?> - <?= htmlspecialchars($order['postcode']) ?></p>
						<?php if (!empty($order['order_notes'])): ?>
							<p><strong>Order Notes:</strong> <?= htmlspecialchars($order['order_notes']) ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Order Items Table -->
			<h5 style="font-weight: bold; margin-top: 10px; margin-bottom: 10px; clear: both;"><i class="fa fa-list"></i> Ordered Items</h5>
			<div class="table-responsive">
				<table class="table table-bordered table-striped" style="margin-bottom: 15px;">
					<thead>
						<tr class="info">
							<th style="width: 60px;">Image</th>
							<th>Txn ID</th>
							<th>Product Title</th>
							<th style="width: 70px;">Size</th>
							<th style="width: 60px;">Qty</th>
							<th>Unit Price</th>
							<th>Shipping</th>
							<th>Total</th>
							<th>Courier / AWB</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$grand_subtotal = 0;
						$total_items_gst = 0;
						$stored_gst_percent = 0;
						if ($items_stmt && $items_stmt->num_rows > 0):
							while ($item = $items_stmt->fetch_assoc()):
								$imgSrc = !empty($item['picture']) ? 'uploads/item-master/' . htmlspecialchars($item['picture']) : 'assets/dist/img/no-image.png';
								$grand_subtotal += $item['row_total'];
								$total_items_gst += (float)($item['gst_amount'] ?? 0);
								if (!empty($item['gst_percent']) && (float)$item['gst_percent'] > 0) {
									$stored_gst_percent = (float)$item['gst_percent'];
								}
						?>
							<tr>
								<td style="text-align: center; vertical-align: middle;">
									<img src="<?= $imgSrc ?>" alt="Item Image" class="img-thumbnail img-popup-trigger" style="max-height: 45px; max-width: 45px; object-fit: contain; cursor: pointer;" title="Click to view full image" onclick="event.stopPropagation(); showImageModal(this.src, '<?= htmlspecialchars(addslashes($item['product_title']), ENT_QUOTES) ?>', event);">
								</td>
								<td style="vertical-align: middle;">
									<span class="label label-primary" style="font-family: monospace; font-size: 11px; letter-spacing: 0.5px;"><?= htmlspecialchars($item['transaction_id'] ?? '-') ?></span>
								</td>
								<td style="vertical-align: middle;"><?= htmlspecialchars($item['product_title']) ?></td>
								<td style="vertical-align: middle;"><?= htmlspecialchars($item['size']) ?></td>
								<td style="vertical-align: middle;"><?= htmlspecialchars($item['qty']) ?></td>
								<td style="vertical-align: middle;">₹<?= htmlspecialchars($item['price']) ?></td>
								<td style="vertical-align: middle;">₹<?= htmlspecialchars($item['shipping']) ?></td>
								<td style="vertical-align: middle;"><strong>₹<?= htmlspecialchars($item['row_total']) ?></strong></td>
								<td style="vertical-align: middle;">
									<?php if (!empty($item['courier_awb'])): ?>
										<span class="label label-info" style="font-size: 11px;"><?= htmlspecialchars($item['courier_awb']) ?></span>
									<?php elseif (!empty($item['dispatch_status']) && $item['dispatch_status'] === 'skipped_test'): ?>
										<span class="label label-default" style="font-size: 11px;">Test (Skipped)</span>
									<?php else: ?>
										<span class="label label-warning" style="font-size: 11px;"><?= htmlspecialchars(strtoupper($item['dispatch_status'] ?? 'Pending')) ?></span>
									<?php endif; ?>
								</td>
							</tr>
						<?php 
							endwhile;
						else:
						?>
							<tr><td colspan="9" class="text-center">No items found for this order.</td></tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>

			<!-- Totals Summary -->
			<?php 
			$sub_amt = (float)($order['subtotal'] ?? $grand_subtotal);
			$grand_amt = (float)$order['grand_total'];
			
			// Priority: read stored order GST amount, fallback to items GST sum
			$gst_amt = isset($order['gst_amount']) && (float)$order['gst_amount'] > 0 ? (float)$order['gst_amount'] : $total_items_gst;
			
			// Calculate effective stored GST percentage if not explicitly recorded
			if ($stored_gst_percent <= 0 && $sub_amt > 0 && $gst_amt > 0) {
				$stored_gst_percent = round(($gst_amt / $sub_amt) * 100, 2);
			}

			$shipping_amt = max(0, $grand_amt - ($sub_amt + $gst_amt));
			?>
			<div class="row">
				<div class="col-md-6 col-md-offset-6 text-right">
					<p style="font-size: 14px; margin-bottom: 5px;"><strong>Subtotal:</strong> ₹<?= number_format($sub_amt, 2) ?></p>
					<?php if ($gst_amt > 0): ?>
						<p style="font-size: 14px; margin-bottom: 5px;"><strong>GST<?= $stored_gst_percent > 0 ? ' (' . htmlspecialchars($stored_gst_percent) . '%)' : '' ?>:</strong> ₹<?= number_format($gst_amt, 2) ?></p>
					<?php endif; ?>
					<?php if ($shipping_amt > 0): ?>
						<p style="font-size: 14px; margin-bottom: 5px;"><strong>Shipping Charges:</strong> ₹<?= number_format($shipping_amt, 2) ?></p>
					<?php endif; ?>
					<p style="font-size: 16px; font-weight: bold; color: #009688; margin-bottom: 0; border-top: 1px solid #ddd; padding-top: 5px;"><strong>Grand Total:</strong> ₹<?= number_format($grand_amt, 2) ?></p>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>