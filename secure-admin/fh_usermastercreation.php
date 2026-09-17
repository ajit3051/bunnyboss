<?php include('top.php');?>
<?php
   $msg='';
    if(isset($_POST['submit']))
    {
   
   
   
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
   
   
   
       // FILE UPLOAD PATH
       $path = "uploads/item-master/";
       if (!is_dir($path)) {
           mkdir($path, 0777, true);
       }
   
       function uploadFile($file, $path) {
           if (!empty($file['name']) && $file['error'] === 0) {
   
               $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
               $allowed = ['jpg','jpeg','png','webp'];
   
               if (!in_array($ext, $allowed)) {
                   return "";
               }
   
               $filename = time() . "_" . $file['name'];
               move_uploaded_file($file['tmp_name'], $path . $filename);
   
               return $filename;
           }
           return "";
       }
   
       // IMAGE UPLOAD
       @$picture  = uploadFile($_FILES['picture'], $path);
   
       // PASSWORD HASH
       $password = password_hash($password, PASSWORD_DEFAULT);
   
       $dup=mysqli_query($conn,"select * from tbl_user_master where user_name='$user_name'");
           if(mysqli_num_rows($dup)>0)
           {
               $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                   <button type="button" class="close" data-dismiss="alert">&times;</button>
                   <strong>User Name Already exist!</strong>
                   </div>';
           }
           else{
   
       // INSERT QUERY
       $query = "INSERT INTO tbl_user_master
       (user_code,user_name,mobile_no,store_name,location,user_type,password,
        confirm_password,date_of_joining,picture,gender,status)
       VALUES
       ('$user_code','$user_name','$mobile_no','$store_name','$location','$user_type',
        '$password','$password','$date_of_joining',
        '$picture','$gender','$status')";
   
       $run = mysqli_query($conn,$query);   // ✅ EXECUTE QUERY
   
        if($run)
   {
    echo "
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        showPopup(
            'Success!',
            'User Name Saved Successfully 🎉',
            
        );
    });
    </script>
    ";
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
      <title>User Master Creations || TEJASERP</title>
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
                              <span>User Master Creations !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_usermastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-bars" style="margin-right: 5px;"></i>User Master List</button></a>
                           </div>
                           <form method="post" enctype="multipart/form-data">
                              <?php echo $msg; ?>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>User Code</label>
                                       <input type="password" name="user_code" class="form-control" id="user_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_user_master ORDER BY id DESC");
                                          $po_no = ($no) ? mysqli_fetch_assoc($no) : null;
                                          $next_id = ($po_no && isset($po_no['id'])) ? ($po_no['id'] + 1) : 1;
                                          echo 'UC-000' . $next_id;
                                          ?>">
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>User Name</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="user_name" id="user_name" placeholder="Enter Your Name" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Mobile No</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="mobile_no" id="mobile_no" placeholder="Enter Your Mobile No" required>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
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
                                    <a href="#" class="" data-toggle="modal" data-target="#gst">
                                    <span style="font-size:11px;" class="pull-left"><i class="fa fa-plus"></i>Add New Store Master</span>
                                    </a>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Location</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="location" id="location" placeholder="Enter Your Location" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>User type</label><label style="color:red;">*</label>
                                       <select name="user_type" class="form-control">
                                          <option value="null">Select User Type</option>
                                          <option value="Admin">Super Admin</option>
                                          <option value="Admin">Admin</option>
                                          <option value="Admin">Manager</option>
                                          <option value="Power User">Power User</option>
                                          <option value="User">User</option>
                                       </select>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Password</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="password" id="password" placeholder="Enter Your Password" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Confirm Password</label><label style="color:red;">*</label>
                                       <input type="text" class="form-control" name="confirm_password" id="confirm_password" placeholder="Enter Your Confirm Password" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Date of Joining</label><label style="color:red;">*</label>
                                       <input id='minMaxExample' type="text" name="date_of_joining" id="date_of_joining" class="form-control" placeholder="Enter Date...">
                                    </div>
                                 </div>
                              </div>
                              <br>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Picture upload</label>
                                       <input type="file" name="picture" id="picture">
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
                              <br>
                              <div class="container-fluid">
                                 <div class="row">
                                    <button type="submit" name="submit" class="btn btn-warning" style="width:100px;">Reset</button>
                                    <button type="submit" name="submit" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                                 </div>
                              </div>
                              <br>
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
                              <span>User Master List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_usermastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>User Master Creation</button></a>
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
                                       <td style="background-color:#2A3F54" class="button-btn-btn-btn21">
                                          <a class=""onClick="return confirm('Are you sure you want to Update User Master Update?')" href="fh_usermastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                          <span
                                             data-toggle="tooltip" title="Are you sure you want to Update Store Profile" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><br><span style="margin-left:0px; color: white;">EDIT</span>
                                          </span></a><br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Store Profile List ?')" href="fh_usermastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Store Profile List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa-trash-o"></i><span style="margin-left:0px; color:white;"class="">DELETE</span></span></a>
                                          <br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Details Item Name List ?')" href="fh_Detailsmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Brand Name List" type="button" class="" ><i style="margin-left: 0px; color:white;" class="fa fa fa-list-alt"></i><span style="margin-left:0px; color:white;">DETAILS</span></span></a>
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
                                       <td data-toggle="offcanvas"><?php echo substr($user['mobile_no'],0,50); ?></td>
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
               <!-- model Modal1 -->
               <div class="modal fade" id="gst" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header modal-header-primary">
                           <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                           <h3><i class="fa fa-plus m-r-5"></i> Add New Store Master Creation</h3>
                        </div>
                        <div class="modal-body">
                           <div class="row">
                              <div class="col-md-12">
                                 <form method="post">
                                    <?php
                                       $msg='';
                                        if(isset($_POST['submit_btn']))
                                        {
                                         $store_code = mysqli_real_escape_string($conn, $_POST['store_code']);
                                         $store_name = mysqli_real_escape_string($conn, $_POST['store_name']);
                                         $location_name = mysqli_real_escape_string($conn, $_POST['location_name']);
                                         $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
                                         $status = mysqli_real_escape_string($conn, $_POST['status']);
                                         
                                             $dup=mysqli_query($conn,"select * from tbl_store_master where store_name='$store_name'");
                                               if(mysqli_num_rows($dup)>0)
                                               {
                                                   $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Already exist!</strong>
                                                       </div>';
                                               }
                                               else{
                                                   $query="Insert into tbl_store_master(store_code,store_name,location_name,company_name,status)values('$store_code','$store_name','$location_name','$company_name','$status')";
                                                   if(mysqli_query($conn,$query)){
                                                       $msg='<div class="alert alert-success alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Creations Success..!</strong>
                                                     </div>';
                                                   }
                                                   else{
                                                       $msg='<div class="alert alert-danger alert-dismissible fade1 show">
                                                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                       <strong>Store Master Creations Failed!</strong>
                                                       </div>';
                                                   }
                                               } 
                                           }
                                        ?>
                                    <div class="row">
                                       <div class="col-sm-6">
                                          <div class="form">
                                             <label>Store Code</label>
                                             <input type="password" name="store_code" class="form-control" id="store_code" readonly value="<?php @$no = mysqli_query($conn, "SELECT id FROM  tbl_store_master ORDER BY id DESC");
                                                $po_no = ($no) ? mysqli_fetch_assoc($no) : null;
                                                $next_id = ($po_no && isset($po_no['id'])) ? ($po_no['id'] + 1) : 1;
                                                echo 'SC' . $next_id;
                                                ?>">
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Store Name</label>
                                             <input type="text" class="form-control" name="store_name" id="store_name" placeholder="Enter Your Store Name" required>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Company Name</label>
                                             <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter Your Company Name" required>
                                          </div>
                                       </div>
                                       <div class="col-sm-6">
                                          <div class="form-group">
                                             <label>Store Location</label>
                                             <input type="text" class="form-control" name="location_name" id="location_name" placeholder="Enter Your Location Name" required>
                                          </div>
                                       </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                       <div class="col-sm-4">
                                          <div class="form-check">
                                             <label>Status</label><br>
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
                                          <button type="submit" name="submit_btn" class="btn btn-success" style="width:100px;background-color: #009688; color: white;">Save</button>
                                       </div>
                                    </div>
                                    <br>
                                 </form>
                              </div>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
                        </div>
                     </div>
                     <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
               </div>
               <!-- model Modal1-end -->
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