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

        return [
            'orders' => $orders
        ];
    }
}
