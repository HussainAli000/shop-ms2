<?php
include '../includes/db.php';
include '../includes/auth.php';
include '../includes/functions.php';

renderHeader($conn, 'Admin Dashboard');
?>

<h2>Admin Dashboard</h2>
<p>Use the following pages to manage products.</p>
<div class="admin-actions">
    <a class="btn" href="admin_add.php">Add Product</a>
    <a class="btn" href="admin_edit.php">Search & Modify Product</a>
    <a class="btn danger" href="admin_delete.php">Search & Delete Product</a>
</div>

<?php renderFooter(); ?>
