<?php 
include_once("include/config.php"); 

$cart_session = get_cart_session();
$cart_count = get_cart_count($cart_session);

if ($cart_count <= 0) {
    header("Location: " . _BASEURL . "index.php");
    exit;
}

include('include/top.php');
?>  

<?php
$db = connect();

$result = $db->select("SELECT CI.*, (SELECT image_path FROM tbl_item_images WHERE item_id = CI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture FROM tbl_cart_items as CI WHERE CI.cart_session = ?", 's', $cart_session);

$imageUrl     = _IMAGE_PATH; 

?>
        <main class="main">
        	
<!--        		<div class="container">-->
<!--    <span class="cart-title">Shopping Cart</span>-->
<!--</div>-->
<!--<style>-->
<!--   .cart-title {-->
<!--    display: block;-->
<!--    text-align: center;-->
<!--}-->
<!--}-->
<!--</style>-->
        	
            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= _BASEURL ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= _BASEURL ?>product-list.php">Shop</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
                    </ol>
                </div><!-- End .container -->
            </nav><!-- End .breadcrumb-nav -->

            <div class="page-content">
            	<div class="cart">
	                <div class="container">
	                	<div class="row">
	                		<div class="col-lg-9">
	                			<table class="table table-cart table-mobile">
									<thead>
										<tr>
											<th>Product</th>
											<th>Size</th>
											<th>Price</th>
											<th>Quantity</th>
											<th>Total</th>
											<th></th>
										</tr>
									</thead>

									<tbody>
                                        <?php while ($row = $result->fetch_assoc()){
                                            $subtotal = $row['price'] * $row['quantity'];
                                            $picture       = htmlspecialchars($row['picture']);
                                        ?>
										<tr data-id="<?php echo $row['id']; ?>" data-product-id="<?php echo $row['product_id']; ?>">
											<td class="product-col">
												<div class="product">
													<figure class="product-media">
														<a href="<?= _BASEURL ?>product-details.php?product-id=<?php echo $row['product_id']; ?>">
															<img src="<?= $imageUrl ?>item-master/<?= $picture ?>" alt="Product image">
														</a>
													</figure>

													<h3 class="product-title">
														<a href="<?= _BASEURL ?>product-details.php?product-id=<?php echo $row['product_id']; ?>"><?php echo htmlspecialchars($row['product_name']); ?></a>
													</h3><!-- End .product-title -->
												</div><!-- End .product -->
											</td>
											<td class="size-value"><?php echo $row['size']; ?></td>
											<td class="price-col">₹<?php echo number_format($row['price'], 2); ?></td>
											<td class="quantity-col">
                                                <div class="cart-product-quantity">
                                                    <input type="number" class="form-control" value="<?php echo (int)$row['quantity']; ?>" min="1" max="10" step="1" data-decimals="0" required>
                                                </div><!-- End .cart-product-quantity -->
                                            </td>
											<td class="total-col">₹<?php echo number_format($subtotal, 2); ?></td>
											<td class="remove-col"><button class="btn-remove remove-btn" data-id="<?php echo $row['id']; ?>"><i class="icon-close"></i></button></td>
										</tr>
                                        <?php } ?>
										
									</tbody>
								</table><!-- End .table table-wishlist -->

	                		<!-- End .cart-bottom -->
	                		</div><!-- End .col-lg-9 -->
	                		<aside class="col-lg-3">
	                			<div class="summary summary-cart">
	                				<h3 class="summary-title">Cart Total</h3><!-- End .summary-title -->

	                				<table class="table table-summary">
	                					<tbody>
	                						<tr class="summary-subtotal cart-subtotal">
	                							<td>Subtotal:</td>
	                							<td>0.00</td>
	                						</tr><!-- End .summary-subtotal -->

	                						<tr class="summary-shipping">
	                							<td>Shipping:</td>
	                							<td>&nbsp;</td>
	                						</tr>

	                						<tr class="summary-shipping-row">
	                							<td>
	                								<div class="custom-control custom-radio">
														<input type="radio" id="express-shipping" name="shipping" class="custom-control-input" checked>
														<label class="custom-control-label" for="express-shipping">Express:</label>
													</div>
	                							</td>
	                							<td>₹<?= number_format(defined('_SHIPPING_CHARGE_') ? (float)_SHIPPING_CHARGE_ : 150, 2) ?></td>
	                						</tr> 

	                						<tr class="summary-total cart-total-before-gst" style="border-top: 1px solid #ebebeb; font-weight: 500;">
	                							<td>Total:</td>
	                							<td>0.00</td>
	                						</tr>

	                						<tr class="summary-gst cart-gst">
	                							<td>GST (<?= (defined('_GST_') ? _GST_ : '5') ?>%):</td>
	                							<td>0.00</td>
	                						</tr><!-- End .summary-gst -->

	                						<tr class="summary-total cart-grand-total" style="border-top: 2px solid #333; font-weight: bold;">
	                							<td>Total (GST included):</td>
	                							<td>0.00</td>
	                						</tr><!-- End .summary-total -->
	                					</tbody>
	                				</table><!-- End .table table-summary -->
									<?php
										if($cart_count > 0){
										?>
	                				<a href="<?= _BASEURL ?>checkout.php" id="checkout-btn" class="btn btn-outline-primary-2 btn-order btn-block">PROCEED TO CHECKOUT</a>
									<?php } ?>
	                			</div><!-- End .summary -->

		            			<a href="<?= _BASEURL ?>product-list.php" class="btn btn-outline-dark-2 btn-block mb-3"><span>CONTINUE SHOPPING</span><i class="icon-refresh"></i></a>
	                		</aside><!-- End .col-lg-3 -->
	                	</div><!-- End .row -->
	                </div><!-- End .container -->
                </div><!-- End .cart -->
            </div><!-- End .page-content -->
        </main><!-- End .main -->
 		<script>
 			window.GST_PERCENT = <?= (defined('_GST_') ? (float)_GST_ : 5) ?>;
 			window.SHIPPING_CHARGE = <?= (defined('_SHIPPING_CHARGE_') ? (float)_SHIPPING_CHARGE_ : 150) ?>;
 			window.SHIPPING_CHARGE_PER_ITEM = <?= (defined('_SHIPPING_CHARGE_PER_ITEM_') ? (float)_SHIPPING_CHARGE_PER_ITEM_ : 100) ?>;
 		</script>
 		<script src="<?= _BASEURL ?>assets/js/page/cart.js?v=2.4"></script>
        <?php include('include/bottom.php');?> 