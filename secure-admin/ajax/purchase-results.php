<?php include_once("../include/config.php"); ?>

<?php
$validationHelper = new validation();

$db = connect();

$today = date("Y-m-d");

$page 		= $_POST['page'];
$sortOrder 	= $_POST['sortOrder'];
$sortField 	= $_POST['sortField'];
$exportType = $_REQUEST['export'];

$params = '';
$fields = array();

if ($exportType) {
   $columns = 'purchage_date, bill_number, invoice_number, account_name, gst_type, gst_amount, SUM(amount) as amount, status';
} else {
   $columns = 'id, invoice_number, purchage_date, bill_number, account_name, gst_type, gst_amount, SUM(amount) as amount, status';
}

$query = "SELECT $columns FROM tbl_purchase GROUP BY bill_number";

if (empty($fields)) {

   $stmt = $db->select($query);
} else {

   $stmt = $db->select($query, $params, $param_fields);
}

if ($exportType == 'excel') {
   exportResult('purchase-item', $stmt);
   exit();
}
if($exportType == 'pdf'){
   exportResultPdf('purchase-report', $stmt); 
   exit();
}


$totalRecords = $stmt->num_rows();

$recordsPerPage = $_SESSION["_RECORDPERPAGE_"] ? $_SESSION["_RECORDPERPAGE_"] : _RECORDPERPAGE_;
$offSet = ($page - 1) * $recordsPerPage;

$query .= " ORDER BY $sortField $sortOrder LIMIT $offSet,$recordsPerPage";

if (empty($fields)) {

   $stmt = $db->select($query);
} else {

   $stmt = $db->select($query, $params, $param_fields);
}

$totalRecordsWithLimit = $stmt->num_rows();

if ($totalRecordsWithLimit > 0) {
   $html = '';
   for ($i = 1; $i <= $totalRecordsWithLimit; $i++) {
      $row = $stmt->fetch_assoc();

      $html .= '<tr>
         <td>
            <div class="checkbox checkbox-info">
               <input id="checkbox'. $i .'" name="recent-chk[]" type="checkbox" value="'. $row['id'] .'">
               
            </div>
         </td>
         <td> <label class="s_no" for="checkbox'. $i .'">'. $i .'</label></td>
         <td>'. dateformat($row['purchage_date']) .'</td>
         <td>'. $row['bill_number'] .'</td>
         <td>'. $row['invoice_number'] .'</td>
         <td>'. $row['account_name'] .'</td>
         <td>'. $row['gst_type'] .'</td>
         <td>'. $row['gst_amount'] .'</td>
         <td>'. $row['amount'] .'</td>
         <td>';
             if ($row['status'] == 'success') { 
               $html .= '<span class="label label-success">'. $row['status'] .'</span>';
             } else { 
               $html .= '<span class="label label-warning">'. $row['status'] .'</span>';
            } 
            $html .= '</td>
         <td>
            <a class="btn-add btn-xs" onclick="view_details('. $row['id'] .');" href="javascript:void(0);" data-toggle="tooltip" title="View Details">
               <span class="fa fa-list-alt"></span></a>
            <a href="bill-invoice.php?type=purchase&bill_no='. $row['bill_number'] .'" class="btn-add btn-xs"><span class="fa fa-file"></span></a>
            <a href="#" class="btn-add btn-xs" data-toggle="modal" data-target="#details"><span class="fa fa-whatsapp"></span></a>
            <a role="button" class="btn-add btn-xs" onclick="sendMail(\''. $row['bill_number'] .'\');"><span class="fa fa-envelope"></span></a>
            <a href="#" class="btn-add btn-xs" data-toggle="modal" data-target="#details"><span class="fa fa-print"></span></a>
         </td>
         <td>
            <a class="" onClick="return confirm(\'Are you sure you want to Update Bill?\')" href="purchase-master-creation.php?bill_no='. $row['bill_number'] .'"><button data-toggle="tooltip" title="Update Bill" type="button" class="btn-add btn-xs"><i class="fa fa-pencil"></i><span style=""></span></button></a>

            <a class="btn-danger btn btn-xs" onclick="deleterecord('. $row['id'] .')" href="javascript:void(0);" data-toggle="tooltip" title="Delete">

               <i class="fa fa-trash-o"></i><span></span></button></a>

         </td>
      </tr>';
    }

    $data['pagination'] = include_pagination_component($page, $recordsPerPage, $totalRecords);
} else { 
   $html = '<tr>
      <td colspan="12"> No Recently added</td>
   </tr>';
 }  
 $data['htmlData'] = $html;
echo json_encode($data);
	die;