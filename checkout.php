<?php
include_once("include/config.php");

$cart_session = get_cart_session();
$cart_count = get_cart_count($cart_session);

if ($cart_count <= 0) {
	header("Location: " . _BASEURL . "index.php");
	exit;
}

// Always populate/refresh $_SESSION['checkout_products'] and $_SESSION['checkout_summary'] from database cart items
$db = connect();
$stmt = $db->select("SELECT CI.*, (SELECT image_path FROM tbl_item_images WHERE item_id = CI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture FROM tbl_cart_items as CI WHERE CI.cart_session = ? ORDER BY CI.id DESC", 's', $cart_session);

$checkout_products = [];
$subtotal = 0;
$total_qty = 0;

if ($stmt) {
	while ($row = $stmt->fetch_assoc()) {
		$row_total = (float)$row['price'] * (int)$row['quantity'];
		$subtotal += $row_total;
		$total_qty += (int)$row['quantity'];

		$checkout_products[] = [
			'product_id'    => (int)$row['product_id'],
			'product_title' => $row['product_name'],
			'unit_price'    => (float)$row['price'],
			'quantity'      => (int)$row['quantity'],
			'size'          => $row['size'],
			'row_total'     => $row_total
		];
	}
}

$base_shipping = defined('_SHIPPING_CHARGE_') ? (float)_SHIPPING_CHARGE_ : 150;
$per_item_shipping = defined('_SHIPPING_CHARGE_PER_ITEM_') ? (float)_SHIPPING_CHARGE_PER_ITEM_ : 100;
$shipping = $total_qty > 0 ? ($base_shipping + (($total_qty - 1) * $per_item_shipping)) : 0;

$gst_percent = defined('_GST_') ? (float)_GST_ : 5;
$subtotal_plus_shipping = $subtotal + $shipping;
$gst = $subtotal_plus_shipping * ($gst_percent / 100);

$grand_total = $subtotal_plus_shipping + $gst;

$_SESSION['checkout_products'] = $checkout_products;
$_SESSION['checkout_summary'] = [
	'subtotal'    => $subtotal,
	'shipping'    => $shipping,
	'gst'         => $gst,
	'grand_total' => $grand_total
];

// Check user session for auto-filling and verification
$enable_mobile_verification = defined('_ENABLE_SMS_') && (bool)_ENABLE_SMS_;
$enable_cod = defined('_ENABLE_COD_') ? (bool)_ENABLE_COD_ : true;
$enable_razorpay = defined('_ENABLE_RAZORPAY_') ? (bool)_ENABLE_RAZORPAY_ : true;
$enable_cod_online_deposit = defined('_ENABLE_COD_ONLINE_DEPOSIT_') ? (bool)_ENABLE_COD_ONLINE_DEPOSIT_ : false;
$cod_includes_gst = defined('_COD_INCLUDES_GST_') ? (bool)_COD_INCLUDES_GST_ : true;

$cod_upfront_amount = $shipping + ($cod_includes_gst ? $gst : 0);
$cod_rem_amount = $grand_total - $cod_upfront_amount;
$is_user_logged_in = !empty($_SESSION['user_id']) || !empty($_SESSION['user_mobile']);
$is_phone_verified = !$enable_mobile_verification || $is_user_logged_in;
$logged_user_mobile = $_SESSION['user_mobile'] ?? '';
$logged_user_name = $_SESSION['user_name'] ?? '';

$first_name_val = '';

if ($is_user_logged_in && !empty($_SESSION['user_id'])) {
	$user_stmt = $db->select("SELECT name, mobile FROM tbl_users WHERE id = ?", 'i', $_SESSION['user_id']);
	if ($user_stmt && $u_row = $user_stmt->fetch_assoc()) {
		if (!empty($u_row['mobile'])) {
			$logged_user_mobile = $u_row['mobile'];
		}
		if (!empty($u_row['name'])) {
			$first_name_val = trim($u_row['name']);
		}
	}
}
if (empty($first_name_val) && !empty($logged_user_name) && strpos($logged_user_name, 'User (') === false) {
	$first_name_val = trim($logged_user_name);
}

$checkout_saved_addresses = [];
if ($is_user_logged_in && !empty($_SESSION['user_id'])) {
	$c_addr_stmt = $db->select("SELECT * FROM tbl_user_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC", 'i', $_SESSION['user_id']);
	if ($c_addr_stmt) {
		while ($c_a = $c_addr_stmt->fetch_assoc()) {
			$checkout_saved_addresses[] = $c_a;
		}
	}
}

include('include/top.php');
?>
<style>
/* Checkout Phone & Verify Button Layout Fix */
.checkout-phone-input-group {
    display: flex !important;
    position: relative !important;
    align-items: stretch !important;
    width: 100% !important;
    margin-bottom: 0.3rem !important;
}

.checkout .checkout-phone-input-group .input-group-prepend {
    margin-right: -1px !important;
    display: flex !important;
}

.checkout .checkout-phone-input-group .input-group-prepend .input-group-text {
    height: 42px !important;
    background-color: #f3f4f6 !important;
    border: 1px solid #dcdcdc !important;
    border-right: none !important;
    color: #4b5563 !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    padding: 0 12px !important;
    border-radius: 4px 0 0 4px !important;
    display: flex !important;
    align-items: center !important;
    margin: 0 !important;
}

.checkout .checkout-phone-input-group .form-control#phone {
    height: 42px !important;
    min-height: 42px !important;
    margin-bottom: 0 !important;
    border: 1px solid #dcdcdc !important;
    border-radius: 0 !important;
    font-size: 14px !important;
    padding: 8px 14px !important;
    background-color: #fafafa !important;
    flex: 1 1 auto !important;
    width: 1% !important;
    transition: border-color 0.2s, background-color 0.2s !important;
}

.checkout .checkout-phone-input-group .form-control#phone:focus {
    background-color: #ffffff !important;
    border-color: #cc6666 !important;
    box-shadow: none !important;
}

.checkout .checkout-phone-input-group .input-group-append {
    margin-left: -1px !important;
    display: flex !important;
}

.checkout .checkout-phone-input-group .btn-checkout-verify {
    height: 42px !important;
    min-height: 42px !important;
    min-width: auto !important;
    padding: 0 18px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    letter-spacing: 0.4px !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    border-radius: 0 4px 4px 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    line-height: 1 !important;
    border: 1px solid #cc6666 !important;
    background-color: #cc6666 !important;
    color: #ffffff !important;
    cursor: pointer !important;
    transition: all 0.2s ease-in-out !important;
    box-shadow: 0 2px 4px rgba(204, 102, 102, 0.2) !important;
}

.checkout .checkout-phone-input-group .btn-checkout-verify:hover {
    background-color: #bf4040 !important;
    border-color: #bf4040 !important;
    color: #ffffff !important;
    box-shadow: 0 3px 6px rgba(204, 102, 102, 0.3) !important;
}

.checkout .checkout-phone-input-group .btn-checkout-verify:disabled {
    opacity: 0.65 !important;
    cursor: not-allowed !important;
    box-shadow: none !important;
}

/* When verified and readonly or when verification button is absent */
.checkout .checkout-phone-input-group .form-control#phone[readonly],
.checkout .checkout-phone-input-group .form-control#phone:last-child {
    background-color: #f8fafc !important;
    border-color: #cbd5e1 !important;
    color: #334155 !important;
    border-radius: 0 4px 4px 0 !important;
}

#phone-invalid-msg {
    display: none;
    font-size: 12.5px;
    color: #e53e3e;
    margin-top: 4px;
    font-weight: 500;
}

.checkout-otp-card {
    border-radius: 8px !important;
    border: 1.5px solid #cc6666 !important;
    background-color: #fff9f9 !important;
    box-shadow: 0 4px 15px rgba(204, 102, 102, 0.08) !important;
}

#phone-verified-badge {
    font-size: 11px;
    letter-spacing: 0.3px;
    border-radius: 4px;
}
</style>

<main class="main">
	<div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
		<div class="container">
			<h1 class="page-title">Checkout<span></span></h1>
		</div><!-- End .container -->
	</div><!-- End .page-header -->
	<nav aria-label="breadcrumb" class="breadcrumb-nav">
		<div class="container">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
				<li class="breadcrumb-item"><a href="<?= _BASEURL ?>product-list.php">Shop</a></li>
				<li class="breadcrumb-item active" aria-current="page">Checkout</li>
			</ol>
		</div><!-- End .container -->
	</nav><!-- End .breadcrumb-nav -->

	<div class="page-content">
		<div class="checkout">
			<div class="container">


				<div id="checkout-alert" class="alert d-none" role="alert"></div>

				<form action="<?= _BASEURL ?>process-order.php" method="post" id="checkout-form" novalidate>
					<div class="row">
						<div class="col-lg-9">
							<h2 class="checkout-title">Billing Details</h2><!-- End .checkout-title -->
							<?php if (!empty($checkout_saved_addresses)): ?>
								<div class="card card-body bg-light mb-3 p-3 border-primary" style="border-radius: 8px;">
									<label class="font-weight-bold text-dark mb-1"><i class="icon-map-marker text-warning mr-1"></i> Select Saved Address</label>
									<select class="form-control mb-0" id="select-saved-address">
										<option value="">-- Fill manually or choose saved address --</option>
										<?php foreach ($checkout_saved_addresses as $s_addr): ?>
											<option value="<?= $s_addr['id'] ?>"
												data-name="<?= htmlspecialchars($s_addr['first_name'] . ($s_addr['last_name'] ? ' ' . $s_addr['last_name'] : '')) ?>"
												data-street="<?= htmlspecialchars($s_addr['street_address']) ?>"
												data-city="<?= htmlspecialchars($s_addr['city']) ?>"
												data-postcode="<?= htmlspecialchars($s_addr['postcode']) ?>"
												data-phone="<?= htmlspecialchars($s_addr['phone']) ?>"
												<?= (int)$s_addr['is_default'] === 1 ? 'selected' : '' ?>>
												<?= htmlspecialchars($s_addr['title']) ?>: <?= htmlspecialchars($s_addr['first_name']) ?>, <?= htmlspecialchars($s_addr['street_address']) ?>, <?= htmlspecialchars($s_addr['city']) ?> (<?= htmlspecialchars($s_addr['postcode']) ?>) <?= (int)$s_addr['is_default'] === 1 ? '[Default]' : '' ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							<?php endif; ?>

							<div class="form-group">
								<label>Name *</label>
								<input type="text" class="form-control" name="first_name" id="first_name" value="<?= htmlspecialchars($first_name_val) ?>" placeholder="Enter your full name" required>
								<div class="invalid-feedback">Name is required.</div>
							</div>

							<label>Street address *</label>
							<input type="text" class="form-control" name="street_address" id="street_address" placeholder="House number and Street name" required>
							<div class="invalid-feedback">Street address is required.</div>

							<div class="row">
								<div class="col-sm-6">
									<label>Town / City *</label>
									<input type="text" class="form-control" name="city" id="city" required>
									<div class="invalid-feedback">City is required.</div>
								</div><!-- End .col-sm-6 -->
								<div class="col-sm-6">
									<label>Postcode / ZIP (Pincode) *</label>
									<input type="text" class="form-control" name="postcode" id="postcode" required>
									<div class="invalid-feedback">Valid postcode is required.</div>
								</div><!-- End .col-sm-6 -->
							</div><!-- End .row -->

							<div class="row">
								<div class="col-sm-6">
									<label for="phone" class="d-flex align-items-center justify-content-between">
										<span>Phone Number *</span>
										<?php if ($enable_mobile_verification): ?>
											<span id="phone-verified-badge" class="badge badge-success ml-2 py-1 px-2" style="<?= $is_user_logged_in ? '' : 'display: none;' ?>">
												<i class="icon-check"></i> <?= $is_user_logged_in ? 'Verified Account' : 'Verified' ?>
											</span>
										<?php endif; ?>
									</label>
									<div class="input-group checkout-phone-input-group mb-1">
										<div class="input-group-prepend">
											<span class="input-group-text font-weight-bold">+91</span>
										</div>
										<input type="tel" class="form-control" name="phone" id="phone" value="<?= htmlspecialchars($logged_user_mobile) ?>" placeholder="Enter 10-digit mobile number" maxlength="16" required data-verified="<?= $is_phone_verified ? 'true' : 'false' ?>" <?= $is_user_logged_in ? 'readonly' : '' ?>>
										<?php if ($enable_mobile_verification && !$is_user_logged_in): ?>
											<div class="input-group-append">
												<button type="button" class="btn btn-checkout-verify" id="btn-checkout-send-otp">
													<span>Verify Mobile</span>
												</button>
											</div>
										<?php endif; ?>
									</div>
									<div class="invalid-feedback" id="phone-invalid-msg">Valid 10-digit phone number is required.</div>
								</div><!-- End .col-sm-6 -->
							</div><!-- End .row -->

							<!-- Inline OTP Verification Container for Guest User -->
							<?php if ($enable_mobile_verification && !$is_user_logged_in): ?>
								<div class="row mt-2" id="checkout-otp-wrapper" style="display: none;">
									<div class="col-sm-6">
										<div class="card checkout-otp-card p-3">
											<div class="text-center mb-2">
												<h6 class="font-weight-bold mb-1 text-dark"><i class="icon-mobile mr-1"></i> Verify Mobile Number</h6>
												<p class="small text-muted mb-0">Enter the 6-digit OTP sent to <strong>+91-<span id="checkout-display-mobile"></span></strong></p>
											</div>
											<div class="form-group mb-2">
												<input type="text" class="form-control text-center font-weight-bold" id="checkout-otp-input" placeholder="• • • • • •" maxlength="6" style="font-size: 20px; letter-spacing: 5px; height: 42px;" autocomplete="off">
											</div>
											<div id="checkout-otp-msg" class="mb-2"></div>
											<div class="d-flex justify-content-between align-items-center mb-3 px-1">
												<span id="checkout-timer-text" class="text-muted small">Resend in <strong id="checkout-otp-countdown">30</strong>s</span>
												<button type="button" id="btn-checkout-resend-otp" class="btn btn-link btn-sm p-0 text-primary font-weight-bold" style="display: none; min-width: auto;">Resend OTP</button>
											</div>
											<button type="button" id="btn-checkout-verify-otp" class="btn btn-primary btn-block" style="min-width: auto; height: 42px; font-weight: 600;">
												<span>VERIFY OTP &amp; CONTINUE</span>
												<i class="icon-long-arrow-right ml-2"></i>
											</button>
										</div>
									</div>
								</div>
							<?php endif; ?>

						</div><!-- End .col-lg-9 -->
						<aside class="col-lg-3">
							<div class="summary">
								<h3 class="summary-title">Your Order</h3><!-- End .summary-title -->

								<table class="table table-summary">
									<thead>
										<tr>
											<th>Product</th>
											<th>Total</th>
										</tr>
									</thead>

									<tbody>
										<?php
										foreach ($_SESSION['checkout_products'] as $item) {
										?>
											<tr>
												<td><a href="#"><?= isset($item['product_title']) ? htmlspecialchars($item['product_title']) : 'Unknown Item' ?><?= !empty($item['size']) ? ' (Size: ' . htmlspecialchars($item['size']) . ')' : '' ?><?= isset($item['quantity']) && $item['quantity'] > 1 ? ' × ' . $item['quantity'] : '' ?></a></td>
												<td>₹<?= isset($item['row_total']) ? number_format($item['row_total'], 2) : '0.00' ?></td>
											</tr>

										<?php } ?>
										<tr class="summary-subtotal">
											<td>Subtotal:</td>
											<td>₹<?= isset($_SESSION['checkout_summary']['subtotal']) ? number_format($_SESSION['checkout_summary']['subtotal'], 2) : '0.00' ?></td>
										</tr><!-- End .summary-subtotal -->
										<tr>
											<td>Shipping:</td>
											<td>₹<?= isset($_SESSION['checkout_summary']['shipping']) ? number_format($_SESSION['checkout_summary']['shipping'], 2) : '0.00' ?></td>
										</tr>
										<tr style="border-top: 1px solid #ebebeb; font-weight: 500;">
											<td>Total:</td>
											<td>₹<?= isset($_SESSION['checkout_summary']['subtotal'], $_SESSION['checkout_summary']['shipping']) ? number_format($_SESSION['checkout_summary']['subtotal'] + $_SESSION['checkout_summary']['shipping'], 2) : '0.00' ?></td>
										</tr>
										<tr>
											<td>GST (<?= defined('_GST_') ? _GST_ : '5' ?>%):</td>
											<td>₹<?= isset($_SESSION['checkout_summary']['gst']) ? number_format($_SESSION['checkout_summary']['gst'], 2) : '0.00' ?></td>
										</tr>
										<tr class="summary-total" style="border-top: 2px solid #333;">
											<td>Total (GST included):</td>
											<td><span id="cart-total" data-total="<?= isset($_SESSION['checkout_summary']['grand_total']) ? $_SESSION['checkout_summary']['grand_total'] : 0 ?>">
													₹<?= isset($_SESSION['checkout_summary']['grand_total']) ? number_format($_SESSION['checkout_summary']['grand_total'], 2) : '0.00' ?>
												</span>

											</td>
										</tr><!-- End .summary-total -->
									</tbody>
								</table><!-- End .table table-summary -->

								<!-- One single parent wrapper for BOTH options -->
								<div class="accordion-summary" id="accordion-payment">

									<?php if ($enable_cod): ?>
										<!-- Card 1: COD -->
										<div class="card">
											<div class="card-header" id="heading-1">
												<h2 class="card-title">
													<a class="collapsed d-flex align-items-center" role="button" data-toggle="collapse"
														href="#collapse-1" aria-expanded="false" aria-controls="collapse-1" data-payment="cod">
														Cash on Delivery (COD)
													</a>
												</h2>
											</div>

											<div id="collapse-1" class="collapse" aria-labelledby="heading-1" data-parent="#accordion-payment">
												<div class="card-body">
													<?php if ($enable_cod_online_deposit && $enable_razorpay): ?>
														<?php if ($cod_includes_gst): ?>
															Pay mandatory shipping charge (₹<?= number_format($shipping, 2) ?>) + GST (₹<?= number_format($gst, 2) ?>) = <strong>₹<?= number_format($cod_upfront_amount, 2) ?></strong> online via Razorpay to confirm order. Remaining product balance (₹<?= number_format($cod_rem_amount, 2) ?>) will be collected upon delivery.
														<?php else: ?>
															Pay mandatory shipping charge (₹<?= number_format($shipping, 2) ?>) online via Razorpay to confirm order. Remaining balance (₹<?= number_format($cod_rem_amount, 2) ?>) will be collected upon delivery.
														<?php endif; ?>
													<?php else: ?>
														Pay 100% full amount (₹<?= number_format($grand_total, 2) ?>) in cash upon delivery.
													<?php endif; ?>
												</div>
											</div>
										</div>
									<?php endif; ?>

									<?php if ($enable_razorpay): ?>
										<!-- Card 2: Razorpay -->
										<div class="card">
											<div class="card-header" id="heading-2">
												<h2 class="card-title">
													<a class="collapsed d-flex align-items-center" role="button" data-toggle="collapse"
														href="#collapse-2" aria-expanded="false" aria-controls="collapse-2" data-payment="razorpay">
														Pay Online (Razorpay)
													</a>
												</h2>
											</div>
											<!-- data-parent ALSO points to #accordion-payment -->
											<div id="collapse-2" class="collapse" aria-labelledby="heading-2" data-parent="#accordion-payment">
												<div class="card-body">
													Pay full amount (₹<?= number_format($grand_total, 2) ?>) securely via Credit/Debit Card, UPI, Net Banking or Wallets.
												</div>
											</div>
										</div>
									<?php endif; ?>

									<?php if (!$enable_cod && !$enable_razorpay): ?>
										<div class="alert alert-warning text-center">No payment methods currently available.</div>
									<?php endif; ?>

								</div>

								<!-- Hidden input for your JavaScript validation -->
								<input type="hidden" name="payment_method" id="payment_method" value="">

								<?php
								if ($cart_count > 0 && isset($_SESSION['checkout_summary']['grand_total']) && $_SESSION['checkout_summary']['grand_total'] > 0) {
								?>
									<button type="submit" class="btn btn-outline-primary-2 btn-order btn-block" id="btn-place-order">
										<span class="btn-text">Place Order</span>
										<span class="btn-hover-text">Proceed to Checkout</span>
									</button>
								<?php } ?>
							</div><!-- End .summary -->
						</aside><!-- End .col-lg-3 -->
					</div><!-- End .row -->
				</form>
			</div><!-- End .container -->
		</div><!-- End .checkout -->
	</div><!-- End .page-content -->
</main><!-- End .main -->
<script>
	window.ENABLE_MOBILE_VERIFICATION = <?= json_encode($enable_mobile_verification) ?>;
	window.ENABLE_COD = <?= json_encode($enable_cod) ?>;
	window.ENABLE_RAZORPAY = <?= json_encode($enable_razorpay) ?>;
	window.ENABLE_COD_ONLINE_DEPOSIT = <?= json_encode($enable_cod_online_deposit) ?>;
</script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="<?= _BASEURL ?>assets/js/page/checkout-validation.js?v=3.1"></script>
<script>
	$(document.body).ready(function() {
		function applySelectedAddress() {
			var $opt = $('#select-saved-address option:selected');
			if ($opt.length && $opt.val()) {
				var name = $opt.data('name');
				var street = $opt.data('street');
				var city = $opt.data('city');
				var postcode = $opt.data('postcode');

				if (name) $('#first_name').val(name);
				if (street) $('#street_address').val(street);
				if (city) $('#city').val(city);
				if (postcode) $('#postcode').val(postcode);
			}
		}

		// Auto-fill on page load if default address is selected
		applySelectedAddress();

		// Auto-fill on dropdown selection change
		$('#select-saved-address').on('change', function() {
			applySelectedAddress();
		});
	});
</script>
<?php
$hide_footer = true;
include('include/bottom.php');
?>