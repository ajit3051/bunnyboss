<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/include/config.php';

try {

    $mysqli = connect();

    if (!$mysqli) {
        throw new Exception("Database connection failed.");
    }

    $query = $mysqli->query("
        SELECT *
        FROM tbl_orders
        ORDER BY order_id DESC
    ");

    if (!$query) {
        throw new Exception($mysqli->error);
    }

    $orders = [];

    while ($row = mysqli_fetch_assoc($query)) {

        $orderid = (int)$row['order_id'];
        
        $itemsQuery = $mysqli->query("
    SELECT
        oi.product_id,
        oi.item_id,
        oi.product_title,
        oi.qty,
        oi.size,
        oi.price,
        oi.row_total,
        oi.shipping,
        im.picture AS product_image
    FROM tbl_order_items oi
    LEFT JOIN tbl_item_master im
        ON oi.product_id = im.id
    WHERE oi.order_id = '$orderid'
");

        // $itemsQuery = $mysqli->query("
        //     SELECT
        //         oi.product_id,
        //         oi.item_id,
        //         oi.product_title,
        //         oi.qty,
        //         oi.size,
        //         oi.price,
        //         oi.row_total,
        //         oi.shipping,
        //         im.picture AS product_image
        //     FROM tbl_order_items oi
        //     LEFT JOIN tbl_item_master im
        //         ON oi.item_id = im.id
        //     WHERE oi.order_id = $orderid
        // ");

        $items = [];

        if ($itemsQuery) {
            while ($item = mysqli_fetch_assoc($itemsQuery)) {
                $items[] = $item;
            }
        }

        $row['items'] = $items;
        $orders[] = $row;
    }

    echo json_encode([
        "resp" => true,
        "msg" => "Orders fetched successfully",
        "data" => $orders
    ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

} catch (Exception $e) {

    echo json_encode([
        "resp" => false,
        "msg" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

exit;