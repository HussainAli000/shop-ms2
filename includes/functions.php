<?php
function getCartItemCount(): int
{
    if (empty($_SESSION['cart'])) {
        return 0;
    }

    return array_sum($_SESSION['cart']);
}

function getCartTotal(mysqli $conn): float
{
    if (empty($_SESSION['cart'])) {
        return 0.0;
    }

    $total = 0.0;
    $stmt = $conn->prepare('SELECT price FROM products WHERE id = ?');
    if (!$stmt) {
        return 0.0;
    }

    foreach ($_SESSION['cart'] as $productId => $qty) {
        $id = (int)$productId;
        $quantity = (int)$qty;
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $total += ((float)$row['price']) * $quantity;
        }
    }

    $stmt->close();
    return $total;
}

function renderHeader(mysqli $conn, string $title): void
{
    $isAdminPage = basename(dirname($_SERVER['SCRIPT_NAME'])) === 'admin';
    $basePath = $isAdminPage ? dirname(dirname($_SERVER['SCRIPT_NAME'])) : dirname($_SERVER['SCRIPT_NAME']);
    $basePath = rtrim($basePath, '/');
    if ($basePath === '') {
        $basePath = '/';
    }

    $cartCount = getCartItemCount();
    $cartTotal = number_format(getCartTotal($conn), 2);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?></title>
        <link rel="stylesheet" href="<?= $basePath ?>/css/style.css">
    </head>
    <body>
    <header class="top-nav">
        <h1 class="site-title">Online Shop</h1>
        <nav>
            <a href="<?= $basePath ?>/index.php">Home</a>
            <a href="<?= $basePath ?>/cart.php">Cart (<?= $cartCount ?> items - <?= $cartTotal ?> SAR)</a>
            <a href="<?= $basePath ?>/contact.php">Contact Us</a>
            <?php if (!empty($_SESSION['admin'])): ?>
                <a href="<?= $basePath ?>/admin/admin_dashboard.php">Admin: <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Manager') ?></a>
                <a href="<?= $basePath ?>/admin/admin_logout.php">Logout</a>
            <?php else: ?>
                <a href="<?= $basePath ?>/admin/admin_login.php">Admin Login</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="container">
    <?php
}

function renderFooter(): void
{
    ?>
    </main>
    </body>
    </html>
    <?php
}
?>
