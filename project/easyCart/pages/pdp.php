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
        (function() {
          const images = <?= json_encode($sliderImages) ?>;
          const mainImage = document.getElementById('mainImage');
          const prevBtn = document.getElementById('prevBtn');
          const nextBtn = document.getElementById('nextBtn');
          const dots = document.querySelectorAll('.dot');
          
          let currentIndex = 0;
          
          function showImage(index) {
            // Loop navigation
            if (index < 0) {
              currentIndex = images.length - 1;
            } else if (index >= images.length) {
              currentIndex = 0;
            } else {
              currentIndex = index;
            }
            
            // Update image source
            mainImage.src = images[currentIndex];
            
            // Update dots
            dots.forEach(dot => dot.classList.remove('active'));
            if (dots[currentIndex]) {
              dots[currentIndex].classList.add('active');
            }
          }
          
          if (prevBtn) {
            prevBtn.addEventListener('click', function() {
              showImage(currentIndex - 1);
            });
          }
          
          if (nextBtn) {
            nextBtn.addEventListener('click', function() {
              showImage(currentIndex + 1);
            });
          }
          
          dots.forEach(dot => {
            dot.addEventListener('click', function() {
              const index = parseInt(this.getAttribute('data-index'));
              showImage(index);
            });
          });
        })();
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