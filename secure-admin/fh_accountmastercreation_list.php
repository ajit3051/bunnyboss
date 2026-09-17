<?php include('top.php');?>
<?php

if(isset($_GET['delete_id']))
{
   $id=$_GET['delete_id'];
   $sqld="DELETE FROM tbl_account_master WHERE id='$id'";
   $res=mysqli_query($conn,$sqld);
   if($res)
   {
    header('refresh:.5; url=fh_accountmastercreation_list.php');
   }else{
    echo "<script>alert('Account Master Record Failed');</script>";
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
      <title>Account Master Creations || TEJASERP</title>
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
            
             <!-- Main content -->
            <section class="">

               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading">
                           
                              <div class="btn-group" id="buttonexport">
                              <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>Account Master List !</span></span>

                           </div>
                        </div>
                        
                        <div class="panel-body">
                           <div class="btn-group">
                              <a href="fh_accountmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i> Add New Account Master Creation</button></a>
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
                                                   <td style="background-color:#2A3F54" class="button-btn-btn-btn21">
                                                         <a class=""onClick="return confirm('Are you sure you want to Update Item Master?')" href="fh_accountmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                                            <span
                                                            data-toggle="tooltip" title="Are you sure you want to Update Account Name" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><br><span style="margin-left:0px; color: white;">EDIT</span>
                                                         </span></a><br>

                                          

                                          <a class="" onClick="return confirm('Are you sure you want to Delete Account Name List ?')" href="fh_accountmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">

                                             <span data-toggle="tooltip" title="Are you sure you want to Delete Account Name List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa-trash-o"></i><br><span style="margin-left:0px; color:white;"class="">DELETE</span></span></a>

                                             <a class="" onClick="return confirm('Are you sure you want to Details Item Name List ?')" href="fh_Detailsmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">

                                             <span data-toggle="tooltip" title="Are you sure you want to Delete Brand Name List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa fa-list-alt"></i><br><span style="margin-left:0px; color:white;">DETAILS</span></span></a>

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

