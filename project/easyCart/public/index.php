<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';

$controller = new HomeController();
$data = $controller->index();

$products = $data['products'];
$categories = $data['categories'];
$brands = $data['brands'];

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
    <?php foreach ($products as $product): ?>
      <div class="card">
        <div class="card-image-wrapper">
          <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
        </div>
        <h3><?= htmlspecialchars($product['name']) ?></h3>
        <p class="price">$<?= htmlspecialchars($product['price']) ?></p>
        <div class="product-actions">
          <a href="pdp.php?id=<?= urlencode($product['id']) ?>">
            <button>View Product</button>
          </a>
          <form method="POST" action="pdp.php?id=<?= urlencode($product['id']) ?>" class="quick-add-form ajax-cart-form">
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="card-cart-btn" aria-label="Quick add to cart" title="Add to Cart">
              <img src="assets/images/cart.jpg" alt="Add to Cart" />
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

<?php require '../resources/views/footer.php'; ?>