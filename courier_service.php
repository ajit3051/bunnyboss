<?php

/**
 * Unified Courier Integration Service (Delhivery & Shadowfax)
 * Configurable via include/config.php
 */

/**
 * Get active configured courier provider
 */
function getActiveCourier()
{
    $active = strtolower(defined('_ACTIVE_COURIER_') ? _ACTIVE_COURIER_ : 'delhivery');
    if (!in_array($active, ['delhivery', 'shadowfax', 'auto'], true)) {
        $active = 'delhivery';
    }
    return $active;
}

/**
 * Check pincode serviceability with Shadowfax API
 * 
 * @param string|int $delivery_pincode Customer delivery pincode
 * @param string|int|null $pickup_pincode Optional pickup location pincode
 * @return array ['success' => bool, 'serviceable' => bool, 'message' => string]
 */
function checkShadowfaxServiceability($delivery_pincode, $pickup_pincode = null)
{
    $delivery_pincode = preg_replace('/\D/', '', (string)$delivery_pincode);
    if (strlen($delivery_pincode) < 6) {
        return [
            'success'     => false,
            'serviceable' => false,
            'message'     => 'Invalid 6-digit pincode format.'
        ];
    }

    if (empty($pickup_pincode)) {
        $pickup_pincode = defined('SHADOWFAX_PICKUP_PINCODE') ? SHADOWFAX_PICKUP_PINCODE : '110059';
    }
    $pickup_pincode = preg_replace('/\D/', '', (string)$pickup_pincode);

    $token = defined('SHADOWFAX_API_TOKEN') ? SHADOWFAX_API_TOKEN : 'f1715d10fef84d24fa366892dbc29818ffdc4aca';
    $url   = "https://dale.shadowfax.in/api/v1/serviceability/?pickup_pincode={$pickup_pincode}&delivery_pincode={$delivery_pincode}";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Token " . $token,
            "Accept: application/json"
        ],
        CURLOPT_TIMEOUT        => 10
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err || $httpCode !== 200 || empty($response)) {
        // Fallback: If API timeout occurs, do not block
        return [
            'success'     => false,
            'serviceable' => true,
            'message'     => 'Serviceability check timeout.'
        ];
    }

    $decoded = json_decode($response, true);
    $is_serviceable = !empty($decoded['Serviceability']) || (!empty($decoded['data']['delivery_serviceability']));

    return [
        'success'     => true,
        'serviceable' => $is_serviceable,
        'message'     => $is_serviceable 
            ? "Pincode {$delivery_pincode} is serviceable." 
            : "Sorry, delivery is not available for pincode {$delivery_pincode}."
    ];
}

/**
 * Check if a delivery pincode is serviceable by active courier provider
 * 
 * @param string|int $delivery_pincode Customer delivery pincode
 * @param string|null $courier_name Active courier choice ('shadowfax', 'delhivery', or null)
 * @return array ['success' => bool, 'serviceable' => bool, 'message' => string]
 */
function checkCourierServiceability($delivery_pincode, $courier_name = null)
{
    if (empty($courier_name)) {
        $courier_name = getActiveCourier();
    } else {
        $courier_name = strtolower($courier_name);
    }

    if ($courier_name === 'shadowfax' || $courier_name === 'auto') {
        return checkShadowfaxServiceability($delivery_pincode);
    }

    return [
        'success'     => true,
        'serviceable' => true,
        'message'     => 'Serviceable.'
    ];
}

/**
 * Create shipment using active or specified courier service
 *
 * @param array $order Order details array
 * @param string|null $courier_name Optional explicit courier choice ('delhivery' or 'shadowfax')
 * @return array Result containing success status, courier_name, waybill, and raw response
 */
function createCourierShipment($order, $courier_name = null)
{
    if (empty($courier_name)) {
        $courier_name = getActiveCourier();
    } else {
        $courier_name = strtolower($courier_name);
    }

    if ($courier_name === 'shadowfax') {
        $sf_res = createShadowfaxShipment($order);
        if (!$sf_res['success'] && defined('DELHIVERY_ENABLED') && DELHIVERY_ENABLED) {
            error_log("Shadowfax creation failed for order " . ($order['order_id'] ?? '') . " (" . ($sf_res['error'] ?? 'Unserviceable pincode') . "). Falling back to Delhivery...");
            $delhivery_res = createDelhiveryShipment($order);
            if ($delhivery_res['success']) {
                return $delhivery_res;
            }
        }
        return $sf_res;
    } elseif ($courier_name === 'auto') {
        if (defined('SHADOWFAX_ENABLED') && SHADOWFAX_ENABLED) {
            $sf_res = createShadowfaxShipment($order);
            if ($sf_res['success']) {
                return $sf_res;
            }
            error_log("Shadowfax auto-creation failed, falling back to Delhivery: " . json_encode($sf_res));
        }
        return createDelhiveryShipment($order);
    } else {
        return createDelhiveryShipment($order);
    }
}

/**
/**
 * Execute direct Shadowfax v3 API cURL request
 * 
 * @param array $payload Shadowfax API v3 Payload Array
 * @param string|null $token Optional API token override
 * @param string|null $url Optional API endpoint URL override
 * @return array Standardized response array with success status, http_code, waybill, data, and raw output
 */
function executeShadowfaxCurl($payload, $token = null, $url = null)
{
    $url   = !empty($url) ? $url : (defined('SHADOWFAX_CREATE_URL') ? SHADOWFAX_CREATE_URL : "https://dale.shadowfax.in/api/v3/clients/orders/");
    $token = !empty($token) ? $token : (defined('SHADOWFAX_API_TOKEN') ? SHADOWFAX_API_TOKEN : "f1715d10fef84d24fa366892dbc29818ffdc4aca");

    $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => "POST",
        CURLOPT_POSTFIELDS     => $jsonPayload,
        CURLOPT_HTTPHEADER     => [
            "Content-Type: application/json",
            "Authorization: Token " . $token,
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);

    curl_close($ch);

    if ($error) {
        error_log("Shadowfax cURL error: " . $error);
        return [
            "success"      => false,
            "courier_name" => "shadowfax",
            "http_code"    => 500,
            "waybill"      => null,
            "error"        => $error,
            "raw"          => ["error" => $error]
        ];
    }

    $decoded = json_decode($response, true);

    // Extract AWB number from v3 response formats
    $waybill = $decoded['data']['awb_number']
        ?? $decoded['awb_number']
        ?? $decoded['AWB']
        ?? $decoded['data']['awb']
        ?? $decoded['awb']
        ?? $decoded['tracking_number']
        ?? null;

    if (!$waybill && !empty($decoded['errors']) && is_string($decoded['errors'])) {
        if (preg_match('/AWB\s*:\s*([A-Za-z0-9]+)/i', $decoded['errors'], $matches)) {
            $waybill = $matches[1];
        }
    }

    $msg = $decoded['message'] ?? '';
    $has_failure = (strcasecmp($msg, 'Failure') === 0) || (!empty($decoded['errors']) && empty($waybill));

    $is_success = !empty($waybill) || (($httpCode >= 200 && $httpCode < 300) && !$has_failure);

    return [
        "success"      => $is_success,
        "courier_name" => "shadowfax",
        "http_code"    => $httpCode,
        "waybill"      => !empty($waybill) ? (string)$waybill : null,
        "error"        => $decoded['errors'] ?? ($has_failure ? ($decoded['message'] ?? 'API call failed') : null),
        "data"         => $decoded,
        "raw"          => $decoded ?? ["http_code" => $httpCode, "response" => $response]
    ];
}

/**
 * Shadowfax Shipment Creation (v3 Marketplace/Warehouse Model API)
 * Endpoint: POST https://dale.shadowfax.in/api/v3/clients/orders/
 *
 * @param array $order Order array or full pre-built Shadowfax payload
 * @return array Response payload from executeShadowfaxCurl
 */
function createShadowfaxShipment($order)
{
    // If a full pre-built payload array is passed directly
    if (isset($order['order_details']) && (isset($order['customer_details']) || isset($order['pickup_details']))) {
        return executeShadowfaxCurl($order);
    }

    // 1. Payment mode & amounts
    $raw_pay_mode = strtoupper(trim($order['payment_mode'] ?? ''));
    $grand_total  = (float)($order['grand_total'] ?? 0);
    $subtotal     = (float)($order['subtotal'] ?? $grand_total);
    $paid_amount  = (float)($order['paid_amount'] ?? 0);

    // Calculate rest (unpaid) balance amount for COD
    $rest_amount = max(0.0, round($grand_total - $paid_amount, 2));

    if ($raw_pay_mode === 'COD') {
        if ($rest_amount <= 0.00) {
            // Already fully paid; treat as Prepaid
            $pay_mode   = 'Prepaid';
            $cod_amount = 0.0;
        } else {
            $pay_mode   = 'COD';
            // Only the rest amount goes into Shadowfax as cod_amount
            $cod_amount = isset($order['cod_amount']) ? (float)$order['cod_amount'] : $rest_amount;
        }
    } else {
        $pay_mode   = 'Prepaid';
        $cod_amount = 0.0;
    }

    $weight      = (float)($order['actual_weight'] ?? $order['weight'] ?? 100.0); // Weight in grams
    $order_type  = !empty($order['order_type']) ? $order['order_type'] : (defined('SHADOWFAX_ORDER_TYPE') ? SHADOWFAX_ORDER_TYPE : 'marketplace');

    // 2. Order details object
    $order_details = [
        "client_order_id"   => (string) ($order['client_order_id'] ?? $order['order_id'] ?? '0123'),
        "actual_weight"     => $weight,
        "volumetric_weight" => (float) ($order['volumetric_weight'] ?? $weight),
        "product_value"     => $subtotal > 0 ? $subtotal : ($grand_total > 0 ? $grand_total : 100.0),
        "payment_mode"      => $pay_mode,
        "cod_amount"        => (string) $cod_amount,
        "total_amount"      => $grand_total > 0 ? $grand_total : 100.0,
        "order_service"     => !empty($order['order_service']) ? $order['order_service'] : "regular"
    ];

    if (!empty($order['awb_number'])) {
        $order_details['awb_number'] = (string) $order['awb_number'];
    }
    if (!empty($order['gstin_number'])) {
        $order_details['gstin_number'] = (string) $order['gstin_number'];
    }
    if (!empty($order['eway_bill'])) {
        $order_details['eway_bill'] = (string) $order['eway_bill'];
    }
    if (!empty($order['promised_delivery_date'])) {
        $order_details['promised_delivery_date'] = (string) $order['promised_delivery_date'];
    }

    // 3. Customer details object
    $cust_phone = preg_replace('/\D/', '', (string)($order['phone'] ?? $order['contact'] ?? '9999999999'));
    if (strlen($cust_phone) < 10) {
        $cust_phone = str_pad($cust_phone, 10, '0', STR_PAD_LEFT);
    }
    if (strlen($cust_phone) > 13) {
        $cust_phone = substr($cust_phone, -10);
    }

    $cust_pincode = (int) preg_replace('/\D/', '', (string)($order['pincode'] ?? $order['postcode'] ?? 110059));

    $customer_details = [
        "name"           => !empty($order['customer_name']) ? (string)$order['customer_name'] : (string)($order['name'] ?? 'Customer'),
        "contact"        => $cust_phone,
        "address_line_1" => !empty($order['address']) ? (string)$order['address'] : (!empty($order['address_line_1']) ? (string)$order['address_line_1'] : 'Address'),
        "city"           => !empty($order['city']) ? (string)$order['city'] : 'New Delhi',
        "state"          => !empty($order['state']) ? (string)$order['state'] : 'Delhi',
        "pincode"        => $cust_pincode
    ];

    if (!empty($order['address_line_2'])) {
        $customer_details['address_line_2'] = (string)$order['address_line_2'];
    }
    if (!empty($order['alternate_contact'])) {
        $customer_details['alternate_contact'] = preg_replace('/\D/', '', (string)$order['alternate_contact']);
    }
    if (!empty($order['latitude'])) {
        $customer_details['latitude'] = (string)$order['latitude'];
    }
    if (!empty($order['longitude'])) {
        $customer_details['longitude'] = (string)$order['longitude'];
    }

    // 4. Pickup details object
    $pickup_pincode = (int) preg_replace('/\D/', '', (string)(defined('SHADOWFAX_PICKUP_PINCODE') ? SHADOWFAX_PICKUP_PINCODE : 110059));
    $pickup_contact = preg_replace('/\D/', '', (string)(defined('SHADOWFAX_PICKUP_CONTACT') ? SHADOWFAX_PICKUP_CONTACT : '7838384314'));
    $store_code     = defined('SHADOWFAX_PICKUP_STORE_CODE') ? SHADOWFAX_PICKUP_STORE_CODE : (defined('SHADOWFAX_STORE_CODE') ? SHADOWFAX_STORE_CODE : 'SHOES_01');

    $pickup_details = [
        "name"           => defined('SHADOWFAX_PICKUP_NAME') ? SHADOWFAX_PICKUP_NAME : 'BunnyBoss Warehouse',
        "contact"        => $pickup_contact,
        "address_line_1" => defined('SHADOWFAX_PICKUP_ADDRESS') ? SHADOWFAX_PICKUP_ADDRESS : 'A1-40, Chanakya Place Part-1, 25 Foota Road (C-1 Janak Puri), Opp. Mata Chanan Devi Hospital',
        "city"           => defined('SHADOWFAX_PICKUP_CITY') ? SHADOWFAX_PICKUP_CITY : 'New Delhi',
        "state"          => defined('SHADOWFAX_PICKUP_STATE') ? SHADOWFAX_PICKUP_STATE : 'Delhi',
        "pincode"        => $pickup_pincode,
        "unique_code"    => (string) $store_code
    ];

    if (defined('SHADOWFAX_PICKUP_ADDRESS_LINE_2')) {
        $pickup_details['address_line_2'] = SHADOWFAX_PICKUP_ADDRESS_LINE_2;
    }
    if (defined('SHADOWFAX_PICKUP_LATITUDE')) {
        $pickup_details['latitude'] = (string) SHADOWFAX_PICKUP_LATITUDE;
    }
    if (defined('SHADOWFAX_PICKUP_LONGITUDE')) {
        $pickup_details['longitude'] = (string) SHADOWFAX_PICKUP_LONGITUDE;
    }

    // 5. Return to Seller (RTS / RTO) details object
    $rto_pincode = (int) preg_replace('/\D/', '', (string)(defined('SHADOWFAX_RTO_PINCODE') ? SHADOWFAX_RTO_PINCODE : $pickup_pincode));
    $rto_contact = preg_replace('/\D/', '', (string)(defined('SHADOWFAX_RTO_CONTACT') ? SHADOWFAX_RTO_CONTACT : $pickup_contact));

    $rts_details = [
        "name"           => defined('SHADOWFAX_RTO_NAME') ? SHADOWFAX_RTO_NAME : $pickup_details['name'],
        "contact"        => $rto_contact,
        "address_line_1" => defined('SHADOWFAX_RTO_ADDRESS') ? SHADOWFAX_RTO_ADDRESS : $pickup_details['address_line_1'],
        "city"           => defined('SHADOWFAX_RTO_CITY') ? SHADOWFAX_RTO_CITY : $pickup_details['city'],
        "state"          => defined('SHADOWFAX_RTO_STATE') ? SHADOWFAX_RTO_STATE : $pickup_details['state'],
        "pincode"        => $rto_pincode,
        "unique_code"    => (string) $store_code
    ];

    if (defined('SHADOWFAX_RTO_EMAIL')) {
        $rts_details['email'] = SHADOWFAX_RTO_EMAIL;
    }

    // 6. Product details array
    $product_details = [];
    if (!empty($order['items']) && is_array($order['items'])) {
        foreach ($order['items'] as $item) {
            $sku_id = (string)($item['sku_id'] ?? $item['client_sku_id'] ?? $item['sku'] ?? $item['product_id'] ?? 'MAC789');
            $p_item = [
                "sku_id"        => $sku_id,
                "client_sku_id" => $sku_id,
                "sku_name"      => (string)($item['sku_name'] ?? $item['product_title'] ?? $item['title'] ?? 'macBook Air'),
                "price"         => (float)($item['price'] ?? $item['unit_price'] ?? $grand_total)
            ];
            if (!empty($item['hsn_code'])) {
                $p_item['hsn_code'] = (string)$item['hsn_code'];
            }
            if (!empty($item['invoice_no'])) {
                $p_item['invoice_no'] = (string)$item['invoice_no'];
            }
            if (!empty($item['category'])) {
                $p_item['category'] = (string)$item['category'];
            }
            if (!empty($item['seller_details']) && is_array($item['seller_details'])) {
                $p_item['seller_details'] = $item['seller_details'];
            }
            if (!empty($item['taxes']) && is_array($item['taxes'])) {
                $p_item['taxes'] = $item['taxes'];
            }
            if (!empty($item['additional_details']) && is_array($item['additional_details'])) {
                $p_item['additional_details'] = $item['additional_details'];
            }
            $product_details[] = $p_item;
        }
    } else {
        // Query items from database if available
        if (!empty($order['order_id']) && function_exists('connect')) {
            $db = connect();
            $items_stmt = $db->select(
                "SELECT product_id, product_title, price, qty FROM tbl_order_items WHERE order_id = ?",
                'i',
                (int)$order['order_id']
            );
            if ($items_stmt) {
                while ($itemRow = $items_stmt->fetch_assoc()) {
                    $sku_id = "SKU_" . $itemRow['product_id'];
                    $product_details[] = [
                        "sku_id"        => $sku_id,
                        "client_sku_id" => $sku_id,
                        "sku_name"      => $itemRow['product_title'],
                        "price"         => (float) $itemRow['price'],
                        "additional_details" => [
                            "quantity" => (int) $itemRow['qty']
                        ]
                    ];
                }
                $items_stmt->close();
            }
        }
    }

    if (empty($product_details)) {
        $product_details[] = [
            "sku_id"        => "ORD_" . ($order['order_id'] ?? '0123'),
            "client_sku_id" => "ORD_" . ($order['order_id'] ?? '0123'),
            "sku_name"      => !empty($order['products_desc']) ? $order['products_desc'] : "General Merchandise",
            "price"         => $grand_total > 0 ? $grand_total : 100.0
        ];
    }

    // Assemble final v3 payload
    $payload = [
        "order_type"       => $order_type,
        "order_details"    => $order_details,
        "customer_details" => $customer_details,
        "pickup_details"   => $pickup_details,
        "rts_details"      => $rts_details,
        "rto_details"      => $rts_details, // Alias for backward compatibility
        "product_details"  => $product_details
    ];

    return executeShadowfaxCurl($payload);
}

/**
 * Track shipment across active or specified courier service
 *
 * @param string $waybill AWB or tracking number
 * @param string|null $courier_name Courier provider name ('delhivery' or 'shadowfax')
 * @return array Standardized tracking payload
 */
function trackCourierShipment($waybill, $courier_name = null)
{
    $waybill = trim($waybill);
    if (empty($waybill)) {
        return ['success' => false, 'message' => 'AWB / Tracking number is required.'];
    }

    // Auto-detect courier if not provided
    if (empty($courier_name)) {
        $db = connect();
        $stmt = $db->select("SELECT courier_name FROM tbl_orders WHERE courier_awb = ? OR delhivery_awb = ? LIMIT 1", 'ss', $waybill, $waybill);
        if ($stmt && $row = $stmt->fetch_assoc()) {
            $courier_name = strtolower($row['courier_name'] ?? '');
        }
    }

    if (empty($courier_name)) {
        $courier_name = getActiveCourier();
    }

    if (strtolower($courier_name) === 'shadowfax') {
        return trackShadowfaxShipment($waybill);
    } else {
        return trackDelhiveryShipment($waybill);
    }
}

/**
 * Track Shadowfax Shipment
 */
function trackShadowfaxShipment($waybill)
{
    $api_token   = defined('SHADOWFAX_API_TOKEN') ? SHADOWFAX_API_TOKEN : '';
    $track_url   = defined('SHADOWFAX_TRACK_URL') ? SHADOWFAX_TRACK_URL : 'https://api.shadowfax.in/api/v1/tracking/';
    $url         = rtrim($track_url, '/') . '/' . urlencode($waybill);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER     => [
            "Authorization: Token " . $api_token,
            "Accept: application/json"
        ],
        CURLOPT_TIMEOUT        => 15
    ]);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err       = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['success' => false, 'message' => 'Shadowfax cURL error: ' . $err];
    }

    $data = json_decode($response, true);

    if ($http_code != 200 || empty($data)) {
        return ['success' => false, 'message' => 'Shadowfax API error (HTTP ' . $http_code . ') or AWB not found.'];
    }

    // Standardize Shadowfax tracking response
    $status_str  = $data['status'] ?? $data['current_status'] ?? 'In Transit';
    $status_type = 'IT';
    if (stripos($status_str, 'delivered') !== false) {
        $status_type = 'DL';
    } elseif (stripos($status_str, 'pending') !== false || stripos($status_str, 'created') !== false) {
        $status_type = 'UD';
    }

    $scans = [];
    if (!empty($data['tracking_details']) && is_array($data['tracking_details'])) {
        foreach ($data['tracking_details'] as $track) {
            $scans[] = [
                'ScanDetail' => [
                    'Scan'            => $track['status'] ?? $track['location_status'] ?? '',
                    'ScannedLocation' => $track['location'] ?? $track['hub_name'] ?? '',
                    'ScanDateTime'     => $track['timestamp'] ?? $track['date'] ?? ''
                ]
            ];
        }
    }

    return [
        'success'        => true,
        'courier_name'   => 'Shadowfax',
        'awb'            => $data['awb_number'] ?? $waybill,
        'status'         => $status_str,
        'status_type'    => $status_type,
        'status_date'    => $data['updated_at'] ?? date('Y-m-d H:i:s'),
        'origin'         => $data['origin'] ?? 'Hub',
        'destination'    => $data['destination'] ?? 'Customer',
        'expected_date'  => $data['expected_delivery_date'] ?? '',
        'scans'          => $scans
    ];
}

/**
 * Track Delhivery Shipment
 */
function trackDelhiveryShipment($waybill)
{
    $api_token = defined('DELHIVERY_API_TOKEN') ? DELHIVERY_API_TOKEN : '';
    $track_url = defined('DELHIVERY_TRACK_URL') ? DELHIVERY_TRACK_URL : 'https://track.delhivery.com/api/v1/packages/json/';
    $url = $track_url . '?waybill=' . urlencode($waybill) . '&token=' . $api_token;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Token ' . $api_token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['success' => false, 'message' => 'Delhivery cURL error: ' . $err];
    }

    if ($http_code != 200) {
        return ['success' => false, 'message' => 'Delhivery API returned HTTP ' . $http_code];
    }

    $data = json_decode($response, true);

    if (!isset($data['ShipmentData'][0]['Shipment'])) {
        return ['success' => false, 'message' => 'No tracking data found for this AWB'];
    }

    $shipment = $data['ShipmentData'][0]['Shipment'];

    return [
        'success'        => true,
        'courier_name'   => 'Delhivery',
        'awb'            => $shipment['AWB'] ?? $waybill,
        'status'         => $shipment['Status']['Status'] ?? 'Unknown',
        'status_type'    => $shipment['Status']['StatusType'] ?? '',
        'status_date'    => $shipment['Status']['StatusDateTime'] ?? '',
        'origin'         => $shipment['Origin'] ?? '',
        'destination'    => $shipment['Destination'] ?? '',
        'expected_date'  => $shipment['ExpectedDeliveryDate'] ?? '',
        'scans'          => $shipment['Scans'] ?? []
    ];
}

/**
 * Dispatch an individual order item by item ID
 * client_order_id will be the item's 10-digit transaction_id (bbXXXXXXXX)
 * Testing items are automatically skipped and never pushed to Shadowfax.
 *
 * @param int $order_item_id tbl_order_items primary key ID
 * @param string|null $courier_name 'shadowfax', 'delhivery', or null
 * @return array Result with success, waybill, transaction_id, etc.
 */
function dispatchOrderItemById($order_item_id, $courier_name = null)
{
    $order_item_id = (int)$order_item_id;
    if ($order_item_id <= 0) {
        return ['success' => false, 'message' => 'Invalid Order Item ID.'];
    }

    $db = connect();
    if (function_exists('ensure_order_items_schema')) {
        ensure_order_items_schema($db);
    }
    $pk = function_exists('get_order_items_primary_key') ? get_order_items_primary_key($db) : 'item_id';

    // Fetch the item
    $item_stmt = $db->select("SELECT * FROM tbl_order_items WHERE $pk = ? LIMIT 1", 'i', $order_item_id);
    if (!$item_stmt || !($itemData = $item_stmt->fetch_assoc())) {
        return ['success' => false, 'message' => "Order Item #{$order_item_id} not found."];
    }
    $item_stmt->close();

    // Ensure item has a 10-digit transaction ID (bbXXXXXXXX)
    if (empty($itemData['transaction_id'])) {
        $tx_id = function_exists('generate_item_transaction_id') ? generate_item_transaction_id($db) : ('BB' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT));
        $db->update("UPDATE tbl_order_items SET transaction_id = ? WHERE $pk = ?", 'si', $tx_id, $order_item_id);
        $itemData['transaction_id'] = $tx_id;
    } else {
        $tx_id = $itemData['transaction_id'];
    }

    // CHECK: Is this a testing item?
    $is_test = function_exists('is_testing_order_item') ? is_testing_order_item($itemData) : false;
    if ($is_test) {
        $db->update(
            "UPDATE tbl_order_items SET dispatch_status = 'skipped_test', dispatch_error = 'Testing item excluded from courier push' WHERE $pk = ?",
            'i',
            $order_item_id
        );
        return [
            'success'        => false,
            'is_test'        => true,
            'transaction_id' => $tx_id,
            'message'        => "Item #{$order_item_id} ({$itemData['product_title']}) is a testing item and was excluded from Shadowfax push."
        ];
    }

    // Fetch parent order
    $order_id = (int)$itemData['order_id'];
    $order_stmt = $db->select(
        "SELECT order_id, first_name, last_name, street_address, city, postcode, phone,
                payment_method, payment_status, grand_total, subtotal, COALESCE(paid_amount, 0) as paid_amount
         FROM tbl_orders
         WHERE order_id = ? LIMIT 1",
        'i',
        $order_id
    );
    if (!$order_stmt || !($orderData = $order_stmt->fetch_assoc())) {
        return ['success' => false, 'message' => "Parent Order #{$order_id} not found."];
    }
    $order_stmt->close();

    // Payment mode & amounts for this item
    $payment_method = strtolower($orderData['payment_method'] ?? 'cod');
    $payment_status = strtolower($orderData['payment_status'] ?? 'pending');
    $grand_total    = (float)($orderData['grand_total'] ?? 0);
    $subtotal       = (float)($orderData['subtotal'] ?? 0);
    $paid_amount    = (float)($orderData['paid_amount'] ?? 0);
    $item_total     = (float)($itemData['row_total'] ?? $itemData['price'] ?? 100.0);

    // Determine if the order is fully prepaid or non-COD
    $is_order_paid = ($payment_method !== 'cod') || ($payment_status === 'paid') || ($grand_total > 0 && $paid_amount >= $grand_total);

    if ($is_order_paid) {
        $pay_mode   = 'Prepaid';
        $cod_amount = 0.0;
    } else {
        $pay_mode = 'COD';
        if ($payment_status === 'partial_paid' || $paid_amount > 0) {
            // Online deposit (shipping/GST) was already paid upfront by customer.
            // Only the item product balance (row_total) is collected as the rest amount!
            $cod_amount = $item_total;
        } else {
            // Pure COD with 0 deposit: customer must pay item share including shipping + GST
            $count_stmt = $db->select("SELECT COUNT(*) AS total_items FROM tbl_order_items WHERE order_id = ?", 'i', $order_id);
            $count_row  = $count_stmt ? $count_stmt->fetch_assoc() : null;
            $total_items = (int)($count_row['total_items'] ?? 1);
            if ($count_stmt) $count_stmt->close();

            if ($total_items <= 1) {
                $cod_amount = $grand_total > 0 ? $grand_total : $item_total;
            } else {
                $ratio = ($subtotal > 0) ? ($item_total / $subtotal) : (1 / $total_items);
                $cod_amount = round($grand_total * $ratio, 2);
            }
        }
    }

    $existing_awb = !empty($itemData['courier_awb']) ? $itemData['courier_awb'] : null;

    $shipmentItem = [
        'order_id'          => $order_id,
        'client_order_id'   => $tx_id, // 10-digit transaction ID (e.g. bb12345678)
        'customer_name'     => trim($orderData['first_name'] . ' ' . $orderData['last_name']),
        'phone'             => $orderData['phone'],
        'address'           => $orderData['street_address'],
        'city'              => $orderData['city'],
        'state'             => 'Delhi',
        'pincode'           => $orderData['postcode'],
        'payment_mode'      => $pay_mode,
        'grand_total'       => $item_total,
        'subtotal'          => $item_total,
        'paid_amount'       => ($pay_mode === 'COD' && ($payment_status === 'partial_paid' || $paid_amount > 0)) ? 0.0 : $paid_amount,
        'cod_amount'        => $cod_amount,
        'item_count'        => 1,
        'products_desc'     => $itemData['product_title'],
        'items'             => [
            [
                'sku_id'             => 'SKU_' . ($itemData['product_id'] ?? $order_item_id),
                'client_sku_id'      => 'SKU_' . ($itemData['product_id'] ?? $order_item_id),
                'sku_name'           => $itemData['product_title'],
                'price'              => (float)$itemData['price'],
                'additional_details' => [
                    'quantity'       => (int)($itemData['qty'] ?? 1),
                    'size'           => (string)($itemData['size'] ?? ''),
                    'transaction_id' => $tx_id
                ]
            ]
        ]
    ];

    if (!empty($existing_awb)) {
        $shipmentItem['awb_number'] = $existing_awb;
    }

    $result = createCourierShipment($shipmentItem, $courier_name);

    if ($result['success'] && !empty($result['waybill'])) {
        $courier_used = strtolower($result['courier_name'] ?? 'shadowfax');
        $waybill      = $result['waybill'];

        $db->update(
            "UPDATE tbl_order_items SET courier_name = ?, courier_awb = ?, dispatch_status = ?, dispatch_error = NULL WHERE $pk = ?",
            'sssi',
            $courier_used,
            $waybill,
            $courier_used,
            $order_item_id
        );

        return [
            'success'        => true,
            'is_test'        => false,
            'courier_name'   => $courier_used,
            'waybill'        => $waybill,
            'transaction_id' => $tx_id,
            'message'        => "Item #{$order_item_id} dispatched successfully via " . ucfirst($courier_used) . ". AWB: {$waybill}",
            'raw'            => $result['raw'] ?? []
        ];
    }

    $error_msg = $result['error'] ?? $result['raw']['errors'] ?? $result['raw']['message'] ?? 'Failed to create shipment with courier.';
    if (is_array($error_msg)) {
        $error_msg = json_encode($error_msg);
    }

    $db->update(
        "UPDATE tbl_order_items SET dispatch_status = 'failed', dispatch_error = ? WHERE $pk = ?",
        'si',
        $error_msg,
        $order_item_id
    );

    return [
        'success'        => false,
        'is_test'        => false,
        'courier_name'   => $courier_name ?? getActiveCourier(),
        'transaction_id' => $tx_id,
        'message'        => "Courier creation error for item #{$order_item_id}: {$error_msg}",
        'raw'            => $result['raw'] ?? []
    ];
}

/**
 * Dispatch all items of an order to Shadowfax / active courier item-wise.
 * Testing items are automatically skipped and will not be pushed to courier.
 *
 * @param int $order_id Order ID
 * @param string|null $courier_name 'shadowfax', 'delhivery', or null
 * @return array Result array with summary of dispatch
 */
function dispatchOrderById($order_id, $courier_name = null)
{
    $order_id = (int)$order_id;
    if ($order_id <= 0) {
        return ['success' => false, 'message' => 'Invalid Order ID.'];
    }

    $db = connect();
    if (function_exists('ensure_order_items_schema')) {
        ensure_order_items_schema($db);
    }
    $pk = function_exists('get_order_items_primary_key') ? get_order_items_primary_key($db) : 'item_id';

    $items_stmt = $db->select("SELECT * FROM tbl_order_items WHERE order_id = ?", 'i', $order_id);
    if (!$items_stmt || $items_stmt->num_rows == 0) {
        return ['success' => false, 'message' => "No items found for order #{$order_id}."];
    }

    $item_results       = [];
    $all_awbs           = [];
    $courier_used       = 'shadowfax';
    $has_success        = false;
    $has_failure        = false;
    $skipped_test_count = 0;

    while ($item = $items_stmt->fetch_assoc()) {
        $itemId = (int)$item[$pk];
        $res = dispatchOrderItemById($itemId, $courier_name);
        $item_results[$itemId] = $res;

        if (!empty($res['is_test'])) {
            $skipped_test_count++;
        } elseif (!empty($res['success'])) {
            $has_success = true;
            if (!empty($res['waybill'])) {
                $all_awbs[] = $res['waybill'];
            }
            if (!empty($res['courier_name'])) {
                $courier_used = $res['courier_name'];
            }
        } else {
            $has_failure = true;
        }
    }
    $items_stmt->close();

    // Update parent order level summary
    $awb_string = implode(', ', $all_awbs);
    if ($has_success) {
        $order_dispatch_status = $courier_used;
        $db->update(
            "UPDATE tbl_orders SET dispatch_status = ?, courier_name = ?, courier_awb = ?, delhivery_awb = ?, dispatch_error = NULL, is_pincode_serviceable = 1 WHERE order_id = ?",
            'ssssi',
            $order_dispatch_status,
            $courier_used,
            $awb_string,
            ($courier_used === 'delhivery' ? $awb_string : null),
            $order_id
        );
    } elseif ($has_failure) {
        $db->update(
            "UPDATE tbl_orders SET dispatch_status = 'failed', dispatch_error = 'One or more items failed dispatch' WHERE order_id = ?",
            'i',
            $order_id
        );
    } elseif ($skipped_test_count > 0 && !$has_success && !$has_failure) {
        // Order contained only test items
        $db->update(
            "UPDATE tbl_orders SET dispatch_status = 'skipped_test', dispatch_error = 'All items were test items' WHERE order_id = ?",
            'i',
            $order_id
        );
    }

    return [
        'success'            => $has_success || ($skipped_test_count > 0 && !$has_failure),
        'courier_name'       => $courier_used,
        'waybill'            => $awb_string,
        'awbs'               => $all_awbs,
        'skipped_test_count' => $skipped_test_count,
        'item_results'       => $item_results,
        'message'            => $has_success 
            ? "Order items dispatched. AWBs: " . ($awb_string ?: 'N/A') . ($skipped_test_count > 0 ? " ({$skipped_test_count} test item(s) skipped)" : '')
            : ($skipped_test_count > 0 ? "All items were test items - skipped Shadowfax push." : "Dispatch failed for order items.")
    ];
}
