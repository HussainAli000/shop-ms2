<?php
include '../includes/db.php';

unset($_SESSION['admin'], $_SESSION['admin_name']);
session_regenerate_id(true);

header('Location: admin_login.php');
exit;
?>
