<?php
include '../includes/db.php';
include '../includes/auth.php';
include '../includes/functions.php';

$product = null;
$message = '';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare('SELECT id, name, price, stock FROM products WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare('DELETE FROM products WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $message = 'Product deleted successfully.';
}

renderHeader($conn, 'Search and Delete Product');
?>

<h2>Search Product to Delete</h2>
<?php if ($message !== ''): ?>
    <p class="notice"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="get" onsubmit="return validateDeleteSearch();">
    <label for="search_id">Product ID</label>
    <input id="search_id" type="number" name="id" min="1" required>
    <button class="btn" type="submit">Search</button>
</form>

<?php if ($product): ?>
    <div class="notice">
        <p><strong><?= htmlspecialchars($product['name']) ?></strong></p>
        <p>Price: <?= number_format((float)$product['price'], 2) ?> SAR</p>
        <p>Stock: <?= (int)$product['stock'] ?></p>
    </div>

    <form method="post" onsubmit="return confirm('Delete this product?');">
        <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
        <button class="btn danger" type="submit" name="delete_product">Delete Product</button>
    </form>
<?php elseif (isset($_GET['id'])): ?>
    <p class="notice">No product found with that ID.</p>
<?php endif; ?>

<script>
function validateDeleteSearch() {
    const id = parseInt(document.getElementById('search_id').value, 10);
    if (!id || id < 1) {
        alert('Enter a valid product ID.');
        return false;
    }
    return true;
}
</script>

<?php renderFooter(); ?>
