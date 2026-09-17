<?php include('top.php');?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['delete_id'])) {
   $id = (int)$_GET['delete_id'];
   mysqli_query($conn, "DELETE FROM tbl_item_variants WHERE item_id='$id'");
   $sqld = "DELETE FROM tbl_item_master WHERE id='$id'";
   $res = mysqli_query($conn, $sqld);
   if ($res) {
      header('refresh:.5; url=fh_itemoutofstock_list.php');
   } else {
      echo "<script>alert('Failed to delete item record');</script>";
   }
}
?>

<!DOCTYPE html>
<html lang="en">
   
<head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Out of Stock List || TEJASERP</title>
      <!-- Favicon and touch icons -->
      <?php include('include/css.php');?>  
   </head>
   <body class="hold-transition sidebar-mini">
      <!-- Site wrapper -->
      <div class="wrapper">
         
         <!-- Left side column. contains the sidebar -->
         <aside class="main-sidebar">
             <?php include('include/sidebar-left.php');?>
             <?php include('include/toggle_switch_list.php');?>
             <?php include('include/fh-form-scrolling-data-list.php');?> 
         </aside>
         
         <!-- Content Wrapper. Contains page content -->
         <div class="content-wrapper">
            <div class="container-fluid">
               <div class="row">
                  <?php include('include/menu-header.php');?>
               </div>
            </div> 

            <!-- Main content -->
            <section class="">
               <div class="row">
                  <div class="col-sm-12">
                     <div class="panel panel-bd lobidisable">
                        <div class="panel-heading" data-toggle="offcanvas">
                           <div class="btn-group" id="buttonexport">
                              <span style="font-size: 15px; font-weight: bold; color: #d9534f;">
                                 <i class="fa fa-exclamation-triangle"></i>
                                 <span>Out of Stock Items List</span>
                              </span>
                           </div>
                        </div>
                        <div class="panel-body form-scroll">
                           <div class="btn-group">
                              <a href="fh_itemmastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus"></i> Add New Item Master Creation</button></a>
                              <a href="fh_itemmastercreation_list.php"> <button class="btn btn-default btn-sm" style="margin-left:5px;"><i class="fa fa-list"></i> All Items List</button></a>
                           </div>
                           <?php include('include/button-export-to-data.php');?>
                           <div class="container-fluid">
                              <div class="row">
                                 <input type="text" class="form-control-control" name="company_name" id="company_name" placeholder="Data Search" required>
                              </div>
                           </div>
                           <div class="table-responsive">
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
                                       $query = mysqli_query($conn, "
                                          SELECT m.*, 
                                                 COALESCE(SUM(v.quantity), 0) AS total_stock
                                          FROM tbl_item_master m
                                          LEFT JOIN tbl_item_variants v ON m.id = v.item_id
                                          GROUP BY m.id
                                          HAVING total_stock <= 0
                                          ORDER BY m.id DESC
                                       "); 
                                       $rowcount = mysqli_num_rows($query);
                                       for ($i = 1; $i <= $rowcount; $i++) {
                                         $user = mysqli_fetch_array($query);
                                    ?>
                                    <tr>
                                       <td>
                                          <div class="checkbox checkbox-info">
                                             <input id="checkbox1" type="checkbox">
                                             <label for="checkbox1"><?php echo $i; ?></label>
                                          </div>
                                       </td>
                                       <td style="background-color:#2A3F54" class="button-btn-btn-btn21">
                                          <a onClick="return confirm('Are you sure you want to Update Item Master?')" href="fh_itemmastercreation_update.php?update_id=<?php echo $user['id']; ?>">
                                             <span data-toggle="tooltip" title="Update Item" type="button"><i style="color: white;" class="fa fa-pencil"></i><br><span style="color: white;">EDIT</span></span>
                                          </a><br><br>
                                          <a onClick="return confirm('Are you sure you want to Delete Item Master List?')" href="fh_itemoutofstock_list.php?delete_id=<?php echo $user['id']; ?>">
                                             <span data-toggle="tooltip" title="Delete Item" type="button"><i style="color:white;" class="fa fa-trash-o"></i><span style="color:white;">DELETE</span></span>
                                          </a><br><br>

                                          <a href="#" data-toggle="modal" data-target="#details" onClick="setDetailsModal(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                             <span data-toggle="tooltip" title="Item Details" type="button"><i style="color:white;" class="fa fa-list-alt"></i><span style="color:white;">DETAILS</span></span>
                                          </a>
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
                                              while ($v_row = mysqli_fetch_assoc($v_res)) {
                                                 $v_list[] = $v_row;
                                                 $v_total_qty += (int)$v_row['quantity'];
                                              }
                                              $v_count = count($v_list);
                                           ?>
                                           <button type="button" class="btn btn-xs btn-danger btn-open-variants" 
                                                   data-item-id="<?php echo $user['id']; ?>" 
                                                   data-item-name="<?php echo htmlspecialchars($user['item_name']); ?>" 
                                                   data-item-code="<?php echo htmlspecialchars($user['item_code']); ?>" 
                                                   title="Click to update stock variant wise" 
                                                   style="margin-bottom: 5px; font-weight: 600;">
                                              <i class="fa fa-plus-circle"></i> Add Stock (<?php echo $v_count; ?> Variants)
                                           </button>
                                           <div id="item-variants-container-<?php echo $user['id']; ?>" style="font-size: 11px; max-height: 90px; overflow-y: auto; background: #fff0f0; padding: 4px 6px; border: 1px solid #f5c6cb; border-radius: 3px; margin-bottom: 3px;">
                                              <?php if ($v_count > 0): ?>
                                                 <?php foreach ($v_list as $vl): ?>
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
                                           <div style="font-size: 12px; font-weight: bold; color: #d9534f;">
                                              Total Stock: <span id="item-stock-<?php echo $user['id']; ?>" class="badge badge-danger" style="background-color:#d9534f;"><?php echo $v_total_qty; ?></span>
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

               <!-- Details Modal -->
               <div class="modal fade" id="details" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                     <div class="modal-content">
                        <div class="modal-header modal-header-primary" style="background-color: #2A3F54; color: #fff;">
                           <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color:#fff; opacity:1;">×</button>
                           <h3><i class="fa fa-info-circle m-r-5"></i> Item Master Details</h3>
                        </div>
                        <div class="modal-body" style="padding: 20px;">
                           <div class="row">
                              <div class="col-sm-6">
                                 <p><strong>Item Code:</strong> <span id="det_item_code"></span></p>
                                 <p><strong>Item Name:</strong> <span id="det_item_name"></span></p>
                                 <p><strong>Barcode No:</strong> <span id="det_barcode_no"></span></p>
                                 <p><strong>SKU No:</strong> <span id="det_sku_no"></span></p>
                                 <p><strong>Group / SubGroup:</strong> <span id="det_group"></span> / <span id="det_subgroup"></span></p>
                              </div>
                              <div class="col-sm-6">
                                 <p><strong>Brand Name:</strong> <span id="det_brand"></span></p>
                                 <p><strong>Purchase Price:</strong> ₹<span id="det_price"></span></p>
                                 <p><strong>MRP:</strong> ₹<span id="det_mrp"></span></p>
                                 <p><strong>Status:</strong> <span id="det_status"></span></p>
                                 <p><strong>Description:</strong> <span id="det_description"></span></p>
                              </div>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                        </div>
                     </div>
                  </div>
               </div>

            </section>
         </div>

         <?php include('include/footer.php');?> 
         <?php include('include/Sidenavbuttons.php');?> 
      </div>

      <?php include('include/js.php');?>
      <?php include('popup/variant_stock_modal.php');?>

      <script>
      function setDetailsModal(item) {
         if (!item) return;
         $('#det_item_code').text(item.item_code || '-');
         $('#det_item_name').text(item.item_name || '-');
         $('#det_barcode_no').text(item.barcode_no || '-');
         $('#det_sku_no').text(item.sku_no || '-');
         $('#det_group').text(item.group_name || '-');
         $('#det_subgroup').text(item.subgroup_name || '-');
         $('#det_brand').text(item.brand_name || '-');
         $('#det_price').text(item.purchase_price || '0.00');
         $('#det_mrp').text(item.mrp || '0.00');
         $('#det_status').text(item.status || '-');
         $('#det_description').text(item.description || '-');
      }
      </script>
   </body>
</html>
