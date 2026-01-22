<?php
require '../includes/init.php';
require '../includes/header.php';

/**
 * Load orders
 * Priority:
 * 1. Session orders (after checkout)
 * 2. Default static orders
 */
if (isset($_SESSION['orders']) && !empty($_SESSION['orders'])) {
  $orders = $_SESSION['orders'];
} else {
  $orders = require '../data/orders.php';
}
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
            <td><?= $order['id'] ?></td>
            <td><?= $order['date'] ?></td>
            <td><?= $order['items'] ?></td>
            <td>$<?= $order['total'] ?></td>
            <td><?= $order['status'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  <?php endif; ?>
</section>

<?php require '../includes/footer.php'; ?>