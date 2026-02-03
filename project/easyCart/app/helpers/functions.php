<?php
/**
 * Helper functions to simplify logic and avoid duplication.
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';

// Helper to get products
function get_products() {
    return Product::getAll();
}

/**
 * Add item to cart
 * Handles Phase 8 functionality: Update in Database if user is logged in
 */
function add_to_cart($productId, $quantity) {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $cartRecord = Cart::getActiveCart($userId);
        if ($cartRecord) {
            return Cart::addItem($cartRecord['entity_id'], $productId, $quantity);
        }
    }
    // Note: Per requirements, guests are redirected to login for cart access, 
    // so we don't handle guest sessions here to keep it strictly DB-backed.
    return false;
}

/**
 * Calculate Shipping Cost based on rules
 */
function calculate_shipping($subtotal, $type) {
    if (empty($type) || $type === 'none') {
        return 0;
    }
    switch ($type) {
        case 'standard':
            return 40;
        case 'express':
            return max($subtotal * 0.10, 80);
        case 'white_glove':
            return max($subtotal * 0.05, 150);
        case 'freight':
            return max($subtotal * 0.03, 200); 
        default:
            return 40;
    }
}

/**
 * Calculate all totals (Subtotal, Shipping, Tax, Final)
 */
function get_cart_totals($cart, $shippingType = 'standard') {
    $subtotal = 0;
    $totalItems = 0;

    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['qty'];
        $totalItems += $item['qty'];
    }

    if (!empty($shippingType) && !is_shipping_allowed($subtotal, $shippingType)) {
        $shippingType = ''; 
    }

    $shipping = calculate_shipping($subtotal, $shippingType);
    $tax = $subtotal * 0.10;
    $finalTotal = $subtotal + $shipping + $tax;

    return [
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'tax' => $tax,
        'finalTotal' => $finalTotal,
        'totalItems' => $totalItems,
        'shippingType' => $shippingType
    ];
}

/**
 * Validate allowed shipping methods based on subtotal
 */
function is_shipping_allowed($subtotal, $type) {
    if ($subtotal <= 300) {
        return in_array($type, ['standard', 'express']);
    } else {
        return in_array($type, ['freight', 'white_glove']);
    }
}
