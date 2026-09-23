<?php
require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/../include/courier_service.php';

header('Content-Type: application/json');

$action = trim($_POST['action'] ?? $_GET['action'] ?? 'set_location');

// Action 1: Get saved addresses for logged-in user
if ($action === 'get_saved_addresses') {
    $user_id = (int)($_SESSION['user_id'] ?? 0);
    if (empty($user_id)) {
        echo json_encode(['success' => false, 'logged_in' => false, 'addresses' => []]);
        exit;
    }
    $db = connect();
    $stmt = $db->select("SELECT id, title, first_name, last_name, street_address, city, state, postcode, is_default FROM tbl_user_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC", 'i', $user_id);
    $addresses = [];
    if ($stmt) {
        while ($row = $stmt->fetch_assoc()) {
            $addresses[] = $row;
        }
    }
    echo json_encode([
        'success'   => true, 
        'logged_in' => true, 
        'addresses' => $addresses,
        'current_pincode' => $_SESSION['delivery_pincode'] ?? $_COOKIE['delivery_pincode'] ?? '',
        'current_city'    => $_SESSION['delivery_city'] ?? $_COOKIE['delivery_city'] ?? ''
    ]);
    exit;
}

// Action 2: User clicks a saved address
if ($action === 'select_address') {
    $user_id = (int)($_SESSION['user_id'] ?? 0);
    $address_id = (int)($_POST['address_id'] ?? 0);
    if (empty($user_id) || empty($address_id)) {
        echo json_encode(['success' => false, 'message' => 'Please sign in to select saved address.']);
        exit;
    }
    $db = connect();
    $stmt = $db->select("SELECT city, postcode, street_address, title FROM tbl_user_addresses WHERE id = ? AND user_id = ?", 'ii', $address_id, $user_id);
    if ($stmt && $row = $stmt->fetch_assoc()) {
        $pincode = trim($row['postcode']);
        $city = trim($row['city'] ?? '');
        
        $_SESSION['delivery_pincode'] = $pincode;
        $_SESSION['delivery_city'] = $city;
        setcookie('delivery_pincode', $pincode, time() + (86400 * 30), '/');
        setcookie('delivery_city', $city, time() + (86400 * 30), '/');

        $displayText = !empty($city) ? ($city . ' ' . $pincode) : $pincode;
        
        // Serviceability check
        $svc = function_exists('checkCourierServiceability') ? checkCourierServiceability($pincode) : ['serviceable' => true];
        $is_serviceable = !empty($svc['serviceable']);

        echo json_encode([
            'success'     => true,
            'pincode'     => $pincode,
            'city'        => $city,
            'displayText' => $displayText,
            'serviceable' => $is_serviceable,
            'message'     => 'Delivery location updated to ' . $displayText . '.'
        ]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Selected address could not be found.']);
        exit;
    }
}

// Action 3: Set location via entered 6-digit Pincode
$pincode = trim($_POST['pincode'] ?? $_GET['pincode'] ?? '');
$city    = trim($_POST['city'] ?? $_GET['city'] ?? '');

$pincode = preg_replace('/\D/', '', $pincode);

if (strlen($pincode) !== 6) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid 6-digit Indian PIN code.'
    ]);
    exit;
}

// If city is not provided, try to find in existing addresses
if (empty($city)) {
    $db = connect();
    $find_stmt = $db->select("SELECT city FROM tbl_user_addresses WHERE postcode = ? AND city != '' LIMIT 1", 's', $pincode);
    if ($find_stmt && $f_row = $find_stmt->fetch_assoc()) {
        $city = trim($f_row['city']);
    }
}

// Fallback city name if still empty: mapping common regions by first 2 digits
if (empty($city)) {
    $prefix2 = substr($pincode, 0, 2);
    $prefix_map = [
        '11' => 'Delhi',
        '12' => 'Haryana',
        '13' => 'Haryana',
        '14' => 'Punjab',
        '15' => 'Punjab',
        '16' => 'Chandigarh',
        '17' => 'Himachal Pradesh',
        '18' => 'Jammu & Kashmir',
        '19' => 'Jammu & Kashmir',
        '20' => 'UP (West)',
        '21' => 'UP',
        '22' => 'Lucknow',
        '23' => 'UP',
        '24' => 'Uttarakhand',
        '25' => 'UP',
        '26' => 'Bareilly',
        '27' => 'Gorakhpur',
        '28' => 'Agra',
        '30' => 'Jaipur',
        '31' => 'Udaipur',
        '32' => 'Kota',
        '33' => 'Bikaner',
        '34' => 'Jodhpur',
        '36' => 'Gujarat',
        '37' => 'Gujarat',
        '38' => 'Ahmedabad',
        '39' => 'Surat',
        '40' => 'Mumbai',
        '41' => 'Pune',
        '42' => 'Nashik',
        '43' => 'Aurangabad',
        '44' => 'Nagpur',
        '45' => 'Indore',
        '46' => 'Bhopal',
        '47' => 'Gwalior',
        '48' => 'Jabalpur',
        '49' => 'Raipur',
        '50' => 'Hyderabad',
        '51' => 'Andhra Pradesh',
        '52' => 'Vijayawada',
        '53' => 'Visakhapatnam',
        '56' => 'Bengaluru',
        '57' => 'Mangaluru',
        '58' => 'Hubballi',
        '59' => 'Belagavi',
        '60' => 'Chennai',
        '61' => 'Tamil Nadu',
        '62' => 'Madurai',
        '63' => 'Coimbatore',
        '64' => 'Coimbatore',
        '67' => 'Kozhikode',
        '68' => 'Kochi',
        '69' => 'Thiruvananthapuram',
        '70' => 'Kolkata',
        '71' => 'West Bengal',
        '72' => 'West Bengal',
        '73' => 'Siliguri',
        '74' => 'West Bengal',
        '75' => 'Bhubaneswar',
        '76' => 'Odisha',
        '77' => 'Odisha',
        '78' => 'Guwahati',
        '79' => 'North East',
        '80' => 'Patna',
        '81' => 'Bihar',
        '82' => 'Ranchi',
        '83' => 'Jamshedpur',
        '84' => 'Muzaffarpur',
        '85' => 'Bhagalpur'
    ];
    $city = $prefix_map[$prefix2] ?? 'India';
}

// Check serviceability
$svc = function_exists('checkCourierServiceability') ? checkCourierServiceability($pincode) : ['serviceable' => true];
$is_serviceable = !empty($svc['serviceable']);

$_SESSION['delivery_pincode'] = $pincode;
$_SESSION['delivery_city'] = $city;
setcookie('delivery_pincode', $pincode, time() + (86400 * 30), '/');
setcookie('delivery_city', $city, time() + (86400 * 30), '/');

$displayText = !empty($city) ? ($city . ' ' . $pincode) : $pincode;

echo json_encode([
    'success'     => true,
    'pincode'     => $pincode,
    'city'        => $city,
    'displayText' => $displayText,
    'serviceable' => $is_serviceable,
    'message'     => $is_serviceable 
        ? "Delivery is available for {$displayText}." 
        : "Pincode {$pincode} may have limited delivery options."
]);
exit;
