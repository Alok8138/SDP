<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css" />
</head>

<body>

    <header class="header">
        <nav class="nav">
            <a href="<?= BASE_URL ?>/">Home</a>
            <a href="<?= BASE_URL ?>/plp">Shop</a>
            <a href="<?= BASE_URL ?>/cart">Cart</a>
            <a href="<?= BASE_URL ?>/orders">Orders</a>
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= BASE_URL ?>/logout">Logout</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login">Login</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/cart" class="cart-icon-link">
                <button type="submit" class="card-cart-btn" aria-label="Quick add to cart" title="Add to Cart">
                    <img src="<?= BASE_URL ?>/assets/images/cart.jpg" alt="Add to Cart" />
                </button>
                <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <span class="cart-badge"><?= array_sum(array_column($_SESSION['cart'], 'qty')) ?></span>
                <?php endif; ?>
            </a>
        </nav>
    </header>