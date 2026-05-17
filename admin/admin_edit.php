<?php
include '../includes/db.php';
include '../includes/auth.php';
include '../includes/functions.php';

$product = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $minidesc = trim($_POST['minidesc'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    $currentImage = trim($_POST['current_image'] ?? '');
    $newImage = $currentImage;
    if (!empty($_FILES['img']['name'])) {
        $newImage = basename($_FILES['img']['name']);
        move_uploaded_file($_FILES['img']['tmp_name'], '../images/' . $newImage);
    }

    $stmt = $conn->prepare('UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ?, minidesc = ? WHERE id = ?');
    $stmt->bind_param('ssdissi', $name, $description, $price, $stock, $newImage, $minidesc, $id);
    $stmt->execute();
    header('Location: admin_edit.php?id=' . $id . '&updated=1');
    exit;
}

renderHeader($conn, 'Search and Modify Product');
?>

<h2>Search Product to Modify</h2>
<?php if (isset($_GET['updated'])): ?>
    <p class="notice">Product updated successfully.</p>
<?php endif; ?>

<form method="get" onsubmit="return validateSearchId('search_id');">
    <label for="search_id">Product ID</label>
    <input id="search_id" type="number" name="id" min="1" required>
    <button class="btn" type="submit">Search</button>
</form>

<?php if ($product): ?>
    <hr>
    <form method="post" enctype="multipart/form-data" onsubmit="return validateEditForm();">
        <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
        <input type="hidden" name="current_image" value="<?= htmlspecialchars($product['image']) ?>">

        <label for="name">Name</label>
        <input id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>

        <label for="minidesc">Summary Description</label>
        <textarea id="minidesc" name="minidesc" rows="4"><?= htmlspecialchars($product['minidesc'] ?? '') ?></textarea>

        <label for="price">Price</label>
        <input id="price" name="price" type="number" step="0.01" min="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>

        <label for="stock">Stock</label>
        <input id="stock" name="stock" type="number" min="0" value="<?= (int)$product['stock'] ?>" required>

        <label for="img">Update image (optional)</label>
        <input id="img" type="file" name="img" accept="image/*">

        <button class="btn" type="submit" name="update_product">Update Product</button>
    </form>
<?php elseif (isset($_GET['id'])): ?>
    <p class="notice">No product found with that ID.</p>
<?php endif; ?>

<script>
function validateSearchId(inputId) {
    const id = parseInt(document.getElementById(inputId).value, 10);
    if (!id || id < 1) {
        alert('Enter a valid product ID.');
        return false;
    }
    return true;
}

function validateEditForm() {
    const name = document.getElementById('name').value.trim();
    const price = parseFloat(document.getElementById('price').value);
    const stock = parseInt(document.getElementById('stock').value, 10);
    if (name.length < 2 || !price || price <= 0 || stock < 0) {
        alert('Enter valid data before updating.');
        return false;
    }
    return true;
}
</script>

<?php renderFooter(); ?>
