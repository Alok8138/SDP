<?php
session_start();

// Validates request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Load products
$products = require '../data/products.php';

// Get JSON input or Form data
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit;
}

// Find product
$product = null;
foreach ($products as $p) {
    if ($p['id'] === $productId) {
        $product = $p;
        break;
    }
}

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

// Initialize cart if needed
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add/Update cart
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

// Calculate total items
$totalItems = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalItems += $item['qty'];
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true, 
    'message' => 'Item added to cart',
    'cartCount' => $totalItems
]);
