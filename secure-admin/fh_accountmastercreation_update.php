<?php include('top.php');
if(isset($_GET['update_id']))
{
  $id=$_GET['update_id'];
  $run=mysqli_query($conn,"select * from tbl_account_master where id='$id'");
  $user =mysqli_fetch_assoc($run);
}
///Update Group Master
if(isset($_POST['update']))
   {
    $msg="";
      $date = mysqli_real_escape_string($conn, $_POST['date']);
      $account_code = mysqli_real_escape_string($conn, $_POST['account_code']);
      $account_name = mysqli_real_escape_string($conn, $_POST['account_name']);
      $contact_name = mysqli_real_escape_string($conn, $_POST['contact_name']);
      $mobile_no1 = mysqli_real_escape_string($conn, $_POST['mobile_no1']);
      $mobile_no2 = mysqli_real_escape_string($conn, $_POST['mobile_no2']);
      $phone_no = mysqli_real_escape_string($conn, $_POST['phone_no']);
      $fax_no = mysqli_real_escape_string($conn, $_POST['fax_no']);
      $gst_no = mysqli_real_escape_string($conn, $_POST['gst_no']);
      $pan_no = mysqli_real_escape_string($conn, $_POST['pan_no']);
      $location_name = mysqli_real_escape_string($conn, $_POST['location_name']);
      $address_1 = mysqli_real_escape_string($conn, $_POST['address_1']);
      $address_2 = mysqli_real_escape_string($conn, $_POST['address_2']);
      $unit = mysqli_real_escape_string($conn, $_POST['unit']);
      $unit_address = mysqli_real_escape_string($conn, $_POST['unit_address']);
      $tenure = mysqli_real_escape_string($conn, $_POST['tenure']);
      $email_id = mysqli_real_escape_string($conn, $_POST['email_id']);
      $description = mysqli_real_escape_string($conn, $_POST['description']);
      $hsn_code = mysqli_real_escape_string($conn, $_POST['hsn_code']);
      $area_sqft = mysqli_real_escape_string($conn, $_POST['area_sqft']);
      $gst_per = mysqli_real_escape_string($conn, $_POST['gst_per']);
      $ratepersqft_month = mysqli_real_escape_string($conn, $_POST['ratepersqft_month']);
      $total_amt = mysqli_real_escape_string($conn, $_POST['total_amt']);
      $opn_bal = mysqli_real_escape_string($conn, $_POST['opn_bal']);
      $picture = mysqli_real_escape_string($conn, $_POST['picture']);
      $gender = mysqli_real_escape_string($conn, $_POST['gender']);
      $status = mysqli_real_escape_string($conn, $_POST['status']);

    $account_name = str_replace("'", "\'", $account_name);
    $account_name = str_replace('"', '\"', $account_name);
    
    $query="UPDATE tbl_account_master SET account_code='$account_code', account_name='$account_name',contact_name='$contact_name',mobile_no1='$mobile_no1',mobile_no2='$mobile_no2',phone_no='$phone_no',fax_no='$fax_no', email_id='$email_id', gst_no='$gst_no', pan_no='$pan_no', location_name='$location_name',address_1='$address_1', address_2='$address_2',unit='$unit', unit_address='$unit_address',tenure='$tenure',description='$description', hsn_code='$hsn_code',area_sqft='$area_sqft',gst_per='$gst_per',ratepersqft_month='$ratepersqft_month', total_amt='$total_amt',opn_bal='$opn_bal',status='$status' WHERE id='$id'";
    if(mysqli_query($conn,$query)){
        $msg='<div class="alert alert-success alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Account Name Update..!</strong>
                </div>';
                header('refresh:.5; url=ssenterprises_accountmastercreation.php');
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
      <title>Account Master Edit || TEJASERP</title>
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
                     <div class="panel panel-bd lobidrag">
                        <div class="panel-heading">
                           
                                                       
                             
                            <div class="btn-group" id="buttonexport">
                              <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>Account Master Edit !</span></span>

                           </div>
                           
                        </div>
                        <div class="panel-body">
                            <div class="btn-group">
                              <a href="fh_accountmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Account Master Creation</button></a>
                           </div>
                            <div class="btn-group">
                              <a href="fh_accountmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Account Master List</button></a>
                           </div>
                           <form method="post">
                              <!-- <div class="form-group">
                                 <?php echo $msg; ?>
                             </div> -->
                              <div class="row">
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                            <label>Account Code</label><label style="color:red;">*</label>
                                            <input type="password" name="account_code" class="form-control" readonly value="<?php echo $user['account_code'];?>" />
                                            </div>
                                         </div>

                                         <div class="col-sm-5">
                                          <div class="form-group">
                                          <label>Account Name</label><label style="color:red;">*</label>
                                          <input type="text" class="form-control" name="account_name" id="account_name"  value="<?php echo $user['account_name'];?>" placeholder="Enter Size Name" required>
                                       </div>
                                    </div>

                                    <div class="col-sm-5">
                                       <div class="form-group">
                                       <label>Contact Name</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="contact_name" id="contact_name"  value="<?php echo $user['contact_name'];?>" placeholder="Enter Size Name" required>
                                    </div>
                                 </div>

                              </div>

                        <div class="row">
                           <div class="col-sm-2">
                               <div class="form-group">
                              <label>Date</label><label style="color:red;">*</label>
                              <input id='minMaxExample' type="text" name="date" class="form-control"  value="<?php echo $user['date'];?>" />
                               </div>
                            </div>

                            <div class="col-sm-5">
                              <div class="form-group">
                                 <label>Mobile:1</label><label style="color:red;">*</label>
                                 <input type="text" class="form-control" name="mobile_no1" id="mobile_no1"  value="<?php echo $user['mobile_no1'];?>" placeholder="Enter Mobile No" required>
                              </div>
                           </div>
                                         
                           <div class="col-sm-5">
                              <div class="form-group">
                              <label>Mobile:2</label>
                              <input type="text" class="form-control" name="mobile_no2" id="mobile_no2"  value="<?php echo $user['mobile_no2'];?>" placeholder="Enter Mobile No" >
                           </div>
                        </div>

                     </div>

                        

                        <div class="row">
                           <div class="col-sm-4">
                                    <div class="form-group">
                                                <label>Phone No</label>
                                                <input type="text" class="form-control" name="phone_no" id="phone_no"  value="<?php echo $user['phone_no'];?>" placeholder="Enter Phone Name" >
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
                                                <label>GST No</label><label style="color:red;">*</label>
                                                <input type="text" class="form-control" name="gst_no" id="gst_no"  value="<?php echo $user['gst_no'];?>" placeholder="Enter GST No" required>
                                            </div>
                                         </div>

                                      </div>

         <div class="row">
            <div class="col-sm-4">
               <div class="form-group">
                  <label>Party Type</label><label style="color:red;">*</label>
                  <select name="partytype_name" class="form-control" required>
                  <option value="null">Select Party Type</option>
                  <?php
                  $query = mysqli_query($conn, "SELECT * FROM tbl_partytype_master");
                  while ($row = mysqli_fetch_array($query)) {
                  $selected = ($user['partytype_name'] == $row['partytype_name']) ? 'selected' : '';
                  echo "<option value='{$row['partytype_name']}' {$selected}>{$row['partytype_name']}</option>";
               }
               ?>
            </select>
         </div>
      </div>

      <div class="col-sm-4">
         <div class="form-group">
         <label>Customer Type</label><label style="color:red;">*</label>
         <select name="customertype_name" class="form-control" required>
         <option value="null">Select Customer Type</option>
         <?php
         $query = mysqli_query($conn, "SELECT * FROM tbl_customertype_master");
         while ($row = mysqli_fetch_array($query)) {
         $selected = ($user['customertype_name'] == $row['customertype_name']) ? 'selected' : '';
         echo "<option value='{$row['customertype_name']}' {$selected}>{$row['customertype_name']}</option>";
      }
      ?> 
   </select>
</div>
</div>

<div class="col-sm-4">
   <div class="form-group"><label style="color:red;">*</label>
      <label>Location</label>
      <select name="location_name" class="form-control" required>
      <option value="null">Select Location</option>
      <?php
      $query = mysqli_query($conn, "SELECT * FROM tbl_location_master");
      while ($row = mysqli_fetch_array($query)) {
      $selected = ($user['location_name'] == $row['location_name']) ? 
      'selected' : '';
      echo "<option value='{$row['location_name']}' 
      {$selected}>{$row['location_name']}</option>";
   }
   ?>
</select>
</div>
</div>

</div>

                        <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                                <label>Address:1</label><label style="color:red;">*</label>
                                                <input type="text" class="form-control" name="address_1" id="address_1"  value="<?php echo $user['address_1'];?>" placeholder="Enter Address" required>
                                            </div>
                                         </div>
                                         
                           <div class="col-sm-6">
                                    <div class="form-group">
                                                <label>Address:2</label>
                                                <input type="text" class="form-control" name="address_2" id="address_2"  value="<?php echo $user['address_2'];?>" placeholder="Enter Address" >
                                            </div>
                                         </div>

                              
                           
                        </div>


                        <div class="row">
                                 <div class="col-sm-6">
                                    <div class="form-group">
                                                <label>Opening Bal.</label><label style="color:red;">*</label>
                                                <input type="text" class="form-control" name="opn_bal" id="opn_bal"  value="<?php echo $user['opn_bal'];?>" placeholder="Enter Opening Bal." required>
                                            </div>
                                         </div>
                                         
                                                      
                           
                        </div>

                        
                      

                        <div class="row">
                                                                          
                           <div class="col-sm-4">
                                    <div class="form-group">
                                                <label>Picture upload</label>
                                                   <input type="file" name="picture" id="picture">
                                            </div>
                                         </div>
                                         <div class="col-sm-4">
                                    <div class="form-group">
                                                <label>Gender</label><label style="color:red;">*</label><br>
                                                   <label class="radio-inline"><input name="gender" value="1" checked="checked" type="radio">Male</label> 
                                                   <label class="radio-inline"><input name="gender" value="0" type="radio">Female</label>
                                            </div>
                                         </div>
                                         <div class="col-sm-4">
                                    <div class="form-group">
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
                     <div class="panel panel-bd lobidrag">
                        <div class="panel-heading">
                           
                           <!-- <div class="btn-group" id="buttonlist"> 
                              <a class="btn btn-add " href="clist.html"> 
                              <span style="font-size: 13px;"><i class="fa fa-plus"></i> User master Creations</span> </a>

                           </div> -->
                            
                             
                            <div class="btn-group" id="buttonexport" >
                              <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>Account Master List !</span></span>
                           </div> 

                        </div>
                        
                        <div class="panel-body">
                        <!-- Plugin content:powerpoint,txt,pdf,png,word,xl -->
                          <div class="btn-group">
                              <a href="fh_accountmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i> Add New Account Master Creation</button></a>
                           </div>
                           <?php include('include/button-export-to-data.php');?>
                        <div class="container-fluid">
                              <div class="row">
                                 <input type="text" class="form-control-control" name="company_name" id="company_name" placeholder="Data Search" required>
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
                                       <th>Date</th>
                                       <th>A/C Code</th>
                                       <th>A/C Name</th>
                                       <th>Contact Name</th>
                                       <th>Picture</th>
                                       <th>Mobile No:1</th>
                                       <th>Mobile No:2</th>
                                       <th>Phone No</th>
                                       <th>Email-ID</th>
                                       <th>GST No</th>
                                       <th>Party Type</th>
                                       <th>Customer Type</th>
                                       <th>Location</th>
                                       <th>Address-1</th>
                                       <th>Address-2</th>
                                       <th>Opening Bal.</th>
                                       <th>Gender</th>
                                      
                                       
                                        

                                       

                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                                $query=mysqli_query($conn,"select * from tbl_account_master"); 
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

                                                         <a class=""onClick="return confirm('Are you sure you want to Update Account Master?')" href="fh_accountmastercreation_update.php?update_id=<?php echo $user['id']; ?>"><i class="fa fa-pencil" style="color:white;"></i>
                                                            <br>
                                                            <label style="color: white;">EDIT</label>
                                                            </a>
                                                            <label class="switch1">
                                             <input type="checkbox">
                                             <span class="slider1 round"></span>
                                             </label>

                                          
                                             

                                             
                                          </td>
                                          <td><?php echo substr($user['status'],0,50); ?></td>

                                          <td>
                                             <?php echo $user['date']; ?>
                                                
                                             </td>

                                          <td>
                                             <?php echo $user['account_code']; ?>
                                                
                                             </td>
                                          <td>
                                             <?php echo $user['account_name']; ?>
                                                
                                             </td>
                                             <td><?php echo substr($user['contact_name'],0,50); ?></td>

                                                   <td><img src="assets/dist/img/w1.png" class="img-circle" alt="User Image" width="50" height="50"> </td>
                                                   
                                                   
                                                   <td><?php echo substr($user['mobile_no1'],0,50); ?></td>
                                                   <td><?php echo substr($user['mobile_no2'],0,50); ?></td>
                                                   <td><?php echo substr($user['phone_no'],0,50); ?></td>
                                                   <td><?php echo substr($user['email_id'],0,50); ?></td>
                                                   <td><?php echo substr($user['gst_no'],0,50); ?></td>
                                                   <td><?php echo substr($user['partytype_name'],0,50); ?></td>
                                                    <td><?php echo substr($user['customertype_name'],0,50); ?></td>
                                                    <td><?php echo substr($user['location_name'],0,50); ?></td>
                                                   <td><?php echo substr($user['address_1'],0,50); ?></td>
                                                    <td><?php echo substr($user['address_2'],0,50); ?></td>
                                                    <td><?php echo substr($user['opn_bal'],0,50); ?></td>
                                                   <td><?php echo substr($user['gender'],0,50); ?></td>
                                                   
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

