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
  <style>
    .orders-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; }
    .orders-table th, .orders-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
    .orders-table th { background-color: #f8f9fa; font-weight: 600; color: #333; }
    .status-badge { padding: 4px 12px; border-radius: 12px; font-size: 0.85em; font-weight: bold; background: #e3f2fd; color: #1976d2; }
    .view-details-btn { background: #007bff; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; transition: background 0.3s; }
    .view-details-btn:hover { background: #0056b3; }
    .details-row { display: none; background-color: #fafbfc; }
    .details-content { padding: 20px; border-top: 1px solid #eee; }
    .details-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    .details-table th { background: transparent; border-bottom: 2px solid #eee; padding: 10px 5px; }
    .details-table td { padding: 10px 5px; }
    .price-breakdown { float: right; width: 300px; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #eee; }
    .price-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
    .price-row.total { font-weight: bold; font-size: 1.1em; border-top: 1px solid #eee; pt: 10px; margin-top: 10px; color: #2e7d32; }
    .clearfix::after { content: ""; clear: both; display: table; }
  </style>

  <h2>My Orders</h2>

  <?php if (empty($orders)): ?>
    <div style="text-align: center; padding: 50px 0;">
      <p>You have no orders yet.</p>
      <a href="plp">
        <button style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Shop Now</button>
      </a>
    </div>
  <?php else: ?>

    <table class="orders-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Order Date</th>
          <th>Shipping Type</th>
          <th>Final Amount</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td>#<?= htmlspecialchars($order['entity_id']) ?></td>
            <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
            <td><?= htmlspecialchars(ucfirst($order['shipping_type'] ?? $order['shipping_method'] ?? 'Standard')) ?></td>
            <td style="font-weight: bold; color: #2e7d32;">$<?= number_format($order['total_amount'], 2) ?></td>
            <td><span class="status-badge"><?= htmlspecialchars($order['status']) ?></span></td>
            <td>
              <button class="view-details-btn" onclick="toggleDetails('details-<?= $order['entity_id'] ?>')">View Details</button>
            </td>
          </tr>
          <tr id="details-<?= $order['entity_id'] ?>" class="details-row">
            <td colspan="6">
              <div class="details-content clearfix">
                <h4>Order Items</h4>
                <table class="details-table">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Quantity</th>
                      <th style="text-align: right;">Price</th>
                      <th style="text-align: right;">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $calculatedSubtotal = 0;
                    foreach ($order['items'] as $item): 
                      $itemTotal = $item['price'] * $item['quantity'];
                      $calculatedSubtotal += $itemTotal;
                    ?>
                      <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td style="text-align: right;">$<?= number_format($item['price'], 2) ?></td>
                        <td style="text-align: right;">$<?= number_format($itemTotal, 2) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>

                <div class="price-breakdown">
                  <div class="price-row">
                    <span>Subtotal:</span>
                    <span>$<?= number_format($order['subtotal_amount'] ?? $calculatedSubtotal, 2) ?></span>
                  </div>
                  <div class="price-row">
                    <span>Shipping Charge (<?= htmlspecialchars($order['shipping_type'] ?? $order['shipping_method'] ?? 'Standard') ?>):</span>
                    <span>$<?= number_format($order['shipping_amount'] ?? ($order['total_amount'] - $calculatedSubtotal - ($calculatedSubtotal * 0.1)), 2) ?></span> <!-- Smart fallback for shipping -->
                  </div>
                  <div class="price-row">
                    <span>Tax (<?= htmlspecialchars($order['tax_rate'] ?? 10) ?>%):</span>
                    <span>$<?= number_format($order['tax_amount'] ?? ($calculatedSubtotal * 0.1), 2) ?></span> <!-- Smart fallback for tax -->
                  </div>
                  <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                  <div class="price-row" style="color: #d32f2f;">
                    <span>Discount:</span>
                    <span>-$<?= number_format($order['discount_amount'], 2) ?></span>
                  </div>
                  <?php endif; ?>
                  <div class="price-row total">
                    <span>Final Total:</span>
                    <span>$<?= number_format($order['total_amount'], 2) ?></span>
                  </div>
                </div>

                <div style="margin-top: 20px;">
                  <strong>Shipping Address:</strong><br>
                  <?= htmlspecialchars($order['first_name']) ?> <?= htmlspecialchars($order['last_name']) ?><br>
                  <?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city'] ?? '') ?>, <?= htmlspecialchars($order['postal_code'] ?? '') ?>
                </div>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <script>
      function toggleDetails(id) {
        const row = document.getElementById(id);
        const allRows = document.querySelectorAll('.details-row');
        
        // Optional: Close other open rows
        // allRows.forEach(r => { if(r.id !== id) r.style.display = 'none'; });

        if (row.style.display === 'table-row') {
          row.style.display = 'none';
        } else {
          row.style.display = 'table-row';
        }
      }
    </script>

  <?php endif; ?>
</section>

<?php require '../resources/views/footer.php'; ?>