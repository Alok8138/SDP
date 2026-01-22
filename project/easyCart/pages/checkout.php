<?php
require '../includes/init.php';
require '../includes/header.php';

/**
 * Cart validation
 */
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
  header("Location: cart.php");
  exit;
}

$cart = $_SESSION['cart'];
$subtotal = 0;

/**
 * Calculate subtotal
 */
foreach ($cart as $item) {
  $subtotal += $item['price'] * $item['qty'];
}

/**
 * Place order
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $deliveryType = isset($_POST['delivery_type']) ? $_POST['delivery_type'] : 'normal';
  $deliveryCharge = ($deliveryType === 'express') ? $subtotal * 0.1 : 0;
  $finalTotal = $subtotal + $deliveryCharge;

  // Load existing orders
  $orders = require '../data/orders.php';

  // Create new order
  $orders[] = [
    "id" => "#ORD" . rand(1000, 9999),
    "date" => date("d M Y"),
    "items" => count($cart),
    "total" => $finalTotal,
    "status" => "Placed"
  ];

  // Clear cart
  $_SESSION['cart'] = [];

  // Save orders to session
  $_SESSION['orders'] = $orders;

  header("Location: myOrders.php");
  exit;
}
?>

<section class="container checkout-page">
  <h2>Checkout</h2>

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
          <td><?= $item['name'] ?></td>
          <td><?= $item['qty'] ?></td>
          <td>$<?= $item['price'] * $item['qty'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="cart-summary">
    <h3>Order Summary</h3>

    <div class="summary-row">
      <span>Subtotal:</span>
      <span id="subtotal">$<?= number_format($subtotal, 2) ?></span>
    </div>

    <div class="delivery-section">
      <h4>Delivery Option</h4>
      <div class="delivery-options">
        <label class="delivery-option">
          <input type="radio" name="delivery" value="normal" checked>
          <span>Normal Delivery (Free)</span>
        </label>
        <label class="delivery-option">
          <input type="radio" name="delivery" value="express">
          <span>Express Delivery (+10%)</span>
        </label>
      </div>
    </div>

    <div class="summary-row">
      <span>Delivery Charge:</span>
      <span id="delivery-charge">$0.00</span>
    </div>

    <div class="summary-row total-row">
      <span>Total:</span>
      <span id="final-total">$<?= number_format($subtotal, 2) ?></span>
    </div>

    <form method="POST" id="checkout-form">
      <input type="hidden" name="delivery_type" id="delivery-type" value="normal">
      <button type="submit">Place Order</button>
    </form>
  </div>

  <script>
    (function() {
      const subtotal = <?= $subtotal ?>;
      const deliveryRadios = document.querySelectorAll('input[name="delivery"]');
      const deliveryChargeEl = document.getElementById('delivery-charge');
      const finalTotalEl = document.getElementById('final-total');
      const deliveryTypeInput = document.getElementById('delivery-type');

      function updateTotals() {
        const selectedDelivery = document.querySelector('input[name="delivery"]:checked').value;
        let deliveryCharge = 0;

        if (selectedDelivery === 'express') {
          deliveryCharge = subtotal * 0.1;
        }

        const finalTotal = subtotal + deliveryCharge;

        deliveryChargeEl.textContent = '$' + deliveryCharge.toFixed(2);
        finalTotalEl.textContent = '$' + finalTotal.toFixed(2);
        deliveryTypeInput.value = selectedDelivery;
      }

      deliveryRadios.forEach(radio => {
        radio.addEventListener('change', updateTotals);
      });

      updateTotals();
    })();
  </script>
</section>

<?php require '../includes/footer.php'; ?>