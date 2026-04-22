<?php
if (empty($_SESSION['admin'])) {
  header('Location: admin_login.php');
  exit;
}
?>
