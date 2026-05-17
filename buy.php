<?php
include 'includes/db.php';
include 'includes/functions.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$total = 0;
$purchasedNames = [];
$cartItems = [];

$conn->begin_transaction();

foreach ($_SESSION['cart'] as $id => $qty) {
    $productId = (int) $id;
    $quantity = (int) $qty;

    $result = $conn->query("SELECT name, price, stock FROM products WHERE id = $productId");
    if (!$result || $result->num_rows === 0 || !($product = $result->fetch_assoc())) {
        $conn->rollback();
        die('Invalid product in cart.');
    }

    if ($quantity > (int) $product['stock']) {
        $conn->rollback();
        die('Not enough stock for one or more products.');
    }

    $unitPrice = (float) $product['price'];
    $total += $unitPrice * $quantity;
    $purchasedNames[] = $product['name'] . ' x ' . $quantity;
    $cartItems[] = [
        'id' => $productId,
        'qty' => $quantity,
        'price' => $unitPrice
    ];
}

$conn->query("INSERT INTO orders (total) VALUES ($total)");
$order_id = $conn->insert_id;

foreach ($cartItems as $item) {
    $id = $item['id'];
    $qty = $item['qty'];
    $price = $item['price'];
    $conn->query(
        "INSERT INTO order_items (order_id, product_id, quantity, price)
         VALUES ($order_id, $id, $qty, $price)"
    );

    $conn->query(
        "UPDATE products SET stock = stock - $qty WHERE id = $id"
    );
}

$conn->commit();

$pastPurchasesValue = implode(', ', $purchasedNames);
setcookie('past_purchases', $pastPurchasesValue, time() + 3600 * 24 * 30, '/');
unset($_SESSION['cart']);

renderHeader($conn, 'Order Complete');
$email = $_SESSION['email'] ?? '';
?>
<h2>Order Completed</h2>
<p>Your order number is <strong>#<?= (int) $order_id ?></strong>.</p>
<p>Total paid: <strong><?= number_format((float) $total, 2) ?> SAR</strong></p>
<?php if (!empty($email)): ?>
    <p class="notice">Purchase confirmation sent to <?= htmlspecialchars($email) ?>.</p>
<?php else: ?>
    <p class="notice">Purchase confirmation could not be sent because no email address was provided.</p>
<?php endif; ?>
<a class="btn" href="index.php">Return to Home</a>
<?php renderFooter(); ?>