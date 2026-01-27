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
 * Calculate subtotal (PRODUCTS ONLY)
 */
foreach ($cart as $item) {
  $subtotal += $item['price'] * $item['qty'];
}

/**
 * Handle order submission
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $deliveryType  = $_POST['delivery_type'] ?? 'standard';
  $contactNumber = trim($_POST['contact_number'] ?? '');
  $address       = trim($_POST['address'] ?? '');

  if (empty($contactNumber) || empty($address)) {
    $error = "Please fill in all required fields.";
  } else {

    // ---------------- SHIPPING RULES ----------------
    switch ($deliveryType) {
      case 'standard':
        $shipping = 40;
        break;

      case 'express':
        $shipping = min(80, $subtotal * 0.10);
        break;

      case 'white_glove':
        $shipping = min(150, $subtotal * 0.05);
        break;

      case 'freight':
        $shipping = max($subtotal * 0.03, 200);
        break;

      default:
        $shipping = 40;
    }

    // ---------------- TAX ----------------
    $tax = ($subtotal + $shipping) * 0.18;

    // ---------------- FINAL TOTAL ----------------
    $finalTotal = $subtotal + $shipping + $tax;

    // Load existing orders
    $orders = require '../data/orders.php';

    $orders[] = [
      "id" => "#ORD" . rand(1000, 9999),
      "date" => date("d M Y"),
      "items" => count($cart),
      "subtotal" => round($subtotal, 2),
      "shipping" => round($shipping, 2),
      "tax" => round($tax, 2),
      "total" => round($finalTotal, 2),
      "status" => "Placed",
      "contact" => $contactNumber,
      "address" => $address,
      "shipping_type" => $deliveryType
    ];

    $_SESSION['cart'] = [];
    $_SESSION['orders'] = $orders;

    header("Location: myOrders.php");
    exit;
  }
}
?>

<section class="container checkout-page">
  <h2>Checkout</h2>

  <?php if (isset($error)): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="checkout-layout">

    <!-- LEFT SIDE -->
    <div class="checkout-main">
      <div class="shipping-info">
        <h3>Shipping Information</h3>
        <form method="POST" id="checkout-form">

          <div class="form-group">
            <label for="contact_number">Contact Number *</label>
            <input type="tel" id="contact_number" name="contact_number"
              pattern="[0-9]{10}" required
              value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label for="address">Delivery Address *</label>
            <textarea id="address" name="address" rows="4" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
          </div>

          <input type="hidden" name="delivery_type" id="delivery-type" value="standard">
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
                <td><?= $item['qty'] ?></td>
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

        <label><input type="radio" name="delivery" value="standard" checked> Standard Shipping ($40)</label>
        <label><input type="radio" name="delivery" value="express"> Express (Min $80 or 10%)</label>
        <label><input type="radio" name="delivery" value="white_glove"> White Glove (Min $150 or 5%)</label>
        <label><input type="radio" name="delivery" value="freight"> Freight (3% or $200 min)</label>
      </div>

      <div class="summary-row">
        <span>Shipping:</span>
        <span id="delivery-charge">$40.00</span>
      </div>

      <div class="summary-row">
        <span>Tax (18%):</span>
        <span id="tax-amount">$0.00</span>
      </div>

      <div class="summary-row total-row">
        <span>Total:</span>
        <span id="final-total">$<?= number_format($subtotal + 40, 2) ?></span>
      </div>

      <button type="submit" form="checkout-form" class="checkout-btn">Place Order</button>
    </div>
  </div>

  <script>
    window.checkoutData = {
      subtotal: <?= $subtotal ?>
    };
  </script>
  <script src="../javascript/checkout.js"></script>
</section>

<?php require '../includes/footer.php'; ?>