<?php
require_once(__DIR__ . "/include/config.php");

unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_mobile'], $_SESSION['user_role']);

header("Location: " . _BASEURL . "index.php");
exit;
