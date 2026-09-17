<?php include_once("include/config.php"); ?>
<?php
$validationHelper = new validation();

$db = connect();

$bill_no = $_GET['bill_no'];
$type = $_GET['type'];

if ($bill_no) {

  if($type == 'sale'){
    $stmt = $db->select("SELECT * FROM tbl_bill WHERE bill_number=?", 's', $bill_no);
    $style = 'style1';
  } else if($type == 'purchase'){
    $stmt = $db->select("SELECT * FROM tbl_purchase WHERE bill_number=?", 's', $bill_no);

    $style = '';
  }
  

  $res = $stmt->fetch_assoc();

  foreach ($res as $key => $value) {
    $$key = $validationHelper->filterText($value);
  }
  $stmt->close();
}

?>

<?php include('include/invoice-header.php'); ?>

  <body>
    <div class="cs-container <?= $style ?>">
      <div class="cs-invoice cs-style1 padding_40">
        <div class="cs-invoice_in" id="download_section">
          <div>
            
            <div
              class="cs-invoice_head cs-type1 column border-bottom-none cs-p0"
            >
              <div class="display-flex justify-content-center cs-width_12">
                <div class="cs-text_center">
                  <p class="cs-f14 cs-primary_color cs-bold cs-mb2">
                    <span style="font-size: 25px; color:#FF7F50">RAMALAYA</span>
                  </p>
                  <p class="cs-f14 cs-primary_color cs-bold cs-mb2">
                    Lal Bahadur Shastri International Airport, Varanasi Uttar Pradesh 221006.
                  </p>
                  <p class="cs-mb0 cs-primary_color cs-f12">
                    09AANCM4929Q1ZG
                  </p>
                  <p class="cs-mb0 cs-primary_color cs-f12">
                    Customer Care +91 766 904 7613
                  </p>
                  <p class="cs-mb0 cs-primary_color cs-f12">
                    Email: info@prabhushriram.com
                  </p>
                  <p class="cs-mb0 cs-primary_color cs-f12">www.prabhushriram.com</p>
                </div>
              </div>
            </div>
          </div>

          <div class="cs-border cs-mb20 cs-mt12"></div>
          <div class="cs-f12">
            <div class="display-flex justify-content-space-between flex-wrap">
              <div class="cs-width_6">
                <div class="display-flex">
                  <p class="cs-mb5">Date:&nbsp;</p>
                  <p class="cs-primary_color cs-mb5"><?= dateformat($purchage_date) ?></p>
                </div>
                <div class="display-flex">
                  <p class="cs-mb5">Salesperson:&nbsp;</p>
                  <p class="cs-primary_color cs-mb5"><?= $_SESSION['login_user']; ?></p>
                </div>
              </div>
              <div class="cs-width_6">
                <div class="display-flex">
                  <p class="cs-mb5">Time:&nbsp;</p>
                  <p class="cs-primary_color cs-mb5">11:30 AM</p>
                </div>
                <div class="display-flex">
                  <p>Bill Number:&nbsp;</p>
                  <p class="cs-primary_color"><?= $bill_number ?></p>
                </div>
              </div>
            </div>
          </div>
          <div class="cs-border cs-mb10"></div>
          <div class="cs-width_12 cs-f12">
            <div class="display-flex">
              <p class="cs-mb5">Customer:&nbsp;</p>
              <p class="cs-primary_color cs-mb5"><?= $account_name ?></p>
            </div>
            <div class="display-flex">
              <!--<p class="cs-mb5">Email:&nbsp;</p>-->
              <!--<p class="cs-primary_color cs-mb5">jane@example.com</p>-->
            </div>
            <div class="display-flex">
              <p>Phone:&nbsp;</p>
              <p class="cs-primary_color"><?= $mobile_number ?></p>
            </div>
          </div>
          <div class="cs-border"></div>
          <div class="cs-table cs-style2 padding-rignt-left cs-f12">
            <table>
              <thead>
                <tr class="cs-f12 cs-border_bottom style_1">
                  <th class="cs-width_6 cs-normal cs-primary_color">Description</th>
                  <th class="cs-width_2 cs-normal cs-primary_color">Unit Price</th>
                  <th class="cs-width_2 cs-normal cs-primary_color">Qty</th>
                  <th class="cs-width_2 cs-normal cs-primary_color">Net Amount</th>
                  <th class="cs-normal cs-primary_color cs-text_left">
                    Total Amount
                  </th>
                </tr>
              </thead>
              <tbody class="cs-f12 tm-border-none">
                    <?php
                  $query = $db->select("SELECT * FROM tbl_bill WHERE bill_number=? ORDER BY id DESC", 's', $bill_no);
                  $rowcount = $query->num_rows();
                  if ($rowcount > 0) {
                    $gst_amount = '0.00';
                    $total_amount = '0.00';
                    for ($i = 1; $i <= $rowcount; $i++) {
                      $row = $query->fetch_assoc();

                      $gst_amount += (float)$row['gst_amount']; 
                      $total_amount += (float)$row['amount']; 
                      $total_taxable_amount += (float)$row['taxable_amount']; 
                  ?>
                <tr>
                  <td class="cs-width_6 cs-p0 cs-p-t10 cs-p-b5">
                    <?= $row['item_name'] ?> - HSN :<?= '33074100' ?>
                  </td>
                  <td class="cs-width_2 cs-text_left cs-primary_color cs-p0 cs-p-b5">
                    <?= $row['purchase_price'] ?>
                  </td>
                  <td class="cs-width_2 cs-primary_color cs-p0 cs-p-b5">&nbsp;<?= $row['qty'] ?></td>
                  <td class="cs-width_2 cs-text_left cs-primary_color cs-p0 cs-p-b5">
                   <?= $row['net_price'] ?>
                  </td>
                  <td class="cs-text_right cs-primary_color cs-p0 cs-p-b5">
                    <?= $row['taxable_amount'] ?>
                  </td>
                </tr>
                <?php
                    }
                  } else {
                    ?>
                  <?php } ?>
                
                
              </tbody>
            </table>
          </div>
          <div class="cs-border"></div>
          <div class="cs-table cs-style2 padding-rignt-left">
            <table>
              <tbody class="cs-f12 tm-border-none">
                <tr>
                  <td class="cs-p0 cs-p-t10 cs-p-b5">Subtotal</td>
                  <td
                    class="cs-text_right cs-primary_color cs-p0 cs-p-t10 cs-p-b5"
                  >
                    ₹ <?= number_format($total_taxable_amount, 2) ?>
                  </td>
                </tr>
                 <?php if ($under_gst == 'Inter') { ?>
                      <tr class="cs-border_left">
                        <td class="cs-p0 cs-p-b10">IGST 5%</td>
                        <td class="cs-text_right cs-primary_color cs-p0 cs-p-b10">₹ <?= $gst_amount ?></td>
                      </tr>
                 <?php } else if ($under_gst == 'Local') { ?>
                <tr>
                  <td class="cs-p0 cs-p-b10">CGST 2.5%:</td>
                  <td class="cs-text_right cs-primary_color cs-p0 cs-p-b10">
                    ₹ <?=getGSTValueBygstType($gst_amount)?>
                  </td>
                </tr>
                <tr>
                  <td class="cs-p0 cs-p-b10">SGST 2.5%</td>
                  <td class="cs-text_right cs-primary_color cs-p0 cs-p-b10">
                   ₹ <?=getGSTValueBygstType($gst_amount)?>
                  </td>
                </tr>
                <tr>
                  <td class="cs-p0 cs-p-b10">Tax Amount</td>
                  <td class="cs-text_right cs-primary_color cs-p0 cs-p-b10">
                    ₹ <?= number_format($gst_amount, 2) ?>
                  </td>
                </tr>
                 <?php } ?>
              </tbody>
            </table>
          </div>
          <div class="cs-border"></div>
          <div class="cs-table cs-style2 padding-rignt-left">
            <table>
              <tbody class="cs-f12 tm-border-none">
                <tr>
                  <td class="cs-p0 cs-p-t10 cs-p-b10">Total Amount</td>
                  <td
                    class="cs-text_right cs-primary_color cs-p0 cs-p-t10 cs-p-b10"
                  >
                    ₹ <?= number_format($total_amount, 2) ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="cs-border cs-mb15"></div>
          <div class="cs-width_12 cs-f12">
            <div class="display-flex">
              <p class="cs-mb5">Payment Method&nbsp;</p>
              <p class="cs-primary_color cs-m0"><span style="margin-left: 125px;"><?=  getPayModeByCode($pay_mode) ?></span></p>
            </div>
            <!-- <div class="display-flex">
              <p class="cs-mb10">Card Ending:&nbsp;</p>
              <p class="cs-primary_color cs-m0">**** 5678</p>
            </div> -->
          </div>

          <div class="cs-border cs-mb30"></div>
          <p class="cs-text_center cs-f16 cs-primary_color">
            Thank you for shopping with us!
          </p>
        </div>
        <div class="cs-invoice_btns cs-hide_print">
        <a href="javascript:window.print()" class="cs-invoice_btn cs-color1">
          <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
            <path d="M384 368h24a40.12 40.12 0 0040-40V168a40.12 40.12 0 00-40-40H104a40.12 40.12 0 00-40 40v160a40.12 40.12 0 0040 40h24" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
            <rect x="128" y="240" width="256" height="208" rx="24.32" ry="24.32" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
            <path d="M384 128v-24a40.12 40.12 0 00-40-40H168a40.12 40.12 0 00-40 40v24" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
            <circle cx="392" cy="184" r="24" />
          </svg>
          <span>Print</span>
        </a>
        <button id="download_btn" class="cs-invoice_btn cs-color2">
          <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
            <title>Download</title>
            <path d="M336 176h40a40 40 0 0140 40v208a40 40 0 01-40 40H136a40 40 0 01-40-40V216a40 40 0 0140-40h40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" />
            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 272l80 80 80-80M256 48v288" />
          </svg>
          <span>Download</span>
        </button>
      </div>
      </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jspdf.min.js"></script>
    <script src="assets/js/html2canvas.min.js"></script>
    <script src="assets/js/main.js"></script>
  </body>
<?php include('include/invoice-footer.php'); ?>


