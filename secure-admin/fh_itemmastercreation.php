<?php include('top.php'); ?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$msg = "";

if (isset($_POST['submit'])) {

   // TEXT DATA
   $item_code      = mysqli_real_escape_string($conn, $_POST['item_code']);
   $barcode_no     = mysqli_real_escape_string($conn, $_POST['barcode_no']);
   $sku_no         = mysqli_real_escape_string($conn, $_POST['sku_no']);
   $item_name      = mysqli_real_escape_string($conn, $_POST['item_name']);
   $description    = mysqli_real_escape_string($conn, $_POST['description']);
   $group_name     = mysqli_real_escape_string($conn, $_POST['group_name']);
   $subgroup_name  = mysqli_real_escape_string($conn, $_POST['subgroup_name']);
   $brand_name     = mysqli_real_escape_string($conn, $_POST['brand_name']);
   $mou_name       = mysqli_real_escape_string($conn, $_POST['mou_name']);
   $rac_no         = mysqli_real_escape_string($conn, $_POST['rac_no']);
   $purchase_price = mysqli_real_escape_string($conn, $_POST['purchase_price']);
   $dis_per        = mysqli_real_escape_string($conn, $_POST['dis_per']);
   $dis_amt        = mysqli_real_escape_string($conn, $_POST['dis_amt']);
   $mrp            = mysqli_real_escape_string($conn, $_POST['mrp']);
   $status         = mysqli_real_escape_string($conn, $_POST['status']);
   $is_new         = mysqli_real_escape_string($conn, $_POST['is_new'] ?? 'true');
   $date           = mysqli_real_escape_string($conn, $_POST['date']);
   $about_1        = mysqli_real_escape_string($conn, $_POST['about_1']);
   $about_2        = mysqli_real_escape_string($conn, $_POST['about_2']);
   $about_3        = mysqli_real_escape_string($conn, $_POST['about_3']);
   $about_4        = mysqli_real_escape_string($conn, $_POST['about_4']);



   // DUPLICATE CHECK
   $check = mysqli_query($conn, "SELECT id FROM tbl_item_master WHERE sku_no='$sku_no'");
   if (mysqli_num_rows($check) > 0) {
      $msg = "<div class='alert alert-danger'>Item Name Already Exists!</div>";
   } else {

      // FILE UPLOAD PATH
      $path = "uploads/item-master/";
      if (!is_dir($path)) {
         mkdir($path, 0777, true);
      }

      if (!function_exists('createResizedImageCopy')) {
          function createResizedImageCopy($srcPath, $destPath, $maxWidth, $maxHeight) {
              if (!file_exists($srcPath)) {
                  return;
              }
              $info = @getimagesize($srcPath);
              if (!$info) {
                  @copy($srcPath, $destPath);
                  return;
              }

              list($origWidth, $origHeight, $type) = $info;
              if ($origWidth <= 0 || $origHeight <= 0) {
                  @copy($srcPath, $destPath);
                  return;
              }

              $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
              if ($ratio >= 1) {
                  @copy($srcPath, $destPath);
                  return;
              }

              $newWidth  = (int)round($origWidth * $ratio);
              $newHeight = (int)round($origHeight * $ratio);

              $srcImg = null;
              switch ($type) {
                  case IMAGETYPE_JPEG:
                      $srcImg = @imagecreatefromjpeg($srcPath);
                      break;
                  case IMAGETYPE_PNG:
                      $srcImg = @imagecreatefrompng($srcPath);
                      break;
                  case IMAGETYPE_WEBP:
                      if (function_exists('imagecreatefromwebp')) {
                          $srcImg = @imagecreatefromwebp($srcPath);
                      }
                      break;
              }

              if (!$srcImg) {
                  @copy($srcPath, $destPath);
                  return;
              }

              $dstImg = imagecreatetruecolor($newWidth, $newHeight);
              if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
                  imagealphablending($dstImg, false);
                  imagesavealpha($dstImg, true);
              }

              imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

              $saved = false;
              switch ($type) {
                  case IMAGETYPE_PNG:
                      $saved = @imagepng($dstImg, $destPath, 8);
                      break;
                  case IMAGETYPE_WEBP:
                      if (function_exists('imagewebp')) {
                          $saved = @imagewebp($dstImg, $destPath, 85);
                      }
                      break;
                  default:
                      $saved = @imagejpeg($dstImg, $destPath, 85);
                      break;
              }

              imagedestroy($dstImg);
              imagedestroy($srcImg);

              if (!$saved) {
                  @copy($srcPath, $destPath);
              }
          }
      }

      if (!function_exists('createResizedCopies')) {
          function createResizedCopies($srcPath, $filename, $basePath) {
              $zoomPath        = rtrim($basePath, '/') . '/zoom/' . $filename;
              $productListPath = rtrim($basePath, '/') . '/product_list/' . $filename;
              $thumbPath       = rtrim($basePath, '/') . '/thumbnail/' . $filename;

              createResizedImageCopy($srcPath, $zoomPath, 1200, 1200);
              createResizedImageCopy($srcPath, $productListPath, 600, 600);
              createResizedImageCopy($srcPath, $thumbPath, 200, 200);
          }
      }

      // FILE UPLOAD FUNCTION
      if (!function_exists('uploadFile')) {
          function uploadFile($file, $path) {
              if (isset($file) && !empty($file['name']) && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
                  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                  $allowed = ['jpg','jpeg','png','webp'];
                  if (!in_array($ext, $allowed)) {
                      return "";
                  }

                  foreach (['thumbnail', 'product_list', 'zoom'] as $sub_dir) {
                      $dir_path = rtrim($path, '/') . '/' . $sub_dir . '/';
                      if (!is_dir($dir_path)) {
                          @mkdir($dir_path, 0777, true);
                      }
                  }

                  $filename = time() . "_" . rand(1000, 9999) . "_" . basename($file['name']);
                  $destFile = rtrim($path, '/') . '/' . $filename;

                  if (move_uploaded_file($file['tmp_name'], $destFile)) {
                      createResizedCopies($destFile, $filename, $path);
                      return $filename;
                  }
              }
              return "";
          }
      }

      // Collect uploaded files from dynamic images[] array or individual file inputs
      $uploaded_pictures = [];

      if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
         foreach ($_FILES['images']['name'] as $i => $name) {
            if (!empty($name) && isset($_FILES['images']['error'][$i]) && $_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
               $single_file = [
                  'name'     => $_FILES['images']['name'][$i],
                  'type'     => $_FILES['images']['type'][$i],
                  'tmp_name' => $_FILES['images']['tmp_name'][$i],
                  'error'    => $_FILES['images']['error'][$i],
                  'size'     => $_FILES['images']['size'][$i]
               ];
               $saved = uploadFile($single_file, $path);
               if (!empty($saved)) {
                  $uploaded_pictures[] = $saved;
               }
            }
         }
      }

      for ($k = 0; $k < 10; $k++) {
         $key = ($k === 0) ? 'picture' : 'picture' . $k;
         if (isset($_FILES[$key]) && !empty($_FILES[$key]['name'])) {
            $saved = uploadFile($_FILES[$key], $path);
            if (!empty($saved)) {
               $uploaded_pictures[] = $saved;
            }
         }
      }

      // INSERT QUERY (MATCHING TABLE EXACTLY)
      $sql = "INSERT INTO tbl_item_master (
                        item_code, barcode_no, sku_no, item_name, description,
                        group_name, subgroup_name, brand_name, mou_name,
                        rac_no, purchase_price, dis_per, dis_amt, mrp,
                        status, is_new, date, about_1, about_2, about_3, about_4
                    ) VALUES (
                        '$item_code', '$barcode_no', '$sku_no', '$item_name', '$description',
                        '$group_name', '$subgroup_name',
                        '$brand_name', '$mou_name',
                        '$rac_no', '$purchase_price', '$dis_per','$dis_amt','$mrp',
                        '$status', '$is_new', '$date', '$about_1', '$about_2',
                        '$about_3', '$about_4'
                    )";

      $run = mysqli_query($conn, $sql);   // EXECUTE QUERY

      if ($run) {
         $item_id = mysqli_insert_id($conn);
         foreach ($uploaded_pictures as $sort_idx => $img_file) {
            $img_file_esc = mysqli_real_escape_string($conn, $img_file);
            mysqli_query($conn, "INSERT INTO tbl_item_images (item_id, image_path, sort_order) VALUES ('$item_id', '$img_file_esc', '$sort_idx')");
         }
         if (!empty($_POST['variant_qty']) && is_array($_POST['variant_qty'])) {
            foreach ($_POST['variant_qty'] as $index => $qty) {
               $v_color = isset($_POST['variant_color'][$index]) ? mysqli_real_escape_string($conn, $_POST['variant_color'][$index]) : '';
               $v_size  = isset($_POST['variant_size'][$index]) ? mysqli_real_escape_string($conn, $_POST['variant_size'][$index]) : '';
               $v_style = isset($_POST['variant_style'][$index]) ? mysqli_real_escape_string($conn, $_POST['variant_style'][$index]) : '';
               $v_qty   = (int)$qty;
               $v_price = isset($_POST['variant_price'][$index]) ? (float)$_POST['variant_price'][$index] : 0.00;

               if (!empty($v_color) || !empty($v_size) || !empty($v_style) || $v_qty > 0) {
                  $v_sql = "INSERT INTO tbl_item_variants (item_id, color_name, size_name, style_name, quantity, price) 
                                  VALUES ('$item_id', '$v_color', '$v_size', '$v_style', '$v_qty', '$v_price')";
                  mysqli_query($conn, $v_sql);
               }
            }
         }
         echo "
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                showPopup(
                    'Success!',
                    'Item Master Saved Successfully 🎉',
                    
                );
            });
            </script>
            ";
      }
   }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Item Master Creations || TEJASERP</title>
   <?php include('include/css.php'); ?>
   <!-- End Theme Layout Style
         =====================================================================-->
</head>

<body class="hold-transition sidebar-mini">
   <!--preloader-->
   <!-- <div id="preloader">
         <div id="status"></div>
         </div> -->
   <!-- Site wrapper -->
   <div class="wrapper">

      <!-- =============================================== -->
      <!-- Left side column. contains the sidebar -->
      <aside class="main-sidebar">
         <!-- sidebar -->
         <?php include('include/sidebar-left.php'); ?>
         <?php include('include/toggle_switch_list.php'); ?>
         <!-- /.sidebar -->
      </aside>
      <!-- =============================================== -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
         <!-- Content Header (Page header) -->

         <div class="container-fluid">
            <div class="row">
               <?php include('include/menu-header.php'); ?>
            </div>
         </div>
         <!-- Main content -->
         <?php include('include/fh-popup-message-successfully.php'); ?>
         <?php include('include/fh-form-scrolling-data-list.php'); ?>
         <section class="">
            <div class="row">
               <!-- Form controls -->
               <div class="col-sm-12">
                  <div class="panel panel-bd lobidisable">
                     <div class="panel-heading" data-toggle="offcanvas">
                        <div class="btn-group" id="buttonexport">
                           <span style="font-size: 15px;">
                              <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                 <defs>
                                    <clipPath id="lineMdWatchTwotoneLoop0">
                                       <rect width="24" height="12" />
                                    </clipPath>
                                    <symbol id="lineMdWatchTwotoneLoop1">
                                       <path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)">
                                          <animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z" />
                                          <animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3" />
                                       </path>
                                    </symbol>
                                    <mask id="lineMdWatchTwotoneLoop2">
                                       <use href="#lineMdWatchTwotoneLoop1" />
                                       <use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)" />
                                       <circle cx="12" cy="12" r="0" fill="#fff">
                                          <animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0" />
                                       </circle>
                                    </mask>
                                 </defs>
                                 <rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)" />
                              </svg>
                              <span>Item Master Creations !</span>
                           </span>
                        </div>
                     </div>
                     <div class="panel-body form-scroll">
                        <div class="btn-group">
                           <a href="fh_itemmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Item Master List</button></a>
                        </div>
                        <form method="post" enctype="multipart/form-data">
                           <?php echo $msg; ?>
                           <div class="row">
                              <div class="col-sm-3">
                                 <div class="form-group">
                                    <label>Date</label><label style="color:red;">*</label>

                                    <input id='minMaxExample' type="text" name="date" id="date" class="form-control" required>
                                 </div>
                              </div>
                              <div class="col-sm-3">
                                 <div class="form-group">
                                    <label>ItemCode</label><label style="color:red;">*</label>
                                    <input type="text" name="item_code" class="form-control" id="item_code" readonly value="<?php $no = mysqli_query($conn, "SELECT id FROM  tbl_item_master ORDER BY id DESC");
                                                                                                                              $po_no = ($no) ? mysqli_fetch_assoc($no) : null;
                                                                                                                              $next_id = ($po_no && isset($po_no['id'])) ? ($po_no['id'] + 1) : 1;
                                                                                                                              echo 'IC-000' . $next_id;
                                                                                                                              ?>">
                                 </div>
                              </div>
                              <div class="col-sm-3">
                                 <div class="form-group">
                                    <label>Barcode No.</label><label style="color:red;">*</label>
                                    <input type="text" class="form-control" name="barcode_no" id="barcode_no" placeholder="Enter Your Barcode No" required>
                                 </div>
                              </div>
                              <div class="col-sm-3">
                                 <div class="form-group">
                                    <label>Article No. || SKU No.</label><label style="color:red;">*</label>
                                    <input type="text" class="form-control" name="sku_no" id="sku_no" placeholder="Enter Your Article || SKU No" required>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-sm-6">
                                 <div class="form-group">
                                    <label>Item Name</label><label style="color:red;">*</label>
                                    <input type="text" class="form-control" name="item_name" id="item_name" placeholder="Enter Your Item Name" required>
                                 </div>
                              </div>
                              <div class="col-sm-6">
                                 <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" class="form-control" name="description" id="description" placeholder="Enter Your Description">
                                 </div>
                              </div>
                           </div>

                           <div class="row">
                              <div class="col-lg-4">
                                 <div class="form-group">
                                    <label>Group || Category Name</label><label style="color:red;">*</label>
                                    <select name="group_name" class="form-control" required>
                                       <option value="null">Select Group || Category Nmae </option>
                                       <?php
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_group_master");
                                       while ($row = mysqli_fetch_array($query)) {
                                       ?>
                                          <option><?php echo $row['group_name']; ?></option>
                                       <?php } ?>
                                    </select>
                                 </div>
                                 <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Group Master</span>
                                 </a>
                              </div>
                              <div class="col-lg-4">
                                 <div class="form-group">
                                    <label>SubGroup || SubCategory Name</label><label style="color:red;">*</label>
                                    <select name="subgroup_name" class="form-control" required>
                                       <option value="null">Select SubGroup || SubCategory Name</option>
                                       <?php
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_subgroup_master");
                                       while ($row = mysqli_fetch_array($query)) {
                                       ?>
                                          <option><?php echo $row['subgroup_name']; ?></option>
                                       <?php } ?>
                                    </select>
                                 </div>
                                 <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New SubGroup Master</span>
                                 </a>
                              </div>
                              <div class="col-lg-4">
                                 <div class="form-group">
                                    <label>Brand Name</label><label style="color:red;">*</label>
                                    <select name="brand_name" class="form-control" required>
                                       <option value="null">Select Brand Name</option>
                                       <?php
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_brand_master");
                                       while ($row = mysqli_fetch_array($query)) {
                                       ?>
                                          <option><?php echo $row['brand_name']; ?></option>
                                       <?php } ?>
                                    </select>
                                 </div>
                                 <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Brand Master</span>
                                 </a>
                              </div>
                           </div>
                           <div class="row" style="margin-top: 15px;">
                              <div class="col-lg-6">
                                 <div class="form-group">
                                    <label>Measurement of Units</label><label style="color:red;">*</label>
                                    <select name="mou_name" class="form-control" required>
                                       <option value="null">Select Measurement of Units</option>
                                       <?php
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_mou_master");
                                       while ($row = mysqli_fetch_array($query)) {
                                       ?>
                                          <option><?php echo $row['mou_name']; ?></option>
                                       <?php } ?>
                                    </select>
                                 </div>
                                 <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New MOU Master</span>
                                 </a>
                              </div>
                              <div class="col-lg-6">
                                 <div class="form-group">
                                    <label>Rac No.</label>
                                    <input type="text" class="form-control" name="rac_no" id="rac_no" placeholder="Enter Your Rac No">
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-lg-3">
                                 <div class="form-group">
                                    <label>Purchase Price (CP)</label><label style="color:red;">*</label>
                                    <input type="text" class="form-control" name="purchase_price" id="purchase_price" placeholder="Enter Your Purchase Price" required>
                                 </div>
                              </div>
                              <div class="col-lg-3">
                                 <div class="form-group">
                                    <label>MRP</label><label style="color:red;">*</label>
                                    <input type="text" class="form-control" name="mrp" id="mrp" placeholder="Enter Your MRP" required>
                                 </div>
                              </div>
                              <div class="col-lg-3">
                                 <div class="form-group">
                                    <label>Discount (%)</label><label style="color:red;">*</label>
                                    <input type="text" class="form-control" name="dis_per" id="dis_per" placeholder="Enter Your Discount (%)" required>
                                 </div>
                              </div>
                              <div class="col-lg-3">
                                 <div class="form-group">
                                    <label>Discount Amt.</label><label style="color:red;">*</label>
                                    <input type="text" class="form-control" name="dis_amt" id="dis_amt" placeholder="Enter Your Discount Amt." required>
                                 </div>
                              </div>

                           </div>
                           <br>
                           <!-- PRODUCT VARIANTS SECTION -->
                           <div class="panel panel-bd lobidisable" style="border: 1px solid #009688;">
                              <div class="panel-heading" style="background-color: #009688; color: white; font-weight: bold; font-size: 15px;">
                                 <i class="fa fa-cubes" style="margin-right: 8px;"></i> Product Variants & Quantity Management (Size, Color, Style)
                              </div>
                              <div class="panel-body">
                                 <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="variantTable">
                                       <thead style="background-color: #f5f5f5;">
                                          <tr>
                                             <th width="22%">Color Name</th>
                                             <th width="22%">Size Name</th>
                                             <th width="22%">Style / Design Name</th>
                                             <th width="15%">Quantity</th>
                                             <th width="12%">Price (SP)</th>
                                             <th width="7%" class="text-center">Action</th>
                                          </tr>
                                       </thead>
                                       <tbody id="variantTableBody">
                                          <tr class="variant-row">
                                             <td>
                                                <select name="variant_color[]" class="form-control">
                                                   <option value="">Select Color</option>
                                                   <?php
                                                   $c_query = mysqli_query($conn, "SELECT * FROM tbl_color_master");
                                                   while ($c_row = mysqli_fetch_array($c_query)) {
                                                      echo "<option value='" . htmlspecialchars($c_row['color_name']) . "'>" . htmlspecialchars($c_row['color_name']) . "</option>";
                                                   }
                                                   ?>
                                                </select>
                                             </td>
                                             <td>
                                                <select name="variant_size[]" class="form-control">
                                                   <option value="">Select Size</option>
                                                   <?php
                                                   $s_query = mysqli_query($conn, "SELECT * FROM tbl_size_master");
                                                   while ($s_row = mysqli_fetch_array($s_query)) {
                                                      echo "<option value='" . htmlspecialchars($s_row['size_name']) . "'>" . htmlspecialchars($s_row['size_name']) . "</option>";
                                                   }
                                                   ?>
                                                </select>
                                             </td>
                                             <td>
                                                <select name="variant_style[]" class="form-control">
                                                   <option value="">Select Style</option>
                                                   <?php
                                                   $st_query = mysqli_query($conn, "SELECT * FROM tbl_styledesign_master");
                                                   while ($st_row = mysqli_fetch_array($st_query)) {
                                                      echo "<option value='" . htmlspecialchars($st_row['style_name']) . "'>" . htmlspecialchars($st_row['style_name']) . "</option>";
                                                   }
                                                   ?>
                                                </select>
                                             </td>
                                             <td>
                                                <input type="number" name="variant_qty[]" class="form-control" placeholder="Qty" min="0" value="0">
                                             </td>
                                             <td>
                                                <input type="number" step="0.01" name="variant_price[]" class="form-control" placeholder="Price" min="0" value="0.00">
                                             </td>
                                             <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-variant-row"><i class="fa fa-trash"></i></button>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </div>
                                 <button type="button" class="btn btn-info btn-sm" id="addVariantRow"><i class="fa fa-plus" style="margin-right:5px;"></i> Add Variant Row</button>
                              </div>
                           </div>
                           <script>
                              document.addEventListener('DOMContentLoaded', function() {
                                 const addBtn = document.getElementById('addVariantRow');
                                 const tableBody = document.getElementById('variantTableBody');

                                 if (addBtn && tableBody) {
                                    addBtn.addEventListener('click', function() {
                                       const firstRow = tableBody.querySelector('.variant-row');
                                       if (firstRow) {
                                          const newRow = firstRow.cloneNode(true);
                                          newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
                                          newRow.querySelectorAll('input').forEach(input => {
                                             if (input.type === 'number') {
                                                input.value = input.name.includes('qty') ? '0' : '0.00';
                                             } else {
                                                input.value = '';
                                             }
                                          });
                                          tableBody.appendChild(newRow);
                                       }
                                    });

                                    tableBody.addEventListener('click', function(e) {
                                       const removeBtn = e.target.closest('.remove-variant-row');
                                       if (removeBtn) {
                                          const rows = tableBody.querySelectorAll('.variant-row');
                                          if (rows.length > 1) {
                                             const row = removeBtn.closest('.variant-row');
                                             if (row) row.remove();
                                          } else {
                                             alert('At least one variant row must remain.');
                                          }
                                       }
                                    });
                                 }
                              });
                           </script>
                           <br>
                           <label>About this Item :</label>
                           <div class="row">

                              <div class="col-lg-6">
                                 <div class="form-group">
                                    <label>1st</label>
                                    <input type="text" class="form-control" name="about_1" id="about_1" placeholder="Enter Your About this Item">
                                 </div>
                              </div>
                              <div class="col-lg-6">
                                 <div class="form-group">
                                    <label>2nd</label>
                                    <input type="text" class="form-control" name="about_2" id="about_2" placeholder="Enter Your About this Item">
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-lg-6">
                                 <div class="form-group">
                                    <label>3rd</label>
                                    <input type="text" class="form-control" name="about_3" id="about_3" placeholder="Enter Your About this Item">
                                 </div>
                              </div>
                              <div class="col-lg-6">
                                 <div class="form-group">
                                    <label>4th</label>
                                    <input type="text" class="form-control" name="about_4" id="about_4" placeholder="Enter Your About this Item">
                                 </div>
                              </div>
                           </div>
                           <br>
                           <!-- DYNAMIC IMAGE UPLOADS PANEL -->
                           <div class="panel panel-bd lobidisable" style="border: 1px solid #009688; margin-top: 15px;">
                              <div class="panel-heading" style="background-color: #009688; color: white; font-weight: bold; font-size: 15px;">
                                 <i class="fa fa-picture-o" style="margin-right: 8px;"></i> Product Image Uploads (Max <span id="maxImgCountLabel">10</span> Images)
                              </div>
                              <div class="panel-body">
                                 <div id="imageUploadContainer" class="row">
                                    <div class="col-sm-4 image-upload-item" style="margin-bottom: 15px;">
                                       <div class="well well-sm" style="background-color: #f9f9f9; border: 1px solid #e3e3e3; position: relative;">
                                          <label class="image-title" style="font-weight: 600;">Main / Front Picture (Image 1)</label>
                                          <input type="file" name="images[]" class="form-control-file image-input-field" accept="image/*" onchange="previewImageThumbnail(this)">
                                          <div class="image-preview-container" style="margin-top: 8px; display: none;">
                                             <img class="img-thumbnail" style="max-height: 80px; max-width: 100px; object-fit: contain;">
                                          </div>
                                          <div class="remove-btn-wrapper" style="margin-top: 8px; display: none;">
                                             <button type="button" class="btn btn-danger btn-xs remove-image-row"><i class="fa fa-trash"></i> Remove Image</button>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div style="margin-top: 5px;">
                                    <button type="button" class="btn btn-info btn-sm" id="addImageRowBtn"><i class="fa fa-plus" style="margin-right: 5px;"></i> Add More Image</button>
                                    <span id="imgMaxLimitMsg" class="text-danger" style="display: none; margin-left: 10px; font-weight: bold;"><i class="fa fa-warning"></i> Maximum limit of 10 images reached!</span>
                                 </div>
                              </div>
                           </div>
                           <script>
                              const MAX_IMAGE_UPLOADS = 10; // Easily configurable max upload limit

                              function previewImageThumbnail(input) {
                                 const container = input.closest('.well');
                                 const previewBox = container.querySelector('.image-preview-container');
                                 const img = previewBox.querySelector('img');

                                 if (input.files && input.files[0]) {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                       img.src = e.target.result;
                                       previewBox.style.display = 'block';
                                    }
                                    reader.readAsDataURL(input.files[0]);
                                 } else {
                                    previewBox.style.display = 'none';
                                    img.src = '';
                                 }
                              }

                              document.addEventListener('DOMContentLoaded', function() {
                                 const container = document.getElementById('imageUploadContainer');
                                 const addBtn = document.getElementById('addImageRowBtn');
                                 const maxMsg = document.getElementById('imgMaxLimitMsg');
                                 const countLabel = document.getElementById('maxImgCountLabel');

                                 if (countLabel) countLabel.textContent = MAX_IMAGE_UPLOADS;

                                 function updateImageLabels() {
                                    const items = container.querySelectorAll('.image-upload-item');
                                    items.forEach((item, idx) => {
                                       const title = item.querySelector('.image-title');
                                       const removeBtnWrapper = item.querySelector('.remove-btn-wrapper');
                                       const labelText = (idx === 0) ? 'Main / Front Picture (Image 1)' : `Picture ${idx + 1}`;
                                       if (title) title.textContent = labelText;

                                       // Show remove button for all except the first image if only 1 left
                                       if (removeBtnWrapper) {
                                          removeBtnWrapper.style.display = (items.length > 1 && idx > 0) ? 'block' : 'none';
                                       }
                                    });

                                    if (items.length >= MAX_IMAGE_UPLOADS) {
                                       addBtn.style.display = 'none';
                                       maxMsg.style.display = 'inline-block';
                                    } else {
                                       addBtn.style.display = 'inline-block';
                                       maxMsg.style.display = 'none';
                                    }
                                 }

                                 if (addBtn && container) {
                                    addBtn.addEventListener('click', function() {
                                       const currentCount = container.querySelectorAll('.image-upload-item').length;
                                       if (currentCount < MAX_IMAGE_UPLOADS) {
                                          const nextIndex = currentCount + 1;
                                          const newItem = document.createElement('div');
                                          newItem.className = 'col-sm-4 image-upload-item';
                                          newItem.style.marginBottom = '15px';
                                          newItem.innerHTML = `
                                              <div class="well well-sm" style="background-color: #f9f9f9; border: 1px solid #e3e3e3; position: relative;">
                                                 <label class="image-title" style="font-weight: 600;">Picture ${nextIndex}</label>
                                                 <input type="file" name="images[]" class="form-control-file image-input-field" accept="image/*" onchange="previewImageThumbnail(this)">
                                                 <div class="image-preview-container" style="margin-top: 8px; display: none;">
                                                    <img class="img-thumbnail" style="max-height: 80px; max-width: 100px; object-fit: contain;">
                                                 </div>
                                                 <div class="remove-btn-wrapper" style="margin-top: 8px;">
                                                    <button type="button" class="btn btn-danger btn-xs remove-image-row"><i class="fa fa-trash"></i> Remove Image</button>
                                                 </div>
                                              </div>
                                           `;
                                          container.appendChild(newItem);
                                          updateImageLabels();
                                       }
                                    });

                                    container.addEventListener('click', function(e) {
                                       const removeBtn = e.target.closest('.remove-image-row');
                                       if (removeBtn) {
                                          const item = removeBtn.closest('.image-upload-item');
                                          if (item) {
                                             item.remove();
                                             updateImageLabels();
                                          }
                                       }
                                    });
                                 }

                                 updateImageLabels();
                              });
                           </script>
                           <br>

                           <div class="row">
                              <div class="col-sm-3">
                                 <div class="form-group">
                                    <label>Video Upload</label><br>

                                    <input type="file" name="video" id="video">
                                    <!-- <input type="hidden" name="old_picture"> -->
                                 </div>
                              </div>
                              <div class="col-sm-3">
                                 <div class="form-check">
                                    <label>Status</label><label style="color:red;">*</label><br>
                                    <label class="radio-inline">
                                       <input type="radio" name="status" value="true" checked="checked">Active</label>
                                    <label class="radio-inline"><input type="radio" name="status" value="false">Inctive</label>
                                 </div>
                              </div>
                              <div class="col-sm-3">
                                 <div class="form-check">
                                    <label>New Item / New Arrival</label><label style="color:red;">*</label><br>
                                    <label class="radio-inline">
                                       <input type="radio" name="is_new" value="true" checked="checked">Yes (New)</label>
                                    <label class="radio-inline">
                                       <input type="radio" name="is_new" value="false">No</label>
                                 </div>
                              </div>
                              <div class="col-sm-3">
                                 <br>
                                 <button type="submit" name="submit" class="btn btn-warning" style="width:100px;">Reset</button>
                                 <button type="submit" name="submit" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                              </div>

                           </div>


                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </section>
         <!-- model Modal1 -->
         <div class="modal fade" id="addtrain" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header modal-header-primary">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                     <h3><i class="fa fa-plus m-r-5"></i> Add New Group Master</h3>
                  </div>
                  <div class="modal-body">
                     <div class="row">
                        <div class="col-md-12">
                           <form method="post">
                              <?php
                              $msg = '';
                              if (isset($_POST['submit_btn'])) {
                                 $group_code = mysqli_real_escape_string($conn, $_POST['group_code']);
                                 $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
                                 $hsn_code = mysqli_real_escape_string($conn, $_POST['hsn_code']);
                                 $status = mysqli_real_escape_string($conn, $_POST['status']);

                                 $dup = mysqli_query($conn, "select * from tbl_group_master where group_name='$group_name'");
                                 if (mysqli_num_rows($dup) > 0) {
                                    $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                    <strong>Size Master Already exist!</strong>
                                                    </div>';
                                 } else {
                                    $query = "Insert into tbl_group_master(group_code,group_name,hsn_code,status)values('$group_code','$group_name','$hsn_code','$status')";
                                    if (mysqli_query($conn, $query)) {
                                       $msg = '<div class="alert alert-success alert-dismissible fade1 show">
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                    <strong>Group Creations Success..!</strong>
                                                  </div>';
                                    } else {
                                       $msg = '<div class="alert alert-danger alert-dismissible fade1 show">
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                    <strong>Group Creations Failed!</strong>
                                                    </div>';
                                    }
                                 }
                              }
                              ?>
                              <div class="row">
                                 <div class="col-sm-2">
                                    <div class="form">
                                       <label>Code</label>
                                       <input type="password" name="group_code" class="form-control" id="group_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_group_master ORDER BY id DESC");
                                                                                                                                       $po_no = ($no) ? mysqli_fetch_assoc($no) : null;
                                                                                                                                       $next_id = ($po_no && isset($po_no['id'])) ? ($po_no['id'] + 1) : 1;
                                                                                                                                       echo 'GC-000' . $next_id;
                                                                                                                                       ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-5">
                                    <div class="form-group">
                                       <label>Group Name</label>
                                       <input type="text" class="form-control" name="group_name" id="group_name" placeholder="Enter Your Group Name" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-5">
                                    <div class="form-group">
                                       <label>HSN CODE</label>
                                       <input type="text" class="form-control" name="hsn_code" id="hsn_code" placeholder="Enter Your HSN Code" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="container-fluid">
                                 <div class="row">
                                    <button type="submit" name="submit" class="btn btn-warning" style="width:100px;">Reset</button>
                                    <button type="submit" name="submit_btn" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                                 </div>
                              </div>
                           </form>
                        </div>
                     </div>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
                  </div>
               </div>
               <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
         </div>
         <!-- Main content -->
         <section class="">
            <div class="row">
               <div class="col-sm-12">
                  <div class="panel panel-bd lobidisable">
                     <div class="panel-heading" data-toggle="offcanvas">
                        <div class="btn-group" id="buttonexport">
                           <span style="font-size: 15px;">
                              <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                 <defs>
                                    <clipPath id="lineMdWatchTwotoneLoop0">
                                       <rect width="24" height="12" />
                                    </clipPath>
                                    <symbol id="lineMdWatchTwotoneLoop1">
                                       <path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)">
                                          <animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z" />
                                          <animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3" />
                                       </path>
                                    </symbol>
                                    <mask id="lineMdWatchTwotoneLoop2">
                                       <use href="#lineMdWatchTwotoneLoop1" />
                                       <use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)" />
                                       <circle cx="12" cy="12" r="0" fill="#fff">
                                          <animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0" />
                                       </circle>
                                    </mask>
                                 </defs>
                                 <rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)" />
                              </svg>
                              <span>Item Master List !</span>
                           </span>
                        </div>
                     </div>
                     <div class="panel-body form-scroll">
                        <div class="btn-group">
                           <a href="fh_itemmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i> Add New Item Master Creation</button></a>
                        </div>
                        <?php include('include/button-export-to-data.php'); ?>
                        <div class="container-fluid">
                           <div class="row">
                              <input type="text" class="form-control-control" name="company_name" id="company_name" placeholder="Data Search" required>
                           </div>
                        </div>
                        <div class="table-responsive ">
                           <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                              <thead>
                                 <tr class="info">
                                    <th data-toggle="offcanvas" width="50px;">SrNo</th>
                                    <th data-toggle="offcanvas" width="50px;">Action</th>
                                    <th data-toggle="offcanvas">Status</th>
                                    <th data-toggle="offcanvas">Item Code</th>
                                    <th data-toggle="offcanvas">Barcode No</th>
                                    <th data-toggle="offcanvas">SKU No</th>
                                    <th data-toggle="offcanvas">Item Name</th>
                                    <th data-toggle="offcanvas">Product Images</th>
                                    <th data-toggle="offcanvas">Variants (Stock)</th>
                                    <th data-toggle="offcanvas">Description</th>
                                    <th data-toggle="offcanvas">Group</th>
                                    <th data-toggle="offcanvas">SubGroup</th>
                                    <th data-toggle="offcanvas">Brand</th>
                                    <th data-toggle="offcanvas">MOU</th>
                                    <th data-toggle="offcanvas">PP||CP</th>
                                    <th data-toggle="offcanvas">MRP</th>
                                    <th data-toggle="offcanvas">Discount(%)</th>
                                    <th data-toggle="offcanvas">Discount Amt.</th>
                                    <th data-toggle="offcanvas">RAC No</th>
                                    <th data-toggle="offcanvas">About This Item-1</th>
                                    <th data-toggle="offcanvas">About This Item-2</th>
                                    <th data-toggle="offcanvas">About This Item-3</th>
                                    <th data-toggle="offcanvas">About This Item-4</th>
                                    
                                    <th data-toggle="offcanvas">Status</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 $query = mysqli_query($conn, "select * from tbl_item_master order by id desc");
                                 $rowcount = mysqli_num_rows($query);
                                 for ($i = 1; $i <= $rowcount; $i++) {
                                    $user = mysqli_fetch_array($query);
                                 ?>
                                    <tr>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo $i; ?></label>
                                          </div>
                                       </td>
                                       <td style="background-color:#2A3F54" class="button-btn-btn-btn21">
                                          <a class="" onClick="return confirm('Are you sure you want to Update Item Master?')" href="fh_itemmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                             <span
                                                data-toggle="tooltip" title="Are you sure you want to Update Item Name" type="button" class=""><i style="color: white;" class="fa fa-pencil"></i><br><span style="color: white;">EDIT</span>
                                             </span></a><br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Item Name List ?')" href="fh_itemmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                             <span data-toggle="tooltip" title="Are you sure you want to Delete Item Name List" type="button" class=""><i style="color:white;" class="fa fa-trash-o"></i><span style="color:white;" class="">DELETE</span></span></a><br><br>

                                          <a class="" onClick="return confirm('Are you sure you want to Delete Item Name List ?')" href="fh_itemmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                             <span data-toggle="tooltip" title="Are you sure you want to Delete Brand Name List" type="button" class=""><i style="color:white;" class="fa fa fa-list-alt"></i><span style="color:white;">DETAILS</span></span></a>
                                          <br><br>
                                          <label class="switch1">
                                             <input type="checkbox">
                                             <span class="slider1 round"></span>
                                          </label>
                                       </td>
                                       <td data-toggle="offcanvas">
                                          <label class="switch1">
                                             <input type="checkbox">
                                             <span class="slider1 round"></span>
                                          </label>
                                       </td>
                                       <td data-toggle="offcanvas">
                                          <?php echo $user['item_code']; ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['barcode_no'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['sku_no'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['item_name'], 0, 50); ?></td>
                                        <td data-toggle="offcanvas">
                                          <?php
                                          $item_imgs_res = mysqli_query($conn, "SELECT image_path FROM tbl_item_images WHERE item_id='" . $user['id'] . "' ORDER BY sort_order ASC, id ASC LIMIT 3");
                                          $img_count = 0;
                                          while ($img_r = mysqli_fetch_assoc($item_imgs_res)) {
                                             echo '<img src="uploads/item-master/' . htmlspecialchars($img_r['image_path']) . '" class="img-circle" alt="Image" width="120" height="120" style="margin-right:2px; object-fit:cover;">';
                                             $img_count++;
                                          }
                                          if ($img_count == 0) {
                                             echo '<span class="text-muted">No Image</span>';
                                          }
                                          ?>
                                       </td>
                                       <td data-toggle="offcanvas" style="min-width: 170px;">
                                            <?php
                                            $v_res = mysqli_query($conn, "SELECT id, color_name, size_name, style_name, quantity FROM tbl_item_variants WHERE item_id='" . $user['id'] . "' ORDER BY id ASC");
                                            $v_list = [];
                                            $v_total_qty = 0;
                                            while ($v_row = mysqli_fetch_assoc($v_res)) {
                                               $v_list[] = $v_row;
                                               $v_total_qty += (int)$v_row['quantity'];
                                            }
                                            $v_count = count($v_list);
                                            ?>
                                            <button type="button" class="btn btn-xs btn-info btn-open-variants" 
                                                    data-item-id="<?php echo $user['id']; ?>" 
                                                    data-item-name="<?php echo htmlspecialchars($user['item_name']); ?>" 
                                                    data-item-code="<?php echo htmlspecialchars($user['item_code']); ?>" 
                                                    title="Click to update stock variant wise" 
                                                    style="margin-bottom: 5px; font-weight: 600;">
                                               <i class="fa fa-pencil-square-o"></i> Update Stocks (<?php echo $v_count; ?> Variants)
                                            </button>
                                            <div id="item-variants-container-<?php echo $user['id']; ?>" style="font-size: 11px; max-height: 90px; overflow-y: auto; background: #fafafa; padding: 4px 6px; border: 1px solid #ddd; border-radius: 3px; margin-bottom: 3px;">
                                               <?php if ($v_count > 0): ?>
                                                  <?php foreach ($v_list as $vl): ?>
                                                     <?php 
                                                     $label = trim($vl['color_name'] . ' ' . $vl['size_name']);
                                                     if (!$label) $label = 'Default';
                                                     $b_class = ((int)$vl['quantity'] > 0) ? 'label-success' : 'label-danger';
                                                     ?>
                                                     <div style="margin-bottom: 2px;">
                                                        <strong><?php echo htmlspecialchars($label); ?>:</strong> 
                                                        <span class="label <?php echo $b_class; ?>" id="v-badge-<?php echo $vl['id']; ?>"><?php echo (int)$vl['quantity']; ?> in stock</span>
                                                     </div>
                                                  <?php endforeach; ?>
                                               <?php else: ?>
                                                  <span class="text-muted">No variant records</span>
                                               <?php endif; ?>
                                            </div>
                                            <div style="font-size: 12px; font-weight: bold; color: #333;">
                                               Total Stock: <span id="item-stock-<?php echo $user['id']; ?>" class="badge badge-success" style="background-color:#5cb85c;"><?php echo $v_total_qty; ?></span>
                                            </div>
                                         </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['description'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['group_name'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['subgroup_name'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['brand_name'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['mou_name'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['purchase_price'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['mrp'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['dis_per'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['dis_amt'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['rac_no'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['about_1'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['about_2'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['about_3'], 0, 50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['about_4'], 0, 50); ?></td>
                                      
                                       <td data-toggle="offcanvas"><?php echo substr($user['status'], 0, 50); ?></td>
                                    </tr>
                                 <?php } ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- customer Modal1 -->
            <!-- Add salary Modal1 -->
            <!-- details Modal1 -->
            <div class="modal fade" id="details" tabindex="-1" role="dialog" aria-hidden="true">
               <!-- <div class="modal-dialog"> -->
               <div class="container" style="margin-left:100px;">
                  <div class="modal-content">
                     <div class="modal-header modal-header-primary">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3><i class="fa fa-plus m-r-5"></i> Account Master Details</h3>
                     </div>
                     <div class="form-horizontal col-md-12">
                        <div class="panel" style="background-color:#009688; text-align:center; color:white;">
                           <div class="panel-heading">
                              <div class="panel-title">
                                 <strong><?php echo $user['account_name']; ?></strong>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="modal-body">
                        <div class="row">
                           <div class="col-md-3">
                              <!-- User widget -->
                              <div class="user-widget list-group">
                                 <div class="list-group-item heading">
                                    <img class="media-object img-circle" src="assets/dist/img/avatar5.png" alt="image">
                                    <div class="clearfix"></div>
                                 </div>
                                 <br>
                                 <div class="text-wrap">
                                    <a class="group-item">
                                       <p class="list-group-item-text"><strong><?php echo $user['account_name']; ?></strong></p>
                                    </a>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-9">
                              <!-- User widget -->
                              <div class="row">
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Account Code</strong></p>
                                    <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Contact Name</strong></p>
                                    <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Group Name</strong></p>
                                    <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Mobile No</strong></p>
                                    <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Mobile No</strong></p>
                                    <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Email-ID</strong></p>
                                    <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>GST No</strong></p>
                                    <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>State Code</strong></p>
                                    <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Location</strong></p>
                                    <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Address</strong></p>
                                    <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Address</strong></p>
                                    <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Location</strong></p>
                                    <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Mobile No</strong></p>
                                    <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Email-ID</strong></p>
                                    <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Address</strong></p>
                                    <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Address</strong></p>
                                    <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Party Type</strong></p>
                                    <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Opening Balance</strong></p>
                                    <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Credit Limit</strong></p>
                                    <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Gender</strong></p>
                                    <p class="pull-left"><?php echo $user['contact_name']; ?></p>
                                 </div>
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Creation Date</strong></p>
                                    <p class="pull-left"><?php echo $user['group_name']; ?></p>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <p class="list-group-item-text"><strong>Status</strong></p>
                                    <p class="pull-left"><?php echo $user['account_code']; ?></p>
                                 </div>
                              </div>
                           </div>
                           <div class="form-horizontal col-md-12">
                              <div class="panel" style="background-color: #FFB61E; color: white;">
                                 <div class="panel-heading">
                                    <div class="panel-title">
                                       <strong>Total Balance Amount</strong>
                                       <p class="pull-right"><?php echo $user['contact_name']; ?></p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
                     </div>
                  </div>
                  <!-- /.modal-content -->
               </div>
               <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
            <!-- /.modal -->
            <!-- /.modal -->
         </section>
         <!-- /.content -->
         <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <!-- /.footer -->
      <?php include('include/footer.php'); ?>
      <!-- /.footer -->
      <?php include('include/Sidenavbuttons.php'); ?>
   </div>
   <?php include('include/js.php'); ?>
   <?php include('popup/variant_stock_modal.php'); ?>
</body>

</html>