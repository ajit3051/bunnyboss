<?php
include_once(__DIR__ . '/config.php');
if (!isset($conn) || !$conn) {
    $conn = connect();
}
?>
