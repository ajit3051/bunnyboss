<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect("localhost", "admin", "admin", "u488042670_bunnyboss_in");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_query($conn, "TRUNCATE TABLE tbl_item_variants;");

$res = mysqli_query($conn, "SELECT * FROM tbl_item_master");
$migrated_count = 0;

while ($item = mysqli_fetch_assoc($res)) {
    $item_id = $item['id'];
    $colors = array_map('trim', explode(',', $item['color_name']));
    $sizes  = array_map('trim', explode(',', $item['size_name']));
    $styles = array_map('trim', explode(',', $item['style_name']));

    $colors = array_values(array_filter($colors, function ($val) {
        return $val !== '' && $val !== 'null';
    }));
    $sizes  = array_values(array_filter($sizes, function ($val) {
        return $val !== '' && $val !== 'null';
    }));
    $styles = array_values(array_filter($styles, function ($val) {
        return $val !== '' && $val !== 'null';
    }));

    if (empty($colors)) $colors = [''];
    if (empty($sizes)) $sizes = [''];
    if (empty($styles)) $styles = [''];

    $qty   = (int)($item['min_qty'] ?? 0);
    $price = (float)($item['sp'] ?? 0.00);
    $mrp   = (float)($item['mrp'] ?? 0.00);
    $status = mysqli_real_escape_string($conn, $item['status'] ?? 'active');

    foreach ($colors as $c) {
        $c_esc = mysqli_real_escape_string($conn, $c);
        foreach ($sizes as $s) {
            $s_esc = mysqli_real_escape_string($conn, $s);
            foreach ($styles as $st) {
                $st_esc = mysqli_real_escape_string($conn, $st);

                $sql = "INSERT INTO tbl_item_variants (item_id, color_name, size_name, style_name, quantity, price, mrp, status) 
                        VALUES ('$item_id', '$c_esc', '$s_esc', '$st_esc', '$qty', '$price', '$mrp', '$status')";
                mysqli_query($conn, $sql) or die(mysqli_error($conn));
                $migrated_count++;
            }
        }
    }
}

echo "Migration complete! Total variant records created: $migrated_count\n";
