<?php
include 'includes/db.php';
include 'includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $conn->prepare('SELECT * FROM products WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header('Location: index.php');
    exit;
}

$email = $_SESSION['email'] ?? '';
$message = '';
if (isset($_POST['add'])) {
    $qty = isset($_POST['qty']) ? (int) $_POST['qty'] : 0;
    $emailInput = trim($_POST['email'] ?? '');
    $currentQty = (int) ($_SESSION['cart'][$id] ?? 0);

    if (empty($email)) {
        if ($emailInput === '') {
            $message = 'Please enter your email before buying.';
        } elseif (!filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
        } else {
            $_SESSION['email'] = $emailInput;
            $email = $emailInput;
        }
    } elseif ($emailInput !== '' && $emailInput !== $email) {
        if (!filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
        } else {
            $_SESSION['email'] = $emailInput;
            $email = $emailInput;
        }
    }

    if ($message === '') {
        if ($qty < 1) {
            $message = 'Quantity must be at least 1.';
        } elseif (($currentQty + $qty) > (int) $product['stock']) {
            $message = 'Requested quantity is not available in stock.';
        } else {
            $_SESSION['cart'][$id] = $currentQty + $qty;
            $message = 'Item added to cart.';
            if (!empty($email)) {
                $message .= ' Email saved for purchase.';
            }
        }
    }
}

renderHeader($conn, 'Product Details');
?>

<article class="product-detail">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    <?php if (!empty($product['minidesc'])): ?>
        <p class="product-summary"><?= htmlspecialchars($product['minidesc']) ?></p>
    <?php endif; ?>
    <p><?= htmlspecialchars($product['description'] ?? 'No description available.') ?></p>
    <p><strong>Price:</strong> <?= number_format((float) $product['price'], 2) ?> SAR</p>
    <p><strong>Available stock:</strong> <?= (int) $product['stock'] ?></p>

    <?php if ($message !== ''): ?>
        <p class="notice"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (!empty($email) && $message === ''): ?>
        <p class="notice">Email saved for purchase: <?= htmlspecialchars($email) ?>.</p>
    <?php endif; ?>

    <form method="post" id="add-to-cart-form" onsubmit="return validateProductForm();">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" <?= empty($email) ? 'required' : '' ?>
            value="<?= htmlspecialchars($email) ?>">
        <label for="qty">Quantity</label>
        <input id="qty" type="number" name="qty" min="1" max="<?= (int) $product['stock'] ?>" required>
        <button class="btn" name="add" type="submit">Add to Cart</button>
        <button class="btn secondary" type="button" onclick="openHelpWindow();">Help</button>
    </form>

    <a class="btn" href="cart.php">Go to Checkout</a>
</article>

<script>
    function validateProductForm() {
        const qtyInput = document.getElementById('qty');
        const qty = parseInt(qtyInput.value, 10);
        const maxStock = <?= (int) $product['stock'] ?>;

        if (!qty || qty < 1) {
            alert('Please enter a valid quantity.');
            return false;
        }

        if (qty > maxStock) {
            alert('Quantity exceeds available stock.');
            return false;
        }

        return true;
    }

    function openHelpWindow() {
        const help = window.open('', 'helpWindow', 'width=450,height=300');
        help.document.write('<h3>How to buy this product</h3>');
        help.document.write('<p>1) Enter quantity.</p>');
        help.document.write('<p>2) Click Add to Cart.</p>');
        help.document.write('<p>3) Open Cart to modify items or complete your purchase.</p>');
    }
</script>

<?php renderFooter(); ?>