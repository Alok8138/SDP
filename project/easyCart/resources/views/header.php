<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

    <header class="header">
        <nav class="nav">
            <a href="index.php">Home</a>
            <a href="plp.php">Shop</a>
            <a href="cart.php">Cart</a>
            <a href="myOrders.php">Orders</a>
            <a href="login.php">Login</a>
            <a href="cart.php" class="cart-icon-link">
                <button type="submit" class="card-cart-btn" aria-label="Quick add to cart" title="Add to Cart">
                    <img src="assets/images/cart.jpg" alt="Add to Cart" />
                </button>
                <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <span class="cart-badge"><?= array_sum(array_column($_SESSION['cart'], 'qty')) ?></span>
                <?php endif; ?>
            </a>
        </nav>
    </header>