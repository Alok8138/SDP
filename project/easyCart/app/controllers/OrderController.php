<?php
/**
 * OrderController.php
 */

require_once __DIR__ . '/../models/Order.php';

class OrderController {
    public function index() {
        $staticOrders = require __DIR__ . '/../models/Order.php';
        $sessionOrders = $_SESSION['orders'] ?? [];

        return [
            'orders' => array_merge($staticOrders, $sessionOrders)
        ];
    }
}
