<?php
require '../app/config/database.php';
require '../app/helpers/functions.php';
require '../resources/views/header.php';

/**
 * Load ALL orders
 * - Static orders (default)
 * - Session orders (after checkout)
 */

// Load static orders
$staticOrders = require '../app/models/Order.php';

// Load session orders (if any)
$sessionOrders = $_SESSION['orders'] ?? [];

// Merge both (static first, session last)
$orders = array_merge($staticOrders, $sessionOrders);
?>

<section class="container orders-page">
  <h2>My Orders</h2>

  <?php if (empty($orders)): ?>
    <p>You have no orders yet.</p>
    <a href="plp.php">
      <button>Shop Now</button>
    </a>
  <?php else: ?>

    <table class="orders-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Date</th>
          <th>Items</th>
          <th>Total</th>
          <th>Status</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td><?= htmlspecialchars($order['id']) ?></td>
            <td><?= htmlspecialchars($order['date']) ?></td>
            <td><?= htmlspecialchars($order['items']) ?></td>
            <td>$<?= htmlspecialchars($order['total']) ?></td>
            <td><?= htmlspecialchars($order['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  <?php endif; ?>
</section>

<?php require '../resources/views/footer.php'; ?>