<?php include('top.php');
if(isset($_GET['update_id']))
{
  $id=$_GET['update_id'];
  $run=mysqli_query($conn,"select * from tbl_expensescreation_master where id='$id'");
  $user =mysqli_fetch_assoc($run);
}
///Update Group Master
if(isset($_POST['update']))
   {
    $msg="";
      $date = mysqli_real_escape_string($conn, $_POST['date']);
      $expcode = mysqli_real_escape_string($conn, $_POST['expcode']);
      $party_code = mysqli_real_escape_string($conn, $_POST['party_code']);
      $account_name = mysqli_real_escape_string($conn, $_POST['account_name']);
      $regular_name = mysqli_real_escape_string($conn, $_POST['regular_name']);
      $mobile_no = mysqli_real_escape_string($conn, $_POST['mobile_no']);
      $exphead_name = mysqli_real_escape_string($conn, $_POST['exphead_name']);
      $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
      $paymode_name = mysqli_real_escape_string($conn, $_POST['paymode_name']);
      $amount = mysqli_real_escape_string($conn, $_POST['amount']);


    $account_name = str_replace("'", "\'", $account_name);
    $account_name = str_replace('"', '\"', $account_name);

        
    $query="UPDATE tbl_expensescreation_master SET date='$date',expcode='$expcode', party_code='$party_code',account_name='$account_name',regular_name='$regular_name',mobile_no='$mobile_no', exphead_name='$exphead_name',remarks='$remarks',paymode_name='$paymode_name',amount='$amount' WHERE id='$id'";
    if (mysqli_query($conn,$query)){
        $msg='<div class="alert alert-success alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>ExpensesHead Name Update..!</strong>
                </div>';
                header('refresh:.5; url=fh_expensesheadmastercreation_list.php');
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
      <title>Expenses Creation Master Update || TEJASERP</title>
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
                        <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>Expenses Creations Edit  !</span></span>
                     </div>

                        <div class="panel-body">
                           <form method="post">
   <div class="row">
   <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
         <div id="expenses_cardbox1">
            <div class="statistic-box">
               <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
               <div class="counter-number pull-right">
                  <span style="font-size:13px; color:#808080;" class="count-number" data-toggle="tooltip" title="Total Upcoming Booking">500</span>
                  <span class="slight">
                     <i class="fa fa-play fa-rotate-270"></i>
                  </span>
               </div><br><br>
               <div class="pull-right">
                  <span style="font-size:20px; color:#808080;"><b>CASH !</b></span>
               </div>
               

               <div class="pull-left">
                  <span data-toggle="tooltip" title="Current Date Total Amount"><span style="font-size:20px; color:#808080;"><b>40000</b></span></span>
               </div>
                                          
            </div>
         </div>
      
   </div>

   <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
         <div id="expenses_cardbox2">
            <div class="statistic-box">
               <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
               <div class="counter-number pull-right">
                  <span style="font-size:13px; color:#808080;" class="count-number" data-toggle="tooltip" title="Total Upcoming Booking">500</span>
                  <span class="slight">
                     <i class="fa fa-play fa-rotate-270"></i>
                  </span>
               </div><br><br>
               <div class="pull-right">
                  <span style="font-size:20px; color:#808080;"><b>UPI !</b></span>
               </div>
               

               <div class="pull-left">
                  <span data-toggle="tooltip" title="Current Date Total Amount"><span style="font-size:20px; color:#808080;"><b>40000</b></span></span>
               </div>
                                          
            </div>
         </div>
      
   </div>

   <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
      <div id="expenses_cardbox3">
            <div class="statistic-box">
               <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
               <div class="counter-number pull-right">
                  <span style="font-size:13px; color:#808080;" class="count-number" data-toggle="tooltip" title="Total Upcoming Booking">500</span>
                  <span class="slight">
                     <i class="fa fa-play fa-rotate-270"></i>
                  </span>
               </div><br><br>
               <div class="pull-right">
                  <span style="font-size:20px; color:#808080;"><b>CARD !</b></span>
               </div>
               

               <div class="pull-left">
                  <span data-toggle="tooltip" title="Current Date Total Amount"><span style="font-size:20px; color:#808080;"><b>40000</b></span></span>
               </div>
                                          
            </div>
         </div>
   </div>

   <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
      <div id="expenses_cardbox4">
            <div class="statistic-box">
               <i style="font-size:16px;" class="fa fa-user-plus fa-3x"></i>
               <div class="counter-number pull-right">
                  <span style="font-size:13px; color:#808080;" class="count-number" data-toggle="tooltip" title="Total Upcoming Booking">500</span>
                  <span class="slight">
                     <i class="fa fa-play fa-rotate-270"></i>
                  </span>
               </div><br><br>
               <div class="pull-right">
                  <span style="font-size:20px; color:#808080;"><b>CREDIT !</b></span>
               </div>
               

               <div class="pull-left">
                  <span data-toggle="tooltip" title="Current Date Total Amount"><span style="font-size:20px; color:#808080;"><b>40000</b></span></span>
               </div>
                                          
            </div>
         </div>
   </div>
</div>
                             
               <div class="row">
                  <div class="">
                                             
                        <div class="panel-body">
                           <!-- Nav tabs -->
                           <ul class="nav nav-tabs">
                              <li class="active" style=""><a href="#tab1" data-toggle="tab">Debit || Payment</a></li>
                              <li><a href="#tab2" data-toggle="tab">Credit || Payment</a></li>
                           </ul>
                           <!-- Tab panels -->
                           <div class="tab-content">
                              <div class="tab-pane fade in active" id="tab1">
                                 <div class="panel-body">
                                    
                                       <table class="table table-bordered table-striped table-hover">
                                          <form method="post">
                              

                             <div class="row">
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Code</label><label style="color:red;">*</label>
                                        <input type="password" name="expcode" class="form-control" readonly value="<?php echo $user['expcode'];?>" />
                                    </div>
                                 </div>

                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Date</label><label style="color:red;">*</label>
                                       <input id='minMaxExample' type="text" name="date" id="date"  class="form-control" value="<?php echo $user['date'];?>" placeholder="Enter Date...">
                                    </div>
                                 </div>

                              </div>

                                 <div class="row">
                                    <div class="col-sm-3">
                                       <div class="form-group">
                                          <label>Party Code</label><label style="color:red;">*</label>
                                           <input type="text" class="form-control" name="party_code" id="party_code"  value="<?php echo $user['party_code'];?>" placeholder="Enter Your Group Name" required>
                                       </div>
                                    </div>
                                    <div class="col-sm-3">
               <div class="form-group"><label style="color:red;">*</label>
                  <label>Party Name</label>
                  <select name="account_name" class="form-control" required>
                  <option value="null">Select Party Name </option>
                  <?php
                  $query = mysqli_query($conn, "SELECT * FROM tbl_expensescreation_master");
                  while ($row = mysqli_fetch_array($query)) {
                  $selected = ($user['account_name'] == $row['account_name']) ? 'selected' : '';
                  echo "<option value='{$row['account_name']}' {$selected}>{$row['account_name']}</option>";
               }
               ?>
                  </select>
               </div>
            </div>

                                    <div class="col-sm-5">
                                       <div class="form-group">
                                          <label>Regural Name</label>
                                          <input type="text" class="form-control" name="regular_name" id="regular_name" value="<?php echo $user['regular_name'];?>" placeholder="Enter Your Regular Name" >
                                       </div>
                                    </div>
                                    
                                </div>



                                 <div class="row">
                                    <div class="col-sm-3">
                                       <div class="form-group">
                                       <label>Mobile No.</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="mobile_no" id="mobile_no" value="<?php echo $user['mobile_no'];?>" placeholder="Enter Your Mobile No" required>
                                       </div>
                                    </div>

                                    <div class="col-sm-4">
                                       <div class="form-group">
                                       <label>Expenses Head Name</label><label style="color:red;">*</label>
                                       <select name="exphead_name" class="form-control" required>
                                       <option value="null">Select ExpensesHead Name </option>
                                       <?php
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_expensescreation_master");
                                       while ($row = mysqli_fetch_array($query)) {
                                       $selected = ($user['exphead_name'] == $row['exphead_name']) ? 'selected' : '';
                                       echo "<option value='{$row['exphead_name']}' {$selected}>{$row['exphead_name']}</option>";
                                          }
                                            ?>
                                          </select>
                                    </div>
                                    </div>

                                    <div class="col-sm-5">
                                       <div class="form-group">
                                          <label>Remarks</label>
                                          <input type="text" class="form-control" name="remarks" id="remarks" value="<?php echo $user['remarks'];?>" placeholder="Enter Your Remarks" >
                                       </div>
                                    </div>

                                 </div>

                                <div class="row">
                                    <div class="col-sm-3">
                                       <div class="form-group">
                                       <label>Payment Mode</label><label style="color:red;">*</label>
                                       <select name="paymode_name" class="form-control" required>
                                       <option value="null">Select ExpensesHead Name </option>
                                       <?php
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_paymode_master");
                                       while ($row = mysqli_fetch_array($query)) {
                                       $selected = ($user['paymode_name'] == $row['paymode_name']) ? 'selected' : '';
                                       echo "<option value='{$row['paymode_name']}' {$selected}>{$row['paymode_name']}</option>";
                                          }
                                            ?>
                                          </select>
                                    </div>
                                    </div>

                                    <div class="col-sm-4">
                                       <div class="form-group">
                                          <label>Amount</label><label style="color:red;">*</label>
                                          <input type="text" class="form-control" name="amount" id="amount" value="<?php echo $user['amount'];?>" placeholder="Enter Your Amount" required>
                                       </div>
                                    </div>

                                 </div><br>
                                <div class="container-fluid">
                                 <div class="row">
                                    <a href="ssenterprises_accountmastercreation.php"><button type="Reset" name="Reset" class="btn btn-warning" style="width:100px;">Reset</button></a>
                                <button type="update" name="update" class="btn btn-success" style="width:100px;">Update</button>
                                 </div>
                              </div>
                                </form>
                             </table>
                          </div>
                       </div>

                        <div class="tab-pane fade" id="tab2">
                           <div class="panel-body">
                             <form method="post">
                             <div class="row">
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Code</label><label style="color:red;">*</label>
                                        <input type="password" name="expcode" class="form-control" readonly value="<?php echo $user['expcode'];?>" />
                                    </div>
                                 </div>

                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Date</label><label style="color:red;">*</label>
                                       <input id='minMaxExample' type="text" name="date" id="date"  class="form-control" value="<?php echo $user['date'];?>" placeholder="Enter Date...">
                                    </div>
                                 </div>

                              </div>

                                 <div class="row">
                                    <div class="col-sm-3">
                                       <div class="form-group">
                                          <label>Party Code</label><label style="color:red;">*</label>
                                           <input type="text" class="form-control" name="party_code" id="party_code"  value="<?php echo $user['party_code'];?>" placeholder="Enter Your Group Name" required>
                                       </div>
                                    </div>
                                    <div class="col-sm-3">
               <div class="form-group">
                  <label>Party Name</label>
                  <select name="account_name" class="form-control" required>
                  <option value="null">Select Party Name </option>
                  <?php
                  $query = mysqli_query($conn, "SELECT * FROM tbl_expensescreation_master");
                  while ($row = mysqli_fetch_array($query)) {
                  $selected = ($user['account_name'] == $row['account_name']) ? 'selected' : '';
                  echo "<option value='{$row['account_name']}' {$selected}>{$row['account_name']}</option>";
               }
               ?>
                  </select>
               </div>
            </div>

                                    <div class="col-sm-5">
                                       <div class="form-group">
                                          <label>Regural Name</label>
                                          <input type="text" class="form-control" name="regular_name" id="regular_name" value="<?php echo $user['regular_name'];?>" placeholder="Enter Your Regular Name" >
                                       </div>
                                    </div>
                                    
                                </div>



                                 <div class="row">
                                    <div class="col-sm-3">
                                       <div class="form-group">
                                       <label>Mobile No.</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="mobile_no" id="mobile_no" value="<?php echo $user['mobile_no'];?>" placeholder="Enter Your Mobile No" required>
                                       </div>
                                    </div>

                                    <div class="col-sm-4">
                                       <div class="form-group">
                                       <label>Expenses Head Name</label><label style="color:red;">*</label>
                                       <select name="exphead_name" class="form-control" required>
                                       <option value="null">Select ExpensesHead Name </option>
                                       <?php
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_expensescreation_master");
                                       while ($row = mysqli_fetch_array($query)) {
                                       $selected = ($user['exphead_name'] == $row['exphead_name']) ? 'selected' : '';
                                       echo "<option value='{$row['exphead_name']}' {$selected}>{$row['exphead_name']}</option>";
                                          }
                                            ?>
                                          </select>
                                    </div>
                                    </div>

                                    <div class="col-sm-5">
                                       <div class="form-group">
                                          <label>Remarks</label>
                                          <input type="text" class="form-control" name="remarks" id="remarks" value="<?php echo $user['remarks'];?>" placeholder="Enter Your Remarks" >
                                       </div>
                                    </div>

                                 </div>

                                <div class="row">
                                    <div class="col-sm-3">
                                       <div class="form-group">
                                       <label>Payment Mode</label><label style="color:red;">*</label>
                                       <select name="paymode_name" class="form-control" required>
                                       <option value="null">Select ExpensesHead Name </option>
                                       <?php
                                       $query = mysqli_query($conn, "SELECT * FROM tbl_paymode_master");
                                       while ($row = mysqli_fetch_array($query)) {
                                       $selected = ($user['paymode_name'] == $row['paymode_name']) ? 'selected' : '';
                                       echo "<option value='{$row['paymode_name']}' {$selected}>{$row['paymode_name']}</option>";
                                          }
                                            ?>
                                          </select>
                                    </div>
                                    </div>

                                    <div class="col-sm-4">
                                       <div class="form-group">
                                          <label>Amount</label><label style="color:red;">*</label>
                                          <input type="text" class="form-control" name="amount" id="amount" value="<?php echo $user['amount'];?>" placeholder="Enter Your Amount" required>
                                       </div>
                                    </div>

                                 </div><br>
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
                     
                  </div>
               </div>
            </section>
             <!-- Main content -->
            <section class="">

               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidrag">
                        <div class="panel-heading">
                        <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>ExpensesHead Master List !</span></span>

                           
                           

                        </div>

                        <div class="panel-body">
                           <div class="btn-group">
                                  <a href="fh_expensesmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Expenses Creation</button></a>
                                </div>
                                <?php include('include/button-export-to-data.php');?>
                                <div class="container-fluid">
                                  <div class="row">
                                 <input type="text" class="form-control-control" name="user_name" id="user_name" placeholder="Data Search" required>
                                  </div>
                                </div>  
                           <div class="table-responsive ">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th>SrNo</th>
                                       <th>Action</th>
                                       <th>Status</th>
                                       <th>ExpensesHead Code</th>
                                       <th>ExpensesHead Name</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                                $query=mysqli_query($conn,"select * from tbl_expenseshead_master"); 
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
                                          <a class=""onClick="return confirm('Are you sure you want to Update Expenses Master')" href="fh_expensesmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                          <span
                                             data-toggle="tooltip" title="Are you sure you want to Update Expenses Master" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><br><span style="margin-left:0px; color: white;">EDIT</span>
                                          </span></a><br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Expenses Master List ?')" href="fh_expensesmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Expenses Master List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa-trash-o"></i><span style="margin-left:0px; color:white;"class="">DELETE</span></span></a>
                                          <br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Details Item Name List ?')" href="fh_Detailsmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Expenses Name List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa fa-list-alt"></i><span style="margin-left:0px; color:white;">DETAILS</span></span></a>
                                          <br><br>
                                          <label class="switch1">
                                          <input type="checkbox">
                                          <span class="slider1 round"></span>
                                          </label>
                                       </td>
                                       </td>
                                       <td>
                                          <label class="switch1">
                                             <input type="checkbox">
                                             <span class="slider1 round"></span>
                                             </label>
                                          </td>
                                          <td><?php echo $user['exphead_code']; ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['exphead_name'],0,50); ?>
                                             
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

