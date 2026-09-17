<?php include('top.php');?>
<?php
    $msg='';
     if(isset($_POST['submit']))
     {
      $head_code = mysqli_real_escape_string($conn, $_POST['head_code']);
      $head_name = mysqli_real_escape_string($conn, $_POST['head_name']);
      $status    = mysqli_real_escape_string($conn, $_POST['status']);
      
      // $picture=$_POST['picture'];
      // $file1=rand(1111111,9999999).'_'.$_FILES['picture']['name'];
      // $file_tmp1=$_FILES['picture']['tmp_name'];
      // $data=[]; $data1=[]; $data2=[];$data3=[];
      // $data=[$file1];
      // $picture=implode(' ',$data);

      
    
          $dup=mysqli_query($conn,"select * from tbl_head_master where head_name='$head_name'");
            if(mysqli_num_rows($dup)>0)
            {
                $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Head Name Already exist!</strong>
                    </div>';
            }
            else{
                $query="Insert into tbl_head_master(head_code,head_name,status)values('$head_code','$head_name','$status')";
                $run = mysqli_query($conn,$query);   // ✅ EXECUTE QUERY
                if($run)
   {
    echo "
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        showPopup(
            'Success!',
            'Head Name Saved Successfully 🎉',
            
        );
    });
    </script>
    ";
                }
                else{
                    $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Head Name Creations Failed!</strong>
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
      <title>Head Master Creation || Fashion Hub</title>
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
             <?php include('include/fh-popup-message-successfully.php');?>
             <?php include('include/fh-form-scrolling-data-list.php');?>
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
               <div class="row" style="margin-bottom:5px;">
               <?php include('include/menu-header.php');?>
              
                </div>
            </div> 
            
            

            <!-- Main content -->
            <section class="container-fluid">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                  <div class="panel-heading" data-toggle= "offcanvas">
                        <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>Head Master Creations !</span></span>
                     </div>

                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_headmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Head Name List</button></a>
                           </div>
                           <form method="post">
                              <div class="form-group">
                                 <?php echo $msg; ?>
                             </div>
                             
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                                        <label>Head Code</label>
                                                        <input type="password" name="head_code" class="form-control" id="head_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_head_master ORDER BY id DESC");
                                                        $po_no = ($no) ? mysqli_fetch_assoc($no) : null;
                                                        $next_id = ($po_no && isset($po_no['id'])) ? ($po_no['id'] + 1) : 1;
                                                        echo 'HC-000' . $next_id;
                                                    ?>">
                                                </div>
                                             </div>
                                             <div class="col-sm-8">
                                                <div class="form-group">
                                                   <label>Head Name</label><label style="color:red;">*</label>
                                                   <input type="text" class="form-control" name="head_name" id="head_name" placeholder="Enter Your Head Name" required>
                                                </div>
                                             </div>
                                             
                                          </div>


                                          
                                          <div class="row">
                                             <div class="col-sm-4">
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
           <section class="container-fluid">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                  <div class="panel-heading" data-toggle= "offcanvas">
                        <span style="font-size: 15px;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><clipPath id="lineMdWatchTwotoneLoop0"><rect width="24" height="12"/></clipPath><symbol id="lineMdWatchTwotoneLoop1"><path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)"><animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z"/><animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3"/></path></symbol><mask id="lineMdWatchTwotoneLoop2"><use href="#lineMdWatchTwotoneLoop1"/><use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)"/><circle cx="12" cy="12" r="0" fill="#fff"><animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0"/></circle></mask></defs><rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)"/></svg><span>Head Master List !</span></span>
                     </div>

                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_headmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>Head Master Creation</button></a>
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
                                       <th data-toggle="offcanvas">Head Code</th>
                                       <th data-toggle="offcanvas">Head Name</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                                $query=mysqli_query($conn,"select * from tbl_head_master"); 
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
                                          <a class=""onClick="return confirm('Are you sure you want to Update Head Name Master?')" href="fh_headmastercreation_update.php?update_id=<?php echo $user['id']; ?>"><i class="fa fa-pencil" style="color:white;"></i><br>
                                          <label style="color:white;"> EDIT</label>
                                          </a>
                                          <br><br>
                                          <a class=""onClick="return confirm('Are you sure you want to Delete Head Name Master?')" href="fh_headmastercreation_list.php?delete_id=<?php echo $user['id']; ?>"><i class="fa fa-list-alt" style="color:white;"></i><br>
                                          <label style="color:white;"> DELETE</label>
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
                                          <td data-toggle="offcanvas"><?php echo $user['head_code']; ?>
                                             
                                          </td>
                                          <td data-toggle="offcanvas"><?php echo substr($user['head_name'],0,50); ?>
                                             
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

