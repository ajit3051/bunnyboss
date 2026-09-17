<?php include('top.php');?>
<?php

if(isset($_GET['delete_id']))
{
   $id=$_GET['delete_id'];
   $sqld="DELETE FROM tbl_salesman_master WHERE id='$id'";
   $res=mysqli_query($conn,$sqld);
   if($res)
   {
    header('refresh:.5; url=fh_salesmanmastercreation_list.php');
   }else{
    echo "<script>alert('Salesman Master Record Failed');</script>";
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
      <title>SalesMan Master List || Fashion Hub</title>
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

         <?php include('include/notification.php');?> 
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
            <section class="content-header">
                <?php include('include/fh_menuheader.php');?>
                     
                  </section>
           <div class="container-fluid">
               <div class="row">
               <?php include('include/menu-header.php');?>
               <div class="pull-right"style=" margin-right: 15px;margin-bottom: 5px; margin-top: 6px;">
                        <a href="account-master-list.php"><button data-toggle="tooltip" title="Account Master List !" class="button-btn-btn-btn">Next !</button></a>
                        </div>
                        <div class="pull-left" style="margin-bottom: 5px;margin-left: 15px; margin-top: 6px;">
                        <a href="account-master-list.php"><button data-toggle="tooltip" title="Account Master List !" class="button-btn-btn">Previous !</button></a>
                        </div>
                </div>
            </div> 
            
            

            <!-- Main content -->
            
              
             <!-- Main content -->
           <section class="container-fluid">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                  <div class="panel-heading">
                        <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>SalesMan Master List !</span></span>
                     </div>

                        <div class="panel-body">
                           <?php include('include/button-export-to-data.php');?>
                         <?php include('include/searchbox.php');?>
                           <div class="table-responsive ">
                              <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                                 <thead>
                                    <tr class="info">
                                       <th>SrNo</th>
                                       <th>Action</th>
                                       <th>Status</th>
                                       <th>SalesMan Code</th>
                                       <th>SalesMan Name</th>
                                       <th>Mobile No</th>
                                       <th>Store Name</th>
                                       <th>Location</th>
                                       <th>Picture</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                                $query=mysqli_query($conn,"select * from tbl_salesman_master"); 
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
                                               <td style="background-color:#2A3F54">
                                                         <a class=""onClick="return confirm('Are you sure you want to Update SalesMan Name?')" href="fh_salesmanmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                                            <span
                                                            data-toggle="tooltip" title="Are you sure you want to Update SalesMan Name" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><span style="margin-left:5px; color: white;">Edit</span>
                                                         </span></a>

                                          

                                          <a class="" onClick="return confirm('Are you sure you want to Delete SalesMan Name List ?')" href="fh_salesmanmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">

                                             <span data-toggle="tooltip" title="Are you sure you want to Delete SalesMan Name List" type="button" class="" ><i style="margin-left: 15px; color:white;" class="fa fa-trash-o"></i><span style="margin-left:5px; color:white;"class="">Delete</span></span></a>

                                             
                                          </td>
                                       </td>
                                       <td>
                                          <label class="switch1">
                                             <input type="checkbox">
                                             <span class="slider1 round"></span>
                                             </label>
                                          </td>
                                          <td><?php echo $user['salesman_code']; ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['salesman_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo $user['mobile_no']; ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['store_name'],0,50); ?>
                                             
                                          </td>
                                          <td><?php echo $user['location_name']; ?>
                                             
                                          </td>
                                          <td><img src="assets/dist/img/w1.png" class="img-circle" alt="User Image" width="50" height="50"> </td>
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

