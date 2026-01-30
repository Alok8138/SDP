<?php
require '../includes/init.php';
require '../includes/header.php';

$products = require '../data/featuredProduct.php';
$categories = require '../data/categories.php';
$brands = require '../data/brands.php';

// var_dump($_SESSION['cart'][1]['name']);
?>

<!-- Hero Section -->

<section class="hero">
  <h1>Welcome to EasyCart</h1>
  <p>Your one-stop online shopping destination</p>
  <a href="plp.php"><button>Shop Now</button></a>
</section>

<!-- Featured Products -->
<section class="container">
  <h2>Featured Products</h2>

  <div class="grid three">
    <?php foreach ($products as $product): ?>
      <div class="card">
        <div class="card-image-wrapper">
          <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" />
        </div>
        <h3><?= $product['name'] ?></h3>
        <p class="price">$<?= $product['price'] ?></p>
        <div class="product-actions">
          <a href="pdp.php?id=<?= $product['id'] ?>">
            <button>View Product</button>
          </a>
          <form method="POST" action="pdp.php?id=<?= $product['id'] ?>" class="quick-add-form ajax-cart-form">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="card-cart-btn" aria-label="Quick add to cart" title="Add to Cart">
              <img src="../images/cart.jpg" alt="Add to Cart" />
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Categories -->
<section class="light-section">
  <div class="container">
    <h2>Popular Categories</h2>

    <div class="grid three category-grid">
      <?php foreach ($categories as $category): ?>
        <a href="plp.php" class="category-card"><?= $category ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Brands -->
<section class="container">
  <h2>Popular Brands</h2>

  <div class="grid three brand-grid">
    <?php foreach ($brands as $brand): ?>
      <a href="plp.php?brand[]=<?= urlencode($brand) ?>" class= "brand-card"><?= $brand ?></a>
    <?php endforeach; ?>
  </div>
</section>

<?php require '../includes/footer.php'; ?>