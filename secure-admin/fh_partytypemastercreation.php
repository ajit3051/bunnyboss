<?php include('top.php');?>
<?php
    $msg='';
     if(isset($_POST['submit']))
     {
      $partytype_code = mysqli_real_escape_string($conn, $_POST['partytype_code']);
      $partytype_name = mysqli_real_escape_string($conn, $_POST['partytype_name']);
      $status = mysqli_real_escape_string($conn, $_POST['status']);
      
      // $picture=$_POST['picture'];
      // $file1=rand(1111111,9999999).'_'.$_FILES['picture']['name'];
      // $file_tmp1=$_FILES['picture']['tmp_name'];
      // $data=[]; $data1=[]; $data2=[];$data3=[];
      // $data=[$file1];
      // $picture=implode(' ',$data);

      
    
          $dup=mysqli_query($conn,"select * from tbl_partytype_master where partytype_name='$partytype_name'");
            if(mysqli_num_rows($dup)>0)
            {
                $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>PartyType Name Already exist!</strong>
                    </div>';
            }
            else{
                $query="Insert into tbl_partytype_master(partytype_code,partytype_name,status)values('$partytype_code','$partytype_name','$status')";
                if(mysqli_query($conn,$query)){
                    $msg='<div class="alert alert-success alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>PartyType Name Creations Success..!</strong>
                  </div>';
                }
                else{
                    $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>PartyType Name Creations Failed!</strong>
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
      <title>Party Type Master Creation || TEJASERP</title>
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
                  <div class="panel-heading">
                        <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>Party Type Master Creations !</span></span>
                     </div>

                        <div class="panel-body">
                           <div class="btn-group">
                              <a href="fh_partytypemastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Party Type Master List</button></a>
                           </div>
                           <form method="post">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                             </div>
                             
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                                        <label>PartyType Code</label>
                                                        <input type="password" name="partytype_code" class="form-control" id="partytype_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_partytype_master ORDER BY id DESC");
                                                        $po_no = ($no) ? mysqli_fetch_assoc($no) : null;
                                                        $next_id = ($po_no && isset($po_no['id'])) ? ($po_no['id'] + 1) : 1;
                                                        echo 'PTC-000' . $next_id;
                                                    ?>">
                                                </div>
                                             </div>
                                             <div class="col-sm-8">
                                                <div class="form-group">
                                                   <label>PartyType Name</label>
                                                   <input type="text" class="form-control" name="partytype_name" id="partytype_name" placeholder="Enter Your PartyType Name" required>
                                                </div>
                                             </div>
                                             
                                          </div>
                                          
                                          <div class="row">
                                             <div class="col-sm-4">
                                                <div class="form-check">
                                                   <label>Status</label><br>
                                                   <label class="radio-inline">
                                                      <input type="radio" name="status" value="true" checked="checked">Active</label>
                                                      <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                                   </div>
                                                </div>
                                             </div><br>
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
              
             <!-- Main content -->
           <section class="">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                  <div class="panel-heading">
                        <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>PartyType Master List !</span></span>
                     </div>

                        <div class="panel-body">
                           <div class="btn-group">
                              <a href="fh_partytypemastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Party Type Master Creation</button></a>
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
                                       <th>PartyType Code</th>
                                       <th>party Type</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                                $query=mysqli_query($conn,"select * from tbl_partytype_master"); 
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
                                          <a class=""onClick="return confirm('Are you sure you want to Update Party Type Master')" href="fh_partytypemastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                          <span
                                             data-toggle="tooltip" title="Are you sure you want to Update Party Type Master" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><br><span style="margin-left:0px; color: white;">EDIT</span>
                                          </span></a><br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Party Type Master List ?')" href="fh_partytypemastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Party Type Master List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa-trash-o"></i><span style="margin-left:0px; color:white;"class="">DELETE</span></span></a>
                                          <br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Details Item Name List ?')" href="fh_Detailsmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Party Type Name List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa fa-list-alt"></i><span style="margin-left:0px; color:white;">DETAILS</span></span></a>
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
                                          <td><?php echo $user['partytype_code']; ?>
                                             
                                          </td>
                                          <td><?php echo substr($user['partytype_name'],0,50); ?>
                                             
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

