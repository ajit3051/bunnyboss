<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login_user']) || empty($_SESSION['login_user'])) {
    header("Location: index.php");
    exit();
}

include_once('include/config.php');
global $conn;
$conn = connect();
$db = $conn;
$user_check = $_SESSION['login_user'];
$result = $db->select("SELECT * FROM tbl_admin WHERE username = ?", "s", $user_check);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $login_session = $row['username'];
} else {
    header("Location: index.php");
    exit();
}
?>