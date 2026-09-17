 <section class="category-section">
     <div class="container" style="margin-bottom:0px; margin-top:0px;">
         <div class="category-grid">

             <?php
                $group_home = getGroupList();

                $imageUrl     = _IMAGE_PATH;

                while ($group = $group_home->fetch_assoc()) {
                    $picture       = htmlspecialchars($group['picture']);
                ?>
                 <a href="<?= _BASEURL . "product-list.php?category=" . urlencode($group['group_name']) ?>" class="category-card">
                     <img src="<?= $imageUrl ?>group-master/<?= $picture ?>" alt="">
                     <span><?= htmlspecialchars($group['group_name']) ?></span>
                 </a>
             <?php
                }

                ?>
         </div>
     </div>
 </section>
 <style>
     .category-section {
         padding: 60px 0;
         background: #f8f9fb;
     }

     .section-title {
         text-align: center;
         margin-bottom: 35px;
     }

     .section-title h2 {
         font-size: 32px;
         font-weight: 700;
         color: #222;
     }

     .category-grid {
         display: grid;
         grid-template-columns: repeat(6, 1fr);
         gap: 20px;
     }

     .category-card {
         background: #37475a;
         border-radius: 20px;
         overflow: hidden;
         text-align: center;
         text-decoration: none;
         box-shadow: 0 5px 20px rgba(0, 0, 0, .06);
         transition: .4s ease;
     }

     .category-card:hover {
         transform: translateY(-8px);
         box-shadow: 0 15px 35px rgba(0, 0, 0, .12);
         background: #fff;
         color: #000000;
     }

     .category-card:hover span {
         display: block;
         padding: 15px;
         font-size: 16px;
         font-weight: 600;
         color: #000000;
     }

     .category-card img {
         width: 100%;
         height: 180px;
         object-fit: cover;
         transition: .5s;
     }

     .category-card:hover img {
         transform: scale(1.08);
     }

     .category-card span {
         display: block;
         padding: 15px;
         font-size: 16px;
         font-weight: 600;
         color: #fff;
     }

     /* Tablet */
     @media (max-width: 992px) {
         .category-grid {
             grid-template-columns: repeat(3, 1fr);
             gap: 15px;
         }

         .category-card img {
             height: 150px;
         }
     }

     /* Mobile */
     @media (max-width: 768px) {
         .category-section {
             padding: 30px 0;
         }

         .category-grid {
             grid-template-columns: repeat(2, 1fr);
             gap: 12px;
         }

         .category-card {
             border-radius: 12px;
         }

         .category-card img {
             height: 120px;
         }

         .category-card span {
             font-size: 14px;
             padding: 10px;
         }
     }

     /* Small Mobile */
     @media (max-width: 480px) {
         .category-grid {
             grid-template-columns: repeat(2, 1fr);
             gap: 10px;
         }

         .category-card img {
             height: 100px;
         }

         .category-card span {
             font-size: 13px;
             font-weight: 600;
         }
     }
 </style>