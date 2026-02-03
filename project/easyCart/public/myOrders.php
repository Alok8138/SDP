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

    <div class="orders-list">
        <?php foreach ($orders as $order): ?>
        <div class="order-card" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
          <div class="order-header" style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
            <strong>Order ID: #<?= htmlspecialchars($order['entity_id']) ?></strong>
            <span class="status" style="background: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 12px; font-size: 0.85em; font-weight: bold;"><?= htmlspecialchars($order['status']) ?></span>
          </div>
          <div class="order-details" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; font-size: 0.95em;">
            <p><strong>Date:</strong> <?= date('d M Y', strtotime($order['created_at'])) ?></p>
            <p><strong>Total Amount:</strong> <span style="font-weight: bold; color: #2e7d32;">$<?= htmlspecialchars($order['total_amount']) ?></span></p>
            <p><strong>Shipping Method:</strong> <?= htmlspecialchars(ucfirst($order['shipping_method'])) ?></p>
            <p><strong>Shipping Address:</strong><br><?= htmlspecialchars($order['first_name']) ?> <?= htmlspecialchars($order['last_name']) ?><br><?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['postal_code']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>
</section>

<?php require '../resources/views/footer.php'; ?>