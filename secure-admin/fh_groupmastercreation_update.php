<?php include('top.php');
   if(isset($_GET['update_id']))
   {
     $id=$_GET['update_id'];
     $run=mysqli_query($conn,"select * from tbl_group_master where id='$id'");
     $user =mysqli_fetch_assoc($run);
   }
   ///Update Group Master
   if(isset($_POST['update']))
      {
       $msg="";
         $group_code = mysqli_real_escape_string($conn, $_POST['group_code']);
         $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
         $hsn_code = mysqli_real_escape_string($conn, $_POST['hsn_code']);
         $status = mysqli_real_escape_string($conn, $_POST['status']);
   
       $group_name = str_replace("'", "\'", $group_name);
       $group_name = str_replace('"', '\"', $group_name);
   
       // FILE UPLOAD PATH
       $path = "uploads/group-master/";
       if (!is_dir($path)) {
           mkdir($path, 0777, true);
       }
       @chmod($path, 0777);
       
       $new_image = "";
       if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
           $ext = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));
           $allowed = ['jpg','jpeg','png','webp','gif','svg'];
           if (in_array($ext, $allowed)) {
               $clean_filename = preg_replace('/[^a-zA-Z0-9\._-]/', '_', basename($_FILES['picture']['name']));
               $new_image = time() . "_" . $clean_filename;
               move_uploaded_file($_FILES['picture']['tmp_name'], $path . $new_image);
           }
       }
       
       $old_picture = isset($user['picture']) ? $user['picture'] : '';
       $picture = !empty($new_image) ? $new_image : $old_picture;
           
       $query="UPDATE tbl_group_master SET group_code='$group_code',group_name='$group_name',hsn_code='$hsn_code',picture='$picture',status='$status' WHERE id='$id'";
       if (mysqli_query($conn,$query)){
           $msg='<div class="alert alert-success alert-dismissible fade1 show">
                       <button type="button" class="close" data-dismiss="alert">&times;</button>
                       <strong>Group Name Update..!</strong>
                   </div>';
                   header('refresh:.5; url=fh_groupmastercreation_list.php');
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
      <title>Group Master Update || TEJASERP</title>
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
                              <span>Group Master Edit !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_groupmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Group  Master Creation !</button></a>
                           </div>
                           <div class="btn-group">
                              <a href="fh_groupmastercreation_list.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-list-alt" style="margin-right: 5px;"></i>Group  Master List !</button></a>
                           </div>
                           <form method="post" enctype="multipart/form-data">
                              <!-- <div class="form-group">
                                 <?php echo $msg; ?>
                                 </div> -->
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Head Name</label>
                                       <select name="head_name" class="form-control">
                                          <option value="null">Select Head Name 
                                          </option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_head_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                             $selected = ($user['head_name'] == $row['head_name']) ? 'selected' : '';
                                             echo "<option value='{$row['head_name']}' {$selected}>{$row['head_name']}</option>";
                                             }
                                             ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>SubHead Name</label>
                                       <select name="subhead_name" class="form-control">
                                          <option value="null">Select SubHead Name</option>
                                          <?php
                                             $query = mysqli_query($conn, "SELECT * FROM tbl_subhead_master");
                                             while ($row = mysqli_fetch_array($query)) {
                                             $selected = ($user['subhead_name'] == $row['subhead_name']) ? 'selected' : '';
                                             echo "<option value='{$row['subhead_name']}' {$selected}>{$row['subhead_name']}</option>";
                                             }
                                             ?> 
                                       </select>
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Group Code</label>
                                       <input type="password" name="group_code" class="form-control" readonly value="<?php echo $user['group_code'];?>" />
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <label>Group Name</label>
                                       <input type="text" class="form-control" name="group_name" id="group_name"  value="<?php echo $user['group_name'];?>" placeholder="Enter Your Group Name" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>HSN Code</label>
                                       <input type="text" class="form-control" name="hsn_code" id="hsn_code"  value="<?php echo $user['hsn_code'];?>" placeholder="Enter Your HSN Code" required>
                                    </div>
                                 </div>
                                 <div class="col-sm-3">
                                    <div class="form-group">
                                       <label>Category Image</label><br>
                                       <?php if (!empty($user['picture'])) { ?>
                                          <img src="uploads/group-master/<?php echo $user['picture']; ?>" width="50" height="50" class="img-circle" style="margin-bottom: 5px;">
                                       <?php } ?>
                                       <input type="file" class="form-control" name="picture" id="picture">
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
                              </div>
                              <div class="container-fluid">
                                 <div class="row">
                                     <button type="reset" class="btn btn-warning" style="width:100px;">Reset</button>
                                     <button type="submit" name="update" class="btn btn-success" style="width:100px;">Update</button>
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
                              <span>Group Master List !</span>
                           </span>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_groupmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Group  Master Creation !</button></a>
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
                                       <th data-toggle="offcanvas">Head Name</th>
                                       <th data-toggle="offcanvas">SubHead Name</th>
                                       <th data-toggle="offcanvas">Group Code</th>
                                       <th data-toggle="offcanvas">Group Name</th>
                                       <th data-toggle="offcanvas">Category Image</th>
                                       <th data-toggle="offcanvas">HSN Code</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                       $query=mysqli_query($conn,"select * from tbl_group_master"); 
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
                                       <td class="button-btn-btn-btn2111">
                                          <a class=""onClick="return confirm('Are you sure you want to Update Group Master?')" href="fh_groupmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                          <span
                                             data-toggle="tooltip" title="Are you sure you want to Update Group Master" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><br>
                                          <span style=" color: white;">EDIT</span>
                                          </span></a>
                                          <br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Size Master List ?')" href="fh_groupmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Group Master List" type="button" class="" ><i style="color:white;" class="fa fa-trash-o"></i>
                                          <span style="color:white;"class="">DELETE</span></span></a>
                                          <br><br>
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
                                       <td data-toggle="offcanvas"><?php echo substr($user['head_name'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['subhead_name'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo $user['group_code']; ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['group_name'],0,50); ?>
                                       </td>
                                       <td data-toggle="offcanvas">
                                          <?php if (!empty($user['picture'])) { ?>
                                             <img src="uploads/group-master/<?php echo $user['picture']; ?>" class="img-circle" alt="Category Image" width="50" height="50">
                                          <?php } else { echo "No Image"; } ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['hsn_code'],0,50); ?>
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