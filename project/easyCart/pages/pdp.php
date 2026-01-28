<?php
require '../includes/init.php';
require '../includes/header.php';

$products = require '../data/products.php';

// Validate product ID
if (!isset($_GET['id'])) {
  echo "<p style='padding:20px'>Product not found.</p>";
  require '../includes/footer.php';
  exit;
}

$productId = (int) $_GET['id'];
$product = null;

// Find product by ID
foreach ($products as $item) {
  if ($item['id'] === $productId) {
    $product = $item;
    break;
  }
}

// If product not found
if (!$product) {
  echo "<p style='padding:20px'>Product not found.</p>";
  require '../includes/footer.php';
  exit;
}

// Initialize cart
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $product['id'];
  $qty = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

  // Ensure quantity is at least 1
  if ($qty < 1) {
    $qty = 1;
  }

  if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['qty'] += $qty;
  } else {
    $_SESSION['cart'][$id] = [
      'id' => $product['id'],
      'name' => $product['name'],
      'price' => $product['price'],
      'image' => $product['image'],
      'qty' => $qty
    ];
  }

  header("Location: cart.php");
  exit;
}
?>

<section class="container pdp">
  <div class="pdp-layout">

    <div class="pdp-image" id="pdpImageContainer">
      <?php
      // Prepare images array: favour gallery if available, otherwise fallback to single image
      $sliderImages = !empty($product['gallery']) ? $product['gallery'] : [$product['image']];
      $hasMultipleImages = count($sliderImages) > 1;
      ?>
      
      <img src="<?= $sliderImages[0] ?>" alt="<?= $product['name'] ?>" id="mainImage">
      
      <?php if ($hasMultipleImages): ?>
        <button class="slider-btn prev-btn" id="prevBtn" aria-label="Previous image">&#10094;</button>
        <button class="slider-btn next-btn" id="nextBtn" aria-label="Next image">&#10095;</button>
        
        <div class="slider-dots" id="sliderDots">
          <?php foreach ($sliderImages as $index => $img): ?>
            <span class="dot <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>"></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <script>
  window.pdpData = {
    sliderImages: <?= json_encode($sliderImages) ?>
  };
</script>
    </div>

    <div class="pdp-details">
      <h1><?= $product['name'] ?></h1>

      <p class="price">
        $<?= $product['price'] ?>
        <?php if (!empty($product['old_price'])): ?>
          <span class="old-price">$<?= $product['old_price'] ?></span>
        <?php endif; ?>
      </p>

      <p class="description"><?= $product['description'] ?></p>

      <?php if (!empty($product['features'])): ?>
        <ul class="features">
          <?php foreach ($product['features'] as $feature): ?>
            <li><?= $feature ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <form method="POST" id="addToCartForm" class="ajax-cart-form">
        <div class="quantity-wrapper">
          <span class="qty-label">Quantity:</span>
          <div class="quantity-box">
            <button type="button" id="decrementBtn" aria-label="Decrease quantity">−</button>
            <span class="qty-value" id="quantityValue">1</span>
            <button type="button" id="incrementBtn" aria-label="Increase quantity">+</button>
          </div>
        </div>
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <input type="hidden" name="quantity" id="quantityInput" value="1">
        <button type="submit">Add to Cart</button>
      </form>
    </div>

  </div>
</section>

<script src="../javascript/pdp.js"></script>

<?php require '../includes/footer.php'; ?>