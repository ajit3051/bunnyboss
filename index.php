<?php include_once("include/config.php"); ?>
<?php include('include/top.php');?>  
<?php include('include/hp-banner.php');?> 

<style>
.product-label.label-new {
    color: #fff;
    background-color: #37475a; }
</style>
      <!-- End .header-middle -->
      <main class="main">
         <!-- End .row -->
         <div class="page-content">
            <div class="container">
               <div class="row">
                  <div class="col-lg-12">
                     <div class="products mb-1">
                        <div class="row justify-content-center">
                           <?php
                           echo getHTMLProductList(0, 8);
                           ?>
                           
                        </div>
                        <!-- End .row -->
                     </div>
                     <!-- End .products -->
                  </div>
                  <!-- End .col-lg-9 -->
                  <!-- End .col-lg-3 -->
               </div>
               <!-- End .row -->
            </div>
            <!-- End .container -->
         </div>
         <div class="row" style="margin-left:1px; margin-right: 1px; margin-top: -30px; margin-bottom: 30px;">
            <?php include('include/hp-discountbanner.php');?>
         </div>
         
         <!-- End .page-content -->
         <div class="row">
            <div class="col-lg-3 col-sm-6">
               <div class="icon-box text-center">
                  <span class="icon-box-icon text-dark">
                  <i class="icon-truck"></i>
                  </span>
                  <div class="icon-box-content">
                     <h3 class="icon-box-title">Payment & Delivery</h3>
                     <!-- End .icon-box-title -->
                     <p>shipping for orders over</p>
                  </div>
                  <!-- End .icon-box-content -->
               </div>
               <!-- End .icon-box -->
            </div>
            <!-- End .col-lg-3 col-sm-6 -->
            <div class="col-lg-3 col-sm-6">
               <div class="icon-box text-center">
                  <span class="icon-box-icon text-dark">
                  <i class="icon-rotate-left"></i>
                  </span>
                  <div class="icon-box-content">
                     <h3 class="icon-box-title">Return & Refund</h3>
                     <!-- End .icon-box-title -->
                     <p>Free 100% money back guarantee</p>
                  </div>
                  <!-- End .icon-box-content -->
               </div>
               <!-- End .icon-box -->
            </div>
            <!-- End .col-lg-3 col-sm-6 -->
            <div class="col-lg-3 col-sm-6">
               <div class="icon-box text-center">
                  <span class="icon-box-icon text-dark">
                  <i class="icon-rotate-left"></i>
                  </span>
                  <div class="icon-box-content">
                     <h3 class="icon-box-title">Secure Payment</h3>
                     <!-- End .icon-box-title -->
                     <p>100% secure payment</p>
                  </div>
                  <!-- End .icon-box-content -->
               </div>
               <!-- End .icon-box -->
            </div>
            <!-- End .col-lg-3 col-sm-6 -->
            <div class="col-lg-3 col-sm-6">
               <div class="icon-box text-center">
                  <span class="icon-box-icon text-dark">
                  <i class="icon-headphones"></i>
                  </span>
                  <div class="icon-box-content">
                     <h3 class="icon-box-title">Quality Support</h3>
                     <!-- End .icon-box-title -->
                     <p>Alway online feedback 24/7</p>
                  </div>
                  <!-- End .icon-box-content -->
               </div>
               <!-- End .icon-box -->
            </div>
            <!-- End .col-lg-3 col-sm-6 -->
         </div>
         <!-- End .row -->
         </div><!-- End .container-fluid -->
         <div class="page-content">
            <div class="container">
               <div class="row">
                  <div class="col-lg-12">
                     <div class="products mb-1">
                        <div class="row justify-content-center">
                           <?php
                           echo getHTMLProductList(8, 8);
                           ?>
                        </div>
                        <!-- End .row -->
                     </div>
                     <!-- End .products -->
                  </div>
                  <!-- End .col-lg-9 -->
                  <!-- End .col-lg-3 -->
               </div>
               <!-- End .row -->
            </div>
            <!-- End .container -->
         </div>
         <div class="row" style="margin-left:1px; margin-right: 1px; margin-top: -30px;">
            <?php include('include/hp-discountbanner.php');?>
         </div>
         <!-- End .page-content -->



         <style>
            .product {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.35s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
            height: 100%;
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.3);
            }
            .product:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,.12);
            }
            .product-media {
            position: relative;
            overflow: hidden;
            margin-bottom: 10px;
            }
            /*.product-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            transition: transform .5s ease;
            }*/
            .product:hover .product-image {
            transform: scale(1.08);
            }
            .product-action-vertical {
            position: absolute;
            top: 15px;
            right: -50px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: .3s;
            }
            .product:hover .product-action-vertical {
            right: 15px;
            }
            .btn-product-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,.1);
            }
            .product-body {
            padding: 18px;
            }
            .product-cat a {
            color: #8b8b8b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            }
            .product-title {
            font-size: 16px;
            font-weight: 600;
            margin: 10px 0;
            line-height: 1.4;
            }
            .product-price {
            color: #111;
            font-size: 20px;
            font-weight: 700;
            }
            .product-label {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 2;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            }
            .label-new {
            background: #10b981;
            }
            .label-out {
            background: #ef4444;
            }
            .label-top {
            background: #f59e0b;
            }
            @media (max-width:768px){
            .products-grid{
            grid-template-columns: repeat(2,1fr);
            gap:15px;
            }
            .product-image{
            height:180px;
            }
            .product-title{
            font-size:14px;
            }
            .product-price{
            font-size:16px;
            }
            .btn-cart{
            padding:10px;
            font-size:13px;
            }

            .product-action-vertical{
            right:10px;
            opacity:1;
            }
            }
            /* Tablet */
@media (max-width: 768px) {
    .product-image {



        
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform .5s ease;
        


        
    }
}

/* Small Mobile */
@media (max-width: 480px) {
    .product-image {
         
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform .5s ease;
            
    }
}
         </style>
         
         <!-- End .page-content -->
      </main>
      <!-- End .main -->
      <section class="modern-video-banner" style="margin-left: 10px; margin-right: 10px;">
         <div class="video-banner-overlay"></div>
         <div class="video-content">
            <span class="video-tag">Watch Our Story</span>
            <h2><span style="color:white">Discover the New Collection</span></h2>
            <p>
               Experience premium fashion with modern designs crafted for everyday elegance.
            </p>
            <a href="https://www.youtube.com/@BunnyBS0000"
               class="video-play-btn btn-iframe">
            <span class="play-icon">
            <i class="icon-play"></i>
            </span>
            </a>
         </div>
      </section>
      <style>
         .modern-video-banner {
         position: relative;
         height: 450px;
         background: url('<?= _BASEURL ?>assets/images/demos/demo-24/video-banner/banner.jpg')
         center center/cover no-repeat;
         border-radius: 30px;
         overflow: hidden;
         margin: 30px auto;
         }
         .video-banner-overlay {
         position: absolute;
         inset: 0;
         background:
         linear-gradient(
         rgba(0,0,0,0.25),
         rgba(0,0,0,0.55)
         );
         backdrop-filter: blur(2px);
         }
         .video-content {
         position: relative;
         z-index: 2;
         height: 100%;
         display: flex;
         flex-direction: column;
         justify-content: center;
         align-items: center;
         padding: 30px;
         text-align: center;
         color: #fff;
         }
         .video-tag {
         display: inline-block;
         background: rgba(255,255,255,.15);
         backdrop-filter: blur(10px);
         border: 1px solid rgba(255,255,255,.2);
         padding: 8px 18px;
         border-radius: 50px;
         margin-bottom: 20px;
         font-size: 14px;
         letter-spacing: 1px;
         }
         .video-content h2 {
         font-size: 56px;
         font-weight: 700;
         margin-bottom: 15px;
         max-width: 700px;
         }
         .video-content p {
         max-width: 600px;
         font-size: 18px;
         opacity: .9;
         margin-bottom: 35px;
         }
         .video-play-btn {
         display: flex;
         align-items: center;
         justify-content: center;
         text-decoration: none;
         }
         .play-icon {
         width: 90px;
         height: 90px;
         border-radius: 50%;
         background: rgba(255,255,255,.15);
         backdrop-filter: blur(15px);
         border: 1px solid rgba(255,255,255,.3);
         color: #fff;
         font-size: 30px;
         display: flex;
         align-items: center;
         justify-content: center;
         position: relative;
         transition: .4s ease;
         }
         .play-icon:hover {
         transform: scale(1.1);
         background: #fff;
         color: #111;
         }
         .play-icon::before {
         content: '';
         position: absolute;
         width: 130%;
         height: 130%;
         border: 2px solid rgba(255,255,255,.4);
         border-radius: 50%;
         animation: pulse 2s infinite;
         }
         @keyframes pulse {
         0% {
         transform: scale(.8);
         opacity: 1;
         }
         100% {
         transform: scale(1.4);
         opacity: 0;
         }
         }
         @media (max-width: 768px) {
         .modern-video-banner {
         height: 350px;
         border-radius: 20px;
         }
         .video-content h2 {
         font-size: 30px;
         }
         .video-content p {
         font-size: 14px;
         margin-bottom: 25px;
         }
         .play-icon {
         width: 70px;
         height: 70px;
         font-size: 22px;
         }
         }
      </style>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
      <style>
         .productSlider{
         padding:20px 0 60px;
         }
         .product-card{
         border-radius:0px;
         overflow:hidden;
         transition:.4s;
         text-align:center;
         }
         .product-card:hover{
         transform:translateY(-8px);
         }
         .product-card img{
         width:100%;
         height:300px;
         object-fit:cover;
         transition:.5s;
         }
         .product-card:hover img{
         transform:scale(1.05);
         }
         .product-card h5{
         padding:15px 10px 5px;
         font-size:16px;
         font-weight:600;
         }
         .product-card p{
         padding-bottom:15px;
         color:#d9232d;
         font-weight:700;
         }
         /* Navigation */
         .swiper-button-next,
         .swiper-button-prev{
         width:45px;
         height:45px;
         background:#fff;
         border-radius:50%;
         box-shadow:0 5px 15px rgba(0,0,0,.15);
         color:#000;
         }
         .swiper-button-next:after,
         .swiper-button-prev:after{
         font-size:16px;
         font-weight:bold;
         }
         /* Mobile */
         @media(max-width:768px){
         .product-card img{
         height:220px;
         }
         .swiper-button-next,
         .swiper-button-prev{
         display:none;
         }
         }
         .cart-btn{
         display:block;
         text-align:center;
         background:linear-gradient(
         135deg,
         #009688,
         #009688
         );
         color:#fff;
         padding:12px;
         border-radius:0px;
         text-decoration:none;
         font-weight:600;
         transition:.4s;
         }
         .cart-btn:hover{
         color:#fff;
         transform:scale(1.05);
         }
      </style>
     
      <div class="page-content">
         <div class="container" style="margin-bottom:-90px;">
            <div class="row">
               <div class="col-lg-12">
                  <div class="products mb-1">
                     <div class="row justify-content-center">
                        <?php
                           echo getHTMLProductList(16, 100);
                           ?>
                     </div>
                     <!-- End .row -->
                  </div>
                  <!-- End .products -->
               </div>
               <!-- End .col-lg-9 -->
               <!-- End .col-lg-3 -->
            </div>
            <!-- End .row -->
         </div>
         <!-- End .container -->
      </div><br><br>
     <?php include('include/category-name.php');?> <br><br>
     
      <div class="" style="margin-top: 0px;">
         <div class="video-banner video-banner-bg bg-image text-center" style= "background-image: url(<?= _BASEURL ?>assets/images/demos/demo-16/bg-1.jpg)">
            <a href="https://youtube.com/@bunnybs0000?si=cnzEKyB0QvwhDMf7" class="btn-video btn-iframe"><i class="icon-play"></i></a>
         </div>
         <!-- End .video-banner bg-image -->
      </div>
      <div class="bg-light-2 pt-6 pb-6 testimonials">
         <div class="container">
            <h2 class="title text-center mb-2">Our Customers Say</h2>
            <!-- End .title text-center -->
            <div class="owl-carousel owl-simple owl-testimonials" data-toggle="owl" 
               data-owl-options='{
               "nav": false, 
               "dots": true,
               "margin": 20,
               "loop": false,
               "responsive": {
               "1200": {
               "nav": true
               }
               }
               }'>
               <blockquote class="testimonial testimonial-icon text-center">
                  <p class="lead">“Really great store”</p>
                  <!-- End .lead -->
                  <p>“ Donec nec justo eget felis facilisis fermentum. Aliquam porttitor mauris sit amet orci. Aenean dignissim pellentesque felis. Morbi in sem quis dui placerat ornare. Pellentesque odio nisi, euismod in, pharetra<br>a, ultricies in, diam. Sed arcu. ”</p>
                  <cite>
                  Charly Smith,
                  <span>Customer</span>
                  </cite>
               </blockquote>
               <!-- End .testimonial -->
               <blockquote class="testimonial testimonial-icon text-center">
                  <p class="lead">“Friendly Support”</p>
                  <!-- End .lead -->
                  <p>“ Impedit, ratione sequi, sunt incidunt magnam et. Delectus obcaecati optio eius error libero perferendis nesciunt atque dolores magni recusandae! Doloremque quidem error eum quis similique doloribus natus qui ut ipsum.”</p>
                  <cite>
                  Damon Stone
                  <span>Customer</span>
                  </cite>
               </blockquote>
               <!-- End .testimonial -->
               <blockquote class="testimonial testimonial-icon text-center">
                  <p class="lead">“Free Shipping”</p>
                  <!-- End .lead -->
                  <p>“ Molestias animi illo natus ut quod neque ad accusamus praesentium fuga! Dolores odio alias sapiente odit delectus quasi, explicabo a, modi voluptatibus. Perferendis perspiciatis, voluptate, distinctio earum veritatis animi tempora eget blandit nunc tortor mollis ”</p>
                  <cite>
                  John Smith
                  <span>Customer</span>
                  </cite>
               </blockquote>
               <!-- End .testimonial -->
            </div>
            <!-- End .testimonials-slider owl-carousel -->
         </div>
         <!-- End .container -->
      </div>
      <!-- End .bg-light pt-5 pb-5 -->
      <!--<section class="map-section">-->
      <!--   <iframe  class="map w-100"  src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=1%20Grafton%20Street,%20Dublin,%20Ireland+(My%20Business%20Name)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>-->
      <!--</section>-->
<?php include('include/backto-top.php');?> 
<?php include('include/bottom.php');?> 






