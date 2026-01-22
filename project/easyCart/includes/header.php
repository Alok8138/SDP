<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../style/style.css" />
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
                <!-- <svg class="cart-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 2L7 6m0 0L5 2m2 4h12l-1 6H8m-3 0L4 22h16M8 22h8"/>
                </svg> -->
                <button type="submit" class="card-cart-btn" aria-label="Quick add to cart" title="Add to Cart">
                    <img src="../images/cart.jpg" alt="Add to Cart" />
                </button>
                <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <span class="cart-badge"><?= array_sum(array_column($_SESSION['cart'], 'qty')) ?></span>
                <?php endif; ?>
            </a>
        </nav>
    </header>