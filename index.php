<?php
include 'includes/db.php';
include 'includes/functions.php';

renderHeader($conn, 'Products');
?>

<h2>All Products</h2>

<?php if (!empty($_COOKIE['past_purchases'])): ?>
    <section class="notice">
        <h3>Past Purchases</h3>
        <p><?= htmlspecialchars($_COOKIE['past_purchases']) ?></p>
    </section>
<?php endif; ?>

<section class="products-grid">
    <?php
        $result = $conn->query('SELECT id, name, image, price, minidesc FROM products ORDER BY id DESC');
        while ($product = $result->fetch_assoc()):
        ?>
            <article class="product">
                <a href="product.php?id=<?= (int)$product['id'] ?>">
                    <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                </a>
                <?php if (!empty($product['minidesc'])): ?>
                    <p class="product-summary"><?= htmlspecialchars($product['minidesc']) ?></p>
                <?php endif; ?>
                <p><?= number_format((float)$product['price'], 2) ?> SAR</p>
                <a class="btn" href="product.php?id=<?= (int)$product['id'] ?>">View details</a>
            </article>
    <?php endwhile; ?>
</section>

<?php renderFooter(); ?>
