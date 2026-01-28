<?php
require '../includes/init.php';
require '../includes/header.php';

/**
 * Initialize cart
 */
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$subtotal = 0;

/**
 * Remove item from cart
 */
if (isset($_GET['remove'])) {
  $removeId = (int) $_GET['remove'];
  unset($_SESSION['cart'][$removeId]);

  header("Location: cart.php");
  exit;
}

/**
 * Update item quantity
 */
if (isset($_GET['update']) && isset($_GET['qty'])) {
  $updateId = (int) $_GET['update'];
  $newQty = (int) $_GET['qty'];

  if ($newQty < 1) {
    $newQty = 1;
  }

  if (isset($_SESSION['cart'][$updateId])) {
    $_SESSION['cart'][$updateId]['qty'] = $newQty;
  }

  header("Location: cart.php");
  exit;
}
?>

<section class="container cart-page">
  <h2>Your Cart</h2>

  <?php if (empty($cart)): ?>
    <p>Your cart is empty.</p>
    <a href="plp.php">
      <button>Continue Shopping</button>
    </a>
  <?php else: ?>

    <div class="cart-layout">
      <div class="cart-items">
        <table class="cart-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($cart as $item): ?>
              <?php
              $itemTotal = $item['price'] * $item['qty'];
              $subtotal += $itemTotal;
              ?>
              <tr data-id="<?= $item['id'] ?>">
                <td class="cart-product">
                  <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>" />
                  <span class="cart-product-name"><?= $item['name'] ?></span>
                </td>
                <td class="cart-price">$<?= $item['price'] ?></td>
                <td class="cart-quantity">
                  <div class="quantity-box">
                    <a href="#" class="qty-btn" data-action="decrease" data-id="<?= $item['id'] ?>" aria-label="Decrease quantity">−</a>
                    <span class="qty-value"><?= $item['qty'] ?></span>
                    <a href="#" class="qty-btn" data-action="increase" data-id="<?= $item['id'] ?>" aria-label="Increase quantity">+</a>
                  </div>
                </td>
                <td class="cart-total">$<?= number_format($itemTotal, 2) ?></td>
                <td class="cart-action">
                  <a href="#" class="remove-link" data-id="<?= $item['id'] ?>">Remove</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="cart-summary">
        <h3>Order Summary</h3>
        <div class="summary-row">
          <span>Subtotal:</span>
          <span id="cart-subtotal">$<?= number_format($subtotal, 2) ?></span>
        </div>
        <div class="summary-row">
          <span>Tax:</span>
          <span id="cart-tax">$<?= number_format($subtotal * 0.1, 2) ?></span>
        </div>
        <div class="summary-row total-row">
          <span>Total:</span>
          <span id="cart-total">$<?= number_format($subtotal * 1.1, 2) ?></span>
        </div>
        <a href="checkout.php">
          <button class="checkout-btn">Proceed to Checkout</button>
        </a>
      </div>
    </div>
    <script src="../javascript/cart_manager.js"></script>
    </div>

  <?php endif; ?>
</section>

<?php require '../includes/footer.php'; ?>