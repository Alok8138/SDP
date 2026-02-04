<?php
/**
 * index.php - Front Controller
 */
require_once '../app/config/database.php';
require_once '../app/helpers/functions.php';

// Simple Router
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Correct the base path if running in a subfolder
$basePath = '/Internship/project/easyCart/public';
if (!defined('BASE_URL')) {
    define('BASE_URL', $basePath);
}
$route = trim(str_replace($basePath, '', $request), '/');

// Dispatch to the correct page script
switch ($route) {
    case 'login':
        require 'login.php';
        exit;
    case 'signup':
        require 'signup.php';
        exit;
    case 'logout':
        require 'logout.php';
        exit;
    case 'cart':
        require 'cart.php';
        exit;
    case 'checkout':
        require 'checkout.php';
        exit;
    case 'orders':
        require 'myOrders.php';
        exit;
    case 'dashboard':
        require 'dashboard.php';
        exit;
    case 'plp':
        require 'plp.php';
        exit;
    case 'pdp':
        require 'pdp.php';
        exit;
    case '':
    case 'index':
        // Home page logic (already in this file below)
        break;
}

require_once __DIR__ . '/../app/controllers/ProductController.php';

// Instantiate Controller
$controller = new ProductController();

// Fetch products from Database via Controller (Task 2: 6 Random Products)
$products = $controller->getHomeProducts();

// Dynamic Categories and Brands (Task 1)
$categories = Category::getAll();
$brands = Product::getBrands();

require_once '../resources/views/header.php';
?>

<!-- Hero Section -->

<section class="hero">
  <h1>Welcome to EasyCart</h1>
  <p>Your one-stop online shopping destination</p>
  <a href="plp"><button>Shop Now</button></a>
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
              <img src="<?= BASE_URL ?>/<?= htmlspecialchars($product['image'] ?? 'assets/images/default.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
            </div>
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p class="price">$<?= htmlspecialchars($product['price']) ?></p>
            
            <?php if (!empty($product['old_price'])): ?>
                <p class="old-price" style="text-decoration: line-through; color: #777;">$<?= htmlspecialchars($product['old_price']) ?></p>
            <?php endif; ?>

            <p class="brand">Brand: <?= htmlspecialchars($product['brand']) ?></p>

            <div class="product-actions">
              <a href="<?= BASE_URL ?>/pdp?id=<?= urlencode($product['entity_id'] ?? $product['id']) ?>">
                <button>View Product</button>
              </a>
              <form method="POST" action="<?= BASE_URL ?>/pdp?id=<?= urlencode($product['entity_id'] ?? $product['id']) ?>" class="quick-add-form ajax-cart-form">
                <input type="hidden" name="product_id" value="<?= (int)($product['entity_id'] ?? $product['id']) ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="card-cart-btn" aria-label="Quick add to cart" title="Add to Cart">
                  <img src="<?= BASE_URL ?>/assets/images/cart.jpg" alt="Add to Cart" />
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
        <a href="plp?category_id=<?= (int)$category['entity_id'] ?>" class="category-card"><?= htmlspecialchars($category['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Brands -->
<section class="container">
  <h2>Popular Brands</h2>

  <div class="grid three brand-grid">
    <?php foreach ($brands as $brand): ?>
      <a href="plp?brand[]=<?= urlencode($brand) ?>" class= "brand-card"><?= htmlspecialchars($brand) ?></a>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once '../resources/views/footer.php'; ?>