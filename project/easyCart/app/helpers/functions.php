<?php
/**
 * Helper functions to simplify logic and avoid duplication.
 * Phase 4 & 5 Requirement: Business Logic in PHP.
 */

// Helper to get products (Phase 2)
function get_products() {
    return require __DIR__ . '/../models/Product.php';
}

/**
 * Add item to cart
 * Handles Phase 4 functionality: Update quantity in session
 */
function add_to_cart($productId, $quantity) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Load products to verify ID and get details
    $products = get_products();
    $product = null;
    foreach ($products as $p) {
        if ($p['id'] === $productId) {
            $product = $p;
            break;
        }
    }

    if (!$product) {
        return false;
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['qty'] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'qty' => $quantity
        ];
    }
    return true;
}

/**
 * Calculate Shipping Cost based on rules (Phase 4)
 */
function calculate_shipping($subtotal, $type) {
    if (empty($type) || $type === 'none') {
        return 0;
    }
    switch ($type) {
        case 'standard':
            return 40;
        case 'express':
            // 10% of subtotal, minimum $80
            return max($subtotal * 0.10, 80);
        case 'white_glove':
            // 5% of subtotal, minimum $150
            return max($subtotal * 0.05, 150);
        case 'freight':
            // 3% of subtotal, minimum $200
            return max($subtotal * 0.03, 200); 
        default:
            return 40;
    }
}

/**
 * Calculate all totals (Subtotal, Shipping, Tax, Final)
 * Centralizes duplicate math from Cart/Checkout/AJAX
 */
function get_cart_totals($cart, $shippingType = 'standard') {
    $subtotal = 0;
    $totalItems = 0;

    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['qty'];
        $totalItems += $item['qty'];
    }

    // Default shipping type if not allowed
    if (!empty($shippingType) && !is_shipping_allowed($subtotal, $shippingType)) {
        $shippingType = ''; // Reset if invalid
    }

    $shipping = calculate_shipping($subtotal, $shippingType);
    
    // Tax = 10% of Subtotal
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
function is_shipping_allowed($subtotal, $type)
{
    if ($subtotal <= 300) {
        // Only Standard & Express allowed
        return in_array($type, ['standard', 'express']);
    } else {
        // Only Freight & White Glove allowed
        return in_array($type, ['freight', 'white_glove']);
    }
}
