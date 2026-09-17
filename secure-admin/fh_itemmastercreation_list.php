<?php include('top.php');?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(isset($_GET['delete_id']))
{
   $id=$_GET['delete_id'];
   mysqli_query($conn, "DELETE FROM tbl_item_variants WHERE item_id='$id'");
   $sqld="DELETE FROM tbl_item_master WHERE id='$id'";
   $res=mysqli_query($conn,$sqld);
   if($res)
   {
    header('refresh:.5; url=fh_itemmastercreation_list.php');
   }else{
    echo "<script>alert('Item Master Record Failed');</script>";
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
      <title>Item Master List || TEJASERP</title>
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
            
             <!-- Main content -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading"data-toggle="offcanvas">
                           <div class="btn-group" id="buttonexport">
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
                                 <span>Item Master List !</span>
                              </span>
                           </div>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_itemmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i> Add New Item Master Creation</button></a>
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
                                       <th data-toggle="offcanvas">Item Code</th>
                                       <th data-toggle="offcanvas">Barcode No</th>
                                       <th data-toggle="offcanvas">SKU No</th>
                                       <th data-toggle="offcanvas">Item Name</th>
                                       <th data-toggle="offcanvas">Variants (Stock)</th>
                                       <th data-toggle="offcanvas">Description</th>
                                       <th data-toggle="offcanvas">Group</th>
                                       <th data-toggle="offcanvas">SubGroup</th>
                                       <th data-toggle="offcanvas">Brand</th>
                                       <th data-toggle="offcanvas">MOU</th>
                                       <th data-toggle="offcanvas">PP||CP</th>
                                       <th data-toggle="offcanvas">MRP</th>
                                       <th data-toggle="offcanvas">Discount(%)</th>
                                       <th data-toggle="offcanvas">Discount Amt.</th>
                                       <th data-toggle="offcanvas">Rac No</th>
                                       <th data-toggle="offcanvas">About This Item-1</th>
                                       <th data-toggle="offcanvas">About This Item-2</th>
                                       <th data-toggle="offcanvas">About This Item-3</th>
                                       <th data-toggle="offcanvas">About This Item-4</th>
                                       <th data-toggle="offcanvas">Product Images</th>
                                       <th data-toggle="offcanvas">Status</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                       $query=mysqli_query($conn,"select * from tbl_item_master order by id desc"); 
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
                                          <a class=""onClick="return confirm('Are you sure you want to Update Item Master?')" href="fh_itemmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                          <span
                                             data-toggle="tooltip" title="Are you sure you want to Update Item Name" type="button" class="" ><i style="color: white;" class="fa fa-pencil"></i><br><span style="color: white;">EDIT</span>
                                          </span></a><br><br>
                                          <a class="" onClick="return confirm('Are you sure you want to Delete Item Name List ?')" href="fh_itemmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Item Name List" type="button" class="" ><i style="color:white;" class="fa fa-trash-o"></i><span style="color:white;"class="">DELETE</span></span></a><br><br>

                                          <a class="" onClick="return confirm('Are you sure you want to Delete Item Name List ?')" href="fh_itemmastercreation_list.php?delete_id=<?php echo $user['id']; ?>">
                                          <span data-toggle="tooltip" title="Are you sure you want to Delete Brand Name List" type="button" class="" ><i style="color:white;" class="fa fa fa-list-alt"></i><span style="color:white;">DETAILS</span></span></a>
                                          <br><br>
                                          <label class="switch1">
                                          <input type="checkbox">
                                          <span class="slider1 round1"></span>
                                          </label>
                                       </td>
                                       <td data-toggle="offcanvas">
                                          <label class="switch1">
                                          <input type="checkbox">
                                          <span class="slider1 round"></span>
                                          </label>
                                       </td>
                                       <td data-toggle="offcanvas">
                                          <?php echo $user['item_code']; ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['barcode_no'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['sku_no'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['item_name'],0,50); ?></td>
                                        <td data-toggle="offcanvas" style="min-width: 170px;">
                                            <?php
                                               $v_res = mysqli_query($conn, "SELECT id, color_name, size_name, style_name, quantity FROM tbl_item_variants WHERE item_id='".$user['id']."' ORDER BY id ASC");
                                               $v_list = [];
                                               $v_total_qty = 0;
                                               while($v_row = mysqli_fetch_assoc($v_res)) {
                                                  $v_list[] = $v_row;
                                                  $v_total_qty += (int)$v_row['quantity'];
                                               }
                                               $v_count = count($v_list);
                                            ?>
                                            <button type="button" class="btn btn-xs btn-info btn-open-variants" 
                                                    data-item-id="<?php echo $user['id']; ?>" 
                                                    data-item-name="<?php echo htmlspecialchars($user['item_name']); ?>" 
                                                    data-item-code="<?php echo htmlspecialchars($user['item_code']); ?>" 
                                                    title="Click to update stock variant wise" 
                                                    style="margin-bottom: 5px; font-weight: 600;">
                                               <i class="fa fa-pencil-square-o"></i> Update Stocks (<?php echo $v_count; ?> Variants)
                                            </button>
                                            <div id="item-variants-container-<?php echo $user['id']; ?>" style="font-size: 11px; max-height: 90px; overflow-y: auto; background: #fafafa; padding: 4px 6px; border: 1px solid #ddd; border-radius: 3px; margin-bottom: 3px;">
                                               <?php if ($v_count > 0): ?>
                                                  <?php foreach($v_list as $vl): ?>
                                                     <?php 
                                                        $label = trim($vl['color_name'] . ' ' . $vl['size_name']);
                                                        if (!$label) $label = 'Default';
                                                        $b_class = ((int)$vl['quantity'] > 0) ? 'label-success' : 'label-danger';
                                                     ?>
                                                     <div style="margin-bottom: 2px;">
                                                        <strong><?php echo htmlspecialchars($label); ?>:</strong> 
                                                        <span class="label <?php echo $b_class; ?>" id="v-badge-<?php echo $vl['id']; ?>"><?php echo (int)$vl['quantity']; ?> in stock</span>
                                                     </div>
                                                  <?php endforeach; ?>
                                               <?php else: ?>
                                                  <span class="text-muted">No variant records</span>
                                               <?php endif; ?>
                                            </div>
                                            <div style="font-size: 12px; font-weight: bold; color: #333;">
                                               Total Stock: <span id="item-stock-<?php echo $user['id']; ?>" class="badge badge-success" style="background-color:#5cb85c;"><?php echo $v_total_qty; ?></span>
                                            </div>
                                         </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['description'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['group_name'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['subgroup_name'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['brand_name'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['mou_name'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['purchase_price'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['mrp'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['dis_per'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['dis_amt'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['rac_no'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['about_1'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['about_2'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['about_3'],0,50); ?></td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['about_4'],0,50); ?></td>
                                       <td data-toggle="offcanvas">
                                          <?php
                                             $item_imgs_res = mysqli_query($conn, "SELECT image_path FROM tbl_item_images WHERE item_id='".$user['id']."' ORDER BY sort_order ASC, id ASC LIMIT 3");
                                             $img_count = 0;
                                             while ($img_r = mysqli_fetch_assoc($item_imgs_res)) {
                                                 echo '<img src="uploads/item-master/' . htmlspecialchars($img_r['image_path']) . '" class="img-circle" alt="Image" width="40" height="40" style="margin-right:2px; object-fit:cover;">';
                                                 $img_count++;
                                             }
                                             if ($img_count == 0) {
                                                 echo '<span class="text-muted">No Image</span>';
                                             }
                                          ?>
                                       </td>
                                       <td data-toggle="offcanvas"><?php echo substr($user['status'],0,50); ?></td>
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
                                    </div>
                                    <br>
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
                                 </div>
                                 <br>
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
                                 </div>
                                 <br>
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
                                 </div>
                                 <br>
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
                                 </div>
                                 <br>
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
                                 </div>
                                 <br>
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
                                 </div>
                                 <br>
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
                                 </div>
                                 <br>
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
       <?php include('popup/variant_stock_modal.php');?>



         
      <!-- End Theme label Script
         =====================================================================-->
   </body>

<!-- Mirrored from thememinister.com/crm/add-customer.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 05 Aug 2022 06:10:42 GMT -->
</html>

