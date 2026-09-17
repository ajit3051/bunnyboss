<?php include('top.php');
if(isset($_GET['update_id']))
{
  $id=$_GET['update_id'];
  $run=mysqli_query($conn,"select * from tbl_discount_master where id='$id'");
  $user =mysqli_fetch_assoc($run);
}
///Update Group Master
if(isset($_POST['update']))
   {
    $msg="";
      $dis_code = mysqli_real_escape_string($conn, $_POST['dis_code']);
      $distype_name = mysqli_real_escape_string($conn, $_POST['distype_name']);
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
      

    $group_name = str_replace("'", "\'", $group_name);
    $group_name = str_replace('"', '\"', $group_name);
    
    $query="UPDATE tbl_discount_master SET dis_code='$dis_code', distype_name='$distype_name',group_name='$group_name',brand_name='$brand_name',barcode_no='$barcode_no',item_name='$item_name',store_name='$store_name', location_name='$location_name', discount_per='$discount_per', discount_amt='$discount_amt', mrp='$mrp',sp='$sp', status='$status' WHERE id='$id'";
    if(mysqli_query($conn,$query)){
        $msg='<div class="alert alert-success alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Discount Master Update..!</strong>
                </div>';
                header('refresh:.5; url=fh_discountmastercreation_list.php');
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
      <title>Discount Master Edit || TEJASERP</title>
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
             <?php include('include/menu-header.php');?>
            
                      
            <!-- Main content -->
            <section class="">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           <div class="btn-group" id="buttonexport">
                              <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>Discount Master Edit !</span></span>

                           </div>
                           
                        </div>
                        <div class="panel-body">
                           <form method="post">
                              <!-- <div class="form-group">
                                 <?php echo $msg; ?>
                             </div> -->
                              <div class="row">
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                            <label>Item Code</label><label style="color:red;">*</label>
                                            <input type="password" name="dis_code" class="form-control" readonly value="<?php echo $user['dis_code'];?>" />
                                         </div>
                                      </div>

                                      <div class="col-sm-3">
                                       <div class="form-group">
                                          <label>Discount Type</label><label style="color:red;">*</label>
                                          <select name="distype_name" class="form-control" required>
                                             <option value="null">Select Discount Type
                                             </option>
                                             <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_distype_master");
                                                        while ($row = mysqli_fetch_array($query)) {
                                                        $selected = ($user['distype_name'] == $row['distype_name']) ? 'selected' : '';
                                                        echo "<option value='{$row['distype_name']}' {$selected}>{$row['distype_name']}</option>";
                                                     }
                                                     ?>
                                                    </select>
                                                 </div>
                                              </div>

                                              <div class="col-sm-3">
               <div class="form-group">
                  <label>Group || Category Name</label><label style="color:red;">*</label>
                  <select name="group_name" class="form-control" required>
                                                        <option value="null">Select Group || Category Nmae 
                                                        </option>
                                                        <?php
                                                        $query = mysqli_query($conn, "SELECT * FROM tbl_group_master");
                                                        while ($row = mysqli_fetch_array($query)) {
                                                        $selected = ($user['group_name'] == $row['group_name']) ? 'selected' : '';
                                                        echo "<option value='{$row['group_name']}' {$selected}>{$row['group_name']}</option>";
                                                     }
                                                     ?>
                                                    </select>
                                                 </div>
                                              </div>

                                              <div class="col-sm-3">
                                                 <div class="form-group">
                                                  <label>Brand Name</label>
                                                    <select name="brand_name" class="form-control" >
                                                        <option value="null">Select Brand Name</option>
                                                        <?php
                                                        $query = mysqli_query($conn, "SELECT * FROM tbl_brand_master");
                                                        while ($row = mysqli_fetch_array($query)) {
                                                        $selected = ($user['brand_name'] == $row['brand_name']) ? 'selected' : '';
                                                        echo "<option value='{$row['brand_name']}' {$selected}>{$row['brand_name']}</option>";
                                                     }
                                                     ?> 
                                                    </select>
                                                 </div>
                                              </div>


                                           </div>
<div class="row">
   <div class="col-sm-3">
      <div class="form-group">
         <label>Barcode No</label><label style="color:red;">*</label>
         <input type="text" class="form-control" name="barcode_no" id="barcode_no"  value="<?php echo $user['barcode_no'];?>" placeholder="Enter Barcode No" required>
      </div>
   </div>

   <div class="col-sm-3">
      <div class="form-group">
         <label>Item Name</label><label style="color:red;">*</label>
         <select name="item_name" class="form-control" required>
         <option value="null">Select Item Name</option>
         <?php
         $query = mysqli_query($conn, "SELECT * FROM tbl_item_master");
         while ($row = mysqli_fetch_array($query)) {
         $selected = ($user['item_name'] == $row['item_name']) ? 'selected' : '';
         echo "<option value='{$row['item_name']}' {$selected}>{$row['item_name']}</option>";
      }
      ?>
   </select></div></div>

   <div class="col-sm-3">
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
   </select></div></div>

   <div class="col-sm-3">
      <div class="form-group">
         <label>Location Name</label><label style="color:red;">*</label>
         <input type="text" class="form-control" name="location_name" id="location_name"  value="<?php echo $user['location_name'];?>" placeholder="Enter Location Name" required>
      </div>
   </div>

</div>

<div class="row">
   <div class="col-sm-3">
      <div class="form-group">
         <label>Discount Percentage</label><label style="color:red;">*</label>
         <input type="text" class="form-control" name="discount_per" id="discount_per"  value="<?php echo $user['discount_per'];?>" placeholder="Enter Discount Percentage" required>
      </div>
   </div>

   <div class="col-sm-3">
      <div class="form-group">
         <label>Discount Amount</label><label style="color:red;">*</label>
         <input type="text" class="form-control" name="discount_amt" id="discount_amt"  value="<?php echo $user['discount_amt'];?>" placeholder="Enter Discount Amt" required>
      </div>
   </div>

   <div class="col-sm-3">
      <div class="form-group">
         <label>MRP</label><label style="color:red;">*</label>
         <input type="text" class="form-control" name="mrp" id="mrp"  value="<?php echo $user['mrp'];?>" placeholder="Enter MRP">
      </div>
   </div>

   <div class="col-sm-3">
      <div class="form-group">
         <label>SP</label><label style="color:red;">*</label>
         <input type="text" class="form-control" name="sp" id="sp"  value="<?php echo $user['sp'];?>" placeholder="Enter SP">
      </div>
   </div>

</div>
         

                                           <div class="row">
                                             <div class="col-sm-3">
                                                <div class="form-group">
                                                   <label>Picture upload</label>
                                                   <input type="file" name="picture" id="picture">
                                                   <!-- <input type="hidden" name="old_picture"> -->
                                                </div>
                                             </div>
                                             
                                             <div class="col-sm-3">
                                                <div class="form-check">
                                                   <label>Status</label><label style="color:red;">*</label><br>
                                                   <label class="radio-inline">
                                                      <input type="radio" name="status" value="true" checked="checked">Active</label>
                                                      <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                                   </div>
                                                </div>
                                             </div>

                        
                        




                        
                              <div class="container-fluid">
                                 <div class="row">
                                    <a href="ssenterprises_accountmastercreation.php"><button type="Reset" name="Reset" class="btn btn-warning" style="width:100px;">Reset</button></a>
                                <button type="update" name="update" class="btn btn-success" style="width:100px;">Update</button>
                                 </div>
                                 


                              </div>
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
                        <div class="panel-heading">
                           
                           <!-- <div class="btn-group" id="buttonlist"> 
                              <a class="btn btn-add " href="clist.html"> 
                              <span style="font-size: 13px;"><i class="fa fa-plus"></i> User master Creations</span> </a>

                           </div> -->
                            
                             
                            <div class="btn-group" id="buttonexport" >
                              <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>Discount Master List !</span></span>
                           </div> 

                        </div>
                        
                        <div class="panel-body">
                        <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
                         <div class="btn-group">
                              <a href="fh_discountmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Discount Master Creation</button></a>
                           </div>
                           <?php include('include/button-export-to-data.php');?>
                           <div class="container-fluid">
                              <div class="row">
                                 <input type="text" class="form-control-control" name="company_name" id="company_name" placeholder="Search" required>
                              </div>
                           </div>
                        
                           <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
                           <div class="table-responsive ">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">

                                       <th>SrNo</th>
                                       <th>Action</th>
                                       <th>Status</th>
                                       <th>Discount Code</th>
                                       <th style="width:0px;">Discount Type</th>
                                       <th style="width:0px;">Group Name</th>
                                       <th style="width:0px;">Brand Name</th>
                                       <th style="width:0px;">Barcode No</th>
                                       <th style="width:0px;">Item Name</th>
                                       <th style="width:0px;">Store Name</th>
                                       <th style="width:0px;">Location Name</th>
                                       <th style="width:0px;">Discount %</th>
                                       <th style="width:0px;">Discount Amt</th>
                                       <th style="width:0px;">MRP</th>
                                       <th style="width:0px;">SP</th>
                                      
                                       
                                        

                                       

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
                                                   <td style="background-color:Orange;" class="button-btn-btn-btn1">

                                                         <a class=""onClick="return confirm('Are you sure you want to Update Discount Master?')" href="fh_discountmastercreation_update.php?update_id=<?php echo $user['id']; ?>"><i class="fa fa-pencil" style="color:white;"></i>
                                                            <br>
                                                            <label style="color: white;">EDIT</label>
                                                            </a>
                                                            <label class="switch1">
                                             <input type="checkbox">
                                             <span class="slider1 round"></span>
                                             </label>

                                          
                                             

                                             
                                          </td>
                                          <td>
                                             <label class="switch1">
                                             <input type="checkbox">
                                             <span class="slider1 round"></span>
                                             </label>
                                          </td>
                                          <td><?php echo $user['dis_code']; ?>
                                             
                                          </td>
                                          
                                           <td><?php echo substr($user['distype_name'],0,50); ?>
                                             
                                          </td>
                                           <td><?php echo substr($user['group_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['brand_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['barcode_no'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['item_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['store_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['location_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['discount_per'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['discount_amt'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['mrp'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['sp'],0,50); ?>
                                             
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
                                    </div><br>
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
                                  
                                 </div><br>
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
                                  
                                 </div><br>
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
                                  
                                 </div><br>
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
                                  
                                 </div><br>
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
                                 
                                 </div><br>
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
                                 
                                 </div><br>
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
                                 
                                 </div><br>
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

