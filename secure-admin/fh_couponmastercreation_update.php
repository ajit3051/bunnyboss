<?php include('top.php');
   if(isset($_GET['update_id']))
   {
     $id=$_GET['update_id'];
     $run=mysqli_query($conn,"select * from tbl_coupon_master where id='$id'");
     $user =mysqli_fetch_assoc($run);
   }
   ///Update Group Master
   if(isset($_POST['update']))
      {
       $msg="";
         $coupon_code = mysqli_real_escape_string($conn, $_POST['coupon_code']);
         $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
         $store_name = mysqli_real_escape_string($conn, $_POST['store_name']);
         $description_1 = mysqli_real_escape_string($conn, $_POST['description_1']);
         $description_2 = mysqli_real_escape_string($conn, $_POST['description_2']);
         $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
         $discount_per = mysqli_real_escape_string($conn, $_POST['discount_per']);
         $discount_amt = mysqli_real_escape_string($conn, $_POST['discount_amt']);
         $minshopping_amt = mysqli_real_escape_string($conn, $_POST['minshopping_amt']);
         $tc_1 = mysqli_real_escape_string($conn, $_POST['tc_1']);
         $tc_2 = mysqli_real_escape_string($conn, $_POST['tc_2']);
         $tc_3 = mysqli_real_escape_string($conn, $_POST['tc_3']);
         $tc_4 = mysqli_real_escape_string($conn, $_POST['tc_4']);
         $tc_5 = mysqli_real_escape_string($conn, $_POST['tc_5']);
         $tc_6 = mysqli_real_escape_string($conn, $_POST['tc_6']);
         $tc_7 = mysqli_real_escape_string($conn, $_POST['tc_7']);
         $tc_8 = mysqli_real_escape_string($conn, $_POST['tc_8']);
         $tc_9 = mysqli_real_escape_string($conn, $_POST['tc_9']);
         $tc_10 = mysqli_real_escape_string($conn, $_POST['tc_10']);
         $creation_date = mysqli_real_escape_string($conn, $_POST['creation_date']);
         $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);
        
         $status = mysqli_real_escape_string($conn, $_POST['status']);
         
   
       $tc_10 = str_replace("'", "\'", $tc_10);
       $tc_10 = str_replace('"', '\"', $tc_10);
       
       $query="UPDATE tbl_coupon_master SET coupon_code='$coupon_code', company_name='$company_name',store_name='$store_name',description_1='$description_1',description_2='$description_2',remarks='$remarks',discount_per='$discount_per', discount_amt='$discount_amt', minshopping_amt='$minshopping_amt', tc_1='$tc_1', tc_2='$tc_2',tc_3='$tc_3', tc_4='$tc_4',tc_5='$tc_5', tc_6='$tc_6',tc_7='$tc_7',tc_8='$tc_8', tc_9='$tc_9',tc_10='$tc_10',creation_date='$creation_date',expiry_date='$expiry_date', status='$status' WHERE id='$id'";
       if(mysqli_query($conn,$query)){
           $msg='<div class="alert alert-success alert-dismissible fade1 show">
                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                       <strong>Coupon Name Update..!</strong>
                   </div>';
                   header('refresh:.5; url=fh_couponmastercreation_list.php');
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
      <title>Coupon Master Edit</title>
      <!-- Favicon and touch icons -->
      <?php include('include/css.php');?>  
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
         
         <?php include('include/toggle_switch_list.php');?>  
         <!-- =============================================== -->
         <!-- Left side column. contains the sidebar -->
         <aside class="main-sidebar">
            <!-- sidebar -->
            <?php include('include/sidebar-left.php');?>
            <!-- /.sidebar -->
         </aside>
         <!-- =============================================== -->
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            
            <div class="container-fluid">
               <div class="row" style="margin-bottom:5px;">
                  <?php include('include/menu-header.php');?>
               </div>
            </div>
            <!-- Main content -->
            <section class="">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading"data-toggle="offcanvas">
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
                                 <span>Coupon Master List !</span>
                              </span>
                           </div>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                        <a href="fh_couponmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Add New Coupon Master Creation</button></a>
                     </div>
                           <div class="btn-group">
                        <a href="fh_couponmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Coupon Master List</button></a>
                     </div>
                     <div class="btn-group">
                        <a href="fh_couponmastercreation_view.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Coupon Master View</button></a>
                     </div>
                           <form method="post">
                              <!-- <div class="form-group">
                                 <?php echo $msg; ?>
                                 </div> -->
                              <div class="row">
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Coupon Code</label><label style="color:red;">*</label>
                                       <input type="password" name="coupon_code"id="coupon_code" class="form-control" readonly value="<?php echo $user['coupon_code'];?>" />
                                    </div>
                                 </div>
                                 <div class="col-sm-5">
                                    <div class="form-group">
                                       <label>Company Name</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="company_name" id="company_name"  value="<?php echo $user['company_name'];?>" placeholder="Enter Company Name" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-5">
                                    <div class="form-group">
                                       <label>Store Name</label><label style="color:red;">*</label>
                                       <select name="store_name" class="form-control" required>
                                          <option value="null">Select Store Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_store_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                $selected = ($user['store_name'] == $row['store_name']) ? 'selected' : '';
                                                   echo "<option value='{$row['store_name']}' {$selected}>{$row['store_name']}</option>";
                                                }
                                                ?>
                                       </select>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Description-1</label>
                                       <input type="text" class="form-control" name="description_1" id="description_1"  value="<?php echo $user['description_1'];?>" placeholder="Enter Description Name" >
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Description-2</label>
                                       <input type="text" class="form-control" name="description_2" id="description_2"  value="<?php echo $user['description_2'];?>" placeholder="Enter Description" >
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Remarks</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="remarks" id="remarks"  value="<?php echo $user['remarks'];?>" placeholder="Enter Remarks" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Discount %</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="discount_per" id="discount_per"  value="<?php echo $user['discount_per'];?>" placeholder="Enter Discount %" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Discount Amount</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="discount_amt" id="discount_amt"  value="<?php echo $user['discount_amt'];?>" placeholder="Enter Discount Amt" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Minimum Shopping Amount</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="minshopping_amt" id="minshopping_amt"  value="<?php echo $user['minshopping_amt'];?>" placeholder="Enter Minimum Shopping Amt" required>
                                    </div>
                                 </div>
                              </div>
                              <label>Terms & Conditions ||</label>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-1</label>
                                       <input type="text" class="form-control" name="tc_1" id="tc_1"  value="<?php echo $user['tc_1'];?>" >
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-2</label>
                                       <input type="text" class="form-control" name="tc_2" id="tc_2"  value="<?php echo $user['tc_2'];?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-3</label>
                                       <input type="text" class="form-control" name="tc_3" id="tc_3"  value="<?php echo $user['tc_3'];?>" >
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-4</label>
                                       <input type="text" class="form-control" name="tc_4" id="tc_4"  value="<?php echo $user['tc_4'];?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-5</label>
                                       <input type="text" class="form-control" name="tc_5" id="tc_5"  value="<?php echo $user['tc_5'];?>" >
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-6</label>
                                       <input type="text" class="form-control" name="tc_6" id="tc_6"  value="<?php echo $user['tc_6'];?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-7</label>
                                       <input type="text" class="form-control" name="tc_7" id="tc_7"  value="<?php echo $user['tc_7'];?>" >
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-8</label>
                                       <input type="text" class="form-control" name="tc_8" id="tc_8"  value="<?php echo $user['tc_8'];?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-9</label>
                                       <input type="text" class="form-control" name="tc_9" id="tc_9"  value="<?php echo $user['tc_9'];?>" >
                                    </div>
                                 </div>
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                       <label>TC-10</label>
                                       <input type="text" class="form-control" name="tc_10" id="tc_10"value="<?php echo $user['tc_10'];?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Creation Date</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="creation_date" id="minMaxExample"value="<?php echo $user['creation_date'];?>"required>
                                    </div>
                                 </div>
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                       <label>Expiry Date</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="expiry_date" id="date"value="<?php echo $user['expiry_date'];?>"required>
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Picture upload</label>
                                       <input type="file" name="picture" id="picture">
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Logo upload</label>
                                       <input type="file" name="picture" id="picture">
                                    </div>
                                 </div>
                                 <div class="col-sm-2">
                                    <div class="form-check">
                                       <label>Status</label><label style="color:red;">*</label><br>
                                       <label class="radio-inline">
                                       <input type="radio" name="status" value="true" checked="checked">Active</label>
                                       <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                    </div>
                                 </div>
                              </div><br>
                              <div class="container-fluid">
                                 <div class="row">
                                    <a href="ssenterprises_accountmastercreation.php"><button type="Reset" name="Reset" class="btn btn-warning" style="width:100px;">Reset</button></a>
                                    <button type="update" name="update" class="btn btn-success" style="width:100px;">Update</button>
                                 </div>
                              </div><br>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Main content -->
                        <!-- Main content -->
            <!-- Main content -->
            <section class="">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading"data-toggle="offcanvas">
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
                              <span>Coupon Master List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_couponmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Add New Coupon Master Creation</button></a>
                           </div>
                           <div class="btn-group">
                              <a href="fh_couponmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Coupon Master List</button></a>
                           </div>
                           <div class="btn-group">
                              <a href="fh_couponmastercreation_view.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Coupon Master List View</button></a>
                           </div>
                           <?php include('include/button-export-to-data.php');?>
                           <div class="container-fluid">
                              <div class="row">
                                 <input type="text" class="form-control-control" name="company_name" id="company_name" placeholder="Search" required>
                              </div>
                           </div>
                           <div class="table-responsive">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th data-toggle="offcanvas" width="50px;">SrNo</th>
                                       <th data-toggle="offcanvas" width="50px;">Action</th>
                                       <th data-toggle="offcanvas">Status</th>
                                       <th data-toggle="offcanvas">Coupon Code</th>
                                       <th data-toggle="offcanvas">Company Name</th>
                                       <th data-toggle="offcanvas">Store Name</th>
                                       <th data-toggle="offcanvas">Description-1</th>
                                       <th data-toggle="offcanvas">Description-2</th>
                                       <th data-toggle="offcanvas">Remarks</th>
                                       <th data-toggle="offcanvas">Discount %</th>
                                       <th data-toggle="offcanvas">Discount Amt</th>
                                       <th data-toggle="offcanvas">Min-Shopping Amt</th>
                                       <th data-toggle="offcanvas">TC-1</th>
                                       <th data-toggle="offcanvas">TC-2</th>
                                       <th data-toggle="offcanvas">TC-3</th>
                                       <th data-toggle="offcanvas">TC-4</th>
                                       <th data-toggle="offcanvas">TC-5</th>
                                       <th data-toggle="offcanvas">TC-6</th>
                                       <th data-toggle="offcanvas">TC-7</th>
                                       <th data-toggle="offcanvas">TC-8</th>
                                       <th data-toggle="offcanvas">TC-9</th>
                                       <th data-toggle="offcanvas">TC-10</th>
                                       <th data-toggle="offcanvas">Creation Date</th>
                                       <th data-toggle="offcanvas">Expiry Date</th>
                                       <th data-toggle="offcanvas">Picture</th>
                                       <th data-toggle="offcanvas">Logo</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                       $query=mysqli_query($conn,"select * from tbl_coupon_master"); 
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
                                       <td style="background-color:#2A3F54" class="button-btn-btn-btn21">
                                          <a class=""onClick="return confirm('Are you sure you want to Update Coupon Master?')" href="fh_couponmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                          <span
                                             data-toggle="tooltip" title="Are you sure you want to Update Coupon Master" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><br><span style="color: white;">EDIT</span>
                                          </span></a><br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Coupon Master List ?')" href="fh_couponmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Coupon Master List" type="button" class="" ><i style="color:white;" class="fa fa-trash-o"></i><br><span style="color:white;"class="">DELETE</span></span></a><br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Item Name List ?')" href="fh_itemmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Brand Name List" type="button" class="" ><i style="color:white;" class="fa fa fa-list-alt"></i><span style="color:white;">DETAILS</span></span></a><br><br>
                                          <label class="switch1">
                                          <input type="checkbox">
                                          <span class="slider1 round"></span>
                                          </label>
                                       </td>
                                       <td class=""data-toggle="offcanvas">
                                          <label class="switch1">
                                          <input type="checkbox">
                                          <span class="slider1 round"></span>
                                          </label>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo $user['coupon_code']; ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['company_name'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['store_name'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['description_1'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['description_2'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['remarks'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['discount_per'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['discount_amt'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['minshopping_amt'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['tc_1'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['tc_2'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['tc_3'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['tc_4'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['tc_5'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['tc_6'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['tc_7'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['tc_8'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['tc_9'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['tc_10'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['creation_date'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas"><?php echo substr($user['expiry_date'],0,50); ?>
                                       </td>
                                       <td class=""data-toggle="offcanvas">
                                          <img src="uploads/item-master/<?php echo $user['picture_upload']; ?>" class="img-circle" alt="User Image" width="50" height="50">
                                       </td>
                                       <td class=""data-toggle="offcanvas">
                                          <img src="uploads/item-master/<?php echo $user['logo_upload']; ?>" class="img-circle" alt="User Image" width="50" height="50">
                                       </td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
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
            <!-- /.content -->
            <!-- /.content -->
         </div>
         <!-- /.content-wrapper -->
         <!-- /.footer -->
         