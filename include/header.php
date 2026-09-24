<?php $cart_session = get_cart_session(); ?>
<style>
    /* Prevent horizontal scrollbar glitches */
    html,
    body {
        overflow-x: clip !important;
    }

    /* IMPORTANT: Prevent page-wrapper from creating a CSS containing block via transform */
    .page-wrapper {
        display: block !important;
        transform: none !important;
        transition: none !important;
        perspective: none !important;
        filter: none !important;
        will-change: auto !important;
    }

    body.mmenu-active .page-wrapper {
        transform: translateX(280px) !important;
    }

    /* Fix Logo Bar + Category Menu Bar together at Top 0 on Desktop */
    @media screen and (min-width: 768px) {
        .desktop-header-wrap {
            display: block !important;
            visibility: visible !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 1020 !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: whitesmoke !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12) !important;
        }

        .header-middle {
            display: block !important;
            visibility: visible !important;
            background-color: whitesmoke !important;
            margin: 0 !important;
            padding: 10px 0 !important;
        }

        .header-middle .container {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        .header-middle .header-left {
            display: flex !important;
            align-items: center !important;
            gap: 20px !important;
            flex-shrink: 0 !important;
        }

        .header-middle .logo {
            margin: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            flex-direction: row !important;
            gap: 6px !important;
            white-space: nowrap !important;
        }

        .header-middle .wishlist {
            margin: 0 !important;
        }

        .header-middle .wishlist a {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            text-decoration: none !important;
            color: #333 !important;
        }

        .header-middle .wishlist .icon {
            font-size: 20px !important;
            color: #37475a !important;
            margin: 0 !important;
        }

        .header-middle .header-center {
            flex: 1 !important;
            max-width: 550px !important;
            margin: 0 20px !important;
        }

        .header-middle .header-right {
            display: flex !important;
            align-items: center !important;
            gap: 25px !important;
            margin-left: auto !important;
            flex-shrink: 0 !important;
        }

        .header-middle .compare-dropdown,
        .header-middle .cart-dropdown {
            margin: 0 !important;
        }

        .header-bottom {
            display: block !important;
            visibility: visible !important;
            background-color: #37475a !important;
            width: 100% !important;
            height: 50px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .header-intro-clearance .header-bottom .container::before,
        .header-intro-clearance .header-bottom .container::after {
            display: none !important;
        }
    }

    /* Fix Mobile Header at Top 0 on Mobile */
    @media screen and (max-width: 767px) {
        .mobile-header {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 1020 !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: whitesmoke !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12) !important;
        }
    }

    /* Ensure mobile menu container & overlays sit above fixed header */
    .mobile-menu-overlay,
    .mobile-menu-container {
        z-index: 2000 !important;
    }

    /* Modals & Popups must always sit above headers, offer-marquee, and backdrops */
    .modal-backdrop {
        z-index: 105000 !important;
    }

    .modal,
    #signin-modal,
    #location-modal {
        z-index: 105005 !important;
    }

    .modal-dialog {
        z-index: 105010 !important;
    }

    /* Category Menu Bar */
    .header-bottom {
        background-color: #37475a !important;
        width: 100% !important;
        height: 50px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .header-bottom .container {
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
    }

    .header-bottom .menu-nav-wrap,
    .header-bottom .main-nav {
        width: 100% !important;
        height: 100% !important;
    }

    .header-bottom .menu {
        display: flex !important;
        align-items: center !important;
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
        overflow-x: auto !important;
        white-space: nowrap !important;
    }

    .header-bottom .menu::-webkit-scrollbar {
        display: none !important;
    }

    .header-bottom .menu>li {
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
        flex-shrink: 0 !important;
    }

    .header-bottom .menu>li>a {
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
        padding: 0 20px !important;
        text-decoration: none !important;
        transition: background 0.2s ease, color 0.2s ease !important;
    }

    .header-bottom .menu>li>a:hover,
    .header-bottom .menu>li.active>a {
        background-color: #232f3e !important;
        color: #ffffff !important;
    }

    .header-bottom .menu>li>a span {
        color: inherit !important;
    }

    /* Offer Marquee Bar */
    .offer-bar {
        margin: 0 !important;
    }

    .cart-dropdown .cart-count {
        background-color: #37475a;
    }

    .pulse-ring {
        position: absolute;
        width: 16px;
        height: 16px;
        background: rgba(255, 59, 48, .4);
        border-radius: 50%;
        animation: pulseRing 1.5s infinite;
        z-index: 1;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        30% {
            transform: translateY(-8px);
        }

        60% {
            transform: translateY(-3px);
        }
    }

    @keyframes pulseRing {
        0% {
            transform: scale(1);
            opacity: .8;
        }

        100% {
            transform: scale(4);
            opacity: 0;
        }
    }

    .logo-text {
        font-size: 28px;
        font-weight: 800;
        color: #777;
        /*animation: pulseLogo 1.8s infinite;*/
    }

    .boss {
        background: linear-gradient(90deg, #777, #777, #ffd54f, #808000, #ff6b35);
        background-size: 300% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: bossShine 3s linear infinite, bossFloat 2s ease-in-out infinite;
        display: inline-block;
        text-shadow: 0 5px 15px rgba(255, 152, 0, .25);
    }

    @keyframes bossShine {
        0% {
            background-position: 0% center;
        }

        100% {
            background-position: 300% center;
        }
    }

    @keyframes bossFloat {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-4px);
        }
    }

    @keyframes pulseLogo {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.08);
        }
    }
</style>

<script>
    (function() {
        function getHeaderEl() {
            return window.innerWidth < 768 ?
                document.querySelector('.mobile-header') :
                document.querySelector('.desktop-header-wrap');
        }

        function adjustHeaderPadding() {
            var targetEl = getHeaderEl();
            if (targetEl) {
                document.body.style.paddingTop = targetEl.offsetHeight + 'px';
            }
        }

        // Initial calc
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', adjustHeaderPadding);
        } else {
            adjustHeaderPadding();
        }
        window.addEventListener('load', adjustHeaderPadding);
        window.addEventListener('resize', adjustHeaderPadding);

        // Recalc once icon/web fonts finish loading — this is what usually causes the
        // header to grow taller AFTER padding was already set from a smaller measurement
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(adjustHeaderPadding);
        }

        // Keep padding in sync with ANY future height change: menu wrap, cart badge/price
        // updates, category list length, mobile address-bar show/hide, etc.
        if (window.ResizeObserver) {
            var ro = new ResizeObserver(adjustHeaderPadding);
            var desktopEl = document.querySelector('.desktop-header-wrap');
            var mobileEl = document.querySelector('.mobile-header');
            if (desktopEl) ro.observe(desktopEl);
            if (mobileEl) ro.observe(mobileEl);
        } else {
            // Fallback for old browsers without ResizeObserver
            setInterval(adjustHeaderPadding, 500);
        }
    })();
</script>
<div id="toast"></div>

<?php
$current_loc = function_exists('getCurrentDeliveryLocation') 
    ? getCurrentDeliveryLocation() 
    : ['displayText' => 'Delhi 110059', 'city' => 'Delhi', 'pincode' => '110059'];
?>
<!-- DESKTOP HEADER (Logo Bar + Category Menu Bar) -->
<div class="desktop-header-wrap">
    <!-- 1. LOGO BAR (Header Middle) -->
    <div class="header-middle" style="margin-top: 0px; margin-bottom: 0px; background-color: whitesmoke;">
        <div class="container">
            <div class="header-left">
                <button class="mobile-menu-toggler">
                    <span class="sr-only">Toggle mobile menu</span>
                    <i class="icon-bars"></i>
                </button>
                <a href="<?= _BASEURL ?>index.php" class="logo">
                    <img src="<?= _BASEURL ?>assets/images/logo-m.png" style="height: 38px; width: auto; flex-shrink: 0;" alt="BunnyBoss Logo">
                    <span class="pulse-ring"></span>
                    <strong class="logo-text">BUNNY<span class="boss">BOSS</span></strong>
                </a>
                <div class="wishlist header-delivery-btn" style="margin-left: 15px;">
                    <a href="#location-modal" data-toggle="modal" title="Update Delivery Location" style="cursor: pointer; text-decoration: none;">
                        <div class="icon">
                            <i class="icon-map-marker"></i>
                        </div>
                        <p style="font-size: 11px; margin: 0; line-height: 1.2;">Delivering to <strong class="header-loc-city"><?= htmlspecialchars($current_loc['displayText']) ?></strong><br><span style="font-weight: 600; color: #cc9966;" class="header-loc-action">Update Location</span></p>
                    </a>
                </div>
            </div>

            <div class="header-center">
                <div class="header-search header-search-visible header-search-no-radius">
                    <a href="#" class="search-toggle" role="button">
                        <i class="icon-search"></i>
                    </a>
                    <form action="<?= _BASEURL ?>product-list.php" method="get" class="">
                        <div class="header-search-wrapper search-wrapper-wide" style="border-radius:20px;">
                            <div class="select-custom">
                                <select id="cat" name="cat">
                                    <option value="">All Departments</option>
                                    <?php
                                    $group_Stmt = getGroupList();

                                    while ($group = $group_Stmt->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($group['group_name']) . '">' . htmlspecialchars($group['group_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <label for="q" class="sr-only">Search</label>
                            <input type="search" class="form-control" name="q" id="q" placeholder="Search product ..." required>
                            <button class="btn btn-primary" type="submit">
                                <i class="icon-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="header-right">
                <?php
                $is_user_logged_in = !empty($_SESSION['user_id']);
                $user_disp_name = $is_user_logged_in ? htmlspecialchars($_SESSION['user_name']) : 'sign in';
                $user_role_val = $_SESSION['user_role'] ?? '';
                ?>
                <div class="dropdown compare-dropdown">
                    <?php if ($is_user_logged_in): ?>
                        <a href="<?= _BASEURL ?>dashboard.php" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <div class="icon">
                                <i class="icon-user"></i>
                            </div>
                            <p>Hello, <?= $user_disp_name ?><br> My Account</p>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" style="min-width: 180px; padding: 12px; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                            <a class="dropdown-item text-dark font-weight-bold" href="<?= _BASEURL ?>dashboard.php"><i class="icon-dashboard mr-2"></i> My Dashboard</a>
                            <a class="dropdown-item text-dark" href="<?= _BASEURL ?>dashboard.php?tab=orders"><i class="icon-shopping-cart mr-2"></i> My Orders</a>
                            <a class="dropdown-item text-dark" href="<?= _BASEURL ?>dashboard.php?tab=addresses"><i class="icon-map-marker mr-2"></i> Saved Addresses</a>
                            <a class="dropdown-item text-dark" href="<?= _BASEURL ?>dashboard.php?tab=profile"><i class="icon-user mr-2"></i> Basic Details</a>
                            <?php if (in_array($user_role_val, ['admin', 'staff'], true)): ?>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item text-primary font-weight-bold" href="<?= _ADMIN_URL ?>index.php"><i class="icon-dashboard mr-1"></i> Admin Panel</a>
                            <?php endif; ?>
                            <div class="dropdown-divider my-1"></div>
                            <a class="dropdown-item text-danger" href="<?= _BASEURL ?>logout.php"><i class="icon-long-arrow-right mr-1"></i> Logout</a>
                        </div>
                    <?php else: ?>
                        <a href="#signin-modal" class="dropdown-toggle" data-toggle="modal" role="button">
                            <div class="icon">
                                <i class="icon-user"></i>
                            </div>
                            <p>Hello, sign in<br> Accounts & List</p>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="dropdown cart-dropdown">
                    <a href="<?= _BASEURL ?>cart.php" class="dropdown-toggle" role="button">
                        <div class="icon">
                            <i class="icon-shopping-cart"></i>
                            <span class="cart-count"><?= get_cart_count($cart_session); ?></span>
                        </div>
                        <p>Cart</p>
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- 2. CATEGORY MENU BAR (Header Bottom) -->
    <div class="header-bottom">
        <div class="container">
            <div class="menu-nav-wrap">
                <nav class="main-nav">
                    <ul class="menu sf-arrows">
                        <?php
                        $group_header = getGroupList();
                        $curr_category = $_GET['category'] ?? '';

                        while ($group = $group_header->fetch_assoc()) {
                            $g_name = $group['group_name'];
                            $is_active_cat = (strcasecmp($curr_category, $g_name) === 0);
                            $cat_active_cls = $is_active_cat ? ' active' : '';
                        ?>
                            <li class="megamenu-container<?= $cat_active_cls ?>">
                                <a href="<?= _BASEURL ?>product-list.php?category=<?= urlencode($g_name) ?>"><span><?= htmlspecialchars($g_name) ?></span></a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<style>
    .party {
        display: inline-block;
        animation: floatParty 1.2s infinite ease-in-out;
    }

    @keyframes floatParty {
        0% {
            transform: translateY(0) rotate(0deg);
        }

        25% {
            transform: translateY(-8px) rotate(-10deg);
        }

        50% {
            transform: translateY(0) rotate(10deg);
        }

        75% {
            transform: translateY(-5px) rotate(-5deg);
        }

        100% {
            transform: translateY(0) rotate(0deg);
        }
    }

    .hundred {
        display: inline-block;
        animation: pulse100 1s infinite ease-in-out;
    }

    @keyframes pulse100 {
        0% {
            transform: scale(1) rotate(0deg);
            filter: brightness(1);
        }

        25% {
            transform: scale(1.15) rotate(-5deg);
            filter: brightness(1.3);
        }

        50% {
            transform: scale(1.3) rotate(5deg);
            filter: brightness(1.6);
        }

        75% {
            transform: scale(1.15) rotate(-5deg);
            filter: brightness(1.3);
        }

        100% {
            transform: scale(1) rotate(0deg);
            filter: brightness(1);
        }
    }

    .delivery {
        font-size: 32px;
        font-weight: bold;
        overflow: hidden;
        white-space: nowrap;
        position: relative;
    }

    .truck {
        display: inline-block;
        animation: truckRun 2s linear infinite;
    }

    @keyframes truckRun {
        0% {
            transform: translateX(0px);
        }

        50% {
            transform: translateX(10px);
        }

        100% {
            transform: translateX(-10px);
        }
    }

    .call-now {
        font-size: 30px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .phone-icon {
        display: inline-block;
        transform-origin: 70% 70%;
        animation: ring 0.8s infinite;
    }

    @keyframes ring {
        0% {
            transform: rotate(0deg);
        }

        10% {
            transform: rotate(15deg);
        }

        20% {
            transform: rotate(-15deg);
        }

        30% {
            transform: rotate(15deg);
        }

        40% {
            transform: rotate(-15deg);
        }

        50% {
            transform: rotate(10deg);
        }

        60% {
            transform: rotate(-10deg);
        }

        70% {
            transform: rotate(5deg);
        }

        80% {
            transform: rotate(-5deg);
        }

        100% {
            transform: rotate(0deg);
        }
    }

    .order-now {
        font-size: 32px;
        font-weight: bold;
        color: #222;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .fire {
        display: inline-block;
        animation: fireBurn 0.5s infinite alternate ease-in-out;
        transform-origin: bottom center;
    }

    @keyframes fireBurn {
        0% {
            transform: scale(1) rotate(-3deg);
            filter: brightness(1);
        }

        25% {
            transform: scale(1.15) rotate(3deg);
            filter: brightness(1.4);
        }

        50% {
            transform: scale(0.95) rotate(-2deg);
            filter: brightness(1.2);
        }

        75% {
            transform: scale(1.12) rotate(2deg);
            filter: brightness(1.5);
        }

        100% {
            transform: scale(1) rotate(-3deg);
            filter: brightness(1);
        }
    }
</style>

<style>
    .breaking {
        background: #ff0000;
        color: #fff;
        padding: 2px 10px;
        font-weight: 600;
        animation: zooming 1s ease-in-out infinite;
        border-radius: 50px;
        height: 30px;
    }

    @keyframes zooming {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.15);
        }
    }

    .offer-bar {
        width: 100%;
        background: #111;
        color: #fff;
        overflow: hidden;
        padding: 12px 0;
        white-space: nowrap;
        border-radius: 0px;
    }

    .offer-track {
        display: inline-flex;
        gap: 60px;
        align-items: center;
        animation: scrollText 20s linear infinite;
        will-change: transform;
    }

    /* Mobile */
    @media(max-width:767px) {
        .offer-bar {
            padding: 10px 0;
        }

        .offer-track span {
            font-size: 14px;
        }

        .offer-track {
            gap: 40px;
            animation-duration: 15s;
        }
    }
</style>
<div class="mobile-header">
    <!-- Top -->
    <div class="row mobile-top" style="margin-right:5px;">
        <button class="mobile-menu-toggler">
            <i class="icon-bars"></i>
        </button>
        <a href="<?= _BASEURL ?>index.php" class="logo">
            <img src="<?= _BASEURL ?>assets/images/logo-m.png" style="height: 28px; width: auto; flex-shrink: 0;" alt="BunnyBoss Logo">
            <span class="pulse-ring"></span>
            <strong class="logo-text">BUNNY<span class="boss">BOSS</span></strong>
        </a>
        <div class="mobile-icons">
            <?php if ($is_user_logged_in): ?>
                <a href="<?= _BASEURL ?>dashboard.php" title="My Dashboard">
                    <i class="icon-user"></i>
                </a>
            <?php else: ?>
                <a href="#signin-modal" data-toggle="modal" title="Sign In">
                    <i class="icon-user"></i>
                </a>
            <?php endif; ?>
            <a href="<?= _BASEURL ?>cart.php" class="cart-icon" title="View Cart">
                <i class="icon-shopping-cart"></i>
                <span class="cart-count"><?= get_cart_count($cart_session); ?></span>
            </a>
        </div>
    </div>
    <!-- Location -->
    <div class="mobile-location" data-toggle="modal" data-target="#location-modal" style="margin-top:-20px; cursor: pointer;">
        <i class="icon-map-marker"></i>
        Delivering to <strong class="header-loc-city"><?= htmlspecialchars($current_loc['displayText']) ?></strong> -
        <span class="header-loc-action" style="text-decoration: underline;">Update Location</span>
    </div>
    <!-- Search -->
    <form action="<?= _BASEURL ?>product-list.php" method="get">
        <div class="row">
            <div class="search-box">
                <input type="text" name="q" style="margin-left:6px" placeholder="Search products...">
                <button type="submit">
                    <i class="icon-search"></i>
                </button>
            </div>
        </div>
    </form>
    <div class="mobile-category" style="margin-left:-4px; height:40px; overflow-x: auto; white-space: nowrap; display: flex; align-items: center; gap: 8px;">
        <?php
        $group_mobile_bar = getGroupList();
        while ($g_mob = $group_mobile_bar->fetch_assoc()) {
        ?>
            <a href="<?= _BASEURL ?>product-list.php?category=<?= urlencode($g_mob['group_name']) ?>"><?= htmlspecialchars($g_mob['group_name']) ?></a>
        <?php } ?>
    </div>

</div>
<div class="offer-marquee">
    <div class="offer-track">
        <span class="breaking">⚡Hurry Up!</span>
        <span><span class="fire" style="margin-right:5px;">🔥</span>Flat 50% OFF on Shoes</span>
        <span><a href="tel:+919310386790"> <span class="phone-icon" style="margin-right:5px;">📞</span><span style="color: white;">Order Now -</span> <span style="color:white;">+91 9310386790</a></span>
        <span><span class="truck" style="margin-right:10px;">🚚</span>Paid Delivery Available</span>
        <span><span class="hundred" style="margin-right:5px;">💯</span>Premium Quality Shoes</span>
        <span><span class="party" style="margin-right:5px;">🎉</span>Special Discount This Week</span>
    </div>
</div>
<style>
    /* ================= MOBILE ICONS ================= */
    @media (max-width:767px) {
        .mobile-icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .mobile-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #37475a;
            position: relative;
            font-size: 22px;
        }

        /* Cart Badge */
        .cart-icon {
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -6px;
            right: -8px;
            width: 13px;
            height: 13px;
            border-radius: 30%;
            background: #ff9800;
            color: #000;
            font-size: 10px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }

    .offer-marquee {
        position: relative;
        z-index: 990;
        width: 100%;
        background: #232f3e;
        color: #fff;
        overflow: hidden;
        white-space: nowrap;
        height: 38px;
        display: flex;
        align-items: center;
        margin: 0;
        padding: 0;
    }

    body.modal-open .offer-marquee,
    body.modal-open .offer-track,
    body.modal-open .offer-bar {
        z-index: 1 !important;
        opacity: 0.3 !important;
        pointer-events: none !important;
    }

    /* Pause on hover */
    .offer-bar:hover .offer-track {
        animation-play-state: paused;
        animation: scrollText 20s linear infinite;
    }

    .offer-track {
        display: inline-flex;
        align-items: center;
        gap: 60px;
        animation: marquee 20s linear infinite;
    }

    .offer-track span {
        font-size: 14px;
        font-weight: 600;
    }

    .offer-marquee:hover .offer-track {
        animation-play-state: paused;
    }

    @keyframes marquee {
        0% {
            transform: translateX(100%);
        }

        100% {
            transform: translateX(-100%);
        }
    }

    /* Mobile */
    @media(max-width:767px) {
        .offer-marquee {
            padding: 6px 0;
        }

        .offer-track {
            gap: 35px;
            animation-duration: 15s;
        }

        .offer-track span {
            font-size: 12px;
        }
    }

    /* Mobile */
    @media(max-width:767px) {
        .offer-bar {
            padding: 10px 0;
        }

        .offer-track span {
            font-size: 14px;
        }

        .offer-track {
            gap: 40px;
            animation-duration: 15s;
        }
    }

    /* Desktop Hide */
    .mobile-header {
        display: none;
    }

    /* ================= MOBILE ================= */
    @media (max-width:767px) {

        /* Hide Desktop Header */
        .header-middle,
        .header-bottom,
        .offer-bar {
            display: none;
        }

        /* Show Mobile Header */
        .mobile-header {
            display: block;
            background: whitesmoke;
            color: #fff;
        }

        /* Top */
        .mobile-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0px;
            padding-right: 10px;
        }

        .mobile-menu-toggler {
            background: none;
            color: #37475a;
            padding: 8px 10px;
        }

        .mobile-header .logo {
            display: inline-flex !important;
            align-items: center !important;
            flex-direction: row !important;
            gap: 5px !important;
            white-space: nowrap !important;
            text-decoration: none !important;
        }

        .mobile-header .logo-text {
            color: #37475a;
            font-size: 20px !important;
            font-weight: 800;
            white-space: nowrap !important;
            line-height: 1 !important;
        }

        .mobile-icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .mobile-icons a {
            color: #37475a;
            font-size: 22px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-count {
            position: absolute;
            top: -6px;
            right: -8px;
            width: 16px;
            height: 16px;
            background: #232f3e;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
        }

        /* Search */
        .mobile-search {
            padding: 0 10px 10px;
            width: 100%;
        }

        .search-box {
            display: flex;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
        }

        .search-box input {
            flex: 1;
            border: none;
            height: 44px;
            padding: 0 15px;
            font-size: 15px;
        }

        .search-box input:focus {
            outline: none;
        }

        .search-box button {
            width: 52px;
            border: none;
            background: whitesmoke;
            font-size: 22px;
        }

        /* Category */
        .mobile-category {
            display: flex !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            background: #232f3e;
            white-space: nowrap;
            height: 38px !important;
            align-items: center !important;
            padding: 0 10px !important;
            gap: 8px !important;
        }

        .mobile-category::-webkit-scrollbar {
            display: none;
        }

        .mobile-category a {
            color: #fff !important;
            padding: 5px 14px !important;
            text-decoration: none !important;
            flex: 0 0 auto !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            border-radius: 14px !important;
            background: rgba(255, 255, 255, 0.15) !important;
            transition: background 0.2s ease;
        }

        .mobile-category a:hover,
        .mobile-category a:active {
            background: rgba(255, 255, 255, 0.35) !important;
            color: #fff !important;
        }

        /* Location */
        .mobile-location {
            background: #37475a;
            padding: 10px;
            font-size: 13px;
        }

        .mobile-location span {
            color: #fff;
            font-weight: bold;
            text-decoration: underline;
        }
    }
</style>