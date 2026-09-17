<?php
mysqli_report(MYSQLI_REPORT_OFF);

$host = 'localhost';
$user = 'u488042670_bunnyboss_in';
$pass = 'C|yUjed8Tu!8';
$db   = 'u488042670_bunnyboss_in';

$conn = @new mysqli($host, $user, $pass, $db);

if ($conn->connect_errno) {
    echo "Connection failed (" . $conn->connect_errno . "): " . $conn->connect_error;
    exit;
}

echo "Database connected successfully!";
$conn->close();