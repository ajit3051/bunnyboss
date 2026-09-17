<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
    .hero-slider {
        border-radius: 0px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, .12);
        border: none;
        margin-bottom: 10px;

    }

    .hero-slider .swiper-slide {
        position: relative;
    }

    .hero-slider img {
        width: 100%;
        height: 240px;
        object-fit: cover;
        display: block;
    }
    

    /* Overlay */
    .slide-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: #fff;
        z-index: 2;
        width: 100%;
    }

    .slide-content h2 {
        font-size: 40px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #fff;
    }

    .slide-content p {
        font-size: 25px;
        margin: 0;
        color: #fff;
    }

    .swiper-slide::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to top,
                rgba(0, 0, 0, .6),
                rgba(0, 0, 0, .1));
        z-index: 1;
    }

    /* Pagination */
    .swiper-pagination-bullet {
        width: 12px;
        height: 12px;
    }

    .swiper-pagination-bullet-active {
        background: #fff;
    }

    /* Mobile */
    @media(max-width:767px) {

        .hero-slider img {
            height: 335px;
        }

        .slide-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #fff;
            z-index: 2;
            width: 100%;
        }

        .slide-content h2 {
            font-size: 40px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #fff;
        }

        .slide-content p {
            font-size: 25px;
            margin: 0;
            color: #fff;
        }




    }

 /* ========================================
   SOCIAL ICONS - DESKTOP
======================================== */

.social-icons {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.social-icon {
    width: 30px;
    height: 30px;
    min-width: 30px;
    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #fff;
    font-size: 18px;
    line-height: 1;
    text-decoration: none;

    box-sizing: border-box;
    flex-shrink: 0;

    transition: all 0.3s ease;
}

.social-icon:hover {
    transform: translateY(-3px) scale(1.05);
    color: #fff;
}


/* ========================================
   INSTAGRAM
======================================== */

.instagram {
    background: linear-gradient(
        135deg,
        #7f00ff 0%,
        #e100ff 30%,
        #ff0069 55%,
        #ff3d00 75%,
        #ffc400 100%
    );
}


/* ========================================
   YOUTUBE
======================================== */

.youtube {
    background: linear-gradient(
        135deg,
        #ff0000,
        #cc0000
    );
}


/* ========================================
   FACEBOOK
======================================== */

.facebook {
    background: linear-gradient(
        135deg,
        #1877f2,
        #0d47a1
    );
}


/* ========================================
   TABLET
   768px - 1024px
======================================== */

@media (min-width: 768px) and (max-width: 1024px) {

    .social-icons {
        width: 100%;
        justify-content: center;
        gap: 8px;
    }

    .social-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;
        font-size: 17px;
    }
}


/* ========================================
   MOBILE
   481px - 767px
======================================== */

@media (max-width: 767px) {
    .social-icons {
        width: 100% !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        text-align: center;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    .social-icon {
        flex: 0 0 auto;
    }
}

/* ========================================
   SMALL MOBILE
   320px - 480px
======================================== */

@media (max-width: 480px) {

    .social-icons {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        padding: 0;
        margin: 0;
    }

    .social-icon {
        width: 28px;
        height: 28px;
        min-width: 28px;
        font-size: 15px;
    }

    .social-icon:hover {
        transform: translateY(-2px) scale(1.03);
    }
}


/* ========================================
   VERY SMALL MOBILE
   320px and below
======================================== */

@media (max-width: 360px) {

    .social-icons {
        gap: 5px;
    }

    .social-icon {
        width: 26px;
        height: 26px;
        min-width: 26px;
        font-size: 14px;
    }
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<div class="swiper hero-slider">
    <div class="swiper-wrapper">
        
        
        
        <div class="swiper-slide">
            <img src="<?= _BASEURL ?>assets/images/banners/hotbossbanner-1.jpg" alt="">
            <div class="slide-content">
                <h2 class="live-order"><span class="fire">📍</span> Address in Details</h2>
                <span style="font-size:20px;">A1-40, Chanakya Place Part-1, 25 Foota Road (C-1 Janak Puri),Opp. Mata Chanan Devi Hospital</span>
                <span class="social-icons">
                   
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE=" class="social-icon instagram">
                        <i class="fa-brands fa-instagram"></i>


                    </a>
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE="><span style="color:white; margin-left:-15px;">official_bunny_boss_</span></a>

                    <!-- YouTube -->
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS" class="social-icon youtube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS"><span style="color:white; margin-left:-15px;">@BunnyBS0000</span></a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr" class="social-icon facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                   <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr"><span style="color:white; margin-left:-15px;">Deepak Sharma</span>
                   </a>

                </span>
               <p class="banner-text">
                    <span class="phone-icon">📞</span>
                    Call Now : <a href="tel:7428068439" style="color:white;">+91 7428068439</a> | <a href="tel:7838384314" style="color:white;">+91 7838384314</a> | <a href="tel:7838384318" style="color:white;">+91 7838384318</a>
                </p>
            </div>
        </div>
        
        <div class="swiper-slide">
            <img src="<?= _BASEURL ?>assets/images/banners/bunnyboss.PNG" alt="">
           
        </div>
        
        

        <div class="swiper-slide">
            <img src="<?= _BASEURL ?>assets/images/banners/hotbossbanner-1.jpg" alt="">
            <div class="slide-content">
                <h2 class="live-order"><span class="fire">🔥</span> Order Now</h2>
                <h2>welcome in the world of Bunny Boss</h2>
                <span class="social-icons">
                   
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE=" class="social-icon instagram">
                        <i class="fa-brands fa-instagram"></i>


                    </a>
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE="><span style="color:white; margin-left:-15px;">official_bunny_boss_</span></a>

                    <!-- YouTube -->
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS" class="social-icon youtube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS"><span style="color:white; margin-left:-15px;">@BunnyBS0000</span></a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr" class="social-icon facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                   <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr"><span style="color:white; margin-left:-15px;">Deepak Sharma</span>
                   </a>

                </span>
               <p class="banner-text">
                    <span class="phone-icon">📞</span>
                    Call Now : <a href="tel:7428068439" style="color:white;">+91 7428068439</a> | <a href="tel:7838384314" style="color:white;">+91 7838384314</a> | <a href="tel:7838384318" style="color:white;">+91 7838384318</a>
                </p>
            </div>
        </div>

        <div class="swiper-slide">
            <img src="<?= _BASEURL ?>assets/images/banners/hotbossbanner-7.jpg" alt="">
            <div class="slide-content">
                <h2 class="live-order"><span class="fire">🔥</span> Order Now</h2>
                <h2>welcome in the world of Bunny Boss</h2>
                <span class="social-icons">
                   
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE=" class="social-icon instagram">
                        <i class="fa-brands fa-instagram"></i>


                    </a>
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE="><span style="color:white; margin-left:-15px;">official_bunny_boss_</span></a>

                    <!-- YouTube -->
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS" class="social-icon youtube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS"><span style="color:white; margin-left:-15px;">@BunnyBS0000</span></a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr" class="social-icon facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                   <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr"><span style="color:white; margin-left:-15px;">Deepak Sharma</span>
                   </a>
                </span>
                <p class="banner-text">
                    <span class="phone-icon">📞</span>
                   Call Now : <a href="tel:7428068439" style="color:white;">+91 7428068439</a> | <a href="tel:7838384314" style="color:white;">+91 7838384314</a> | <a href="tel:7838384318" style="color:white;">+91 7838384318</a>
                </p>
            </div>
        </div>
        <div class="swiper-slide">
            <img src="<?= _BASEURL ?>assets/images/banners/hotbossbanner-1.jpg" alt="">
            <div class="slide-content">
                <h2 class="live-order"><span class="fire">📍</span> Address in Details</h2>
                <span style="font-size:20px;">A1-40, Chanakya Place Part-1, 25 Foota Road (C-1 Janak Puri),Opp. Mata Chanan Devi Hospital</span>
                <span class="social-icons">
                   
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE=" class="social-icon instagram">
                        <i class="fa-brands fa-instagram"></i>


                    </a>
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE="><span style="color:white; margin-left:-15px;">official_bunny_boss_</span></a>

                    <!-- YouTube -->
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS" class="social-icon youtube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS"><span style="color:white; margin-left:-15px;">@BunnyBS0000</span></a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr" class="social-icon facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                   <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr"><span style="color:white; margin-left:-15px;">Deepak Sharma</span>
                   </a>

                </span>
               <p class="banner-text">
                    <span class="phone-icon">📞</span>
                    Call Now : <a href="tel:7428068439" style="color:white;">+91 7428068439</a> | <a href="tel:7838384314" style="color:white;">+91 7838384314</a> | <a href="tel:7838384318" style="color:white;">+91 7838384318</a>
                </p>
            </div>
        </div>
 <div class="swiper-slide">
            <img src="<?= _BASEURL ?>assets/images/banners/bunnyboss.PNG" alt="">
           
        </div>
        <div class="swiper-slide">
            <img src="<?= _BASEURL ?>assets/images/banners/hotbossbanner-4.jpg" alt="">
            <div class="slide-content">
                <h2 class="live-order"><span class="fire">🔥</span>Order Now</h2>
                <h2>welcome in the world of BunnyBoss</h2>
                <span class="social-icons">
                   
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE=" class="social-icon instagram">
                        <i class="fa-brands fa-instagram"></i>


                    </a>
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE="><span style="color:white; margin-left:-15px;">official_bunny_boss_</span></a>

                    <!-- YouTube -->
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS" class="social-icon youtube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS"><span style="color:white; margin-left:-15px;">@BunnyBS0000</span></a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr" class="social-icon facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                   <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr"><span style="color:white; margin-left:-15px;">Deepak Sharma</span>
                   </a>

                </span>
                <p class="banner-text">
                    <span class="phone-icon">📞</span>
                   Call Now : <a href="tel:7428068439" style="color:white;">+91 7428068439</a> | <a href="tel:7838384314" style="color:white;">+91 7838384314</a> | <a href="tel:7838384318" style="color:white;">+91 7838384318</a>
                </p>
            </div>
        </div>

        <div class="swiper-slide">
            <img src="<?= _BASEURL ?>assets/images/banners/hotbossbanner-6.jpg" alt="">
            <div class="slide-content">
                <h2 class="live-order"><span class="fire">🔥</span> Order Now</h2>
                <h2>welcome in the world of Bunny Boss</h2>
                <span class="social-icons">
                   
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE=" class="social-icon instagram">
                        <i class="fa-brands fa-instagram"></i>


                    </a>
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE="><span style="color:white; margin-left:-15px;">official_bunny_boss_</span></a>

                    <!-- YouTube -->
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS" class="social-icon youtube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS"><span style="color:white; margin-left:-15px;">@BunnyBS0000</span></a>

                     <!-- Facebook -->
                    <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr" class="social-icon facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                   <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr"><span style="color:white; margin-left:-15px;">Deepak Sharma</span>
                   </a>

                </span>
                <p class="banner-text">
                    <span class="phone-icon">📞</span>
                   Call Now : <a href="tel:7428068439" style="color:white;">+91 7428068439</a> | <a href="tel:7838384314" style="color:white;">+91 7838384314</a> | <a href="tel:7838384318" style="color:white;">+91 7838384318</a>
                </p>
            </div>
        </div>
 <div class="swiper-slide">
            <img src="<?= _BASEURL ?>assets/images/banners/bunnyboss.PNG" alt="">
           
        </div>
        <div class="swiper-slide">
            <img src="<?= _BASEURL ?>assets/images/banners/hotbossbanner-5.jpg" alt="">
            <div class="slide-content">
                <h2 class="live-order"><span class="fire">🔥</span>Order Now</h2>
                <h2>welcome in the world of BunnyBoss</h2>
                <span class="social-icons">
                   
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE=" class="social-icon instagram">
                        <i class="fa-brands fa-instagram"></i>


                    </a>
                    <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE="><span style="color:white; margin-left:-15px;">official_bunny_boss_</span></a>

                    <!-- YouTube -->
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS" class="social-icon youtube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="https://www.youtube.com/results?search_query=BUNNYBOSS"><span style="color:white; margin-left:-15px;">@BunnyBS0000</span></a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr" class="social-icon facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                   <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr"><span style="color:white; margin-left:-15px;">Deepak Sharma</span>
                   </a>
                </span>
                <p class="banner-text">
                    <span class="phone-icon">📞</span>
                    Call Now : <a href="tel:7428068439" style="color:white;">+91 7428068439</a> | <a href="tel:7838384314" style="color:white;">+91 7838384314</a> | <a href="tel:7838384318" style="color:white;">+91 7838384318</a>
                </p>
            </div>
        </div>

    </div>

    <div class="swiper-pagination"></div>
</div>




<style>
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








<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    new Swiper(".hero-slider", {
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        speed: 1000,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        }
    });
</script>