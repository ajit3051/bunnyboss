<?php include('top.php');
   if(isset($_GET['update_id']))
   {
     $id=$_GET['update_id'];
     $run=mysqli_query($conn,"select * from tbl_user_master where id='$id'");
     $user =mysqli_fetch_assoc($run);
   }
   ///Update Group Master
   if(isset($_POST['update']))
      {
       $msg="";
         $user_code = mysqli_real_escape_string($conn, $_POST['user_code']);
         $user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
         $mobile_no = mysqli_real_escape_string($conn, $_POST['mobile_no']);
         $store_name = mysqli_real_escape_string($conn, $_POST['store_name']);
         $location = mysqli_real_escape_string($conn, $_POST['location']);
         $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
         $password = mysqli_real_escape_string($conn, $_POST['password']);
         $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
         $date_of_joining = mysqli_real_escape_string($conn, $_POST['date_of_joining']);
         $gender = mysqli_real_escape_string($conn, $_POST['gender']);
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
    if (isset($file) && $file['error'] == 0) {
   
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
   
        if (!in_array($ext, $allowed)) {
            return "";
        }
   
        $filename = time() . "_" . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $path . $filename);
   
        return $filename;
    }
    return "";
   }
   
   // Upload new image
   $new_image = uploadFile($_FILES['picture'], $path);
   
   // Keep old image if no new upload
   $old_picture = isset($user['picture']) ? $user['picture'] : '';
   $picture = !empty($new_image) ? $new_image : $old_picture;
   
   
   
   
   
       $user_name = str_replace("'", "\'", $user_name);
       $user_name = str_replace('"', '\"', $user_name);
       
       $query="UPDATE tbl_user_master SET user_code='$user_code', user_name='$user_name',mobile_no='$mobile_no', store_name='$store_name',location='$location', user_type='$user_type', password='$password',confirm_password='$confirm_password', date_of_joining='$date_of_joining',picture='$picture', gender='$gender', status='$status' WHERE id='$id'";
       if(mysqli_query($conn,$query)){
           $msg='<div class="alert alert-success alert-dismissible fade1 show">
                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                       <strong>User Master Name Update..!</strong>
                   </div>';
                   header('refresh:.5; url=fh_usermastercreation_list.php');
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
      <title>User Master Update</title>
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
             <?php include('include/fh-popup-message-successfully.php');?>
            <?php include('include/fh-form-scrolling-data-list.php');?>
            <section class="">
               <div class="row">
                  <!-- Form controls -->
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading "data-toggle="offcanvas">
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
                              <span>User Master Edit !</span>
                           </span>
                        </div>
                        <div class="panel-body">
                           <div class="btn-group">
                              <a href="fh_usermastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>User Master Creation</button></a>
                              <a href="fh_usermastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>User Master List</button></a>
                           </div>
                           <form method="post" enctype="multipart/form-data">
                              <!-- <div class="form-group">
                                 <?php echo $msg; ?>
                                 </div> -->
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>User Code</label>
                                       <input type="password" name="user_code" class="form-control" readonly value="<?php echo $user['user_code'];?>" />
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>User Name</label>
                                       <input type="text" class="form-control" name="user_name" id="user_name"  value="<?php echo $user['user_name'];?>" placeholder="Enter Your User Name" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Mobile No</label>
                                       <input type="text" class="form-control" name="mobile_no" id="mobile_no"  value="<?php echo $user['mobile_no'];?>" placeholder="Enter Your Mobile No" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Store Name</label>
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
                                    <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Store Name</span>
                                    </a>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Location</label>
                                       <input type="text" class="form-control" name="location" id="location"  value="<?php echo $user['location'];?>" placeholder="Enter Your Location" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>User Type</label>
                                       <select name="user_type" class="form-control" required>
                                          <option value="null">Select User Type</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_user_master");
                                             while ($row = mysqli_fetch_array($query)) 
                                             {
                                             $selected = ($user['user_type'] == $row['user_type']) ? 'selected' : '';
                                             echo "<option value='{$row['user_type']}' {$selected}>{$row['user_type']}</option>";
                                             }
                                             ?> 
                                       </select>
                                    </div>
                                    <a href="#" class="" data-toggle="modal" data-target="#addtrain">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Store Name</span>
                                    </a>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Password</label>
                                       <input type="text" class="form-control" name="password" id="password"  value="<?php echo $user['password'];?>" placeholder="Enter Your Password" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Confirm Password</label>
                                       <input type="text" class="form-control" name="confirm_password" id="confirm_password"  value="<?php echo $user['confirm_password'];?>" placeholder="Enter Your Confirm Password" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Date Of Joining</label>
                                       <input id='minMaxExample' type="text" class="form-control" name="date_of_joining" id="date_of_joining"  value="<?php echo $user['date_of_joining'];?>" placeholder="Enter Your Date Of Joining" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Front</label><br>
                                       <label>Picture upload</label>
                                       <?php if (!empty($user['picture'])) { ?>
                                       <img src="uploads/item-master/<?php echo $user['picture']; ?>" width="80">
                                       <?php } ?>
                                       <input type="file" name="picture" id="picture" ;?>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Gender</label><br>
                                       <label class="radio-inline"><input name="gender" value="1" checked="checked" type="radio">Male</label> 
                                       <label class="radio-inline"><input name="gender" value="0" type="radio">Female</label>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-check">
                                       <label>Status</label><br>
                                       <label class="radio-inline">
                                       <input type="radio" name="status" value="true" checked="checked">Active</label>
                                       <label class="radio-inline"><input type="radio" name="status" value="false" >Inctive</label>
                                    </div>
                                 </div>
                              </div>
                              <div class="container-fluid">
                                 <div class="row">
                                    <a href="user-master-creation.php"><button type="Reset" name="Reset" class="btn btn-warning" style="width:100px;">Reset</button></a>
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
                        <div class="panel-heading" data-toggle = "offcanvas">
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
                              <span>User Master Edit List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_usermastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>User Master Creation</button></a>
                              <a href="fh_usermastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>User Master List</button></a>
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
                                       <th data-toggle="offcanvas">Picture</th>
                                       <th data-toggle="offcanvas">User Code</th>
                                       <th data-toggle="offcanvas">User Name</th>
                                       <th data-toggle="offcanvas">Mobile No</th>
                                       <th data-toggle="offcanvas">Store Name</th>
                                       <th data-toggle="offcanvas">Location</th>
                                       <th data-toggle="offcanvas">User Type</th>
                                       <th data-toggle="offcanvas">Password</th>
                                       <th data-toggle="offcanvas">Confirm Password</th>
                                       <th data-toggle="offcanvas">DOJ</th>
                                       <th data-toggle="offcanvas">Gender</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                       $query=mysqli_query($conn,"select * from tbl_user_master"); 
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
                                          <a class=""onClick="return confirm('Are you sure you want to Update User Name Master?')" href="fh_usermastercreation_update.php?update_id=<?php echo $user['id']; ?>"><i class="fa fa-pencil" style="color:white;"></i><br>
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
                                          <span class="label-custom label label-default"><?php echo $user['status']; ?>
                                          </span>
                                       </td>
                                       <td data-toggle="offcanvas">
                                          <img src="uploads/item-master/<?php echo $user['picture']; ?>" class="img-circle" alt="User Image" width="50" height="50">
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo $user['user_code']; ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['user_name'],0,50); ?></td>
                                       <td><?php echo substr($user['mobile_no'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['store_name'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['location'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['user_type'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['password'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['confirm_password'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['date_of_joining'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['gender'],0,50); ?></td>
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