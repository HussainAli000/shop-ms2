<?php
include 'includes/db.php';
include 'includes/functions.php';

$notice = '';

if (isset($_POST['empty'])) {
    unset($_SESSION['cart']);
    header('Location: cart.php');
    exit;
}

if (isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    unset($_SESSION['cart'][$deleteId]);
    header('Location: cart.php');
    exit;
}

if (isset($_POST['update_id'], $_POST['new_qty'])) {
    $updateId = (int)$_POST['update_id'];
    $newQty = (int)$_POST['new_qty'];
    if ($newQty <= 0) {
        unset($_SESSION['cart'][$updateId]);
    } else {
        $check = $conn->prepare('SELECT stock FROM products WHERE id = ?');
        $check->bind_param('i', $updateId);
        $check->execute();
        $row = $check->get_result()->fetch_assoc();
        if ($row && $newQty <= (int)$row['stock']) {
            $_SESSION['cart'][$updateId] = $newQty;
            header('Location: cart.php');
            exit;
        } else {
            $notice = 'Unable to update quantity: stock is not enough.';
        }
    }
}

if (isset($_POST['buy'])) {
    header('Location: buy.php');
    exit;
}

renderHeader($conn, 'Checkout');
?>

<h2>Checkout</h2>
<?php if ($notice !== ''): ?>
    <p class="notice"><?= htmlspecialchars($notice) ?></p>
<?php endif; ?>

<?php if (empty($_SESSION['cart'])): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
    <table class="cart-table">
        <thead>
        <tr>
            <th>Product</th>
            <th>Unit Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $grandTotal = 0.0;
        $stmt = $conn->prepare('SELECT id, name, image, price, stock FROM products WHERE id = ?');
        foreach ($_SESSION['cart'] as $id => $qty):
            $pid = (int)$id;
            $quantity = (int)$qty;
            $stmt->bind_param('i', $pid);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            if (!$product) {
                continue;
            }
            $lineTotal = ((float)$product['price']) * $quantity;
            $grandTotal += $lineTotal;
        ?>
            <tr>
                <td>
                    <img class="cart-thumb" src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?= htmlspecialchars($product['name']) ?>
                </td>
                <td><?= number_format((float)$product['price'], 2) ?> SAR</td>
                <td><?= $quantity ?></td>
                <td><?= number_format($lineTotal, 2) ?> SAR</td>
                <td>
                    <form method="post" class="inline-form" onsubmit="return validateUpdateQty(this);">
                        <input type="hidden" name="update_id" value="<?= $pid ?>">
                        <input type="number" name="new_qty" min="1" max="<?= (int)$product['stock'] ?>" required>
                        <button class="btn secondary" type="submit">Modify</button>
                    </form>
                    <form method="post" class="inline-form">
                        <input type="hidden" name="delete_id" value="<?= $pid ?>">
                        <button class="btn danger" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php $stmt->close(); ?>
        </tbody>
    </table>

    <p><strong>Grand Total:</strong> <?= number_format($grandTotal, 2) ?> SAR</p>
    <form method="post" class="inline-form">
        <button class="btn" type="submit" name="buy">Buy Products</button>
        <button class="btn danger" type="submit" name="empty">Delete All / Empty Cart</button>
    </form>
<?php endif; ?>

<script>
function validateUpdateQty(form) {
    const qtyInput = form.querySelector('input[name="new_qty"]');
    const value = parseInt(qtyInput.value, 10);
    if (!value || value < 1) {
        alert('Enter a valid quantity greater than zero.');
        return false;
    }
    return true;
}
</script>

<?php renderFooter(); ?>
