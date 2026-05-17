<?php
include '../includes/db.php';
include '../includes/auth.php';
include '../includes/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $minidesc = trim($_POST['minidesc'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);

    if ($name === '' || $price <= 0 || $stock < 0 || empty($_FILES['img']['name'])) {
        $message = 'Please fill all required fields correctly.';
    } else {
        $imageName = basename($_FILES['img']['name']);
        $targetPath = '../images/' . $imageName;
        move_uploaded_file($_FILES['img']['tmp_name'], $targetPath);

        $stmt = $conn->prepare('INSERT INTO products (name, image, price, stock, description, minidesc) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssdiss', $name, $imageName, $price, $stock, $description, $minidesc);
        $stmt->execute();
        $message = 'Product added successfully.';
    }
}

renderHeader($conn, 'Add Product');
?>

<h2>Add Product</h2>
<?php if ($message !== ''): ?>
    <p class="notice"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" onsubmit="return validateAddForm();">
    <label for="name">Name</label>
    <input id="name" name="name" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4"></textarea>

    <label for="minidesc">Summary Description</label>
    <textarea id="minidesc" name="minidesc" rows="4"></textarea>

    <label for="price">Price</label>
    <input id="price" name="price" type="number" min="0.01" step="0.01" required>

    <label for="stock">Stock</label>
    <input id="stock" name="stock" type="number" min="0" required>

    <label for="img">Image</label>
    <input id="img" type="file" name="img" accept="image/*" required>

    <button class="btn" type="submit">Add Product</button>
</form>

<script>
    function validateAddForm() {
        const name = document.getElementById('name').value.trim();
        const price = parseFloat(document.getElementById('price').value);
        const stock = parseInt(document.getElementById('stock').value, 10);
        const image = document.getElementById('img').value;

        if (name.length < 2 || !image || !price || price <= 0 || stock < 0) {
            alert('Please enter valid product details.');
            return false;
        }
        return true;
    }
</script>

<?php renderFooter(); ?>