<aside class="main-sidebar">
   <!-- sidebar -->
   <div class="sidebar">
      <!-- sidebar menu -->
      <ul class="sidebar-menu">
         <li class="sidebar-toggle" data-toggle="offcanvas">
            <a href=""><i class="fa fa-bars"></i><span style="font-style: !important; font-size: 15px;"> Open Sidebar</span>
               <span class="pull-right-container">
               </span>
            </a>
         </li>
         <li class="active">
            <a href="fh_dashboard.php"><i class="hvr-buzz-out fa fa-home"></i><span style="font-style: !important; font-size: 13px;">Dashboard</span>
               <span class="pull-right-container">
               </span>
            </a>
         </li>
         <li class="treeview">
            <a href="#">
               <i class="fa fa-users"></i><span>ERP Master</span><br><span style="margin-left:30px;">Management !</span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#">
                     <i class="fa fa-users"></i><span style="font-style: !important; font-size: 15px;">User Master</span>

                     <i class="fa fa-angle-left pull-right"></i>

                  </a>
                  <ul class="treeview-menu">
                     <li><a href="fh_usermastercreation.php">User Master Creation</a></li>
                     <li><a href="fh_usermastercreation_list.php">User Master List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>

               </li>

               <li>
                  <a href="#"><i class="fa fa-users"></i><span style="font-style: !important; font-size: 15px;">User Permission</span></a>
               </li>
               <?php include('include/divider-dotted.php'); ?>


               <li>
                  <a href="#">
                     <i class="fa fa-users"></i><span style="font-style: !important; font-size: 15px;">Store Master</span>
                     <i class="fa fa-angle-left pull-right"></i>
                  </a>
                  <ul class="treeview-menu">
                     <li><a href="fh_storemastercreation.php">Store Master Creation</a></li>
                     <li><a href="fh_storemastercreation_list.php">Store Master List</a></li>
                  </ul>


               </li>


               <?php include('include/divider-dotted.php'); ?>







               <li class="treeview">
                  <a href="#">
                     <i class="fa fa-cube"></i><span style="font-style: !important; font-size: 15px;">Item Master</span>

                     <i class="fa fa-angle-left pull-right"></i>

                  </a>
                  <ul class="treeview-menu">


                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Category|Group</span><span>Master</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_groupmastercreation.php">Category|Group <br>Creation</a></li>
                           <li><a href="fh_groupmastercreation_list.php">Category|Group List</a></li>

                        </ul>

                     </li>

                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px; margin-bottom: 35px;">SubCategory|SubGroup</span>
                           <br><span style="margin-left:10px;">Master</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_subgroupmastercreation.php">SubCategory|SubGroup<br>Creation</a></li>
                           <li><a href="fh_subgroupmastercreation_list.php">SubCategory|SubGroup<br>List</a></li>
                        </ul>

                     </li>
                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Brand Master</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_brandmastercreation.php">Brand Master Creation</a></li>
                           <li><a href="fh_brandmastercreation_list.php">Brand Master List</a></li>
                        </ul>

                     </li>
                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Color Master</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_colormastercreation.php">Color Master Creation</a></li>
                           <li><a href="fh_colormastercreation_list.php">Color Master List</a></li>
                        </ul>

                     </li>
                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Size Master</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_sizemastercreation.php">Size Master Creation</a></li>
                           <li><a href="fh_sizemastercreation_list.php">Size Master List</a></li>
                        </ul>

                     </li>
                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Style|Design Master</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_styledesignmastercreation.php">Style|Design Creation</a></li>
                           <li><a href="fh_styledesignmastercreation_list.php">Style|Design List</a></li>
                        </ul>
                     </li>

                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Item Master</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_itemmastercreation.php">Item Master Creation</a></li>
                           <li><a href="fh_itemmastercreation_list.php">Item Master List</a></li>
                           <li><a href="fh_itemoutofstock_list.php">Out of Stock List</a></li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php include('include/divider-dotted.php'); ?>
               <li class="treeview">
                  <a href="#">
                     <i class="fa fa-percent"></i><span style="font-style: !important; font-size: 13px;">GST Master<br><span style="margin-left:20px;">Management</span></span>
                     <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                     </span>
                  </a>
                  <ul class="treeview-menu">
                     <li>
                        <a href="#"><span style="font-style: !important; font-size: 13px;">GST Master</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_gstmastercreation.php">GST Creation</a></li>
                           <li><a href="fh_gstmastercreation_list.php">GST List</a></li>
                        </ul>

                     </li>
                     <li>
                        <a href="#"><span style="font-style: !important; font-size: 13px;">GST Reports</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_storeprofilecreation.php">GST Report 1</a></li>
                           <li><a href="fh_storeprofilecreation_list.php">GST Report 2</a></li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php include('include/divider-dotted.php'); ?>
               <li class="treeview">
                  <a href="#">
                     <i class="fa fa-credit-card"></i><span style="font-style: !important; font-size: 13px;">Payment Mode Master<br><span style="margin-left:20px;"> Management</span></span>
                     <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                     </span>
                  </a>
                  <ul class="treeview-menu">
                     <li><a href="fh_paymodemastercreation.php">Payment Mode Creation</a></li>
                     <li><a href="fh_paymodemastercreation_list.php">Payment Mode List</a></li>
                  </ul>
               </li>
               <?php include('include/divider-dotted.php'); ?>
               <li class="treeview">
                  <a href="#">
                     <i class="fa fa-tags"></i><span style="font-style: !important; font-size: 13px;">Rate Master<br><span style="margin-left:20px;"> Management</span></span>
                     <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                     </span>
                  </a>
                  <ul class="treeview-menu">
                     <li><a href="ssenterprises_usermastercreation.php">Rate Master Creation</a></li>
                     <li><a href="ssenterprises_usermastercreation_list.php">Rate Master List</a></li>
                  </ul>
               </li>
               <?php include('include/divider-dotted.php'); ?>
               <li class="treeview">
                  <a href="#">
                     <i class="fa fa-percent"></i><span style="font-style: !important; font-size: 13px;">Discount Master<br><span style="margin-left:20px;"> Management</span></span>
                     <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                     </span>
                  </a>
                  <ul class="treeview-menu">
                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Festival Master</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_festivalmastercreation.php">Festival Master Creation</a></li>
                           <li><a href="fh_festivalmastercreation_list.php">Festival Master List</a></li>
                        </ul>

                     </li>
                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Point Master</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_pointmastercreation.php">Point Master Creation</a></li>
                           <li><a href="fh_pointmastercreation_list.php">Point Master List</a></li>
                        </ul>

                     </li>
                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">Coupon Master</span></a>
                        <ul class="treeview-menu">
                           <li><a href="fh_couponmastercreation.php">Coupon Master<br> Creation</a></li>
                           <li><a href="fh_couponmastercreation_list.php">Coupon Master List</a></li>
                           <li><a href="fh_couponmastercreation_view.php">Coupon Master View</a></li>
                        </ul>

                     </li>
                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px; margin-bottom: 35px;">Discount Master</a>
                        <ul class="treeview-menu">
                           <li><a href="fh_discountmastercreation.php">Discount Master<br> Creation</a></li>
                           <li><a href="fh_discountmastercreation_list.php">Discount Master List</a></li>
                        </ul>
                     </li>
                  </ul>
               </li>




            </ul>

         </li>

         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


         <li class="treeview">
            <a href="#">
               <i class="fa fa-cubes"></i><span style="font-style: !important; font-size: 13px;">Inventory|Purchase Master<br><span style="margin-left:30px;"> Management</span></span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Inventory ||<br> Purchase Order (PO)</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_festivalmastercreation.php">Inventory ||<br> Purchase Order (PO)<br>Creation</a></li>
                     <li><a href="fh_festivalmastercreation_list.php">Inventory ||<br> Purchase Order (PO)<br> BillWise List</a></li>
                     <li><a href="fh_festivalmastercreation_list.php">Inventory ||<br> Purchase Order (PO)<br> ItemWise List</a></li>
                     <li><a href="fh_festivalmastercreation_list.php">Inventory ||<br> Purchase Order (PO)<br> Report</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span>Inventory || Purchase</span></a>
                  <ul class="treeview-menu">
                     <li><a href="purchase-master-creation.php">Inventory || Purchase<br>Creation</a></li>
                     <li><a href="purchase-master-billwise-list.php">Inventory || Purchase<br>BillWise List</a></li>
                     <li><a href="purchase-master-itemwise-list.php">Inventory || Purchase<br>ItemWise List</a></li>
                     <li><a href="purchase-master-report.php">Inventory || Purchase<br>Report</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Inventory || Purchase<br>Return</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_couponmastercreation.php">Inventory || Purchase<br>Return Creation</a></li>
                     <li><a href="fh_couponmastercreation_list.php">Inventory || Purchase<br>Return BillWise List</a></li>
                     <li><a href="fh_couponmastercreation_view.php">Inventory || Purchase<br>Return ItemWise List</a></li>
                     <li><a href="fh_couponmastercreation_view.php">Inventory || Purchase<br>Return Report</a></li>
                  </ul>
               </li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
               <i class="fa fa-barcode"></i><span style="font-style: !important; font-size: 13px;">Barcode Master<br><span style="margin-left:30px;"> Management</span></span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Barcode Print</span></a>
                  <ul class="treeview-menu">
                     <li><a href="tejaserp-inventorybarcodeprint.php">Inventory || Purchase<br>Barcode Print</a></li>
                     <li><a href="tejaserp-inventorybarcodeprint-list.php">Inventory || Purchase<br>Barcode Print List</a></li>
                     <li><a href="tejaserp-inventorybarcodeprint-report.php">Inventory || Purchase<br>Barcode Print Report</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Barcode Re Print</span></a>
                  <ul class="treeview-menu">
                     <li><a href="tejaserp-inventorybarcodeprint-re-print.php">Inventory || Purchase<br>Barcode Re Print</a></li>
                     <li><a href="fh_festivalmastercreation_list.php">Inventory || Purchase<br>Barcode Re Print List</a></li>
                     <li><a href="fh_festivalmastercreation_list.php">Inventory || Purchase<br>Barcode Re Print Report</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Regular Barcode Print</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_couponmastercreation.php">Regular Barcode Print</a></li>
                     <li><a href="fh_couponmastercreation_list.php">Regular Barcode Print List</a></li>
                  </ul>
               </li>
            </ul>
         </li>
         <li class="treeview">
            <a href="#">
               <i class="fa fa-exchange"></i><span style="font-style: !important; font-size: 13px;">Stock Transfer<br><span style="margin-left:30px;"> Management</span></span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Stock Transfer</span></a>
                  <ul class="treeview-menu">
                     <li><a href="tejaserp-stocktransfer-creation.php">Stock Transfer<br>Creation</a></li>
                     <li><a href="fh_festivalmastercreation_list.php">Stock Transfer<br> BillWise List</a></li>
                     <li><a href="fh_festivalmastercreation_list.php">Stock Transfer<br> ItemWise List</a></li>
                     <li><a href="fh_festivalmastercreation_list.php">Stock Transfer<br> Report</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Stock Transfer Return</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_pointmastercreation.php">Stock Transfer Return<br>Creation</a></li>
                     <li><a href="fh_pointmastercreation_list.php">Stock Transfer Return<br>BillWise List</a></li>
                     <li><a href="fh_pointmastercreation_list.php">Stock Transfer Return<br>ItemWise List</a></li>
                     <li><a href="fh_pointmastercreation_list.php">Stock Transfer Return<br>Report</a></li>
                  </ul>
               </li>
            </ul>
         </li>
         <!--bill sale management-->
         <li class="treeview">
            <a href="#">
               <i class="fa fa-file-text-o"></i><span style="font-style: !important; font-size: 13px;">Bill || Sale<br><span style="margin-left:30px;"> Management</span></span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Bill || Sale </span></a>
                  <ul class="treeview-menu">
                     <li><a href="sale-creations.php">Bill || Sale<br>Creation</a></li>
                     <li><a href="tejaserp-sale-billwise-list.php">Sale BillWise List</a></li>
                     <li><a href="tejaserp-sale-itemwise-list.php">Sale ItemWise List</a></li>
                     <li><a href="tejaserp-sale-dsr-1.php">DSR-1</a></li>
                     <li><a href="tejaserp-sale-dsr-2.php">DSR-2</a></li>
                  </ul>

               </li>

            </ul>
         </li>
         <li class="treeview">
            <a href="#">
               <i class="fa fa-book"></i><span style="font-style: !important; font-size: 13px;">Accounts Master<br><span style="margin-left:30px;"> Management</span></span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Expense Head Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_expenses_headmastercreation.php">Expense Head Master<br>Creation</a></li>
                     <li><a href="fh_expensesheadmastercreation_list.php">Expense Head Master<br>List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Customer Type Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_customertypemastercreation.php">Customer Type Master<br>Creation</a></li>
                     <li><a href="fh_customertypemastercreation_list.php">Customer Type Master<br>List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Party Type</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_partytypemastercreation.php">Party Type<br>Creation</a></li>
                     <li><a href="fh_partytypemastercreation_list.php">Party Type<br>List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Account Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_accountmastercreation.php">Account Master<br>Creation</a></li>
                     <li><a href="fh_accountmastercreation_list.php">Account Master<br>List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Expense Entry</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_expensesmastercreation.php">Expense Entry<br>Creation</a></li>
                     <li><a href="fh_expensesmastercreation_list.php">Expense Entry<br>List</a></li>
                     <li><a href="fh_expensesmasterbalancesheet.php">Expense Entry<br>Balance Sheet</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Payment Entry</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_paymentmastercreation.php">Payment Entry<br>Creation</a></li>
                     <li><a href="fh_paymentmastercreation_list.php">Payment Entry<br>List</a></li>
                     <li><a href="">Payment Entry<br>Balance Sheet</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#">Ladger</a>
               </li>
            </ul>
         </li>
         <!--bill sale management-->
         <li class="treeview">
            <a href="#">
               <i class="fa fa-address-book"></i><span style="font-style: !important; font-size: 13px;">CRM<span style="margin-left:5px;">(Management)</span></span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">

               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">SalesMan Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_SalesManmastercreation.php">SalesMan Master<br>Creation</a></li>
                     <li><a href="fh_SalesManmastercreation_list.php">SalesMan Master<br>List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">SalesMan Target Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_Salesman_target_mastercreation.php">SalesMan Target Master<br>Creation</a></li>
                     <li><a href="fh_Salesman_target_mastercreation_list.php">SalesMan Target Master<br>List</a></li>
                  </ul>
               </li>
            </ul>
         </li>
         <!--reports management-->
         <li class="treeview">
            <a href="#">
               <i class="fa fa-bar-chart"></i><span style="font-style: !important; font-size: 13px;">Reports<br><span style="margin-left:30px;">(Management)</span></span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Stock Report</span></a>

                  <ul class="treeview-menu">
                     <li><a href="tejaserp-stockreport-inshortcut.php">Stock Report<br>(IN ShortCut)</a></li>
                     <li><a href="tejaserp-stockreport-indetails.php">Stock Report<br>(IN Details)</a></li>

                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Stock Transfer Report</span></a>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Sale Report</span></a>
               </li>
            </ul>
         </li>
         <!--attendance management-->
         <li class="treeview">
            <a href="#">
               <i class="fa fa-calendar-check-o"></i><span style="font-style: !important; font-size: 13px;">Attendance<br><span style="margin-left:30px;">(Management)</span></span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Customer Type Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_festivalmastercreation.php">Customer Type Master<br>Creation</a></li>
                     <li><a href="fh_festivalmastercreation_list.php">Customer Type Master<br>List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
            </ul>
         </li>
         <!--payroll management-->
         <li class="treeview">
            <a href="#">
               <i class="fa fa-money"></i><span style="font-style: !important; font-size: 13px;">PayRoll<br><span style="margin-left:30px;">(Management)</span></span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Customer Type Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_festivalmastercreation.php">Customer Type Master<br>Creation</a></li>
                     <li><a href="fh_festivalmastercreation_list.php">Customer Type Master<br>List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
            </ul>
         </li>
         <!-- /.website management -->
         <li class="treeview">
            <a href="#">
               <i class="fa fa-globe"></i><span style="font-style: !important; font-size: 13px;">WebSite<br><span style="margin-left:30px;">(Management)</span></span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">HomePage Manage</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_web_1strowcreation.php">1st Row Creations</a></li>
                     <li><a href="fh_web_1strowcreation_list.php">1st Row List</a></li>
                     <br>



                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
               <li class="treeview">
                  <a href="#">
                     <span style="font-style: !important; font-size: 13px;">Order Manage</span>
                     <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                     </span>
                  </a>
                  <ul class="treeview-menu">
                     <li>
                        <a href="#"><span style="background-color: #009688; color: white; padding: 6px; padding-right: 10px; padding-left: 10px;">All Order Manage</span>
                        </a>
                        <ul class="treeview-menu">
                           <li><a href="fh_order_list.php">All Order List</a></li>
                           <li><a href="fh_shadowfax_webhook.php">Shadowfax Webhook</a></li>
                           <li><a href="#">Confirm Order</a></li>
                           <li><a href="#">Processing Order</a></li>
                           <li><a href="#">Upcoming Order</a></li>
                           <li><a href="#">Cancel Order</a></li>
                           <li><a href="#">Pending Order</a></li>
                           <li><a href="#">Delivered Order</a></li>
                        </ul>
                     </li>
                     <br>
                  </ul>
            </ul>
         </li>
         <!--Manufacturing  management-->
         <li class="treeview">
            <a href="#">
               <i class="fa fa-industry"></i><span style="font-style: !important; font-size: 13px;">Manufacturing<br><span style="margin-left:30px;">(Management)</span></span>
               <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
               </span>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="#"><span style="font-style: !important; font-size: 13px;">Customer Type Master</span></a>
                  <ul class="treeview-menu">
                     <li><a href="fh_festivalmastercreation.php">Customer Type Master<br>Creation</a></li>
                     <li><a href="fh_festivalmastercreation_list.php">Customer Type Master<br>List</a></li>
                  </ul>
                  <?php include('include/divider-dotted.php'); ?>
               </li>
            </ul>
         </li>
      </ul>
      <!-- /.end reports management -->
      <!-- sidebar menu -->
      <!-- /.manufacturing management -->
   </div>
   <!-- /.sidebar -->
</aside>