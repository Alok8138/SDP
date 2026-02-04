<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/CartController.php';

$controller = new CartController();
$data = $controller->showCart();

$cart = $data['cart'];
$subtotal = $data['subtotal'];

require_once '../app/helpers/functions.php';
require_once '../resources/views/header.php';
?>

<section class="container cart-page">
  <h2>Your Cart</h2>

  <?php if (empty($cart)): ?>
    <p>Your cart is empty.</p>
    <a href="plp">
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
              ?>
              <tr data-id="<?= htmlspecialchars($item['id']) ?>">
                <td class="cart-product">
                  <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" />
                  <span class="cart-product-name"><?= htmlspecialchars($item['name']) ?></span>
                </td>
                <td class="cart-price">$<?= htmlspecialchars($item['price']) ?></td>
                <td class="cart-quantity">
                  <div class="quantity-box">
                    <a href="#" class="qty-btn" data-action="decrease" data-id="<?= htmlspecialchars($item['id']) ?>" aria-label="Decrease quantity">−</a>
                    <span class="qty-value"><?= (int)$item['qty'] ?></span>
                    <a href="#" class="qty-btn" data-action="increase" data-id="<?= htmlspecialchars($item['id']) ?>" aria-label="Increase quantity">+</a>
                  </div>
                </td>
                <td class="cart-total">$<?= number_format($itemTotal, 2) ?></td>
                <td class="cart-action">
                  <a href="#" class="remove-link" data-id="<?= htmlspecialchars($item['id']) ?>">Remove</a>
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
        <div class="summary-note">
           <small>Shipping & Tax calculated at checkout</small>
        </div>
        <a href="checkout">
          <button class="checkout-btn">Proceed to Checkout</button>
        </a>
      </div>
    </div>
    <script src="<?= BASE_URL ?>/assets/js/cart_manager.js"></script>
    </div>

  <?php endif; ?>
</section>

<?php require '../resources/views/footer.php'; ?>