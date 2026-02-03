<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';

$controller = new OrderController();
$data = $controller->index();

$orders = $data['orders'];

require_once '../app/helpers/functions.php';
require_once '../resources/views/header.php';
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