<?php include('top.php');?>
<?php
   $msg='';
    if(isset($_POST['submit']))
    {
     $dis_code = mysqli_real_escape_string($conn, $_POST['dis_code']);
     $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
     $brand_name = mysqli_real_escape_string($conn, $_POST['brand_name']);
     $barcode_no = mysqli_real_escape_string($conn, $_POST['barcode_no']);
     $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
     $store_name = mysqli_real_escape_string($conn, $_POST['store_name']);
     $location_name = mysqli_real_escape_string($conn, $_POST['location_name']);
     $discount_per = mysqli_real_escape_string($conn, $_POST['discount_per']);
     $discount_amt = mysqli_real_escape_string($conn, $_POST['discount_amt']);
     $mrp = mysqli_real_escape_string($conn, $_POST['mrp']);
     $sp = mysqli_real_escape_string($conn, $_POST['sp']);
     $status = mysqli_real_escape_string($conn, $_POST['status']);
     
     
   
     
   
         $dup=mysqli_query($conn,"select * from tbl_discount_master where group_name='$group_name'");
           if(mysqli_num_rows($dup)>0)
           {
               $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                   <button type="button" class="close" data-dismiss="alert">&times;</button>
                   <strong>Discount Master Already exist!</strong>
                   </div>';
           }
           else{
               $query="Insert into tbl_discount_master(dis_code,group_name,brand_name,barcode_no,item_name,store_name,location_name,discount_per,discount_amt,mrp,sp,status)values('$dis_code',
               '$group_name','$brand_name','$barcode_no','$item_name','$store_name','$location_name','$discount_per','$discount_amt','$mrp','$sp','$status')";
               $run = mysqli_query($conn,$query);   // ✅ EXECUTE QUERY
   
       if($run)
   {
   echo "
   <script>
   document.addEventListener('DOMContentLoaded', function() {
       showPopup(
           'Success!',
           'Discount Master Saved Successfully 🎉',
           
       );
   });
   </script>
   ";
               }
               else{
                   $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                   <button type="button" class="close" data-dismiss="alert">&times;</button>
                   <strong>Discount Master Creations Failed!</strong>
                   </div>';
               }
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
      <title>Discount Master Creation || TEJASERP</title>
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
        
         <!-- =============================================== -->
         <!-- Left side column. contains the sidebar -->
         <aside class="main-sidebar">
            <!-- sidebar -->
            <?php include('include/sidebar-left.php');?>
            <?php include('include/toggle_switch_list.php');?>
            <?php include('include/fh-form-scrolling-data-list.php');?> 
            <?php include('include/fh-popup-message-successfully.php');?> 
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
                              <span>Discount Master Creations !</span>
                              
                           </span>
                           
                        </div>
                        <div class="panel-body">
                            <div class="btn-group">
                              <a href="fh_discountmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Discount Master List</button></a>
                           </div>
                           <form method="post">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                              </div>
                              <div class="row">
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Discount Code</label><label style="color:red;">*</label>
                                       <input type="password" name="dis_code" class="form-control" id="dis_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_discount_master ORDER BY id DESC");
                                          $po_no = mysqli_fetch_assoc($no);echo 'DC-000'.($po_no['id']+1);
                                          ?>">
                                    </div>
                                 </div>
                                  <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>Store Name</label><label style="color:red;">*</label>
                                       <select name="store_name" class="form-control" required>
                                          <option value="null">Select Store Name </option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_store_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                ?>
                                          <option><?php echo $row['store_name']; ?></option>
                                          <?php } ?>
                                       </select>
                                    </div>
                                    <a href="#" class="" data-toggle="modal" data-target="#addtrain1">
                                    <span style="font-size:11px;" class="pull-left">
                                    <i class="fa fa-plus"></i>Add New Store Master
                                    </span>
                                    </a>
                                 </div>
                                 <div class="col-lg-5">
                                    <div class="form-group">
                                       <label>Location Name</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="location_name" id="location_name" placeholder="Enter Your Location Name" required>
                                    </div>
                                 </div>
                                 
                              </div>
                              <div class="row">
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Festival Name</label><label style="color:red;">*</label>
                                       <select name="fest_name" class="form-control" required>
                                          <option value="null">Select Festival Name </option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_festival_master");
                                                              while ($row = mysqli_fetch_array($query)) {
                                                              ?>
                                          <option><?php echo $row['fest_name']; ?></option>
                                          <?php } ?> 
                                       </select>
                                    </div>
                                    <a href="#" class="" data-toggle="modal" data-target="#festival">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Festival Name</span>
                                    </a>
                                 </div>
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
                                 <div class="col-lg-5">
                                    <div class="form-group">
                                       <label>Brand Name</label>
                                       <select name="brand_name" class="form-control" required>
                                          <option value="null">Brand </option>
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
                              <div class="row">
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Barcode No</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="barcode_no" id="barcode_no" placeholder="Enter Your Barcode No" required>
                                    </div>
                                 </div>
                                 <div class="col-lg-4">
                                    <div class="form-group">
                                       <label>SKU No</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="barcode_no" id="barcode_no" placeholder="Enter Your Barcode No" required>
                                    </div>
                                 </div>
                                 <div class="col-lg-5">
                                    <div class="form-group">
                                       <label>Item Name</label><label style="color:red;">*</label>
                                       <select name="item_name" class="form-control" required>
                                          <option value="null">Select Item Name </option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_item_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                                ?>
                                          <option><?php echo $row['item_name']; ?></option>
                                          <?php } ?> 
                                       </select>
                                    </div>
                                 </div>
                                 
                                
                              </div>
                              <div class="row">
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Discount Percentage</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="discount_per" id="discount_per" placeholder="Enter Your Discount Percentage" required>
                                    </div>
                                 </div>
                                 <div class="col-lg-3">
                                    <div class="form-group">
                                       <label>Discount Amount</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="discount_amt" id="discount_amt" placeholder="Enter Your Discount Amount" required>
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
                                       <label>SP</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="sp" id="sp" placeholder="Enter Your SP" required>
                                    </div>
                                 </div>
                              </div><br>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Coupon || Voucher </label><br>
                                       <br>
                                       <input type="file" name="picture1" id="picture1">
                                    </div>
                                 </div>
                                 
                                 
                                 <div class="col-sm-4">
                                    <div class="form-check">
                                       <label>Status</label><label style="color:red;">*</label><br><br>
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
            <?php include('include/fh_modal_dialog.php');?>   
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
                              <span>Discount Master List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_discountmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Discount Master Creation !</button></a>
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
                                       <th data-toggle="offcanvas" width="50px;">SrNo</th>
                                       <th data-toggle="offcanvas" width="50px;">Action</th>
                                       <th data-toggle="offcanvas">Status</th>
                                       <th data-toggle="offcanvas">Discount Code</th>
                                       <th data-toggle="offcanvas">Discount Type</th>
                                       <th data-toggle="offcanvas">Group Name</th>
                                       <th data-toggle="offcanvas">Brand Name</th>
                                       <th data-toggle="offcanvas">Barcode No</th>
                                       <th data-toggle="offcanvas">Item Name</th>
                                       <th data-toggle="offcanvas">Store Name</th>
                                       <th data-toggle="offcanvas">Location Name</th>
                                       <th data-toggle="offcanvas">Discount %</th>
                                       <th data-toggle="offcanvas">Discount Amt</th>
                                       <th data-toggle="offcanvas">MRP</th>
                                       <th data-toggle="offcanvas">SP</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                       $query=mysqli_query($conn,"select * from tbl_discount_master"); 
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
                                          <a class=""onClick="return confirm('Are you sure you want to Update Discount Master?')" href="fh_discountmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                          <span
                                             data-toggle="tooltip" title="Are you sure you want to Update Discount Master" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i>
                                             <br>
                                             <span style="color: white;">EDIT</span>
                                          </span></a>
                                          <br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Discount Master List ?')" href="fh_discountmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Discount Master List" type="button"><i style="color:white;" class="fa fa-trash-o"></i><span style="color:white;"class="">DELETE</span></span></a>
                                          <br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Discount Master List ?')" href="fh_discountmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Discount Master List" type="button"><i style="color:white;" class="fa fa-list-alt"></i><span style="color:white;"class="">DETAILS</span></span></a>
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
                                       <td data-toggle="offcanvas"><?php echo $user['dis_code']; ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['distype_name'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['group_name'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['brand_name'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['barcode_no'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['item_name'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['store_name'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['location_name'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['discount_per'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['discount_amt'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['mrp'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['sp'],0,50); ?>
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