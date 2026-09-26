<?php


function setSytemMailConstants($template_file)
{
    $mail_body_template = file_get_contents(_BASEPATH . "pdf_templates/header.html") .
        file_get_contents(_BASEPATH . "pdf_templates/" . $template_file) .
        file_get_contents(_BASEPATH . "pdf_templates/footer.html");

    $mail_body = str_replace("[GENERATED_DATE]", date('Y-m-d H:i:s'), $mail_body_template);
    // $mail_body = str_replace("[CLOUD_NAME]", _CLOUD_NAME_, $mail_body);
    // $mail_body = str_replace("[CLOUD_URL]", _CLOUD_URL_, $mail_body);
    // $mail_body = str_replace("[_BASEURL]", _BASEURL, $mail_body);
    // $mail_body = str_replace("[_PORTAL_LOGO]", _PORTAL_LOGO, $mail_body);
    return $mail_body;
}

function dateformat($date, $time = 0)
{
    if (!empty($date) && $date != '0000-00-00 00:00:00' && $date != '0000-00-00') {
        if ($time == 1) {
            return date('d-m-Y h:i:s A', strtotime($date));
        } else {
            return date('d-m-Y', strtotime($date));
        }
    }
}

function timeformat($time, $timeformat = 0)
{
    if (!empty($time) && $time != '00:00:00') {
        if ($timeformat == 1) {
            return  date('H:i:s', strtotime($time)); //24 Hrs
        } else {
            return date('h:i:s A', strtotime($time)); //12 Hrs
        }
    } else {
        //return '00:00:00'; 
    }
}

function isDateExist($date)
{
    if (!empty($date) && $date != '0000-00-00 00:00:00' && $date != '0000-00-00') {

        return $date;
    }
}

function getLocation($id)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT description FROM tbl_store_master where id = $id");
    $row = mysqli_fetch_array($query);
    return $row['description'];
}

function getBrandName($id)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT description FROM tbl_brand_master where id = $id");
    $row = mysqli_fetch_array($query);
    return $row['description'];
}
function getSizeListByCategory($category)
{
    $db = connect();
    $group_Stmt = $db->select("SELECT DISTINCT V.size_name FROM tbl_item_variants V INNER JOIN tbl_item_master M ON M.id = V.item_id WHERE M.status=? AND M.group_name=? AND V.size_name != ''", "ss", "true", $category);
    return $group_Stmt;
}
function getColorListByCategory($category)
{
    $db = connect();
    $group_Stmt = $db->select("SELECT DISTINCT V.color_name FROM tbl_item_variants V INNER JOIN tbl_item_master M ON M.id = V.item_id WHERE M.status=? AND M.group_name=? AND V.color_name != ''", "ss", "true", $category);
    return $group_Stmt;
}
function getGroupList()
{
    $db = connect();
    $group_Stmt = $db->select("SELECT * FROM tbl_group_master WHERE status=?", "s", "true");
    return $group_Stmt;
}
function getGroupName($id)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT description FROM tbl_group_master where id = $id");
    $row = mysqli_fetch_array($query);
    return $row['description'];
}

function generateBillNo($table, $column, $prefix)
{
    $db = connect();
    $query = $db->select("select $column as bill from $table ORDER BY $column DESC LIMIT 0,1");

    $row = $query->fetch_assoc();
    // echo "select $column as bill from $table ORDER BY $column DESC LIMIT 0,1"; die;
    $billArray = explode('-', $row['bill']);
    $billno = (int)$billArray[1] + 1;

    return ucwords($prefix) . '-' . sprintf('%04d', $billno);
}

function getProductList($category = '', $offset = 0, $limit = 20)
{
    $db = connect();

    $params = '';
    $fields = array();

    $query = "SELECT tbl_item_master.*, 
        COALESCE((SELECT MIN(price) FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND price > 0), 0) AS sp,
        COALESCE((SELECT SUM(quantity) FROM tbl_item_variants WHERE item_id = tbl_item_master.id), 0) AS min_qty
        FROM tbl_item_master WHERE status='true'";

    if (!empty($category)) {

        $query .= " AND group_name = ?";

        $params .= 's';
        $fields[] = $category;
    }

    $query .= " LIMIT ? OFFSET ?";
    $params .= 'ii';
    $fields[] = (int)$limit;
    $fields[] = (int)$offset;

    if (empty($fields)) {

        $stmt = $db->select($query);
    } else {
        $stmt = $db->select($query, $params, $fields);
    }
    return $stmt;
}

function countRecords($table, $column)
{
    $db = connect();
    $stmt = $db->select("SELECT SUM($column) AS cnt FROM $table");
    $row = $stmt->fetch_assoc();
    return $row['cnt'];
}
function countLatestRecords($table, $column)
{
    $db = connect();

    $today = date("Y-m-d");
    $stmt = $db->select("SELECT SUM($column) AS cnt FROM $table WHERE DATE(created_date) =?", 's', $today);
    $row = $stmt->fetch_assoc();
    return $row['cnt'];
}

function getTotalValue($table, $column)
{
    $db = connect();
    $stmt = $db->select("SELECT SUM(CAST($column AS DECIMAL(10,2))) AS totalvalue FROM $table");
    $row = $stmt->fetch_assoc();
    return $row['totalvalue'];
}

function getTotalLatestValue($table, $column)
{
    $db = connect();
    $today = date("Y-m-d");
    $stmt = $db->select("SELECT SUM(CAST($column AS DECIMAL(10,2))) AS totalvalue FROM $table WHERE DATE(created_date) =?", 's', $today);
    $row = $stmt->fetch_assoc();
    return $row['totalvalue'];
}

function redirectTo($pageName, $message = '', $error = '')
{
    $_SESSION['showMessages'] = $message;
    $_SESSION['errorMessages'] = $error;
    header("location:" . $pageName);
    exit;
}

function xssSafe($str)
{

    if (!empty($str)) {
        return htmlspecialchars(strip_tags($str));
    } else {
        return $str;
    }
}

function beautify($str)
{

    if (!empty($str)) {
        return ucwords(str_replace('_', ' ', htmlspecialchars(strip_tags($str))));
    } else {
        return $str;
    }
}

function menuFoodBtn($day, $startTime, $endTime)
{

    $currentDate = date('Y-m-d H:i:s');
    $currentDay = date("l");
    $startDate = date('Y-m-d') . ' ' . $startTime;
    $date = new DateTime();

    if ($startTime < $endTime) {
        // Format the date to 'Y-m-d'
        $endDate = $date->format('Y-m-d') . ' ' . $endTime;
    } else {
        // Add one day
        $date->modify('+1 day');

        // Format the date to 'Y-m-d'
        $endDate = $date->format('Y-m-d') . ' ' . $endTime;

        $today = date('Y-m-d');
        $dateTime = new DateTime($today);
        $dateTime->modify('-1 day');
        $currentDay = $dateTime->format('l');
    }

    //$time24 = date('H:i:s'); // current time in 24-hour format
    //$time12 = date('h:i:s', strtotime($time24)); // Convert to 12-hour format

    if ($currentDay == $day && $startDate <= $currentDate && $endDate >= $currentDate) {
        return true;
    }
    return false;
}

function nightAuditDate($nightAudit)
{

    if ($nightAudit == 'yes') {

        //$time24 = date('H:i:s'); // current time in 24-hour format
        $time24 = '06:00:00'; // current time in 24-hour format
        if ($time24 >= '00:00:00' && $time24 <= '07:00:00') {
            return date('Y-m-d', strtotime('-1 day'));
        } else {
            return date('Y-m-d');
        }
    }
    return date('Y-m-d');
}

/* ============== End Restrict Document Access ============== */

function getMIMETypes($allowedFileType)
{
    $validMimeTypeArr = array();
    foreach ($allowedFileType as $fkey => $fvalue) {
        switch ($fvalue) {
            case 'xls':
                $validMimeTypeArr[] = 'text/xml';
                break;
            case 'xlsx':
                $validMimeTypeArr[] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;

            case 'pdf':
                $validMimeTypeArr[] = 'application/pdf';
                $validMimeTypeArr[] = 'application/octet-stream';
                break;

            case 'png':
                $validMimeTypeArr[] = 'image/png';
                break;

            case 'jpg':
                $validMimeTypeArr[] = 'image/jpg';
                break;

            case 'jpeg':
                $validMimeTypeArr[] = 'image/jpeg';
                break;

            case 'gif':
                $validMimeTypeArr[] = 'image/gif';
                break;

            case 'docx':
                $validMimeTypeArr[] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                $validMimeTypeArr[] = 'application/octet-stream';
                break;

            case 'doc':
                $validMimeTypeArr[] = 'application/msword';
                break;
            case 'zip':
                $validMimeTypeArr[] = 'application/zip';
                break;
        }
    }

    return $validMimeTypeArr;
}

function validateUploadedFile($filesData, $allowedFileType, $errorMessageArr, $uploadSize = 5)
{

    if (!($uploadSize <= 5)) {
        $errorMessageArr[] = "Invalid File Upload Size. It should be up to 5MB";
    }

    if ($filesData["name"] == "") {
        $errorMessageArr[] = "Please select a file to upload";
    } else if ($filesData["name"] != "") {
        $file_ext = pathinfo($filesData["name"], PATHINFO_EXTENSION);

        if (!in_array($file_ext, $allowedFileType)) {
            $errorMessageArr[] = ' File extension not allowed, please choose ' . implode('/', $allowedFileType) . ' file.';
        }


        ////////////////Size check /////////////////
        $_1_MB_Bytes = 1048576; // MB in Bytes
        if ($filesData['size'] > ($_1_MB_Bytes * $uploadSize)) {
            $errorMessageArr[] = " File size must be up to $uploadSize MB";
        }


        ////////////////mime type check /////////////////
        if (function_exists(mime_content_type)) {
            $mimeType = mime_content_type($filesData['tmp_name']);

            if (!in_array($mimeType, getMIMETypes($allowedFileType))) {
                $errorMessageArr[] = 'Invalid File:' . $mimeType . '. Please upload ' . implode('/', $allowedFileType) . ' files only.';
            }
        }
    }

    return $errorMessageArr;
}

function generateDocName($temp_filename)
{

    return md5(substr(md5(microtime()), 1, 8)) . '_' . preg_replace('/[\W]/', '', pathinfo($temp_filename, PATHINFO_FILENAME)) . '.' . pathinfo($temp_filename, PATHINFO_EXTENSION);
}

function uploadValidatedFile($filesData, $uploadLocation, $temp_filename = '')
{

    if ($temp_filename == '') {
        $fileName = generateDocName($filesData['name']);
    } else {
        $fileName = generateDocName($temp_filename);
    }
    $targetPath = $uploadLocation . $fileName;

    if ($targetPath) {

        if (file_exists($uploadLocation)) { // check Directory exists
            move_uploaded_file($filesData['tmp_name'], $targetPath);
        } else {
            mkdir($uploadLocation, 0777, true);
            move_uploaded_file($filesData['tmp_name'], $targetPath);
        }
        return $fileName;
    }
    return false;
}

function getSessionUserId()
{
    return $_SESSION['auth_user']['user_id'];
}

function getTotalPrice($cart_id)
{
    $db = connect();
    $user_id = getSessionUserId();

    $cart_stmt = $db->select("SELECT (C.quantity * FO.mrp) as price FROM tbl_cart as C INNER JOIN tbl_food_order as FO ON FO.id = C.item_id WHERE C.user_id=? AND C.id=?", 'ii', $user_id, $cart_id);

    $item_res = $cart_stmt->fetch_assoc();
    $cart_stmt->close();
    return $item_res['price'];
}

function getCartTotalPriceForCheckout($extra_charges = [], $discounts = [])
{
    $db = connect();
    $user_id = getSessionUserId();

    // Add extra charges
    $extraCharges = 0;
    if (!empty($extra_charges)) {

        $extraCharges = array_sum($extra_charges);
    }

    // discount price
    $discountPrice = 0;
    if (!empty($discounts)) {

        $discountPrice = array_sum($discounts);
    }

    $cart_stmt = $db->select("SELECT SUM(C.quantity * FO.mrp) as price FROM tbl_cart as C INNER JOIN tbl_food_order as FO ON FO.id = C.item_id WHERE C.user_id=?", 'i', $user_id);

    $item_res = $cart_stmt->fetch_assoc();
    $cart_stmt->close();
    $price = $item_res['price'];

    $total_price = ($price + $extraCharges) - $discountPrice;

    return number_format($total_price, 2);
}

function getPreparedAddressById($id)
{
    $validationHelper = new validation();

    $db = connect();
    $user_stmt = $db->select("SELECT * FROM tbl_order_address WHERE id=?", 'i', $id);

    $add_res = $user_stmt->fetch_assoc();

    foreach ($add_res as $key => $value) {
        $$key = $validationHelper->filterText($value);
    }
    $user_stmt->close();
    $db->close();

    $address = $name . ', ' . $mobile . ', ' . $address . ', ' . $landmark . ', ' . $country . ', ' . $state . ', ' . $city . ', ' . $pincode;

    return $address;
}

function getGSTValueBygstType($gst)
{
    return $gst / 2;
}

function dateInSQLFormat($date)
{
    return date('Y-m-d', strtotime($date));
}

function getPayModeByCode($code)
{
    $db = connect();

    $stmt = $db->select("SELECT paymode_name FROM tbl_paymode_master WHERE paymode_code=?", 's', $code);

    $item_res = $stmt->fetch_assoc();
    $stmt->close();
    return $item_res['paymode_name'];
}

function getAccountEmailBymobile($mobile)
{
    $db = connect();

    $stmt = $db->select("SELECT email_id_1 FROM tbl_account_master WHERE mobile_no_1=?", 's', $mobile);

    $item_res = $stmt->fetch_assoc();
    $stmt->close();
    return $item_res['email_id_1'];
}

function calculateTotalPrice($extra_charges = [], $discounts = [])
{
    $db = connect();

    // Add extra charges
    $extraCharges = 0;
    if (!empty($extra_charges)) {

        $extraCharges = array_sum($extra_charges);
    }

    // discount price
    $discountPrice = 0;
    if (!empty($discounts)) {

        $discountPrice = array_sum($discounts);
    }

    $total_price = $extraCharges - $discountPrice;

    return number_format($total_price, 2);
}

/*-====Function for Pagination =====-*/
function showPagingInfoTxt($pageno, $perpage, $totalrecords)
{

    if ($totalrecords == 0) {
        return "No records found";
    }

    if ($perpage > $totalrecords) {
        $perpage = $totalrecords;
    }

    $maxpageno = (int)ceil($totalrecords / $perpage);
    if ($pageno > $maxpageno || $pageno < 1) {
        $pageno = 1;
    }

    $offset = $perpage * ($pageno - 1);
    $limit = ($perpage * $pageno);
    if ($limit > $totalrecords) {
        $limit = $totalrecords;
    }

    return "Showing " . ($offset + 1) . " to  $limit of $totalrecords records";
}

function include_pagination_component($offset, $recordsPerPage, $totalRecords)
{

    $htmltext = '
    <div class="row justify-content-between align-items-center">
    <div class="col-md-4">
  
    <div class="select-pagination">
                    <div class="d-flex">
					<select class="recordsPerPage form-control">' .
        $pagingRecords = _PAGEARRY_;
    foreach ($pagingRecords as $record) {
        $htmltext .= '<option value="' . $record . '"' . ($recordsPerPage == $record ? "Selected" : "") . '>' . $record . '</option>';
    }
    $qs = '';
    $htmltext .= '</select><span>' . showPagingInfoTxt($offset, $recordsPerPage, $totalRecords) . '</span></div></div></div>';

    $URL = "{@PAGE}";

    $pager = new pager_v2($URL, $totalRecords, $recordsPerPage, $offset);

    $htmltext .= '<div class="col-md-8">';
    if ($pager->totalpages > 1) {

        $htmltext .= $pager->outputlinks();
    }

    $htmltext .= '</div></div>';

    return $htmltext;
}

function get_cart_session()
{

    if (!isset($_COOKIE['cart_session'])) {
        $cart_id = bin2hex(random_bytes(16)); // unique 32-char id
        setcookie('cart_session', $cart_id, time() + (86400 * 7), "/"); // 7 days
        $_COOKIE['cart_session'] = $cart_id; // make available immediately
        return $cart_id;
    }
    return $_COOKIE['cart_session'];
}

function get_cart_count($cart_session)
{
    $db = connect();

    $stmt = $db->select("SELECT SUM(quantity) AS total_qty FROM tbl_cart_items WHERE cart_session = ?", 's', $cart_session);
    $row = $stmt->fetch_assoc();
    return (int) ($row['total_qty'] ?? 0);
}

function restore_checkout_session()
{
    if (!empty($_SESSION['checkout_products']) && !empty($_SESSION['checkout_summary'])) {
        return true;
    }

    $cart_session = get_cart_session();
    $db = connect();
    $stmt = $db->select(
        "SELECT CI.*, (SELECT image_path FROM tbl_item_images WHERE item_id = CI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture 
         FROM tbl_cart_items as CI 
         WHERE CI.cart_session = ? 
         ORDER BY CI.id DESC",
        's',
        $cart_session
    );

    if ($stmt && $stmt->num_rows > 0) {
        $checkout_products = [];
        $subtotal = 0;
        $total_qty = 0;

        while ($row = $stmt->fetch_assoc()) {
            $row_total = (float)$row['price'] * (int)$row['quantity'];
            $subtotal += $row_total;
            $total_qty += (int)$row['quantity'];

            $checkout_products[] = [
                'product_id'    => (int)$row['product_id'],
                'product_title' => $row['product_name'],
                'unit_price'    => (float)$row['price'],
                'quantity'      => (int)$row['quantity'],
                'size'          => $row['size'],
                'row_total'     => $row_total
            ];
        }

        $base_shipping = defined('_SHIPPING_CHARGE_') ? (float)_SHIPPING_CHARGE_ : 150;
        $per_item_shipping = defined('_SHIPPING_CHARGE_PER_ITEM_') ? (float)_SHIPPING_CHARGE_PER_ITEM_ : 100;
        $shipping = $total_qty > 0 ? ($base_shipping + (($total_qty - 1) * $per_item_shipping)) : 0;

        $gst_percent = defined('_GST_') ? (float)_GST_ : 5;
        $subtotal_plus_shipping = $subtotal + $shipping;
        $gst = $subtotal_plus_shipping * ($gst_percent / 100);

        $grand_total = $subtotal_plus_shipping + $gst;

        $_SESSION['checkout_products'] = $checkout_products;
        $_SESSION['checkout_summary'] = [
            'subtotal'    => $subtotal,
            'shipping'    => $shipping,
            'gst'         => $gst,
            'grand_total' => $grand_total,
            'total_qty'   => $total_qty
        ];
        return true;
    }

    return false;
}

function get_cart_dropdown_data($cart_session)
{
    $db = connect();
    $stmt = $db->select("SELECT CI.*, (SELECT image_path FROM tbl_item_images WHERE item_id = CI.product_id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture FROM tbl_cart_items as CI WHERE CI.cart_session = ? ORDER BY CI.id DESC", 's', $cart_session);

    $items = [];
    $total_price = 0;
    $total_qty = 0;

    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {
            $item_total = (float)$row['price'] * (int)$row['quantity'];
            $total_price += $item_total;
            $total_qty += (int)$row['quantity'];
            $items[] = $row;
        }
    }

    return [
        'items' => $items,
        'total_price' => $total_price,
        'total_qty' => $total_qty
    ];
}

function get_cart_dropdown_html($cart_session)
{
    $cart_data = get_cart_dropdown_data($cart_session);
    $items = $cart_data['items'];
    $total_price = $cart_data['total_price'];
    $baseUrl = defined('_BASEURL') ? _BASEURL : '';
    $imageUrl = defined('_IMAGE_PATH') ? _IMAGE_PATH : '';

    $html = '<div class="dropdown-cart-products">';

    if (!empty($items)) {
        foreach ($items as $item) {
            $picture = !empty($item['picture']) ? $imageUrl . 'item-master/' . htmlspecialchars($item['picture']) : $baseUrl . 'assets/images/products/cart/product-1.jpg';
            $productUrl = $baseUrl . 'product-details.php?product-id=' . $item['product_id'];
            $priceFormatted = number_format($item['price'], 2);
            $sizeText = !empty($item['size']) ? ' (Size: ' . htmlspecialchars($item['size']) . ')' : '';

            $html .= '<div class="product">
                <div class="product-cart-details">
                   <h4 class="product-title letter-spacing-normal font-size-normal">
                      <a href="' . $productUrl . '">' . htmlspecialchars($item['product_name']) . '</a>
                   </h4>
                   <span class="cart-product-info">
                   <span class="cart-product-qty">' . (int)$item['quantity'] . '</span>
                   x ₹' . $priceFormatted . $sizeText . '
                   </span>
                </div>
                <figure class="product-image-container">
                   <a href="' . $productUrl . '" class="product-image">
                   <img src="' . $picture . '" alt="product" width="60" height="60">
                   </a>
                </figure>
                <a href="#" class="btn-remove header-cart-remove" data-id="' . $item['id'] . '" title="Remove Product">
                <i class="icon-close"></i>
                </a>
             </div>';
        }
    } else {
        $html .= '<div class="p-3 text-center text-muted" style="font-size: 13px;">Your cart is empty</div>';
    }

    $html .= '</div>';
    $html .= '<div class="dropdown-cart-total">
       <span>Total</span>
       <span class="cart-total-price">₹' . number_format($total_price, 2) . '</span>
    </div>';
    $html .= '<div class="dropdown-cart-action">
       <a href="' . $baseUrl . 'cart.php" class="btn btn-primary">View Cart</a>
       <a href="' . $baseUrl . 'checkout.php" class="btn btn-outline-primary-2' . (empty($items) ? ' disabled' : '') . '">
       <span>Checkout</span>
       <i class="icon-long-arrow-right"></i>
       </a>
    </div>';

    return $html;
}


function getFilterCounts()
{
    $db = connect();
    $sql = "
        SELECT 'category' AS filter_type, CONVERT(group_name USING utf8mb4) AS filter_value, COUNT(DISTINCT id) AS item_count 
        FROM tbl_item_master GROUP BY group_name

        UNION ALL

        SELECT 'brand' AS filter_type, CONVERT(brand_name USING utf8mb4) AS filter_value, COUNT(DISTINCT id) AS item_count 
        FROM tbl_item_master  
        GROUP BY brand_name

        UNION ALL

        SELECT 'color' AS filter_type, CONVERT(color_name USING utf8mb4) AS filter_value, COUNT(DISTINCT item_id) AS item_count 
        FROM tbl_item_variants WHERE color_name != '' 
        GROUP BY color_name

        UNION ALL

        SELECT 'size' AS filter_type, CONVERT(size_name USING utf8mb4) AS filter_value, COUNT(DISTINCT item_id) AS item_count 
        FROM tbl_item_variants WHERE size_name != '' 
        GROUP BY size_name
    ";

    $result = $db->select($sql);


    // 4. Organize the raw rows into a clean, structured multi-dimensional array
    $filters = [
        'categories' => [],
        'brands'     => [],
        'colors'     => [],
        'sizes'      => [],
        'min_price'  => 0,
        'max_price'  => 5000
    ];

    while ($row = $result->fetch_assoc()) {
        if (empty($row['filter_value'])) continue; // Skip blank database entries

        switch ($row['filter_type']) {
            case 'category':
                $filters['categories'][$row['filter_value']] = $row['item_count'];
                break;
            case 'brand':
                $filters['brands'][$row['filter_value']] = $row['item_count'];
                break;
            case 'color':
                $filters['colors'][$row['filter_value']] = $row['item_count'];
                break;
            case 'size':
                $filters['sizes'][$row['filter_value']] = $row['item_count'];
                break;
        }
    }

    $price_stmt = $db->select("SELECT MIN(price) as min_price, MAX(price) as max_price FROM tbl_item_variants WHERE price > 0");
    if ($price_stmt && ($price_row = $price_stmt->fetch_assoc())) {
        $min = (float)($price_row['min_price'] ?? 0);
        $max = (float)($price_row['max_price'] ?? 5000);
        if ($max <= $min) {
            $max = $min + 100;
        }
        $filters['min_price'] = (int)floor($min);
        $filters['max_price'] = (int)ceil($max);
    }

    return $filters;
}

function getHTMLProductList($offset = 0, $limit = 0)
{
    $db = connect();

    $offset = (int)$offset;
    $limit  = (int)$limit;

    if ($limit <= 0) {
        $params = 'i';
        $fields = array($offset);
        $query  = "SELECT tbl_item_master.*, 
            (SELECT image_path FROM tbl_item_images WHERE item_id = tbl_item_master.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture,
            (SELECT GROUP_CONCAT(DISTINCT size_name ORDER BY id ASC SEPARATOR ',') FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND size_name != '') AS size_name,
            COALESCE((SELECT MIN(price) FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND price > 0), 0) AS sp,
            COALESCE((SELECT SUM(quantity) FROM tbl_item_variants WHERE item_id = tbl_item_master.id), 0) AS min_qty
            FROM tbl_item_master WHERE status='true' ORDER BY tbl_item_master.id DESC LIMIT 18446744073709551615 OFFSET ?";
    } else {
        $params = 'ii';
        $fields = array($limit, $offset);
        $query  = "SELECT tbl_item_master.*, 
            (SELECT image_path FROM tbl_item_images WHERE item_id = tbl_item_master.id ORDER BY sort_order ASC, id ASC LIMIT 1) AS picture,
            (SELECT GROUP_CONCAT(DISTINCT size_name ORDER BY id ASC SEPARATOR ',') FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND size_name != '') AS size_name,
            COALESCE((SELECT MIN(price) FROM tbl_item_variants WHERE item_id = tbl_item_master.id AND price > 0), 0) AS sp,
            COALESCE((SELECT SUM(quantity) FROM tbl_item_variants WHERE item_id = tbl_item_master.id), 0) AS min_qty
            FROM tbl_item_master WHERE status='true' ORDER BY tbl_item_master.id DESC LIMIT ? OFFSET ?";
    }

    $stmt = $db->select($query, $params, $fields);

    $html = '';

    // Changed $products to $row to match the fetch loop
    while ($row = $stmt->fetch_assoc()) {

        // Sanitize variables for safety
        $productId   = strtolower($row['id']);
        $groupName   = htmlspecialchars($row['group_name']);
        $groupUrl    = strtolower($row['group_name']);
        $itemName    = htmlspecialchars($row['item_name']);
        $skuNo       = htmlspecialchars(!empty($row['sku_no']) ? $row['sku_no'] : $row['item_code']);
        $price       = htmlspecialchars($row['sp']);
        $picture       = htmlspecialchars($row['picture']);
        $raw_size_str = $row['size_name'] ?? '';
        $size_name_list = array_filter(array_map('trim', explode(',', $raw_size_str)));
        $baseUrl     = _BASEURL;
        $imageUrl     = _IMAGE_PATH;

        $variants_res = $db->select("SELECT size_name, price, quantity FROM tbl_item_variants WHERE item_id = ? AND size_name != ''", 's', $row['id']);
        $variants_by_size = [];
        if ($variants_res) {
            while ($v_row = $variants_res->fetch_assoc()) {
                $s_name = trim($v_row['size_name']);
                if ($s_name !== '') {
                    $variants_by_size[$s_name] = [
                        'price' => (float)$v_row['price'],
                        'quantity' => (int)$v_row['quantity']
                    ];
                }
            }
        }

        $is_new_flag = isset($row['is_new']) ? ($row['is_new'] !== 'false' && $row['is_new'] !== '0' && $row['is_new'] !== 'no') : true;
        $new_badge = $is_new_flag ? '<span class="product-label label-new">New</span>' : '';

        $html .= '<div class="col-6 col-md-4 col-lg-4 col-xl-3">
                <div class="product product-7 text-center">
                    <figure class="product-media">
                        ' . $new_badge . '
                        <a href="' . $baseUrl . 'product-details.php/?product-id=' . $productId . '">
                            <img src="' . $imageUrl . 'item-master/' . $picture . '" alt="Product image" class="product-image">
                        </a>
                        <div class="product-action-vertical">
                            <a href="javascript:void(0);" class="btn-product-icon btn-share" title="Share Product" data-url="' . $baseUrl . 'product-details.php/?product-id=' . $productId . '" data-title="' . htmlspecialchars($itemName) . '"><span>Share Product</span></a>
                        </div>
                    </figure>
                    <div class="product-body">
                        <div class="product-cat">
                            <a href="' . $baseUrl . 'product-details.php/?product-id=' . $productId . '">' . $itemName . '</a>
                        </div>
                        <h3 class="product-title">
                            <a href="' . $baseUrl . 'product-details.php/?product-id=' . $productId . '">' . $skuNo . '</a>
                        </h3>
                        <div class="product-size-select">';

        $active_size = '';
        if (!empty($size_name_list)) {
            foreach ($size_name_list as $sz) {
                $sz_qty = !empty($variants_by_size) ? (isset($variants_by_size[$sz]) ? (int)$variants_by_size[$sz]['quantity'] : 0) : (isset($row['min_qty']) ? (int)$row['min_qty'] : 0);
                if ($sz_qty > 0) {
                    $active_size = $sz;
                    break;
                }
            }
        }

        $active_qty = (isset($row['min_qty']) ? (int)$row['min_qty'] : 0);
        if ($active_size !== '') {
            $active_qty = isset($variants_by_size[$active_size]) ? (int)$variants_by_size[$active_size]['quantity'] : $active_qty;
            if (isset($variants_by_size[$active_size])) {
                $price = $variants_by_size[$active_size]['price'];
            }
        }

        foreach ($size_name_list as $size_name) {
            $v_price = isset($variants_by_size[$size_name]) ? number_format((float)$variants_by_size[$size_name]['price'], 2, '.', '') : number_format((float)$row['sp'], 2, '.', '');
            $v_qty = !empty($variants_by_size) ? (isset($variants_by_size[$size_name]) ? (int)$variants_by_size[$size_name]['quantity'] : 0) : (int)$row['min_qty'];
            $is_disabled = ($v_qty <= 0);
            $active_class = ($size_name === $active_size && !$is_disabled) ? ' active' : '';
            $disabled_class = $is_disabled ? ' disabled' : '';
            $title_attr = $is_disabled ? ' title="Out of Stock"' : '';
            $html .= '<span class="size-option' . $active_class . $disabled_class . '" data-size="' . htmlspecialchars($size_name) . '" data-price="' . htmlspecialchars($v_price) . '" data-qty="' . htmlspecialchars($v_qty) . '"' . $title_attr . '>' . htmlspecialchars($size_name) . '</span>';
        }

        $html .= '</div>
                        <div class="product-price">
                            ₹' . number_format((float)$price, 2, '.', '') . '
                        </div>
                    </div>';

        if ($active_qty <= 0) {
            $html .= '<div class="product-action product-action-split" data-id="' . $row['id'] . '">
                            <a class="btn-product btn-out-of-stock disabled" role="button" style="width: 100%; background-color: #e5e5e5; color: #777; border-color: #ddd; cursor: not-allowed; pointer-events: none;"><i class="icon-ban"></i><span>Out of Stock</span></a>
                        </div>';
        } else {
            $html .= '<div class="product-action product-action-split" data-id="' . $row['id'] . '">
                            <a class="btn-product btn-cart add-cart-btn" role="button" data-id="' . $row['id'] . '" data-size="' . htmlspecialchars($active_size) . '"><i class="icon-shopping-cart"></i><span>Add to Cart</span></a>
                            <a class="btn-product btn-order-now order-now-btn" role="button" data-id="' . $row['id'] . '" data-size="' . htmlspecialchars($active_size) . '"><i class="icon-rocket"></i><span>Order Now</span></a>
                        </div>';
        }
        $html .= '</div>
            </div>';
    }

    return $html;
}

function createDelhiveryShipment($order)
{
    if (defined('DELHIVERY_ENABLED') && !DELHIVERY_ENABLED) {
        return ['success' => false, 'waybill' => null, 'error' => 'Delhivery disabled in config'];
    }

    $api_token   = defined('DELHIVERY_API_TOKEN') ? DELHIVERY_API_TOKEN : '';
    $api_url     = defined('DELHIVERY_CREATE_URL') ? DELHIVERY_CREATE_URL : 'https://track.delhivery.com/api/cmu/create.json';
    $pickup_name = defined('PICKUP_LOCATION_NAME') ? PICKUP_LOCATION_NAME : (defined('DELHIVERY_PICKUP_NAME') ? DELHIVERY_PICKUP_NAME : (defined('SHADOWFAX_PICKUP_NAME') ? SHADOWFAX_PICKUP_NAME : 'BunnyBoss Warehouse'));


    $shipment = [
        "name"          => $order['customer_name'],
        "add"           => $order['address'],
        "pin"           => $order['pincode'],
        "city"          => $order['city'],
        "state"         => $order['state'],
        "country"       => "India",
        "phone"         => preg_replace('/\D/', '', $order['phone']), // digits only
        "order"         => (string) $order['order_id'],               // must be unique
        "payment_mode"  => $order['payment_mode'],                    // "COD" or "Prepaid"
        "total_amount"  => (string) $order['grand_total'],
        "cod_amount"    => $order['payment_mode'] === 'COD' ? (string) ($order['cod_amount'] ?? $order['grand_total']) : "0",
        "products_desc" => $order['products_desc'] ?? "General Merchandise",
        "quantity"      => (string) $order['item_count'],
        "order_date"    => date('Y-m-d H:i:s'),
        "waybill"       => "", // left blank -> Delhivery auto-generates it
        "shipment_width"  => $order['width']  ?? "10",
        "shipment_height" => $order['height'] ?? "10",
        "weight"          => $order['weight'] ?? "0.0", // in kg
    ];

    $payload = [
        "pickup_location" => ["name" => $pickup_name],
        "shipments"        => [$shipment]
    ];

    // The "format=json&data=" prefix is mandatory, not optional
    $post_body = "format=json&data=" . json_encode($payload, JSON_UNESCAPED_SLASHES);

    $ch = curl_init($api_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $post_body,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Token " . $api_token,
            "Content-Type: application/x-www-form-urlencoded"
        ],
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_err  = curl_error($ch);
    curl_close($ch);

    if ($curl_err) {
        error_log("Delhivery cURL error: $curl_err");
        return ['success' => false, 'waybill' => null, 'raw' => ['curl_error' => $curl_err]];
    }

    $data = json_decode($response, true);

    // Success shape: {"success": true, "packages": [{"waybill": "...", "status": "Success", ...}]}
    if (!empty($data['success']) && !empty($data['packages'][0]['waybill'])) {
        return ['success' => true, 'waybill' => $data['packages'][0]['waybill'], 'raw' => $data];
    }

    // Common failure: package saved but flagged "Pending AWB" — no waybill assigned yet
    error_log("Delhivery shipment creation failed for order {$order['order_id']}: " . json_encode($data));

    return ['success' => false, 'waybill' => null, 'raw' => $data ?? ['http_code' => $http_code, 'body' => $response]];
}

/**
 * Deduct purchased item quantities from variant stock when an order is successfully placed/paid.
 * Idempotent: checks is_stock_deducted flag to avoid deducting multiple times.
 */
function deduct_order_stock($order_id)
{
    $order_id = (int)$order_id;
    if ($order_id <= 0) return false;

    $db = connect();

    // Ensure is_stock_deducted column exists in tbl_orders
    static $col_checked = false;
    if (!$col_checked) {
        $check = $db->query("SHOW COLUMNS FROM tbl_orders LIKE 'is_stock_deducted'");
        if ($check && $check->num_rows == 0) {
            $db->query("ALTER TABLE tbl_orders ADD COLUMN is_stock_deducted TINYINT(1) NOT NULL DEFAULT 0");
        }
        $col_checked = true;
    }

    // Check if stock has already been deducted for this order
    $order_stmt = $db->select("SELECT is_stock_deducted FROM tbl_orders WHERE order_id = ?", 'i', $order_id);
    if (!$order_stmt || $order_stmt->num_rows == 0) return false;

    $order_row = $order_stmt->fetch_assoc();
    if (!empty($order_row['is_stock_deducted']) && (int)$order_row['is_stock_deducted'] === 1) {
        return true;
    }

    // Fetch order items and deduct quantities from tbl_item_variants
    $items_stmt = $db->select("SELECT product_id, qty, size FROM tbl_order_items WHERE order_id = ?", 'i', $order_id);
    if ($items_stmt && $items_stmt->num_rows > 0) {
        while ($item = $items_stmt->fetch_assoc()) {
            $product_id = (int)($item['product_id'] ?? 0);
            $qty        = (int)($item['qty'] ?? 0);
            $size       = trim((string)($item['size'] ?? ''));

            if ($product_id > 0 && $qty > 0) {
                if ($size !== '') {
                    $db->update(
                        "UPDATE tbl_item_variants SET quantity = GREATEST(0, quantity - ?) WHERE item_id = ? AND size_name = ?",
                        'iis',
                        $qty,
                        $product_id,
                        $size
                    );
                } else {
                    $db->update(
                        "UPDATE tbl_item_variants SET quantity = GREATEST(0, quantity - ?) WHERE item_id = ? LIMIT 1",
                        'ii',
                        $qty,
                        $product_id
                    );
                }
            }
        }
    }

    // Mark order as stock deducted
    $db->update("UPDATE tbl_orders SET is_stock_deducted = 1 WHERE order_id = ?", 'i', $order_id);
    return true;
}

/**
 * Restore purchased item quantities back to variant stock when an order is cancelled.
 * Idempotent: checks is_stock_deducted flag so quantities are not restored multiple times.
 */
function restore_order_stock($order_id)
{
    $order_id = (int)$order_id;
    if ($order_id <= 0) return false;

    $db = connect();

    // Check if stock was previously deducted
    $order_stmt = $db->select("SELECT is_stock_deducted FROM tbl_orders WHERE order_id = ?", 'i', $order_id);
    if (!$order_stmt || $order_stmt->num_rows == 0) return false;

    $order_row = $order_stmt->fetch_assoc();
    if (empty($order_row['is_stock_deducted']) || (int)$order_row['is_stock_deducted'] === 0) {
        return true; // Stock wasn't deducted or already restored
    }

    // Fetch order items and restore quantities into tbl_item_variants
    $items_stmt = $db->select("SELECT product_id, qty, size FROM tbl_order_items WHERE order_id = ?", 'i', $order_id);
    if ($items_stmt && $items_stmt->num_rows > 0) {
        while ($item = $items_stmt->fetch_assoc()) {
            $product_id = (int)($item['product_id'] ?? 0);
            $qty        = (int)($item['qty'] ?? 0);
            $size       = trim((string)($item['size'] ?? ''));

            if ($product_id > 0 && $qty > 0) {
                if ($size !== '') {
                    $db->update(
                        "UPDATE tbl_item_variants SET quantity = quantity + ? WHERE item_id = ? AND size_name = ?",
                        'iis',
                        $qty,
                        $product_id,
                        $size
                    );
                } else {
                    $db->update(
                        "UPDATE tbl_item_variants SET quantity = quantity + ? WHERE item_id = ? LIMIT 1",
                        'ii',
                        $qty,
                        $product_id
                    );
                }
            }
        }
    }

    // Mark order as stock NOT deducted
    $db->update("UPDATE tbl_orders SET is_stock_deducted = 0 WHERE order_id = ?", 'i', $order_id);
    return true;
}

/**
 * Get primary key column name for tbl_order_items (e.g. 'item_id' or 'id')
 */
function get_order_items_primary_key($db = null)
{
    if (!$db && function_exists('connect')) {
        $db = connect();
    }
    if (!$db) return 'item_id';

    static $pk_name = null;
    if ($pk_name !== null) return $pk_name;

    $cols_res = $db->query("SHOW COLUMNS FROM tbl_order_items");
    if ($cols_res) {
        while ($col = $cols_res->fetch_assoc()) {
            if ($col['Key'] === 'PRI') {
                $pk_name = $col['Field'];
                return $pk_name;
            }
        }
    }
    $pk_name = 'item_id';
    return $pk_name;
}

/**
 * Ensure tbl_order_items has transaction_id, courier_awb, dispatch_status, and dispatch_error columns.
 * Also automatically backfills transaction_id for any existing items.
 */
function ensure_order_items_schema($db = null)
{
    if (!$db && function_exists('connect')) {
        $db = connect();
    }
    if (!$db) return;

    static $is_checked = false;
    if ($is_checked) return;

    $cols = [];
    $res = $db->query("SHOW COLUMNS FROM tbl_order_items");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $cols[] = $row['Field'];
        }
    }

    if (!in_array('transaction_id', $cols)) {
        $db->query("ALTER TABLE tbl_order_items ADD COLUMN transaction_id VARCHAR(20) DEFAULT NULL");
        $db->query("ALTER TABLE tbl_order_items ADD UNIQUE INDEX idx_oi_transaction_id (transaction_id)");
    }
    if (!in_array('courier_name', $cols)) {
        $db->query("ALTER TABLE tbl_order_items ADD COLUMN courier_name VARCHAR(50) DEFAULT NULL");
    }
    if (!in_array('courier_awb', $cols)) {
        $db->query("ALTER TABLE tbl_order_items ADD COLUMN courier_awb VARCHAR(50) DEFAULT NULL");
        $db->query("ALTER TABLE tbl_order_items ADD INDEX idx_oi_courier_awb (courier_awb)");
    }
    if (!in_array('dispatch_status', $cols)) {
        $db->query("ALTER TABLE tbl_order_items ADD COLUMN dispatch_status VARCHAR(30) DEFAULT 'pending'");
    }
    if (!in_array('dispatch_error', $cols)) {
        $db->query("ALTER TABLE tbl_order_items ADD COLUMN dispatch_error TEXT DEFAULT NULL");
    }

    $pk = get_order_items_primary_key($db);

    // Backfill transaction_id for existing rows that lack one
    if ($pk) {
        $missing_rows = [];
        $missing = $db->query("SELECT $pk, product_title, price, product_id FROM tbl_order_items WHERE transaction_id IS NULL OR transaction_id = ''");
        if ($missing) {
            while ($mRow = $missing->fetch_assoc()) {
                $missing_rows[] = $mRow;
            }
            $missing->close();
        }

        foreach ($missing_rows as $mRow) {
            $rowId = (int)$mRow[$pk];
            $txId = generate_item_transaction_id($db);
            $isTest = is_testing_order_item($mRow);
            if ($isTest) {
                $db->query("UPDATE tbl_order_items SET transaction_id = '$txId', dispatch_status = 'skipped_test', dispatch_error = 'Testing item excluded from courier push' WHERE $pk = $rowId");
            } else {
                $db->query("UPDATE tbl_order_items SET transaction_id = '$txId' WHERE $pk = $rowId");
            }
        }
    }

    $is_checked = true;
}

/**
 * Generate a 10-digit transaction ID with 'bb' prefix (e.g. bb12345678)
 * 2 letters ('bb') + 8 numeric digits = exactly 10 characters
 */
function generate_item_transaction_id($db = null)
{
    if (!$db && function_exists('connect')) {
        $db = connect();
    }

    do {
        $random_num = str_pad((string)mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $tx_id = 'BB' . $random_num;
        $exists = false;
        if ($db) {
            $check = $db->query("SELECT 1 FROM tbl_order_items WHERE transaction_id = '$tx_id' LIMIT 1");
            if ($check && $check->num_rows > 0) {
                $exists = true;
            }
            if ($check && is_object($check)) {
                $check->close();
            }
        }
    } while ($exists);

    return $tx_id;
}

/**
 * Determine if an item is a testing item and should NOT be pushed to Shadowfax.
 * Checks title, SKU, price (<= 1.00), and configured test identifiers.
 */
function is_testing_order_item($item_data)
{
    if (is_numeric($item_data)) {
        $db = connect();
        $pk = get_order_items_primary_key($db);
        $res = $db->select("SELECT * FROM tbl_order_items WHERE $pk = ? LIMIT 1", 'i', (int)$item_data);
        if ($res && ($row = $res->fetch_assoc())) {
            $item_data = $row;
        } else {
            return false;
        }
    }

    if (!is_array($item_data)) {
        return false;
    }

    $title      = trim($item_data['product_title'] ?? $item_data['item_name'] ?? $item_data['sku_name'] ?? '');
    $price      = (float)($item_data['price'] ?? $item_data['unit_price'] ?? $item_data['row_total'] ?? 999.0);
    $sku        = trim($item_data['sku'] ?? $item_data['sku_no'] ?? $item_data['item_code'] ?? $item_data['sku_id'] ?? '');
    $product_id = (int)($item_data['product_id'] ?? 0);

    // 1. Keyword check in Title
    if (preg_match('/(?:^|\b|\s|_|-)(test|testing|sample|demo|dummy)(?:$|\b|\s|_|-)/i', $title)) {
        return true;
    }

    // 2. Keyword check in SKU
    if (!empty($sku) && preg_match('/(?:^|\b|\s|_|-)(test|demo|sample|dummy)/i', $sku)) {
        return true;
    }

    // 3. Price check: <= ₹1.00 is treated as a test/dummy price
    if ($price <= 1.00) {
        return true;
    }

    // 4. Configured test product IDs
    if (defined('SHADOWFAX_TEST_PRODUCT_IDS') && is_array(SHADOWFAX_TEST_PRODUCT_IDS)) {
        if (in_array($product_id, SHADOWFAX_TEST_PRODUCT_IDS, true)) {
            return true;
        }
    }

    // 5. Configured test SKUs
    if (defined('SHADOWFAX_TEST_SKUS') && is_array(SHADOWFAX_TEST_SKUS)) {
        if (!empty($sku) && in_array($sku, SHADOWFAX_TEST_SKUS, true)) {
            return true;
        }
    }

    return false;
}

/**
 * Get current active delivery location for the visitor (from session, cookie, or user's default saved address)
 *
 * @return array ['pincode' => string, 'city' => string, 'displayText' => string]
 */
function getCurrentDeliveryLocation()
{
    $pincode = $_SESSION['delivery_pincode'] ?? $_COOKIE['delivery_pincode'] ?? '';
    $city    = $_SESSION['delivery_city'] ?? $_COOKIE['delivery_city'] ?? '';

    // If not set in session/cookie and user is logged in, query their default address
    if (empty($pincode) && !empty($_SESSION['user_id'])) {
        $db = connect();
        $uid = (int)$_SESSION['user_id'];
        $stmt = $db->select("SELECT city, postcode FROM tbl_user_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC LIMIT 1", 'i', $uid);
        if ($stmt && $row = $stmt->fetch_assoc()) {
            if (!empty($row['postcode'])) {
                $pincode = trim($row['postcode']);
                $city = trim($row['city'] ?? '');
                $_SESSION['delivery_pincode'] = $pincode;
                $_SESSION['delivery_city'] = $city;
            }
        }
    }

    // Default fallback if still empty
    if (empty($pincode)) {
        $pincode = '110059';
        $city = 'Delhi';
    }

    $clean_city = trim($city);
    $clean_pin  = trim($pincode);
    $displayText = !empty($clean_city) ? ($clean_city . ' ' . $clean_pin) : $clean_pin;

    return [
        'pincode'     => $clean_pin,
        'city'        => $clean_city,
        'displayText' => $displayText
    ];
}

