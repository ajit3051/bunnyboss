<?php include('top.php');
   if(isset($_GET['update_id']))
   {
     $id=$_GET['update_id'];
     $run=mysqli_query($conn,"select * from tbl_storeprofile_master where id='$id'");
     $user =mysqli_fetch_assoc($run);
   }
   ///Update Group Master
   if(isset($_POST['update']))
      {
       $msg="";
         $code = mysqli_real_escape_string($conn, $_POST['code']);
         $store_code = mysqli_real_escape_string($conn, $_POST['store_code']);
         $store_name = mysqli_real_escape_string($conn, $_POST['store_name']);
         $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
         $mobile_no1 = mysqli_real_escape_string($conn, $_POST['mobile_no1']);
         $mobile_no2 = mysqli_real_escape_string($conn, $_POST['mobile_no2']);
         $phone_no = mysqli_real_escape_string($conn, $_POST['phone_no']);
         $gst_no = mysqli_real_escape_string($conn, $_POST['gst_no']);
         $email_id = mysqli_real_escape_string($conn, $_POST['email_id']);
         $website_link = mysqli_real_escape_string($conn, $_POST['website_link']);
         $address1 = mysqli_real_escape_string($conn, $_POST['address1']);
         $address2 = mysqli_real_escape_string($conn, $_POST['address2']);
         $tc1 = mysqli_real_escape_string($conn, $_POST['tc1']);
         $tc2 = mysqli_real_escape_string($conn, $_POST['tc2']);
         $tc3 = mysqli_real_escape_string($conn, $_POST['tc3']);
         $tc4 = mysqli_real_escape_string($conn, $_POST['tc4']);
         $tc5 = mysqli_real_escape_string($conn, $_POST['tc5']);
         $tc6 = mysqli_real_escape_string($conn, $_POST['tc6']);
         $invoice_type = mysqli_real_escape_string($conn, $_POST['invoice_type']);
         $print_type = mysqli_real_escape_string($conn, $_POST['print_type']);
         $defaultprint_copy = mysqli_real_escape_string($conn, $_POST['defaultprint_copy']);
         $additional_script = mysqli_real_escape_string($conn, $_POST['additional_script']);
         $welcome_script = mysqli_real_escape_string($conn, $_POST['welcome_script']);
         $status = mysqli_real_escape_string($conn, $_POST['status']);
   
   
              $check = mysqli_query(
       $conn,
       "SELECT id FROM tbl_storeprofile_master 
        WHERE store_name='$store_name' AND id!='$id'"
   );
   
      
           // FILE UPLOAD PATH
   $path = "uploads/item-master/";
   if (!is_dir($path)) {
       mkdir($path, 0777, true);
   }
   
   // Upload function
   function uploadFile($file, $path) {
       if (isset($file) && $file['error'] === 0 && is_uploaded_file($file['tmp_name'])) {
   
           $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
           $allowed = ['jpg','jpeg','png','webp'];
   
           if (!in_array($ext, $allowed)) {
               return "";
           }
   
           $filename = time() . "_" . preg_replace("/[^a-zA-Z0-9\.]/", "", $file['name']);
           move_uploaded_file($file['tmp_name'], $path . $filename);
   
           return $filename;
       }
       return "";
   }
   
   // Upload new image
   $new_image = uploadFile($_FILES['picture1'], $path);
   
   // Keep old image if no new upload
   $picture1 = !empty($new_image) ? $new_image : $user['picture1'];
   
   
   
   
   
       $store_name = str_replace("'", "\'", $store_name);
       $store_name = str_replace('"', '\"', $store_name);
       
       $sql="UPDATE tbl_storeprofile_master SET code='$code', store_code='$store_code',store_name='$store_name',company_name='$company_name',mobile_no1='$mobile_no1',mobile_no2='$mobile_no2',phone_no='$phone_no', gst_no='$gst_no', email_id='$email_id', website_link='$website_link', address1='$address1',address2='$address2', tc1='$tc1',tc2='$tc2', tc3='$tc3',tc4='$tc4',tc5='$tc5', tc6='$tc6',invoice_type='$invoice_type',print_type='$print_type',defaultprint_copy='$defaultprint_copy', additional_script='$additional_script',welcome_script='$welcome_script',picture1='$picture1',status='$status' WHERE id='$id'";
       if(mysqli_query($conn,$sql)){
           $msg='<div class="alert alert-success alert-dismissible fade1 show">
                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                       <strong>Store Profile Update..!</strong>
                   </div>';
                   header('refresh:.5; url=fh_storeprofilecreation_list.php');
       }
       else{
           $msg='<div class="alert alert-danger alert-dismissible fade1 show">
               <button type="button" class="close" data-dismiss="alert">&times;</button>
               <strong>Failed!</strong>
            </div>';
       }
      }
     ?>
<!DOCTYPE html>
<html lang="en">
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Store Profile Master Edit || TEJASERP</title>
      <!-- Favicon and touch icons -->
      <?php include('include/css.php');?>
      <?php include('include/fh_modal_dialog.php');?>   
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
            <?php include('include/sidebar-left.php');?>
            <?php include('include/toggle_switch_list.php');?> 
             <?php include('include/fh-form-scrolling-data-list.php');?>
            <!-- /.sidebar -->
         </aside>
         <!-- =============================================== -->
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
           
            <div class="container-fluid">
               <div class="row">
                  <?php include('include/menu-header.php');?>
               </div>
            </div>
            <!-- Main content -->
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
                                          <rect width="24" height="12"/>
                                       </clipPath>
                                       <symbol id="lineMdWatchTwotoneLoop1">
                                          <path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)">
                                             <animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/>
                                             <animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/>
                                          </path>
                                       </symbol>
                                       <mask id="lineMdWatchTwotoneLoop2">
                                          <use href="#lineMdWatchTwotoneLoop1"/>
                                          <use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/>
                                          <circle cx="12" cy="12" r="0" fill="#fff">
                                             <animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/>
                                          </circle>
                                       </mask>
                                    </defs>
                                    <rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/>
                                 </svg>
                                 <span>Store Profile Master Update !</span>
                              </span>
                           </div>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_storeprofilecreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Store Profile Creation</button></a>
                           </div>
                           <div class="btn-group">
                              <a href="fh_storeprofilecreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Store Profile List</button></a>
                           </div>
                           <form method="post" enctype="multipart/form-data">
                              <!-- <div class="form-group">
                                 <?php echo $msg; ?>
                                 </div> -->
                              <div class="row">
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Code</label><label style="color:red;">*</label>
                                       <input type="password" name="code" class="form-control" readonly value="<?php echo $user['code'];?>" />
                                    </div>
                                 </div>
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Store Code</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="store_code" id="store_code"  value="<?php echo $user['store_code'];?>" placeholder="Enter Your Store Code" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Store Name</label><label style="color:red;">*</label>
                                       <select name="store_name" class="form-control" required>
                                          <option value="null">Select Store Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_store_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($user['store_name'] == $row['store_name']) ? 'selected' : '';
                                                echo "<option value='{$row['store_name']}' {$selected}>{$row['store_name']}
                                                </option>";
                                             }
                                             ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Company Name</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="company_name" id="company_name"  value="<?php echo $user['company_name'];?>" placeholder="Enter Company Name" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Mobile:1</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="mobile_no1" id="mobile_no1"  value="<?php echo $user['mobile_no1'];?>" placeholder="Enter Mobile No" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Mobile:2</label>
                                       <input type="text" class="form-control" name="mobile_no2" id="mobile_no2"  value="<?php echo $user['mobile_no2'];?>" placeholder="Enter Mobile No" >
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Phone No.</label>
                                       <input type="text" class="form-control" name="phone_no" id="phone_no"  value="<?php echo $user['phone_no'];?>" placeholder="Enter Phone No" >
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>GST No.</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="gst_no" id="gst_no"  value="<?php echo $user['gst_no'];?>" placeholder="Enter Your GST No" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Email-ID</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="email_id" id="email_id"  value="<?php echo $user['email_id'];?>" placeholder="Enter Email-ID" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Website Link</label>
                                       <input type="text" class="form-control" name="website_link" id="website_link"  value="<?php echo $user['website_link'];?>" placeholder="Enter Your Website Link" >
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Address:1</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="address1" id="address1"  value="<?php echo $user['address1'];?>" placeholder="Enter Address" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Address:2</label>
                                       <input type="text" class="form-control" name="address2" id="address2"  value="<?php echo $user['address2'];?>" placeholder="Enter Address" >
                                    </div>
                                 </div>
                              </div>
                              <label>Term & condition :</label>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>T&C:1</label>
                                       <input type="text" class="form-control" name="tc1" id="tc1"  value="<?php echo $user['tc1'];?>" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>T&C:2</label>
                                       <input type="text" class="form-control" name="tc2" id="tc2"  value="<?php echo $user['tc2'];?>" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>T&C:3</label>
                                       <input type="text" class="form-control" name="tc3" id="tc3"  value="<?php echo $user['tc3'];?>" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>T&C:4</label>
                                       <input type="text" class="form-control" name="tc4" id="tc4"  value="<?php echo $user['tc4'];?>" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>T&C:5</label>
                                       <input type="text" class="form-control" name="tc5" id="tc5"  value="<?php echo $user['tc5'];?>" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>T&C:6</label>
                                       <input type="text" class="form-control" name="tc6" id="tc6"  value="<?php echo $user['tc6'];?>" placeholder="Enter Your T&C" >
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Invoive Type</label><label style="color:red;">*</label>
                                       <select name="invoice_type" class="form-control" required>
                                          <option value="null">Select Store Name</option>
                                          <option>Tax Invoice</option>
                                          <option>Retail Invoice</option>
                                          <option>Cash Memo</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_storeprofile_master");
                                             
                                             while ($row = mysqli_fetch_array($query)) {
                                               $selected = ($user['invoice_type'] == $row['invoice_type']) ? 'selected' : '';
                                               echo "<option value='{$row['invoice_type']}' {$selected}>{$row['invoice_type']}
                                               </option>";
                                             }
                                             ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Print Type</label><label style="color:red;">*</label>
                                       <select name="print_type" class="form-control" required>
                                          <option value="null">Select Print Type</option>
                                          <option>80 mm </option>
                                          <option>A4</option>
                                          <option>A5</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_storeprofile_master");
                                             
                                             while ($row = mysqli_fetch_array($query)) {
                                               $selected = ($user['print_type'] == $row['print_type']) ? 'selected' : '';
                                               echo "<option value='{$row['print_type']}' {$selected}>{$row['print_type']}
                                               </option>";
                                             }
                                             ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Default Print Copy</label><label style="color:red;">*</label>
                                       <select name="defaultprint_copy" class="form-control" required>
                                          <option value="null">Select Print Copy</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_storeprofile_master");
                                             
                                             while ($row = mysqli_fetch_array($query)) {
                                               $selected = ($user['defaultprint_copy'] == $row['defaultprint_copy']) ? 'selected' : '';
                                               echo "<option value='{$row['defaultprint_copy']}' {$selected}>{$row['defaultprint_copy']}
                                               </option>";
                                             }
                                             ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Additional Script</label>
                                       <input type="text" class="form-control" name="additional_script" id="additional_script"  value="<?php echo $user['additional_script'];?>" placeholder="Enter Additional Script" >
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Logo upload</label>
                                       <?php if (!empty($user['picture1'])) { ?>
                                       <img src="uploads/item-master/<?php echo $user['picture1']; ?>" width="80">
                                       <?php } ?>

                                       <input type="file" name="picture1" id="picture1" ;?>
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Status</label><label style="color:red;">*</label><br>
                                       <label class="radio-inline">
                                       <input type="radio" name="status" value="true" checked="checked">Active</label>
                                       <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>Welcome Script</label>
                                       <input type="text" class="form-control" name="welcome_script" id="welcome_script"  value="<?php echo $user['welcome_script'];?>" placeholder="Enter Welcome Script" >
                                    </div>
                                 </div>
                              </div>
                              <br>
                              <div class="container-fluid">
                                 <div class="row">
                                    <a href="ssenterprises_accountmastercreation.php"><button type="Reset" name="Reset" class="btn btn-warning" style="width:100px;">Reset</button></a>
                                    <button type="update" name="update" class="btn btn-success" style="width:100px;">Update</button>
                                 </div>
                              </div>
                              <br>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Main content -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading" data-toggle="offcanvas">
                           <span style="font-size: 15px;">
                              <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                 <defs>
                                    <clipPath id="lineMdWatchTwotoneLoop0">
                                       <rect width="24" height="12"/>
                                    </clipPath>
                                    <symbol id="lineMdWatchTwotoneLoop1">
                                       <path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)">
                                          <animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/>
                                          <animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/>
                                       </path>
                                    </symbol>
                                    <mask id="lineMdWatchTwotoneLoop2">
                                       <use href="#lineMdWatchTwotoneLoop1"/>
                                       <use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/>
                                       <circle cx="12" cy="12" r="0" fill="#fff">
                                          <animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/>
                                       </circle>
                                    </mask>
                                 </defs>
                                 <rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/>
                              </svg>
                              <span>Store Profile Edit List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_storeprofilecreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Store Profile Creation</button></a>
                           </div>
                           <div class="btn-group">
                              <a href="fh_storeprofilecreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Store Profile List</button></a>
                           </div>
                           <?php include('include/button-export-to-data.php');?>
                           <div class="container-fluid">
                              <div class="row">
                                 <input type="text" class="form-control-control" name="company_name" id="company_name" placeholder="Data Search" required>
                              </div>
                           </div>
                           <div class="table-responsive ">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th data-toggle="offcanvas">SrNo</th>
                                       <th data-toggle="offcanvas">Action</th>
                                       <th data-toggle="offcanvas">Status</th>
                                       <th data-toggle="offcanvas">Logo</th>
                                       <th data-toggle="offcanvas">Code</th>
                                       <th data-toggle="offcanvas">Store Code</th>
                                       <th data-toggle="offcanvas">Store Name</th>
                                       <th data-toggle="offcanvas">Company Name</th>
                                       <th data-toggle="offcanvas">Mobile No-1</th>
                                       <th data-toggle="offcanvas">Mobile No-2</th>
                                       <th data-toggle="offcanvas">Phone No</th>
                                       <th data-toggle="offcanvas">GST No</th>
                                       <th data-toggle="offcanvas">Email-ID</th>
                                       <th data-toggle="offcanvas">Website Link</th>
                                       <th data-toggle="offcanvas">Address-1</th>
                                       <th data-toggle="offcanvas">Address-2</th>
                                       <th data-toggle="offcanvas">TC-1</th>
                                       <th data-toggle="offcanvas">TC-2</th>
                                       <th data-toggle="offcanvas">TC-3</th>
                                       <th data-toggle="offcanvas">TC-4</th>
                                       <th data-toggle="offcanvas">TC-5</th>
                                       <th data-toggle="offcanvas">TC-6</th>
                                       <th data-toggle="offcanvas">Invoice Type</th>
                                       <th data-toggle="offcanvas">Print Type</th>
                                       <th data-toggle="offcanvas">Print Copy</th>
                                       <th data-toggle="offcanvas">Additional Script</th>
                                       <th data-toggle="offcanvas">Welcome Script</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                       $query=mysqli_query($conn,"select * from tbl_storeprofile_master"); 
                                       $rowcount=mysqli_num_rows($query);
                                       for($i=1; $i<=$rowcount; $i++)
                                       {
                                         $user=mysqli_fetch_array($query);
                                       ?>
                                    <tr>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo $i; ?></label>
                                          </div>
                                       </td>
                                       <td class="button-btn-btn-btn211">
                                          <a class=""onClick="return confirm('Are you sure you want to Update Store Profile Name Master?')" href="fh_storeprofilecreation_update.php?update_id=<?php echo $user['id']; ?>"><i class="fa fa-pencil" style="color:white;"></i><br>
                                          <label style="color:white;"> EDIT</label>
                                          </a>
                                          <br><br>
                                          <a class=""onClick="return confirm('Are you sure you want to Update User Name Master?')" href="fh_usermastercreation_update.php?update_id=<?php echo $user['id']; ?>"><i class="fa fa-list-alt" style="color:white;"></i>
                                          <label style="color:white;"> DETAILS</label>
                                          </a>
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
                                          <img src="uploads/item-master/<?php echo $user['picture1']; ?>" class="img-circle" alt="User Image" width="50" height="50">
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo $user['code']; ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['store_code'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['store_name'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['company_name'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['mobile_no1'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['mobile_no2'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['phone_no'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['gst_no'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['email_id'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['website_link'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['address1'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['address2'],0,50); ?></td>
                                       <td><?php echo substr($user['tc1'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['tc2'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['tc3'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['tc4'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['tc5'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['tc6'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['invoice_type'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['print_type'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['defaultprint_copy'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['additional_script'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['welcome_script'],0,50); ?></td>
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
               <!-- model Modal1 -->
               <div class="modal fade" id="storeprofilecreation" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header modal-header-primary">
                           <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                           <h3><i class="fa fa-plus m-r-5"></i> Add New Store Master Creation</h3>
                        </div>
                        <div class="modal-body">
                           <div class="row">
                              <div class="col-md-12">
                                 <form method="post">
                                    <?php
                                       $msg='';
                                        if(isset($_POST['submit_btn']))
                                        {
                                         $store_code = mysqli_real_escape_string($conn, $_POST['store_code']);
                                         $store_name = mysqli_real_escape_string($conn, $_POST['store_name']);
                                         $location_name = mysqli_real_escape_string($conn, $_POST['location_name']);
                                         $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
                                         $status = mysqli_real_escape_string($conn, $_POST['status']);
                                         
                                             $dup=mysqli_query($conn,"select * from tbl_store_master where store_name='$store_name'");
                                               if(mysqli_num_rows($dup)>0)
                                               {
                                                   $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Already exist!</strong>
                                                       </div>';
                                               }
                                               else{
                                                   $query="Insert into tbl_store_master(store_code,store_name,location_name,company_name,status)values('$store_code','$store_name','$location_name','$company_name','$status')";
                                                   if(mysqli_query($conn,$query)){
                                                       $msg='<div class="alert alert-success alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Creations Success..!</strong>
                                                     </div>';
                                                   }
                                                   else{
                                                       $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Creations Failed!</strong>
                                                       </div>';
                                                   }
                                               } 
                                           }
                                        ?>
                                    <div class="row">
                                       <div class="col-sm-6">
                                          <div class="form">
                                             <label>Store Code</label>
                                             <input type="password" name="store_code" class="form-control" id="store_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_store_master ORDER BY id DESC");
                                                $po_no = ($no) ? mysqli_fetch_assoc($no) : null;
                                                $next_id = ($po_no && isset($po_no['id'])) ? ($po_no['id'] + 1) : 1;
                                                echo 'SC' . $next_id;
                                                ?>">
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Store Name</label>
                                             <input type="text" class="form-control" name="store_name" id="store_name" placeholder="Enter Your Store Name" required>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Company Name</label>
                                             <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter Your Company Name" required>
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Store Location</label>
                                             <input type="text" class="form-control" name="location_name" id="location_name" placeholder="Enter Your Location Name" required>
                                          </div>
                                       </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                       <div class="col-sm-4">
                                          <div class="form-check">
                                             <label>Status</label><br>
                                             <label class="radio-inline">
                                             <input type="radio" name="status" value="true" checked="checked">Active</label>
                                             <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                          </div>
                                       </div>
                                    </div>
                                    <br>
                                    <div class="container-fluid">
                                       <div class="row">
                                          <button type="submit" name="submit" class="btn btn-warning" style="width:100px;">Reset</button>
                                          <button type="submit" name="submit_btn" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                                       </div>
                                    </div>
                                    <br>
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
               <!-- model Modal1-end -->
               <!-- /.modal -->
            </section>
            <!-- /.content -->
            <!-- /.content -->
         </div>
         <!-- /.content-wrapper -->
         <!-- /.footer -->
            <?php include('include/footer.php');?> 
         <!-- /.footer -->
         <?php include('include/Sidenavbuttons.php');?>
      </div>
      <!-- ./wrapper -->
      <!-- Start Core Plugins
         =====================================================================-->
      <!-- jQuery -->
      <?php include('include/js.php');?>  
      <!-- End Theme label Script
         =====================================================================-->
   </body>
   <!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
</html>