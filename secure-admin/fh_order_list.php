<?php include_once("include/config.php"); ?>
<?php require_once(_BASEPATH . 'include/generatepdf.php');
?>
<?php
if (isset($_GET['delete_id'])) {
   $id = $_GET['delete_id'];
   $sqld = "DELETE FROM tbl_user_master WHERE id='$id'";
   $res = mysqli_query($conn, $sqld);
   if ($res) {
      header('refresh:.5; url=fh_usermastercreation_list.php');
   } else {
      echo "<script>alert('User Master Record Failed');</script>";
   }
}
?>
<?php include('include/header.php'); ?>
<link rel="stylesheet" href="<?= _BASEURL ?>assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
<style>
   .datepicker {
      z-index: 9999 !important;
   }

   .img-popup-trigger {
      cursor: pointer;
      transition: transform 0.2s;
   }

   .img-popup-trigger:hover {
      transform: scale(1.05);
   }

   .multicheck-dropdown {
      position: relative !important;
      display: inline-block !important;
   }

   .multicheck-dropdown .dropdown-menu {
      display: none;
      position: absolute !important;
      top: 100% !important;
      left: 0 !important;
      z-index: 99999 !important;
      background: #ffffff !important;
      border: 1px solid #d2d6de !important;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.18) !important;
      min-width: 230px !important;
   }

   .multicheck-dropdown.open .dropdown-menu {
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
   }

   .multicheck-dropdown .dropdown-menu li label {
      padding: 4px 8px;
      border-radius: 3px;
      transition: background-color 0.15s;
      width: 100%;
      cursor: pointer;
      font-weight: normal;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
   }

   .multicheck-dropdown .dropdown-menu li label:hover {
      background-color: #f4f6f9;
   }

   #paymentStatusDropdown:hover,
   #dispatchStatusDropdown:hover,
   #paymentMethodDropdown:hover {
      background-color: #e6f7f5 !important;
      border-color: #009688 !important;
   }

   .panel-body {
      overflow-x: visible !important;
   }

   .table-responsive {
      overflow-x: auto !important;
      overflow-y: visible !important;
      -webkit-overflow-scrolling: touch;
      position: relative;
      min-height: 250px;
      margin-bottom: 20px;
      border: 1px solid #e4e5e7;
      border-radius: 4px;
      background: #fff;
   }

   /* Prominent custom horizontal scrollbar */
   .table-responsive::-webkit-scrollbar {
      height: 10px;
   }
   .table-responsive::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 5px;
   }
   .table-responsive::-webkit-scrollbar-thumb {
      background: #009688;
      border-radius: 5px;
   }
   .table-responsive::-webkit-scrollbar-thumb:hover {
      background: #00796b;
   }

   #dataTableExample1 {
      min-width: 2200px !important;
      width: 100%;
      margin-bottom: 0;
   }

   #dataTableExample1 th,
   #dataTableExample1 td {
      white-space: nowrap !important;
      vertical-align: middle !important;
      padding: 8px 10px !important;
   }
</style>

<!-- Main content -->
<section class="">
   <div class="row">
      <div class="col-sm-12">
         <div class="panel panel-bd lobidisable">
            <div class="panel-heading">
               <span style="font-size: 15px;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                     <defs>
                        <clipPath id="lineMdWatchTwotoneLoop0">
                           <rect width="24" height="12" />
                        </clipPath>
                        <symbol id="lineMdWatchTwotoneLoop1">
                           <path fill="#808080" fill-opacity="0" stroke="#808080" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z" clip-path="url(#lineMdWatchTwotoneLoop0)">
                              <animate attributeName="d" dur="6s" keyTimes="0;0.07;0.93;1" repeatCount="indefinite" values="M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 10.4249 18.0751 5.5 12 5.5C5.92487 5.5 1 10.4249 1 16.5z;M23 16.5C23 11.5 18.0751 12 12 12C5.92487 12 1 11.5 1 16.5z" />
                              <animate fill="freeze" attributeName="fill-opacity" begin="0.6s" dur="0.15s" values="0;0.3" />
                           </path>
                        </symbol>
                        <mask id="lineMdWatchTwotoneLoop2">
                           <use href="#lineMdWatchTwotoneLoop1" />
                           <use href="#lineMdWatchTwotoneLoop1" transform="rotate(180 12 12)" />
                           <circle cx="12" cy="12" r="0" fill="#fff">
                              <animate attributeName="r" dur="6s" keyTimes="0;0.03;0.97;1" repeatCount="indefinite" values="0;3;3;0" />
                           </circle>
                        </mask>
                     </defs>
                     <rect width="24" height="24" fill="currentColor" mask="url(#lineMdWatchTwotoneLoop2)" />
                  </svg>
                  <span>Orders List !</span>
               </span>
            </div>
            <div class="panel-body">
               <div class="btn-group">
                  <a href="fh_usermastercreation.php"> <button class="button-btn-btn btn-exp btn-sm"><i class="fa fa-plus" style="margin-right: 5px;"></i>Orders List</button></a>
               </div>
               <?php include('include/button-export-to-data.php'); ?>
               <button type="button" class="btn btn-warning btn-sm" id="btnDispatchSelected" disabled style="margin-left: 10px; padding: 4px 12px; font-weight: bold;">
                  <i class="fa fa-truck" style="margin-right: 5px;"></i> Mark as Dispatched (<span id="selectedCount">0</span>)
               </button>
               <div class="container-fluid" style="padding: 0; margin-top: 15px; margin-bottom: 15px;">
                  <!-- Summary Metric Badges Row -->
                  <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 15px; padding: 0 5px;">
                     <div style="background: #fff; border: 1px solid #e4e5e7; border-radius: 4px; padding: 8px 14px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <i class="fa fa-cubes text-primary" style="font-size: 14px;"></i>
                        <span><strong>Total Qty:</strong> <span id="totalQty" style="font-weight: bold; color: #333;">0</span></span>
                     </div>
                     <div style="background: #fff; border: 1px solid #e4e5e7; border-radius: 4px; padding: 8px 14px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <i class="fa fa-calendar-check-o text-info" style="font-size: 14px;"></i>
                        <span><strong>Current Qty (Today):</strong> <span id="currentQty" style="font-weight: bold; color: #333;">0</span></span>
                     </div>
                     <div style="background: #fff; border: 1px solid #e4e5e7; border-radius: 4px; padding: 8px 14px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <i class="fa fa-money text-danger" style="font-size: 14px;"></i>
                        <span><strong>Pending COD Balance:</strong> <span id="pendingBalance" class="text-danger" style="font-weight: bold;">₹0.00</span></span>
                     </div>
                  </div>

                  <!-- Search & Filter Controls Row -->
                  <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px; padding: 0 5px;">
                     <div style="flex: 1; min-width: 200px;">
                        <input type="text" class="form-control" name="search" id="full_search" placeholder="Search (Customer, Title, Order ID, Address...)" style="height: 34px;">
                     </div>
                     <div style="min-width: 120px;">
                        <input type="text" class="form-control" name="from_date" id="from_date" placeholder="From Date" autocomplete="off" style="height: 34px;">
                     </div>
                     <div style="min-width: 120px;">
                        <input type="text" class="form-control" name="to_date" id="to_date" placeholder="To Date" autocomplete="off" style="height: 34px;">
                     </div>
                     <button type="button" class="btn btn-primary btn-sm" id="btnFilter" style="background-color: #009688; border-color: #009688; height: 34px; padding: 6px 16px; font-weight: bold;"><i class="fa fa-search"></i> Search</button>
                     <button type="button" class="btn btn-default btn-sm" id="btnResetFilter" style="height: 34px; padding: 6px 14px;"><i class="fa fa-refresh"></i> Reset</button>
                  </div>
               </div>
               <form name="searchForm" id="searchForm" method="post" class="search-form" action="" autocomplete="off">
                  <input type="hidden" name="export" value="" />
                  <input type="hidden" name="page" value="1" />
                  <input type="hidden" name="sortOrder" value="DESC" />
                  <input type="hidden" name="sortField" value="O.order_id" />
                  <input type="hidden" name="payment_status_filter" id="hidden_payment_status_filter" value="" />
                  <input type="hidden" name="dispatch_status_filter" id="hidden_dispatch_status_filter" value="" />
                  <input type="hidden" name="payment_method_filter" id="hidden_payment_method_filter" value="" />
                  <input type="hidden" name="search" id="hidden_search" value="" />
                  <input type="hidden" name="from_date" id="hidden_from_date" value="" />
                  <input type="hidden" name="to_date" id="hidden_to_date" value="" />
               </form>
               <div class="table-responsive" style="position: relative; min-height: 200px;">
                  <div id="overlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 99; text-align: center;">
                     <div style="position: absolute; top: 80px; left: 50%; transform: translateX(-50%); text-align: center;">
                        <i class="fa fa-spinner fa-spin fa-3x fa-fw" style="color: #009688;"></i>
                        <p style="margin-top: 10px; font-weight: bold; color: #333; font-size: 14px;">Loading Orders...</p>
                     </div>
                  </div>
                  <table id="dataTableExample1" class="table table-bordered table-striped table-hover">
                     <thead>
                        <tr class="info">
                           <th><input type="checkbox" id="checkAllOrders" style="cursor: pointer;"> SrNo</th>
                           <th>Action</th>
                           <th style="white-space: nowrap; position: relative;">
                              <div class="dropdown multicheck-dropdown" style="display: inline-block;">
                                 <span style="vertical-align: middle;">Payment Status</span>
                                 <button type="button" id="paymentStatusDropdown" class="btn btn-default btn-xs" style="margin-left: 6px; padding: 2px 7px; background: #fff; border: 1px solid #009688; border-radius: 3px; cursor: pointer; vertical-align: middle; line-height: 1.2;" title="Filter by Payment Status">
                                    <i class="fa fa-filter" id="paymentStatusFilterIcon" style="font-size: 12px; color: #009688;"></i>
                                    <span id="paymentStatusBadge" class="badge" style="background-color: #009688; font-size: 10px; padding: 2px 5px; margin-left: 2px; display: inline-block;">3</span>
                                 </button>
                                 <ul class="dropdown-menu" id="paymentStatusMenu" style="padding: 10px 14px; min-width: 230px; font-size: 13px; font-weight: normal; text-align: left; text-transform: none; color: #333; border-radius: 4px; left: 0; top: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.18); z-index: 1060;">
                                    <li style="margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                                       <label style="cursor: pointer; font-weight: bold; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" id="checkAllPaymentStatus" style="margin: 0; cursor: pointer;"> <span>Select All</span>
                                       </label>
                                       <a href="javascript:void(0);" id="clearPaymentStatus" style="font-size: 11px; color: #888; text-decoration: underline;">Clear</a>
                                    </li>
                                    <li style="margin-bottom: 5px;">
                                       <label style="cursor: pointer; font-weight: normal; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" class="payment-status-check" value="paid" checked style="margin: 0; cursor: pointer;"> <span class="label label-success" style="padding: 2px 6px; font-size: 10px;">PAID</span> <span>Paid</span>
                                       </label>
                                    </li>
                                    <li style="margin-bottom: 5px;">
                                       <label style="cursor: pointer; font-weight: normal; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" class="payment-status-check" value="partial_paid" checked style="margin: 0; cursor: pointer;"> <span class="label label-info" style="padding: 2px 6px; font-size: 10px;">PARTIAL</span> <span>Partial Paid</span>
                                       </label>
                                    </li>
                                    <li style="margin-bottom: 5px;">
                                       <label style="cursor: pointer; font-weight: normal; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" class="payment-status-check" value="cod" checked style="margin: 0; cursor: pointer;"> <span class="label label-primary" style="padding: 2px 6px; font-size: 10px;">COD</span> <span>Cash on Delivery</span>
                                       </label>
                                    </li>

                                    <li style="margin-bottom: 8px;">
                                       <label style="cursor: pointer; font-weight: normal; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" class="payment-status-check" value="pending" style="margin: 0; cursor: pointer;"> <span class="label label-default" style="padding: 2px 6px; font-size: 10px;">UNPAID</span> <span>Pending (Unpaid)</span>
                                       </label>
                                    </li>
                                    <li style="border-top: 1px solid #eee; padding-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                                       <button type="button" class="btn btn-default btn-xs" id="btnClosePaymentStatusMenu" style="font-size: 11px;">Close</button>
                                       <button type="button" class="btn btn-primary btn-xs" id="btnApplyPaymentStatusFilter" style="background-color: #009688; border-color: #009688; font-weight: bold; padding: 3px 12px;"><i class="fa fa-filter"></i> Apply Filter</button>
                                    </li>
                                 </ul>
                              </div>
                           </th>
                           <th style="white-space: nowrap; position: relative;">
                              <div class="dropdown multicheck-dropdown" style="display: inline-block;">
                                 <span style="vertical-align: middle;">Dispatch Status</span>
                                 <button type="button" id="dispatchStatusDropdown" class="btn btn-default btn-xs" style="margin-left: 6px; padding: 2px 7px; background: #fff; border: 1px solid #ccc; border-radius: 3px; cursor: pointer; vertical-align: middle; line-height: 1.2;" title="Filter by Dispatch Status">
                                    <i class="fa fa-filter" id="dispatchStatusFilterIcon" style="font-size: 12px; color: #777;"></i>
                                    <span id="dispatchStatusBadge" class="badge" style="background-color: #777; font-size: 10px; padding: 2px 5px; margin-left: 2px; display: none;">0</span>
                                 </button>
                                 <ul class="dropdown-menu" id="dispatchStatusMenu" style="padding: 10px 14px; min-width: 220px; font-size: 13px; font-weight: normal; text-align: left; text-transform: none; color: #333; border-radius: 4px; left: 0; top: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.18); z-index: 1060;">
                                    <li style="margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                                       <label style="cursor: pointer; font-weight: bold; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" id="checkAllDispatchStatus" style="margin: 0; cursor: pointer;"> <span>Select All</span>
                                       </label>
                                       <a href="javascript:void(0);" id="clearDispatchStatus" style="font-size: 11px; color: #888; text-decoration: underline;">Clear</a>
                                    </li>
                                    <li style="margin-bottom: 5px;">
                                       <label style="cursor: pointer; font-weight: normal; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" class="dispatch-status-check" value="pending" style="margin: 0; cursor: pointer;"> <span class="label label-warning" style="padding: 2px 6px; font-size: 10px;"><i class="fa fa-clock-o"></i></span> <span>Pending</span>
                                       </label>
                                    </li>
                                    <li style="margin-bottom: 5px;">
                                       <label style="cursor: pointer; font-weight: normal; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" class="dispatch-status-check" value="dispatched" style="margin: 0; cursor: pointer;"> <span class="label label-success" style="padding: 2px 6px; font-size: 10px;"><i class="fa fa-truck"></i></span> <span>Dispatched</span>
                                       </label>
                                    </li>
                                    <li style="margin-bottom: 5px;">
                                       <label style="cursor: pointer; font-weight: normal; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" class="dispatch-status-check" value="shadowfax" style="margin: 0; cursor: pointer;"> <span class="label label-info" style="padding: 2px 6px; font-size: 10px;"><i class="fa fa-truck"></i></span> <span>Shadowfax</span>
                                       </label>
                                    </li>
                                    <li style="margin-bottom: 5px;">
                                       <label style="cursor: pointer; font-weight: normal; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" class="dispatch-status-check" value="out_of_stock" style="margin: 0; cursor: pointer;"> <span class="label label-danger" style="padding: 2px 6px; font-size: 10px;"><i class="fa fa-ban"></i></span> <span>Out of Stock</span>
                                       </label>
                                    </li>
                                    <li style="margin-bottom: 8px;">
                                       <label style="cursor: pointer; font-weight: normal; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" class="dispatch-status-check" value="failed" style="margin: 0; cursor: pointer;"> <span class="label label-danger" style="padding: 2px 6px; font-size: 10px;"><i class="fa fa-exclamation-triangle"></i></span> <span>Failed</span>
                                       </label>
                                    </li>
                                    <li style="border-top: 1px solid #eee; padding-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                                       <button type="button" class="btn btn-default btn-xs" id="btnCloseDispatchStatusMenu" style="font-size: 11px;">Close</button>
                                       <button type="button" class="btn btn-primary btn-xs" id="btnApplyDispatchStatusFilter" style="background-color: #009688; border-color: #009688; font-weight: bold; padding: 3px 12px;"><i class="fa fa-filter"></i> Apply Filter</button>
                                    </li>
                                 </ul>
                              </div>
                           </th>
                           <th style="white-space: nowrap; position: relative;">
                              <div class="dropdown multicheck-dropdown" style="display: inline-block;">
                                 <span style="vertical-align: middle;">Payment Method</span>
                                 <button type="button" id="paymentMethodDropdown" class="btn btn-default btn-xs" style="margin-left: 6px; padding: 2px 7px; background: #fff; border: 1px solid #ccc; border-radius: 3px; cursor: pointer; vertical-align: middle; line-height: 1.2;" title="Filter by Payment Method">
                                    <i class="fa fa-filter" id="paymentMethodFilterIcon" style="font-size: 12px; color: #777;"></i>
                                    <span id="paymentMethodBadge" class="badge" style="background-color: #777; font-size: 10px; padding: 2px 5px; margin-left: 2px; display: none;">0</span>
                                 </button>
                                 <ul class="dropdown-menu" id="paymentMethodMenu" style="padding: 10px 14px; min-width: 210px; font-size: 13px; font-weight: normal; text-align: left; text-transform: none; color: #333; border-radius: 4px; left: 0; top: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.18); z-index: 1060;">
                                    <li style="margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                                       <label style="cursor: pointer; font-weight: bold; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" id="checkAllPaymentMethod" style="margin: 0; cursor: pointer;"> <span>Select All</span>
                                       </label>
                                       <a href="javascript:void(0);" id="clearPaymentMethod" style="font-size: 11px; color: #888; text-decoration: underline;">Clear</a>
                                    </li>
                                    <li style="margin-bottom: 5px;">
                                       <label style="cursor: pointer; font-weight: normal; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" class="payment-method-check" value="cod" style="margin: 0; cursor: pointer;"> <span class="label label-primary" style="padding: 2px 6px; font-size: 10px;"><i class="fa fa-money"></i></span> <span>Cash on Delivery (COD)</span>
                                       </label>
                                    </li>
                                    <li style="margin-bottom: 8px;">
                                       <label style="cursor: pointer; font-weight: normal; margin: 0; display: flex; align-items: center; gap: 8px;">
                                          <input type="checkbox" class="payment-method-check" value="razorpay" style="margin: 0; cursor: pointer;"> <span class="label label-success" style="padding: 2px 6px; font-size: 10px;"><i class="fa fa-credit-card"></i></span> <span>Razorpay / Online</span>
                                       </label>
                                    </li>
                                    <li style="border-top: 1px solid #eee; padding-top: 8px; display: flex; justify-content: space-between; align-items: center;">
                                       <button type="button" class="btn btn-default btn-xs" id="btnClosePaymentMethodMenu" style="font-size: 11px;">Close</button>
                                       <button type="button" class="btn btn-primary btn-xs" id="btnApplyPaymentMethodFilter" style="background-color: #009688; border-color: #009688; font-weight: bold; padding: 3px 12px;"><i class="fa fa-filter"></i> Apply Filter</button>
                                    </li>
                                 </ul>
                              </div>
                           </th>
                           <th>Order ID</th>
                           <th>Txn ID</th>
                           <th>Picture</th>
                           <th>Size</th>
                           <th>Qty</th>
                           <th>Customer</th>
                           <th>Title</th>
                           <th>Unit Price</th>
                           <th>Total Price</th>
                           <th>Shipping</th>
                           <th>Balance Amount</th>
                           <th>Order Date</th>
                           <th>Address</th>
                           <th>City</th>
                           <th>Pincode</th>
                           <th>Mobile No.</th>
                        </tr>
                     </thead>
                     <tbody id="latestRecord">

                     </tbody>
                  </table>
               </div>
               <div id="pagination-result">

               </div>
            </div>
         </div>
      </div>
   </div>

</section>
<form action="" id="deleteform" method="post" autocomplete="off">
   <input type="hidden" name="action" value="delete">
   <input type="hidden" name="id" id="deleteid">
</form>

<!-- Dispatch Confirmation Modal -->
<div class="modal fade" id="dispatchConfirmModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
   <div class="modal-dialog">
      <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.5);">
         <div class="modal-header" style="background-color: #f39c12; color: white; padding: 12px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.9; font-size: 24px;">&times;</button>
            <h4 class="modal-title" style="margin: 0; font-size: 16px; font-weight: 600;"><i class="fa fa-truck" style="margin-right: 8px;"></i>Confirm Order Dispatch</h4>
         </div>
         <div class="modal-body text-center" style="padding: 25px 20px;">
            <i class="fa fa-question-circle fa-4x text-warning" style="margin-bottom: 15px; color: #f39c12;"></i>
            <h4 style="font-weight: bold; margin-bottom: 10px;">Are you sure?</h4>
            <p style="font-size: 14px; color: #555;">You are about to mark <strong id="dispatchModalCount" style="color: #f39c12;">0</strong> selected order(s) as <span class="label label-warning">DISPATCHED</span>.</p>
         </div>
         <div class="modal-footer" style="background-color: #f8f9fa; padding: 10px 20px;">
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-warning btn-sm" id="btnConfirmDispatchAction"><i class="fa fa-check" style="margin-right: 5px;"></i> Yes, Mark Dispatched</button>
         </div>
      </div>
   </div>
</div>

<!-- Single Order Dispatch Status Update Modal -->
<div class="modal fade" id="updateDispatchStatusModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
   <div class="modal-dialog">
      <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.5);">
         <div class="modal-header" style="background-color: #009688; color: white; padding: 12px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.9; font-size: 24px;">&times;</button>
            <h4 class="modal-title" style="margin: 0; font-size: 16px; font-weight: 600;"><i class="fa fa-truck" style="margin-right: 8px;"></i>Update Dispatch Status - Order #<span id="singleModalOrderIdDisplay"></span></h4>
         </div>
         <div class="modal-body" style="padding: 20px;">
            <form id="singleDispatchStatusForm">
               <input type="hidden" id="singleModalOrderId" name="order_id" value="">
               <input type="hidden" id="singleModalItemId" name="item_id" value="">
               <div class="form-group">
                  <label for="singleModalStatusSelect" style="font-weight: 600; margin-bottom: 8px;">Select Dispatch Status:</label>
                  <select id="singleModalStatusSelect" name="dispatch_status" class="form-control" style="height: 40px; font-size: 14px;">
                     <option value="pending">Pending</option>
                     <option value="shadowfax">Shadowfax</option>
                     <option value="dispatched">Dispatched</option>
                     <option value="skipped_test">Skipped (Test Item)</option>
                     <option value="out_of_stock">Out of Stock</option>
                     <option value="failed">Failed</option>
                  </select>
               </div>
            </form>
         </div>
         <div class="modal-footer" style="background-color: #f8f9fa; padding: 10px 20px;">
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary btn-sm" id="btnSaveSingleDispatchStatus" style="background-color: #009688; border-color: #009688;"><i class="fa fa-save" style="margin-right: 5px;"></i> Update Status</button>
         </div>
      </div>
   </div>
</div>


<!-- Full Size Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
   <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 6px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.5);">
         <div class="modal-header" style="background-color: #009688; color: white; padding: 12px 20px;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: white; opacity: 0.9; font-size: 24px;">&times;</button>
            <h4 class="modal-title" id="imagePreviewTitle" style="margin: 0; font-size: 16px; font-weight: 600;"><i class="fa fa-picture-o" style="margin-right: 8px;"></i>Product Image Preview</h4>
         </div>
         <div class="modal-body text-center" style="background-color: #111; padding: 20px; min-height: 300px; display: flex; align-items: center; justify-content: center;">
            <img id="imagePreviewSrc" src="" alt="Product Image" style="max-height: 75vh; max-width: 100%; object-fit: contain; border-radius: 4px; box-shadow: 0 4px 20px rgba(0,0,0,0.6);">
         </div>
         <div class="modal-footer" style="background-color: #f8f9fa; padding: 10px 20px;">
            <a id="imagePreviewLink" href="#" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-external-link" style="margin-right: 5px;"></i> Open Full Size Image</a>
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>
<?php include('include/footer-2.php'); ?>
<script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="assets/dist/js/page/order-list.js?v=<?= time(); ?>" type="text/javascript"></script>