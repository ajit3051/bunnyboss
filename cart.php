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
        <main class="main cart-page">
            <nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
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
                                            $picture  = htmlspecialchars($row['picture'] ?? '');
                                            $imgSrc   = !empty($picture) ? $imageUrl . 'item-master/' . $picture : _BASEURL . 'assets/images/products/cart/product-1.jpg';
                                        ?>
										<tr data-id="<?php echo $row['id']; ?>" data-product-id="<?php echo $row['product_id']; ?>">
											<td class="product-col">
												<div class="product cart-item-product">
													<figure class="product-media cart-item-media">
														<a href="<?= _BASEURL ?>product-details.php?product-id=<?php echo $row['product_id']; ?>">
															<img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($row['product_name']); ?>">
														</a>
													</figure>

													<div class="cart-item-details">
														<h3 class="product-title">
															<a href="<?= _BASEURL ?>product-details.php?product-id=<?php echo $row['product_id']; ?>"><?php echo htmlspecialchars($row['product_name']); ?></a>
														</h3><!-- End .product-title -->
														<div class="cart-item-meta d-lg-none">
															<span class="meta-size">Size: <strong><?php echo $row['size']; ?></strong></span>
															<span class="meta-sep">•</span>
															<span class="meta-price">₹<?php echo number_format($row['price'], 2); ?></span>
														</div>
													</div>
												</div><!-- End .product -->
											</td>
											<td class="size-value d-none d-lg-table-cell"><?php echo $row['size']; ?></td>
											<td class="price-col d-none d-lg-table-cell">₹<?php echo number_format($row['price'], 2); ?></td>
											<td class="quantity-col">
                                                <div class="cart-product-quantity">
                                                    <input type="number" class="form-control" value="<?php echo (int)$row['quantity']; ?>" min="1" max="10" step="1" data-decimals="0" required>
                                                </div><!-- End .cart-product-quantity -->
                                            </td>
											<td class="total-col">₹<?php echo number_format($subtotal, 2); ?></td>
											<td class="remove-col"><button class="btn-remove remove-btn" data-id="<?php echo $row['id']; ?>" title="Remove product"><i class="icon-close"></i></button></td>
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

            <style>
                /* ===================================================
                   CART ITEM LAYOUT - DESKTOP & MOBILE
                   =================================================== */

                /* Ensure product-col in cart uses horizontal layout */
                .cart .product-col .product,
                .cart .cart-item-product {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: center !important;
                    justify-content: flex-start !important;
                    text-align: left !important;
                    background: transparent !important;
                    border: none !important;
                    border-radius: 0 !important;
                    box-shadow: none !important;
                    padding: 0 !important;
                    margin: 0 !important;
                    height: auto !important;
                    width: 100% !important;
                }

                .cart .cart-item-media {
                    width: 68px !important;
                    height: 68px !important;
                    min-width: 68px !important;
                    max-width: 68px !important;
                    margin: 0 14px 0 0 !important;
                    border-radius: 8px !important;
                    overflow: hidden !important;
                    border: 1px solid #ebebeb !important;
                    background: #fbfbfb !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    flex-shrink: 0 !important;
                }

                .cart .cart-item-media img {
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: cover !important;
                    border-radius: 6px !important;
                }

                .cart .cart-item-details {
                    flex: 1 1 auto !important;
                    min-width: 0 !important;
                    text-align: left !important;
                    display: flex !important;
                    flex-direction: column !important;
                    justify-content: center !important;
                    align-items: flex-start !important;
                }

                .cart .cart-item-details .product-title {
                    font-size: 15px !important;
                    font-weight: 600 !important;
                    line-height: 1.35 !important;
                    color: #222222 !important;
                    margin: 0 0 5px 0 !important;
                    text-align: left !important;
                }

                .cart .cart-item-details .product-title a {
                    color: #222222 !important;
                    text-decoration: none !important;
                }

                .cart .cart-item-details .product-title a:hover {
                    color: #37475a !important;
                }

                .cart .cart-item-meta {
                    font-size: 13px !important;
                    color: #666666 !important;
                    margin: 0 !important;
                    display: flex !important;
                    align-items: center !important;
                    gap: 6px !important;
                    line-height: 1.2 !important;
                }

                @media screen and (min-width: 992px) {
                    .cart .cart-item-meta {
                        display: none !important;
                    }
                }

                .cart .cart-item-meta .meta-size strong {
                    color: #222222 !important;
                    font-weight: 700 !important;
                }

                .cart .cart-item-meta .meta-price {
                    font-weight: 600 !important;
                    color: #37475a !important;
                }

                .cart .cart-item-meta .meta-sep {
                    color: #cccccc !important;
                }

                /* ===================================================
                   MOBILE VIEW (<= 991px)
                   =================================================== */
                @media screen and (max-width: 991px) {
                    .cart .table-mobile,
                    .cart .table-mobile tbody {
                        display: block !important;
                        width: 100% !important;
                        border: none !important;
                    }

                    .cart .table-mobile thead {
                        display: none !important;
                    }

                    /* Modern Card Container for each Cart Item */
                    .cart .table-mobile tbody tr {
                        display: flex !important;
                        flex-wrap: wrap !important;
                        position: relative !important;
                        background: #ffffff !important;
                        border: 1px solid #e8e8e8 !important;
                        border-radius: 12px !important;
                        padding: 14px !important;
                        margin-bottom: 14px !important;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
                        width: 100% !important;
                        box-sizing: border-box !important;
                    }

                    /* Remove Button (Top-Right Circle) */
                    .cart .table-mobile .remove-col {
                        position: absolute !important;
                        top: 12px !important;
                        right: 12px !important;
                        width: auto !important;
                        padding: 0 !important;
                        margin: 0 !important;
                        z-index: 5 !important;
                        border: none !important;
                        text-align: right !important;
                        display: block !important;
                    }

                    .cart .table-mobile .remove-col .btn-remove {
                        width: 28px !important;
                        height: 28px !important;
                        border-radius: 50% !important;
                        background-color: #f4f4f4 !important;
                        color: #777777 !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        border: none !important;
                        font-size: 12px !important;
                        padding: 0 !important;
                        margin: 0 !important;
                        line-height: 1 !important;
                        cursor: pointer !important;
                        transition: all 0.2s ease !important;
                    }

                    .cart .table-mobile .remove-col .btn-remove:hover {
                        background-color: #ffebee !important;
                        color: #e53935 !important;
                    }

                    /* Top Section: Row 1 (Image + Title + Size/Price Meta) */
                    .cart .table-mobile .product-col {
                        width: 100% !important;
                        flex: 0 0 100% !important;
                        max-width: 100% !important;
                        padding: 0 36px 12px 0 !important;
                        margin: 0 !important;
                        border: none !important;
                        border-bottom: 1px solid #f0f0f0 !important;
                        text-align: left !important;
                        display: block !important;
                        box-sizing: border-box !important;
                    }

                    /* Desktop-only cells explicitly hidden on mobile */
                    .cart .table-mobile td.size-value,
                    .cart .table-mobile td.price-col {
                        display: none !important;
                        width: 0 !important;
                        height: 0 !important;
                        padding: 0 !important;
                        margin: 0 !important;
                        border: none !important;
                    }

                    /* Bottom Section: Row 2 (Quantity on Left 50%, Total on Right 50%) */
                    .cart .table-mobile .quantity-col {
                        width: 50% !important;
                        flex: 0 0 50% !important;
                        max-width: 50% !important;
                        padding: 12px 0 0 0 !important;
                        margin: 0 !important;
                        border: none !important;
                        text-align: left !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: flex-start !important;
                        gap: 8px !important;
                        box-sizing: border-box !important;
                    }

                    .cart .table-mobile .quantity-col::before {
                        content: "Qty:" !important;
                        font-size: 13px !important;
                        font-weight: 600 !important;
                        color: #555555 !important;
                    }

                    .cart .table-mobile .cart-product-quantity {
                        max-width: 95px !important;
                        margin: 0 !important;
                    }

                    .cart .table-mobile .cart-product-quantity .input-group {
                        border-radius: 6px !important;
                        overflow: hidden !important;
                        border: 1px solid #d5d5d5 !important;
                        margin-bottom: 0 !important;
                    }

                    .cart .table-mobile .cart-product-quantity .form-control {
                        height: 32px !important;
                        padding: 0 !important;
                        font-size: 13px !important;
                        font-weight: 700 !important;
                        text-align: center !important;
                        border: none !important;
                    }

                    .cart .table-mobile .cart-product-quantity .btn-spinner {
                        width: 26px !important;
                        height: 32px !important;
                        padding: 0 !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        font-size: 11px !important;
                        background: #f8f8f8 !important;
                        border: none !important;
                    }

                    .cart .table-mobile .total-col {
                        width: 50% !important;
                        flex: 0 0 50% !important;
                        max-width: 50% !important;
                        padding: 12px 0 0 0 !important;
                        margin: 0 !important;
                        border: none !important;
                        text-align: right !important;
                        font-size: 16px !important;
                        font-weight: 700 !important;
                        color: #111111 !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: flex-end !important;
                        gap: 4px !important;
                        box-sizing: border-box !important;
                    }

                    .cart .table-mobile .total-col::before {
                        content: "Total: " !important;
                        font-size: 13px !important;
                        font-weight: 500 !important;
                        color: #777777 !important;
                    }

                    /* Summary Card on Mobile */
                    .summary.summary-cart {
                        background: #ffffff !important;
                        border: 1px solid #e8e8e8 !important;
                        border-radius: 12px !important;
                        padding: 20px 16px !important;
                        margin-top: 14px !important;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
                    }

                    .summary-title {
                        font-size: 18px !important;
                        font-weight: 700 !important;
                        margin-bottom: 14px !important;
                    }

                    #checkout-btn {
                        background-color: #37475a !important;
                        border-color: #37475a !important;
                        color: #ffffff !important;
                        border-radius: 6px !important;
                        height: 48px !important;
                        font-size: 14px !important;
                        font-weight: 700 !important;
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        letter-spacing: 0.5px !important;
                        transition: all 0.25s ease !important;
                    }

                    #checkout-btn:hover {
                        background-color: #232f3e !important;
                        border-color: #232f3e !important;
                    }
                }
            </style>
        </main><!-- End .main -->
 		<script>
 			window.GST_PERCENT = <?= (defined('_GST_') ? (float)_GST_ : 5) ?>;
 			window.SHIPPING_CHARGE = <?= (defined('_SHIPPING_CHARGE_') ? (float)_SHIPPING_CHARGE_ : 150) ?>;
 			window.SHIPPING_CHARGE_PER_ITEM = <?= (defined('_SHIPPING_CHARGE_PER_ITEM_') ? (float)_SHIPPING_CHARGE_PER_ITEM_ : 100) ?>;
 		</script>
 		<script src="<?= _BASEURL ?>assets/js/page/cart.js?v=2.7"></script>
        <?php include('include/bottom.php');?> 