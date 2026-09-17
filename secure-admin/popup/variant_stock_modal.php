<?php
// Fetch master lists for Color, Size, and Style from DB
$colors_opt = [];
$sizes_opt  = [];
$styles_opt = [];

if (isset($conn) && $conn) {
    $c_q = mysqli_query($conn, "SELECT color_name FROM tbl_color_master ORDER BY color_name ASC");
    while ($c_r = mysqli_fetch_assoc($c_q)) {
        if (!empty($c_r['color_name'])) $colors_opt[] = $c_r['color_name'];
    }

    $s_q = mysqli_query($conn, "SELECT size_name FROM tbl_size_master ORDER BY id ASC");
    while ($s_r = mysqli_fetch_assoc($s_q)) {
        if (!empty($s_r['size_name'])) $sizes_opt[] = $s_r['size_name'];
    }

    $st_q = mysqli_query($conn, "SELECT style_name FROM tbl_styledesign_master ORDER BY style_name ASC");
    while ($st_r = mysqli_fetch_assoc($st_q)) {
        if (!empty($st_r['style_name'])) $styles_opt[] = $st_r['style_name'];
    }
}
$colors_opt = array_values(array_unique($colors_opt));
$sizes_opt  = array_values(array_unique($sizes_opt));
$styles_opt = array_values(array_unique($styles_opt));
?>

<!-- Modal for Viewing & Managing Item Variants -->
<div class="modal fade" id="variantStockModal" tabindex="-1" role="dialog" aria-labelledby="variantStockModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document" style="max-width: 950px; width: 95%;">
      <div class="modal-content">
         <div class="modal-header modal-header-primary" style="background-color: #2A3F54; color: #fff; border-top-left-radius: 4px; border-top-right-radius: 4px;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color:#fff; opacity:1; font-size:24px;">×</button>
            <h4 class="modal-title" id="variantStockModalLabel" style="font-weight:600;">
               <i class="fa fa-cubes"></i> Variant-Wise Stock Management — <span id="vModalItemName"></span> <small style="color:#eee;">(<span id="vModalItemCode"></span>)</small>
            </h4>
         </div>
         <div class="modal-body" style="padding: 20px;">
            <div id="variantModalAlert"></div>
            
            <div class="well well-sm" style="background: #f4f6f9; border-left: 4px solid #009688; margin-bottom: 15px;">
               <div class="row">
                  <div class="col-xs-6">
                     <span style="font-size: 14px; font-weight:600;">Total Variations: </span>
                     <span id="vModalCount" class="label label-info" style="font-size: 14px;">0</span>
                  </div>
                  <div class="col-xs-6 text-right">
                     <span style="font-size: 14px; font-weight:600;">Total Combined Stock: </span>
                     <span id="vModalTotalStock" class="label label-success" style="font-size: 14px;">0</span>
                  </div>
               </div>
            </div>

            <!-- Variant List Table -->
            <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover" id="variantListTable">
                  <thead>
                     <tr class="info" style="background-color: #e8ecef;">
                        <th width="35" class="text-center">#</th>
                        <th width="20%">Color</th>
                        <th width="20%">Size</th>
                        <th width="20%">Style</th>
                        <th width="15%">Price (₹)</th>
                        <th width="15%">Stock Quantity</th>
                        <th width="10%" class="text-center">Action</th>
                     </tr>
                  </thead>
                  <tbody id="variantListTbody">
                     <tr>
                        <td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading variations...</td>
                     </tr>
                  </tbody>
               </table>
            </div>

            <!-- Form to Add New Variant -->
            <div class="panel panel-default" style="margin-top: 15px;">
               <div class="panel-heading" style="background-color: #f5f5f5; font-weight: 600; cursor: pointer;" data-toggle="collapse" data-target="#addVariantCollapse">
                  <i class="fa fa-plus-circle text-primary"></i> Add New Variant / Size for this Item <small class="text-muted pull-right">(Click to expand)</small>
               </div>
               <div id="addVariantCollapse" class="panel-collapse collapse">
                  <div class="panel-body">
                     <form id="formAddNewVariant">
                        <div class="row">
                           <div class="col-sm-3">
                              <label style="font-size: 12px;">Color</label>
                              <select class="form-control input-sm" id="new_v_color">
                                 <option value="">Select Color</option>
                                 <?php foreach ($colors_opt as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                                 <?php endforeach; ?>
                              </select>
                           </div>
                           <div class="col-sm-2">
                              <label style="font-size: 12px;">Size</label>
                              <select class="form-control input-sm" id="new_v_size">
                                 <option value="">Select Size</option>
                                 <?php foreach ($sizes_opt as $s): ?>
                                    <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                 <?php endforeach; ?>
                              </select>
                           </div>
                           <div class="col-sm-3">
                              <label style="font-size: 12px;">Style</label>
                              <select class="form-control input-sm" id="new_v_style">
                                 <option value="">Select Style</option>
                                 <?php foreach ($styles_opt as $st): ?>
                                    <option value="<?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars($st); ?></option>
                                 <?php endforeach; ?>
                              </select>
                           </div>
                           <div class="col-sm-2">
                              <label style="font-size: 12px;">Price (₹)</label>
                              <input type="number" step="0.01" class="form-control input-sm" id="new_v_price" placeholder="Price">
                           </div>
                           <div class="col-sm-2">
                              <label style="font-size: 12px;">Initial Stock</label>
                              <input type="number" min="0" class="form-control input-sm" id="new_v_qty" value="0">
                           </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                           <div class="col-sm-12 text-right">
                              <button type="submit" class="btn btn-sm btn-primary" id="btnAddVariantSubmit">
                                 <i class="fa fa-plus"></i> Add Variant
                              </button>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
            </div>

         </div>
         <div class="modal-footer" style="background-color: #f9f9f9;">
            <button type="button" class="btn btn-success pull-left" id="btnSaveAllVariantStocks"><i class="fa fa-save"></i> Save All Variant Stocks</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>

<script>
if (typeof jQuery !== 'undefined') {
   $(document).ready(function() {
      var currentModalItemId = 0;
      var cachedMasterLists = {
         colors: <?php echo json_encode($colors_opt); ?>,
         sizes:  <?php echo json_encode($sizes_opt); ?>,
         styles: <?php echo json_encode($styles_opt); ?>
      };

      // Helper function to update the row UI on the main table
      function updateRowVariantUI(itemId, totalQty, variantsList) {
         $('#item-stock-' + itemId).text(totalQty);
         var container = $('#item-variants-container-' + itemId);
         if (container.length && variantsList && isArray(variantsList)) {
            var html = '';
            $.each(variantsList, function(i, v) {
               var label = $.trim((v.color_name || '') + ' ' + (v.size_name || ''));
               if (!label) label = 'Default Variant';
               var badgeClass = parseInt(v.quantity) > 0 ? 'label-success' : 'label-danger';
               html += '<div style="margin-bottom:2px;">';
               html += '<strong>' + label + ':</strong> ';
               html += '<span class="label ' + badgeClass + '" id="v-badge-' + v.id + '">' + v.quantity + ' in stock</span>';
               html += '</div>';
            });
            container.html(html);
         }
      }

      function isArray(val) {
         return Object.prototype.toString.call(val) === '[object Array]';
      }

      // Helper to build a select dropdown HTML
      function buildSelectHTML(className, selectedVal, optionsList, defaultLabel) {
         var html = '<select class="form-control input-sm ' + className + '">';
         html += '<option value="">' + defaultLabel + '</option>';
         
         // Ensure selectedVal is included even if not in master options
         var found = false;
         $.each(optionsList, function(i, opt) {
            var sel = (opt === selectedVal) ? 'selected' : '';
            if (opt === selectedVal) found = true;
            html += '<option value="' + escapeHtml(opt) + '" ' + sel + '>' + escapeHtml(opt) + '</option>';
         });
         if (selectedVal && !found) {
            html += '<option value="' + escapeHtml(selectedVal) + '" selected>' + escapeHtml(selectedVal) + '</option>';
         }
         html += '</select>';
         return html;
      }

      function escapeHtml(text) {
         if (!text) return '';
         return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
      }

      // 1. Open Variants Modal
      $(document).on('click', '.btn-open-variants', function(e) {
         e.preventDefault();
         var itemId = $(this).data('item-id');
         var itemName = $(this).data('item-name');
         var itemCode = $(this).data('item-code');
         
         currentModalItemId = itemId;
         $('#vModalItemName').text(itemName);
         $('#vModalItemCode').text(itemCode);
         $('#variantModalAlert').hide().html('');
         $('#variantListTbody').html('<tr><td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading variations...</td></tr>');
         
         $('#variantStockModal').modal('show');
         
         loadItemVariants(itemId);
      });

      function loadItemVariants(itemId) {
         $.ajax({
            url: 'ajax/item_variant_stock',
            type: 'GET',
            data: { action: 'get_variants', item_id: itemId },
            dataType: 'json',
            success: function(res) {
               if (res.status === 'success') {
                  $('#vModalCount').text(res.variants.length);
                  $('#vModalTotalStock').text(res.total_qty);
                  
                  if (res.masters) {
                     if (res.masters.colors) cachedMasterLists.colors = res.masters.colors;
                     if (res.masters.sizes)  cachedMasterLists.sizes  = res.masters.sizes;
                     if (res.masters.styles) cachedMasterLists.styles = res.masters.styles;
                  }
                  
                  updateRowVariantUI(itemId, res.total_qty, res.variants);

                  var rowsHtml = '';
                  if (res.variants.length === 0) {
                     rowsHtml = '<tr><td colspan="7" class="text-center text-muted">No variant records found for this product yet. Use the "Add New Variant" form below to create one.</td></tr>';
                  } else {
                     $.each(res.variants, function(idx, v) {
                        rowsHtml += '<tr data-variant-id="' + v.id + '">';
                        rowsHtml += '<td class="text-center">' + (idx + 1) + '</td>';
                        rowsHtml += '<td>' + buildSelectHTML('v-color-select', v.color_name, cachedMasterLists.colors, 'Select Color') + '</td>';
                        rowsHtml += '<td>' + buildSelectHTML('v-size-select', v.size_name, cachedMasterLists.sizes, 'Select Size') + '</td>';
                        rowsHtml += '<td>' + buildSelectHTML('v-style-select', v.style_name, cachedMasterLists.styles, 'Select Style') + '</td>';
                        rowsHtml += '<td><input type="number" step="0.01" class="form-control input-sm v-price-input" value="' + parseFloat(v.price).toFixed(2) + '"></td>';
                        rowsHtml += '<td><input type="number" min="0" class="form-control input-sm v-qty-input" data-variant-id="' + v.id + '" value="' + v.quantity + '" style="font-weight:bold; color:#000;"></td>';
                        rowsHtml += '<td class="text-center"><button type="button" class="btn btn-xs btn-primary btn-save-single-var" data-variant-id="' + v.id + '" title="Save Variant Stock"><i class="fa fa-save"></i> Save</button></td>';
                        rowsHtml += '</tr>';
                     });
                  }
                  $('#variantListTbody').html(rowsHtml);
               } else {
                  $('#variantListTbody').html('<tr><td colspan="7" class="text-danger text-center">' + (res.message || 'Error loading variations') + '</td></tr>');
               }
            },
            error: function() {
               $('#variantListTbody').html('<tr><td colspan="7" class="text-danger text-center">Server error loading variations.</td></tr>');
            }
         });
      }

      // 2. Save Single Variant inside Modal (Color, Size, Style, Price, Stock)
      $(document).on('click', '.btn-save-single-var', function() {
         var btn = $(this);
         var varId = btn.data('variant-id');
         var tr = btn.closest('tr');
         var colorVal = tr.find('.v-color-select').val();
         var sizeVal  = tr.find('.v-size-select').val();
         var styleVal = tr.find('.v-style-select').val();
         var priceVal = tr.find('.v-price-input').val();
         var qtyVal   = tr.find('.v-qty-input').val();

         btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

         $.ajax({
            url: 'ajax/item_variant_stock',
            type: 'POST',
            data: {
               action: 'update_single_stock',
               variant_id: varId,
               item_id: currentModalItemId,
               color_name: colorVal,
               size_name: sizeVal,
               style_name: styleVal,
               price: priceVal,
               quantity: qtyVal
            },
            dataType: 'json',
            success: function(res) {
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save');
               if (res.status === 'success') {
                  $('#vModalTotalStock').text(res.total_qty);
                  updateRowVariantUI(currentModalItemId, res.total_qty, res.updated_variants);
                  
                  $('#variantModalAlert').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fa fa-check-circle"></i> Variant updated successfully!</div>').show();
                  setTimeout(function() { $('#variantModalAlert').fadeOut(); }, 2500);
               } else {
                  alert(res.message || 'Failed to update stock');
               }
            },
            error: function() {
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save');
               alert('Server error updating stock');
            }
         });
      });

      // 3. Save All Variant Stocks inside Modal
      $('#btnSaveAllVariantStocks').on('click', function() {
         var btn = $(this);
         var variantsData = [];
         
         $('#variantListTbody tr').each(function() {
            var tr = $(this);
            var vid = tr.find('.v-qty-input').data('variant-id');
            if (vid) {
               variantsData.push({
                  id: vid,
                  color_name: tr.find('.v-color-select').val(),
                  size_name:  tr.find('.v-size-select').val(),
                  style_name: tr.find('.v-style-select').val(),
                  price:      tr.find('.v-price-input').val(),
                  quantity:   tr.find('.v-qty-input').val()
               });
            }
         });

         if (variantsData.length === 0) {
            alert('No variant rows to update.');
            return;
         }

         btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

         $.ajax({
            url: 'ajax/item_variant_stock',
            type: 'POST',
            data: {
               action: 'update_all_stocks',
               item_id: currentModalItemId,
               variants: variantsData
            },
            dataType: 'json',
            success: function(res) {
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save All Variant Stocks');
               if (res.status === 'success') {
                  $('#vModalTotalStock').text(res.total_qty);
                  updateRowVariantUI(currentModalItemId, res.total_qty, res.updated_variants);

                  $('#variantModalAlert').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fa fa-check-circle"></i> All variant stocks updated successfully!</div>').show();
                  setTimeout(function() { $('#variantModalAlert').fadeOut(); }, 2500);
               } else {
                  alert(res.message || 'Failed to update variant stocks');
               }
            },
            error: function() {
               btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save All Variant Stocks');
               alert('Server error updating variant stocks');
            }
         });
      });

      // 4. Add New Variant Form Submit inside Modal
      $('#formAddNewVariant').on('submit', function(e) {
         e.preventDefault();
         var submitBtn = $('#btnAddVariantSubmit');
         var color = $('#new_v_color').val();
         var size = $('#new_v_size').val();
         var style = $('#new_v_style').val();
         var price = $('#new_v_price').val();
         var qty = $('#new_v_qty').val();

         if (!color && !size && !style) {
            alert('Please select at least Color or Size for the variant.');
            return;
         }

         submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');

         $.ajax({
            url: 'ajax/item_variant_stock',
            type: 'POST',
            data: {
               action: 'add_variant',
               item_id: currentModalItemId,
               color_name: color,
               size_name: size,
               style_name: style,
               price: price,
               quantity: qty
            },
            dataType: 'json',
            success: function(res) {
               submitBtn.prop('disabled', false).html('<i class="fa fa-plus"></i> Add Variant');
               if (res.status === 'success') {
                  $('#new_v_color').val('');
                  $('#new_v_size').val('');
                  $('#new_v_style').val('');
                  $('#new_v_price').val('');
                  $('#new_v_qty').val('0');

                  loadItemVariants(currentModalItemId);

                  $('#variantModalAlert').html('<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fa fa-check-circle"></i> New variant added successfully!</div>').show();
                  setTimeout(function() { $('#variantModalAlert').fadeOut(); }, 2500);
               } else {
                  alert(res.message || 'Failed to add variant');
               }
            },
            error: function() {
               submitBtn.prop('disabled', false).html('<i class="fa fa-plus"></i> Add Variant');
               alert('Server error adding variant');
            }
         });
      });
   });
}
</script>
