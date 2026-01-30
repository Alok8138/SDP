<?php
/**
 * Helper functions to simplify logic and avoid duplication.
 * Phase 4 & 5 Requirement: Business Logic in PHP.
 */

// Helper to get products (Phase 2)
function get_products() {
    return require '../data/products.php';
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
    switch ($type) {
        case 'standard':
            return 40;
        case 'express':
            // $80 OR 10% of subtotal (whichever is lower)
            return min(80, $subtotal * 0.10);
        case 'white_glove':
            // $150 OR 5% of subtotal (whichever is lower)
            return min(150, $subtotal * 0.05);
        case 'freight':
            // 3% of subtotal, minimum $200
            return max(200, $subtotal * 0.03); 
            // Note: PHP max() returns the highest value. 
            // "minimum $200" means the cost is AT LEAST 200.
            // So if 3% is 50, we return 200. Thus max is correct.
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

    $shipping = calculate_shipping($subtotal, $shippingType);
    
    // Tax = 18% of (Subtotal + Shipping) (Phase 4 Requirement)
    $tax = ($subtotal + $shipping) * 0.18;

    $finalTotal = $subtotal + $shipping + $tax;

    return [
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'tax' => $tax,
        'finalTotal' => $finalTotal,
        'totalItems' => $totalItems
    ];
}
