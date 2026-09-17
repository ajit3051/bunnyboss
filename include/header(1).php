<?php 
$validationHelper = new validation();
$db = connect(); 
$cart_session = get_cart_session();
?>
<div id="toast"></div>
<!-- End .header-top -->
<div class="header-middle" style="margin-top: -10px; margin-bottom:0px; background-color: whitesmoke;">
   <div class="container">
      <div class="header-left">
         <button class="mobile-menu-toggler">
         <span class="sr-only">Toggle mobile menu</span>
         <i class="icon-bars"></i>
         </button>
         <a href="<?= _BASEURL ?>" class="logo">
            <!-- <img src="assets/images/demos/demo-26/logo.png" alt="Molla Logo" width="105" height="25">
               -->
            <strong style="color: #009688; font-size:20px;">Bunny Boss</strong>
            
         </a>
      </div>
     <!--  <div class="header-left">
         <div class="wishlist">
            <a href="wishlist.html" title="Wishlist">
               <div class="icon">
                  <i class="icon-map-marker"></i>
               </div>
               <p style="font-size: 11px;">Delivering to Delhi 123076<br>Update Location</p>
            </a>
         </div>
      </div> -->
      <!-- End .header-left -->
      <div class="header-center">
         <div class="header-search header-search-visible header-search-no-radius">
            <a href="#" class="search-toggle" role="button">
            <i class="icon-search"></i>
            </a>
            <form action="<?= _BASEURL ?>product-list.php" method="get">
               <div class="header-search-wrapper search-wrapper-wide">
                  <div class="select-custom">
                     <select id="cat" name="category">
                        <option value="">All Departments</option>
                        <?php
                        $group_Stmt = getGroupList();

                        while ($group = $group_Stmt->fetch_assoc()) {
                           echo '<option value="' . htmlspecialchars($group['group_name']). '">' . htmlspecialchars($group['group_name']) . '</option>';
                        }
                        ?>
                     </select>
                  </div>
                  <!-- End .select-custom -->
                  <label for="q" class="sr-only">Search</label>
                  <input type="search" class="form-control" name="q" id="q" placeholder="Search product ..." required>
                  <button class="btn btn-primary" type="submit">
                  <i class="icon-search"></i>
                  </button>
               </div>
               <!-- End .header-search-wrapper -->
            </form>
         </div>
         <!-- End .header-search -->
      </div>
      <div class="header-right">
         <div class="header-dropdown-link">
            <div class="dropdown compare-dropdown">
               <a href="" class="dropdown-toggle" data-toggle="modal" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
                   <!--<a href="#signin-modal" class="dropdown-toggle" data-toggle="modal" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">-->
                  <div class="icon">
                     <i class="icon-user"></i>
                  </div>
                  <p>Hello,sign in<br> Accounts & List</p>
               </a>
            </div>
            <!--<div class="wishlist">-->
            <!--   <a href="wishlist.html" title="Wishlist">-->
            <!--      <div class="icon">-->
            <!--         <i class="icon-shopping-cart"></i>-->
            <!--         <span class="wishlist-count badge">3</span>-->
            <!--      </div>-->
            <!--      <p>Returs & Orders</p>-->
            <!--   </a>-->
            <!--</div>-->
          
            <!-- End .compare-dropdown -->
            <div class="dropdown cart-dropdown">
               <!-- <a href="<?= _BASEURL ?>cart.php" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static"> -->
                  <a href="<?= _BASEURL ?>cart.php" class="dropdown-toggle" role="button">
                  <div class="icon">
                     <i class="icon-shopping-cart"></i>
                     <span class="cart-count"><?= get_cart_count($cart_session); ?></span>
                  </div>
                  <p>Cart</p>
                  <!-- <p>1455</p> -->
               </a>
               <!-- <div class="dropdown-menu dropdown-menu-right">
                  <div class="dropdown-cart-products">
                     <div class="product">
                        <div class="product-cart-details">
                           <h4 class="product-title letter-spacing-normal font-size-normal">
                              <a href="product.html">Beige knitted elastic runner shoes</a>
                           </h4>
                           <span class="cart-product-info">
                           <span class="cart-product-qty">1</span>
                           x 84.00
                           </span>
                        </div>
                        
                        <figure class="product-image-container">
                           <a href="product.html" class="product-image">
                           <img src="assets/images/products/cart/product-1.jpg" alt="product" width="200" height="300">
                           </a>
                        </figure>
                        <a href="#" class="btn-remove" title="Remove Product">
                        <i class="icon-close"></i>
                        </a>
                     </div>
                     
                     <div class="product">
                        <div class="product-cart-details">
                           <h4 class="product-title letter-spacing-normal font-size-normal">
                              <a href="product.html">Blue utility pinafore denim dress</a>
                           </h4>
                           <span class="cart-product-info">
                           <span class="cart-product-qty">1</span>
                           x 76.00
                           </span>
                        </div>
                        
                        <figure class="product-image-container">
                           <a href="product.html" class="product-image">
                           <img src="assets/images/products/cart/product-2.jpg" alt="product">
                           </a>
                        </figure>
                        <a href="#" class="btn-remove" title="Remove Product">
                        <i class="icon-close"></i>
                        </a>
                     </div>
                     
                  </div>
                  
                  <div class="dropdown-cart-total">
                     <span>Total</span>
                     <span class="cart-total-price">160.00</span>
                  </div>
                  
                  <div class="dropdown-cart-action">
                     <a href="<?= _BASEURL ?>cart.php" class="btn btn-primary">View Cart</a>
                     <a href="<?= _BASEURL ?>checkout.php" class="btn btn-outline-primary-2">
                     <span>Checkout</span>
                     <i class="icon-long-arrow-right"></i>
                     </a>
                  </div>
                 
               </div> -->
               <!-- End .dropdown-menu -->
            </div>
            <!-- End .cart-dropdown -->
         </div>
      </div>
      <!-- End .header-right -->
   </div>
   <!-- End .container -->
   <div class="row">
      <div class="header-bottom sticky-header">
         <div class="container" style="margin-bottom:1px;">
            <div class="header" style="height: 50px; background-color: #009688;">
               <nav class="main-nav">
                  <ul class="menu sf-arrows">
                     <?php
                        $group_header = getGroupList();

                        while ($group = $group_header->fetch_assoc()) { 
                        ?>
                           <li class="megamenu-container pr-5">
                              <a href="<?= _BASEURL."product-list.php/?category=" . $group['group_name'] ?>"><span style="color: white;"><?= htmlspecialchars($group['group_name']) ?></span></a>
                           </li>
                           <?php
                        }
                       
                        ?>
                     
                     </ul>
                     
                  <!-- End .menu -->
               </nav>
               <!-- End .main-nav -->
            </div>
            <!-- End .header-center -->
         </div>
         <!-- End .container -->
      </div>
   </div>
</div>
<!-- End .header-middle -->
<!-- End .header-top -->
<!-- End .header-bottom -->
<!-- End .header-bottom -->

<div class="offer-bar" style="margin-top:-10px;">
    <div class="offer-track">
      <span class="breaking">⚡Hurry Up!</span>

        <span>🔥 Order Now - +91 9999873481</span>
        <span>🚚 Cash On Delivery Available</span>
        <span>💯 Premium Quality Shoes</span>
        <span>🎉 Special Discount This Week</span>
    </div>
</div>
<style>
   .breaking{
    background:#ff0000;
    color:#fff;
    padding:2px 10px;
    font-weight:600;
    
     animation:zooming 1s ease-in-out infinite;
    border-radius: 50px;
    height: 30px;
}
@keyframes zooming{
    0%,100%{
        transform:scale(1);
    }
    50%{
        transform:scale(1.15);
    }
}
    .offer-bar{
    width:100%;
    background:#111;
    color:#fff;
    overflow:hidden;
    padding:12px 0;
    white-space:nowrap;
    border-radius:0px;
}

.offer-track{
    display:inline-flex;
    gap:60px;
    align-items:center;
    animation:scrollText 20s linear infinite;
    will-change:transform;
}
/* Pause on hover */
.offer-bar:hover .offer-track{
    animation-play-state: paused;
}
.offer-track span{
    font-size:16px;
    font-weight:600;
}

@keyframes scrollText{
    from{
        transform:translateX(100%);
    }
    to{
        transform:translateX(-100%);
    }
}

/* Mobile */
@media(max-width:767px){
    .offer-bar{
        padding:10px 0;
    }

    .offer-track span{
        font-size:14px;
    }

    .offer-track{
        gap:40px;
        animation-duration:15s;
    }
}
</style>