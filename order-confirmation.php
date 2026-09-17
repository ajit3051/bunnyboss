<?php include_once("include/config.php"); ?>
<?php include('include/top.php');?>

<?php
$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;

$order = null;
$order_items = [];
$error_message = '';

if ($order_id <= 0) {
    $error_message = 'Invalid order reference.';
} else {
    $db = connect();

    // 1. Fetch the order details
    $order_stmt = $db->select("SELECT * FROM tbl_orders WHERE order_id = ?", 'i', $order_id);
    $order = $order_stmt->fetch_assoc();

    if (!$order) {
        $error_message = 'We could not find that order.';
    } else {
        // Initialize the array to prevent warnings if it's empty
        $order_items = []; 
        
        // 2. Fetch all matching items for this order
        $items_result = $db->select("SELECT * FROM tbl_order_items WHERE order_id = ?", 'i', $order_id);
        
        // Loop through all rows returned by the query
        while ($row = $items_result->fetch_assoc()) {
            $order_items[] = $row;
        }

        // Clear cart items from database & session once order confirmation is reached
        if (function_exists('get_cart_session')) {
            $cart_session = get_cart_session();
            if (!empty($cart_session)) {
                $db->delete("DELETE FROM tbl_cart_items WHERE cart_session = ?", 's', $cart_session);
            }
        }
        unset($_SESSION['checkout_products'], $_SESSION['checkout_summary'], $_SESSION['cart_items'], $_SESSION['rzp_order_id'], $_SESSION['rzp_amount']);
    }
}
?>

        <main class="main">
        	<div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
        		<div class="container">
        			<h1 class="page-title">Order Confirmation<span>Shop</span></h1>
        		</div><!-- End .container -->
        	</div><!-- End .page-header -->
            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= _BASEURL ?>index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= _BASEURL ?>product-list.php">Shop</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Order Confirmation</li>
                    </ol>
                </div><!-- End .container -->
            </nav><!-- End .breadcrumb-nav -->

            <div class="page-content">
                <div class="container">

                <?php if ($error_message): ?>

                    <div class="alert alert-danger text-center my-5">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                    <div class="text-center mb-5">
                        <a href="<?= _BASEURL ?>index.php" class="btn btn-outline-primary-2">
                            <span class="btn-text">Back to Home</span>
                        </a>
                    </div>

                <?php else: ?>

                    <div class="order-confirmation text-center my-4">
                        <i class="icon-check-circle" style="font-size: 64px; color: #28a745;"></i>
                        <h2 class="mt-3">Thank you, <?= htmlspecialchars($order['first_name']) ?>!</h2>
                        <p class="text-muted">Your order has been placed successfully.</p>
                        <p>Order Reference: <strong>#<?= htmlspecialchars($order['order_id']) ?></strong></p>
                    </div><!-- End .order-confirmation -->

                    <div class="row">
                        <div class="col-lg-7">
                            <h3 class="mb-3">Billing Details</h3>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><?= htmlspecialchars($order['street_address']) ?>, <?= htmlspecialchars($order['city']) ?> - <?= htmlspecialchars($order['postcode']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td><?= htmlspecialchars($order['phone']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method</th>
                                        <td><?php
                                             $cod_includes_gst_val = defined('_COD_INCLUDES_GST_') ? (bool)_COD_INCLUDES_GST_ : true;
                                             $cod_pm_text = $cod_includes_gst_val ? 'Cash on Delivery (Shipping & GST Paid Online)' : 'Cash on Delivery (Shipping Charge Paid Online)';
                                             echo $order['payment_method'] === 'cod' ? $cod_pm_text : htmlspecialchars($order['payment_method']);
                                         ?></td>
                                    </tr>
                                    <tr>
                                        <th>Order Status</th>
                                        <td><span class="badge badge-success text-capitalize"><?= htmlspecialchars($order['order_status']) ?></span></td>
                                    </tr>
                                    <tr>
                                        <th>Order Date</th>
                                        <td><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td>
                                    </tr>
                                    <?php if (!empty($order['order_notes'])): ?>
                                    <tr>
                                        <th>Order Notes</th>
                                        <td><?= nl2br(htmlspecialchars($order['order_notes'])) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div><!-- End .col-lg-7 -->

                        <div class="col-lg-5">
                            <h3 class="mb-3">Order Summary</h3>
                            <table class="table table-summary">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_title']) ?></td>
                                        <td><?= (int) $item['qty'] ?></td>
                                        <td>₹<?= number_format($item['row_total'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                     <tr class="summary-subtotal">
                                         <td colspan="2">Subtotal:</td>
                                         <td>₹<?= number_format($order['subtotal'], 2) ?></td>
                                     </tr>
                                     <tr>
                                         <td colspan="2">Shipping:</td>
                                         <td>₹<?= isset($_SESSION['checkout_summary']['shipping']) ? number_format($_SESSION['checkout_summary']['shipping'], 2) : '0.00' ?></td>
                                     </tr>
                                     <tr style="border-top: 1px solid #ebebeb; font-weight: 500;">
                                         <td colspan="2">Total:</td>
                                         <td>₹<?= number_format($order['subtotal'] + (isset($_SESSION['checkout_summary']['shipping']) ? (float)$_SESSION['checkout_summary']['shipping'] : 0), 2) ?></td>
                                     </tr>
                                     <tr>
                                         <td colspan="2">GST (<?= defined('_GST_') ? _GST_ : '5' ?>%):</td>
                                         <td>₹<?= number_format($order['gst_amount'], 2) ?></td>
                                     </tr>
                                     <tr class="summary-total" style="border-top: 2px solid #333;">
                                         <td colspan="2">Total (GST included):</td>
                                         <td>₹<?= number_format($order['grand_total'], 2) ?></td>
                                     </tr>
                                </tbody>
                            </table>
                        </div><!-- End .col-lg-5 -->
                    </div><!-- End .row -->

                    <div class="text-center my-5">
                        <a href="<?= _BASEURL ?>product-list.php" class="btn btn-outline-primary-2">
                            <span class="btn-text">Continue Shopping</span>
                            <span class="btn-hover-text">Continue Shopping</span>
                        </a>
                    </div>

                <?php endif; ?>

                </div><!-- End .container -->
            </div><!-- End .page-content -->
        </main><!-- End .main -->

<?php include('include/bottom.php');?>