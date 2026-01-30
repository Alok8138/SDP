<?php
require '../includes/init.php';
require '../includes/header.php';
require_once '../includes/functions.php';


/**
 * Cart validation
 */
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
  header("Location: cart.php");
  exit;
}

$cart = $_SESSION['cart'];
$subtotal = 0;

// Calculate totals (Default to Standard for initial view)
$totals = get_cart_totals($cart, 'standard');
$subtotal = $totals['subtotal'];
$shipping = $totals['shipping'];
$tax = $totals['tax'];
$finalTotal = $totals['finalTotal'];


/**
 * Handle order submission
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // New fields
  $firstName     = trim($_POST['first_name'] ?? '');
  $lastName      = trim($_POST['last_name'] ?? '');
  $email         = trim($_POST['email'] ?? '');
  $contactNumber = trim($_POST['contact_number'] ?? '');
  $address       = trim($_POST['address'] ?? '');
  $city          = trim($_POST['city'] ?? '');
  $postalCode    = trim($_POST['postal_code'] ?? '');
  $deliveryType  = $_POST['delivery_type'] ?? 'standard';

  // Basic validation
  if (
    empty($firstName) || empty($lastName) || empty($email) ||
    empty($contactNumber) || empty($address) ||
    empty($city) || empty($postalCode)
  ) {
    $error = "Please fill in all required fields.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Please enter a valid email address.";
  } else {

    // ---------------- CALCULATION (Centralized) ----------------
    $totals = get_cart_totals($cart, $deliveryType);
    $subtotal = $totals['subtotal'];
    $shipping = $totals['shipping'];
    $tax = $totals['tax'];
    $finalTotal = $totals['finalTotal'];


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

      // customer info
      "first_name" => $firstName,
      "last_name" => $lastName,
      "email" => $email,
      "contact" => $contactNumber,
      "address" => $address,
      "city" => $city,
      "postal_code" => $postalCode,
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
        <span id="delivery-charge">$<?= number_format($shipping, 2) ?></span>
      </div>

      <div class="summary-row">
        <span>Tax (18%):</span>
        <span id="tax-amount">$<?= number_format($tax, 2) ?></span>
      </div>

      <div class="summary-row total-row">
        <span>Total:</span>
        <span id="final-total">$<?= number_format($finalTotal, 2) ?></span>
      </div>

      <button type="submit" form="checkout-form" class="checkout-btn">Place Order</button>
    </div>
  </div>

  <!-- Local data script removed in favor of AJAX -->

  <script src="../javascript/checkout.js"></script>
</section>

<?php require '../includes/footer.php'; ?>