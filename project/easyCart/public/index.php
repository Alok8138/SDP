<?php
/**
 * index.php
 */
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';

// Instantiate Controller
$controller = new ProductController();

// Fetch products from Database via Controller
$products = $controller->getAllProducts();

// For categories and brands, we can still use static for now or add to model
$categories = require '../app/models/Category.php';
$brands = require '../app/models/Brand.php';

require_once '../app/helpers/functions.php';
require_once '../resources/views/header.php';
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
    <?php if (empty($products)): ?>
        <p>No products found in the database. Please run schema.sql to import sample data.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
          <div class="card">
            <div class="card-image-wrapper">
              <!-- Using image from DB join -->
              <img src="<?= htmlspecialchars($product['image'] ?? 'assets/images/default.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
            </div>
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p class="price">$<?= htmlspecialchars($product['price']) ?></p>
            
            <?php if (!empty($product['old_price'])): ?>
                <p class="old-price" style="text-decoration: line-through; color: #777;">$<?= htmlspecialchars($product['old_price']) ?></p>
            <?php endif; ?>

            <p class="brand">Brand: <?= htmlspecialchars($product['brand']) ?></p>

            <div class="product-actions">
              <a href="pdp.php?id=<?= urlencode($product['entity_id'] ?? $product['id']) ?>">
                <button>View Product</button>
              </a>
              <form method="POST" action="pdp.php?id=<?= urlencode($product['entity_id'] ?? $product['id']) ?>" class="quick-add-form ajax-cart-form">
                <input type="hidden" name="product_id" value="<?= (int)($product['entity_id'] ?? $product['id']) ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="card-cart-btn" aria-label="Quick add to cart" title="Add to Cart">
                  <img src="assets/images/cart.jpg" alt="Add to Cart" />
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- Categories -->
<section class="light-section">
  <div class="container">
    <h2>Popular Categories</h2>

    <div class="grid three category-grid">
      <?php foreach ($categories as $category): ?>
        <a href="plp.php" class="category-card"><?= htmlspecialchars($category) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Brands -->
<section class="container">
  <h2>Popular Brands</h2>

  <div class="grid three brand-grid">
    <?php foreach ($brands as $brand): ?>
      <a href="plp.php?brand[]=<?= urlencode($brand) ?>" class= "brand-card"><?= htmlspecialchars($brand) ?></a>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once '../resources/views/footer.php'; ?>