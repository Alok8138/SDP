<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/CheckoutController.php';

$controller = new CheckoutController();
$data = $controller->index();

$cart = $data['cart'];
$subtotal = $data['subtotal'];
$shipping = $data['shipping'];
$tax = $data['tax'];
$finalTotal = $data['finalTotal'];
$deliveryType = $data['deliveryType'];
$error = $data['error'] ?? null;

require_once '../app/helpers/functions.php';
require_once '../resources/views/header.php';
?>

<section class="container checkout-page">
  <h2>Checkout</h2>

  <?php if ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="checkout-layout">

    <!-- LEFT SIDE -->
    <div class="checkout-main">
      <div class="shipping-info">
        <h3>Shipping Information</h3>
        <form method="POST" id="checkout-form">

          <div class="form-row">
            <div class="form-group">
              <label>First Name *</label>
              <input type="text" name="first_name" required
                value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label>Last Name *</label>
              <input type="text" name="last_name" required
                value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
            </div>
          </div>

          <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" required
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Contact Number *</label>
            <input type="tel" name="contact_number" pattern="[0-9]{10}" required
              value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Delivery Address *</label>
            <textarea name="address" rows="3" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>City *</label>
              <input type="text" name="city" required
                value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label>Postal Code *</label>
              <input type="text" name="postal_code" required
                value="<?= htmlspecialchars($_POST['postal_code'] ?? '') ?>">
            </div>
          </div>

          <input type="hidden" name="delivery_type" id="delivery-type" value="<?= htmlspecialchars($deliveryType) ?>">
        </form>
      </div>

      <div class="order-items">
        <h3>Order Items</h3>
        <table class="cart-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Qty</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= (int)$item['qty'] ?></td>
                <td>$<?= number_format($item['price'] * $item['qty'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="cart-summary">
      <h3>Order Summary</h3>

      <div class="summary-row">
        <span>Subtotal:</span>
        <span id="subtotal">$<?= number_format($subtotal, 2) ?></span>
      </div>

      <div class="delivery-section">

        <h4>Delivery Option</h4>
        <div class="shipping-options">
          <label>
            <input type="radio" name="delivery" value="standard" id="ship_standard" 
              <?= ($deliveryType === 'standard') ? 'checked' : '' ?> 
              <?= (!is_shipping_allowed($subtotal, 'standard')) ? 'disabled' : '' ?>> 
            Standard Shipping ($40)
          </label>
          <label>
            <input type="radio" name="delivery" value="express" id="ship_express" 
              <?= ($deliveryType === 'express') ? 'checked' : '' ?> 
              <?= (!is_shipping_allowed($subtotal, 'express')) ? 'disabled' : '' ?>> 
            Express (10%, Min $80)
          </label>
          <label>
            <input type="radio" name="delivery" value="white_glove" id="ship_whiteglove" 
              <?= ($deliveryType === 'white_glove') ? 'checked' : '' ?> 
              <?= (!is_shipping_allowed($subtotal, 'white_glove')) ? 'disabled' : '' ?>> 
            White Glove (5%, Min $150)
          </label>
          <label>
            <input type="radio" name="delivery" value="freight" id="ship_freight" 
              <?= ($deliveryType === 'freight') ? 'checked' : '' ?> 
              <?= (!is_shipping_allowed($subtotal, 'freight')) ? 'disabled' : '' ?>> 
            Freight (3%, Min $200)
          </label>
        </div>
      </div>

      <div class="summary-row">
        <span>Shipping:</span>
        <span id="delivery-charge">$<?= number_format($shipping, 2) ?></span>
      </div>

      <div class="summary-row">
        <span>Tax (10%):</span>
        <span id="tax-amount">$<?= number_format($tax, 2) ?></span>
      </div>

      <div class="summary-row total-row">
        <span>Total:</span>
        <span id="final-total">$<?= number_format($finalTotal, 2) ?></span>
      </div>

      <button type="submit" form="checkout-form" class="checkout-btn">Place Order</button>
    </div>
  </div>

  <script src="assets/js/checkout.js"></script>
</section>

<?php require '../resources/views/footer.php'; ?>