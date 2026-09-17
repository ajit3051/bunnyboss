<?php include_once("../include/config.php"); ?>

<?php
// error_reporting(E_ERROR);
// ini_set('display_errors', 'on');
$validationHelper = new validation();

$db = connect();

$today = date("Y-m-d");

$page       = $_REQUEST['page'];
$sortOrder    = $_REQUEST['sortOrder'];
$sortField    = $_REQUEST['sortField'];
$formId       = $_REQUEST['formId'];
$searchKey       = $_REQUEST['search'];
$from_date       = $_REQUEST['from_date'];
$to_date       = $_REQUEST['to_date'];
$exportType       = $_REQUEST['export'];

$params = '';
$fields = array();

if ($exportType) {
   $columns = 'purchage_date, bill_number, invoice_number, invoice_date, account_name, barcode_number, item_name, category, brand, size, qty, mou_name, rate, purchase_price, percent_discount, discount_amt, net_price, gst,  gst_amount, taxable_amount, amount';
} else {
   $columns = 'id, purchage_date, bill_number, invoice_number, invoice_date, account_name, barcode_number, item_name, category, brand, size, qty, mou_name, rate, purchase_price, percent_discount, discount_amt, net_price, gst,  gst_amount, taxable_amount, amount';
}

$query = "SELECT $columns FROM tbl_purchase WHERE id > 0";

if ($formId == 'latestsearchForm') {
   $query .= " AND DATE(created_date) = ?";
   $params .= 's';
   $fields[] = $today;
}
if ($from_date) {
   $query .= " AND purchage_date >= ?";
   $params .= 's';
   $fields[] = dateInSQLFormat($from_date);
}
if ($to_date) {
   $query .= " AND purchage_date <= ?";
   $params .= 's';
   $fields[] = dateInSQLFormat($to_date);
}

if ($searchKey) {
   $query .= " AND (bill_number LIKE CONCAT('%', ?, '%') OR invoice_number LIKE CONCAT('%', ?, '%') OR barcode_number LIKE CONCAT('%', ?, '%') OR account_name LIKE CONCAT('%', ?, '%') OR gst_type LIKE CONCAT('%', ?, '%') OR CAST(qty AS CHAR) LIKE CONCAT('%', ?, '%') 
        OR CAST(gst_amount AS CHAR) LIKE CONCAT('%', ?, '%') 
        OR CAST(amount AS CHAR) LIKE CONCAT('%', ?, '%'))";

   $params .= 'ssssssss';
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
   $fields[] = $searchKey;
} 
// echo $query; die;
if (empty($fields)) {

   $stmt = $db->select($query);
} else {

   $stmt = $db->select($query, $params, $fields);
}

if($exportType == 'excel'){
   exportResult('purchase-itemwise', $stmt); 
   exit();
}

if($exportType == 'pdf'){
   exportResultPdf('purchase-itemwise', $stmt); 
   exit();
}

// Total Calculation
$total_qty = 0;
$total_gst = 0.00;
$total_amt = 0.00;
while($t_row = $stmt->fetch_assoc()){
   $total_qty += $t_row['qty'];
   $total_gst += (float)$t_row['gst_amount'];
   $total_amt += (float)$t_row['amount'];
}

$totalRecords = $stmt->num_rows();

$recordsPerPage = $_SESSION["_RECORDPERPAGE_"] ? $_SESSION["_RECORDPERPAGE_"] : _RECORDPERPAGE_;
$offSet = ($page - 1) * $recordsPerPage;

$query .= " ORDER BY $sortField $sortOrder LIMIT $offSet,$recordsPerPage";

if (empty($fields)) {

   $stmt = $db->select($query);
} else {

   $stmt = $db->select($query, $params, $fields);
}

$totalRecordsWithLimit = $stmt->num_rows();

if ($totalRecordsWithLimit > 0) {
   $html = '';
   for ($i = 1; $i <= $totalRecordsWithLimit; $i++) {
      $row = $stmt->fetch_assoc();

      $html .= '<tr>
         <td>
            <div class="checkbox checkbox-info">
               <input id="checkbox' . $i . '" name="recent-chk[]" type="checkbox" value="' . $row['id'] . '">
               <label class="s_no" for="checkbox' . $i . '">' . $i . '</label>
            </div>
         </td>
         <td>
            <a class="btn-add btn-xs" onclick="view_details(' . $row['id'] . ');" href="javascript:void(0);" data-toggle="tooltip" title="View Details">
               <span class="fa fa-list-alt"></span></a>
            <a href="bill-invoice.php?type=purchase&bill_no=' . $row['bill_number'] . '" class="btn-add btn-xs"><span class="fa fa-file"></span></a>
            <a href="#" class="btn-add btn-xs" data-toggle="modal" data-target="#details"><span class="fa fa-print"></span></a>
         </td>
         <td>
            <a href="#" class="btn-add btn-xs" data-toggle="modal" data-target="#details"><span class="fa fa-whatsapp"></span></a>
            <a role="button" class="btn-add btn-xs" onclick="sendMail(\'' . $row['bill_number'] . '\');"><span class="fa fa-envelope"></span></a>
         </td>
         <td>
            <a class="" onClick="return confirm(\'Are you sure you want to Update Bill?\')" href="purchase-master-creation.php?bill_no=' . $row['bill_number'] . '"><button data-toggle="tooltip" title="Update Bill" type="button" class="btn-add btn-xs"><i class="fa fa-pencil"></i><span style=""></span></button></a>

            <a class="btn-danger btn btn-xs" onclick="deleterecord(' . $row['id'] . ')" href="javascript:void(0);" data-toggle="tooltip" title="Delete">

               <i class="fa fa-trash-o"></i><span></span></button></a>

         </td>
         <td>' . dateformat($row['purchage_date']) . '</td>
         <td>' . $row['bill_number'] . '</td>
         <td>' . $row['invoice_number'] . '</td>
         <td>' . dateformat($row['invoice_date']) . '</td>
         <td>' . $row['account_name'] . '</td>
         <td>' . $row['barcode_number'] . '</td>
         <td>' . $row['item_name'] . '</td>
         <td>' . $row['item_type'] . '</td>
         <td>' . $row['brand'] . '</td>
         <td>' . $row['size'] . '</td>
         <td>' . $row['qty'] . '</td>
         <td>' . $row['mou_name'] . '</td>
         <td>' . $row['rate'] . '</td>
         <td>' . $row['purchase_price'] . '</td>
         <td>' . $row['percent_discount'] . '</td>
         <td>' . $row['discount_amt'] . '</td>
         <td>' . $row['net_price'] . '</td>
         <td>' . $row['gst'] . '</td>
         <td>' . $row['gst_amount'] . '</td>
         <td>' . $row['taxable_amount'] . '</td>
         <td>' . $row['amount'] . '</td>
         </tr>';
   }

   $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else {
   $html = '<tr>
      <td colspan="25"> No record found</td>
   </tr>';
}
$data['htmlData'] = $html;
$data['total_qty'] = number_format($total_qty);
$data['total_gst'] = number_format($total_gst, 2);
$data['total_amt'] = number_format($total_amt, 2);
echo json_encode($data);
die;
