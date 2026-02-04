<?php
/**
 * OrderController.php
 */

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../helpers/auth_helper.php';

class OrderController {
    
    /**
     * Fetch orders for the logged-in user
     */
    public function index() {
        requireLogin();
        
        $userId = $_SESSION['user_id'];
        $orders = Order::getByUserId($userId);

        // Fetch items for each order
        foreach ($orders as &$order) {
            $order['items'] = Order::getItemsByOrderId($order['entity_id']);
        }

        return [
            'orders' => $orders
        ];
    }

    /**
     * Fetch dashboard statistics and chart data (Task 9 Upgrade)
     */
    public function dashboard() {
        requireLogin();
        
        $userId = $_SESSION['user_id'];
        
        $stats = Order::getUserDashboardStats($userId);
        $history = Order::getOrderHistoryForChart($userId);

        // Process data for Chart.js
        $labels = [];
        $values = [];
        foreach ($history as $row) {
            // Format labels as 'Jan 01'
            $labels[] = date('M d', strtotime($row['order_date']));
            $values[] = (float)$row['daily_total'];
        }

        return [
            'total_orders'    => $stats['total_orders'],
            'total_spent'     => $stats['total_spent'] ?? 0,
            'avg_order_value' => $stats['avg_order_value'] ?? 0,
            'chart_labels'    => $labels,
            'chart_values'    => $values
        ];
    }
}
