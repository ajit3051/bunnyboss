                     <style>
                         .modern-banner {
                             position: relative;
                             border-radius: 28px;
                             overflow: hidden;
                             min-height: 200px;
                             height: 200px;
                             background: url('assets/images/banners/hotbossbanner-4.jpg') center/cover no-repeat;
                             display: flex;
                             align-items: center;
                             justify-content: center;
                             padding: 0px 30px;
                             box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);
                             transition: 0.4s ease;
                         }

                         .modern-banner::before {
                             content: "";
                             position: absolute;
                             inset: 0;
                             background: linear-gradient(135deg,
                                     rgba(0, 0, 0, .70),
                                     rgba(13, 110, 253, .35));
                         }

                         .modern-banner:hover {
                             transform: translateY(-5px);
                         }

                         .banner-content {
                             position: relative;
                             z-index: 2;
                             text-align: center;
                             max-width: 700px;
                             margin-top: 40px;
                         }
                         

                         .discount-badge {
                             display: inline-block;
                             background: rgba(255, 255, 255, .15);
                             backdrop-filter: blur(10px);
                             color: #fff;
                             padding: 0px 0px;
                             border-radius: 30px;
                             font-size: 14px;
                             font-weight: 400;
                             margin-bottom: 0px;
                         }

                         .banner-title {
                             font-size: 30px;
                             font-weight: 800;
                             color: #fff;
                             line-height: 1.2;
                             margin-bottom: 0px;
                             
                         }

                         .banner-text {
                             font-size: 22px;
                             color: #fff;
                             margin-bottom: 20px;
                         }

                         .banner-text span {
                             color: #ffd43b;
                             font-weight: 700;
                         }

                         .banner-btn {
                             display: inline-block;
                             padding: 14px 35px;
                             background: #fff;
                             color: #009688;
                             border-radius: 50px;
                             font-weight: 700;
                             text-decoration: none;
                             transition: 0.3s;
                             margin-bottom: 30px;
                         }

                         .banner-btn:hover {
                             background: #2A3F54;
                             color: #fff;
                         }

                         /* MOBILE */
                         @media (max-width: 768px) {
    .banner-content {
        margin-top: 80px;
    }
}
                         @media (max-width:767px) {
                             .modern-banner {
                                 min-height: 150px;
                                 height: 150px;
                                 padding: 20px 15px;
                                 border-radius: 16px;
                             }

                             .banner-title {
                                 font-size: 18px;
                                 line-height: 1.2;
                                 margin-bottom: 8px;
                             }

                             .banner-text {
                                 font-size: 15px;
                                 margin-bottom: 15px;
                             }

                             .discount-badge {
                                 font-size: 10px;
                                 padding: 5px 12px;
                                 margin-bottom: 10px;
                             }

                             .banner-btn {
                                 padding: 8px 18px;
                                 font-size: 12px;

                             }
                         }



.social-icons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    width: 100%;
    margin: 0 auto;
    padding: 0;
}

.social-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: #fff !important;
    text-decoration: none;
    font-size: 17px;
    flex-shrink: 0;
    transition: 0.3s ease;
}

.social-icon:hover {
    transform: translateY(-3px);
    color: #fff !important;
}

.instagram {
    background: linear-gradient(
        135deg,
        #833ab4,
        #fd1d1d,
        #fcb045
    );
}

.facebook {
    background: #1877f2;
}

.youtube {
    background: #ff0000;
}


/* MOBILE */
@media (max-width: 767px) {

    .social-icons {
        width: 100% !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 7px;
        margin: 0 auto !important;
    }

    .social-icon {
        width: 30px;
        height: 30px;
        min-width: 30px;
        font-size: 16px;
    }
}


/* SMALL MOBILE */
@media (max-width: 480px) {

    .social-icons {
        gap: 6px;
    }

    .social-icon {
        width: 28px;
        height: 28px;
        min-width: 28px;
        font-size: 15px;
    }
}                         
                     </style>
                     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
                     <div class="container">

                         <div class="modern-banner">

                             <div class="banner-content" style="margin-right: 100px;">



                                 <h2 class="banner-title">
                                     <span class="fire">🔥</span>Order Now
                                 </h2>
                                <div class="social-icons">
    
     <a href="https://www.instagram.com/official_bunny__boss____?igsi=bnhxZDFldXIzcmE=" class="social-icon instagram">
                        <i class="fa-brands fa-instagram"></i>


                    </a>
                     

     <a href="https://www.facebook.com/share/1BmzbUWqNL/?mibextid=wwXIfr" class="social-icon facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                   

     <a href="https://www.youtube.com/results?search_query=BUNNYBOSS" class="social-icon youtube">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                     
</div>

                                 <p class="banner-text">
                                     <span class="phone-icon">📞</span>
                                      Call Now : <a href="tel:7428068439" style="color:white;">+91 7428068439</a> | <a href="tel:7838384314" style="color:white;">+91 7838384314</a> | <a href="tel:7838384318" style="color:white;">+91 7838384318</a>
                                 </p>

                                 <a href="<?= _BASEURL ?>product-list.php" style="text-decoration:none" class="banner-btn">
                                     Shop Now
                                 </a>

                             </div>

                         </div>
                     </div>