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

    <div class="pdp-image">
      <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
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

      <form method="POST" id="addToCartForm">
        <div class="quantity-wrapper">
          <span class="qty-label">Quantity:</span>
          <div class="quantity-box">
            <button type="button" id="decrementBtn" aria-label="Decrease quantity">−</button>
            <span class="qty-value" id="quantityValue">1</span>
            <button type="button" id="incrementBtn" aria-label="Increase quantity">+</button>
          </div>
        </div>
        <input type="hidden" name="quantity" id="quantityInput" value="1">
        <button type="submit">Add to Cart</button>
      </form>
    </div>

  </div>
</section>

<script>
  // Quantity control - prevent going below 1
  (function() {
    const decrementBtn = document.getElementById('decrementBtn');
    const incrementBtn = document.getElementById('incrementBtn');
    const quantityValue = document.getElementById('quantityValue');
    const quantityInput = document.getElementById('quantityInput');

    let quantity = 1;
    const minQuantity = 1;

    function updateQuantity(newQty) {
      if (newQty < minQuantity) {
        newQty = minQuantity;
      }
      quantity = newQty;
      quantityValue.textContent = quantity;
      quantityInput.value = quantity;

      // Disable decrement button if at minimum
      if (quantity <= minQuantity) {
        decrementBtn.disabled = true;
        decrementBtn.style.opacity = '0.5';
        decrementBtn.style.cursor = 'not-allowed';
      } else {
        decrementBtn.disabled = false;
        decrementBtn.style.opacity = '1';
        decrementBtn.style.cursor = 'pointer';
      }
    }

    decrementBtn.addEventListener('click', function() {
      if (quantity > minQuantity) {
        updateQuantity(quantity - 1);
      }
    });

    incrementBtn.addEventListener('click', function() {
      updateQuantity(quantity + 1);
    });

    // Initialize
    updateQuantity(1);
  })();
</script>

<?php require '../includes/footer.php'; ?>