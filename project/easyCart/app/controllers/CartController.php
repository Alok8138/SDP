<?php
/**
 * CartController.php
 */

require_once __DIR__ . '/../helpers/functions.php';

class CartController {
    public function showCart() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $cart = $_SESSION['cart'];
        $totals = get_cart_totals($cart);

        return [
            'cart' => $cart,
            'subtotal' => $totals['subtotal']
        ];
    }
}
