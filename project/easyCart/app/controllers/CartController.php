<?php
/**
 * CartController.php
 */

require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../models/Cart.php';

class CartController {
    
    /**
     * Display the cart page
     */
    public function showCart() {
        requireLogin();
        
        $userId = $_SESSION['user_id'];
        $cartRecord = Cart::getActiveCart($userId);
        
        if (!$cartRecord) {
            return ['cart' => [], 'subtotal' => 0];
        }

        $items = Cart::getItems($cartRecord['entity_id']);
        
        // Calculate subtotal from DB items
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        return [
            'cart' => $items,
            'subtotal' => $subtotal
        ];
    }

    /**
     * Handle Add to Cart action
     */
    public function addToCart() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login?redirect=cart');
            exit;
        }
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        if ($productId > 0) {
            $userId = $_SESSION['user_id'];
            $cartRecord = Cart::getActiveCart($userId);
            
            if ($cartRecord && Cart::addItem($cartRecord['entity_id'], $productId, $quantity)) {
                return ['success' => true];
            }
        }
        
        return ['success' => false];
    }
}
