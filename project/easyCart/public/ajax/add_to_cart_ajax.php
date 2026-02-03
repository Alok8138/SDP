<?php
require '../../app/config/database.php';

// Validates request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}


// Load helper functions
require_once '../../app/helpers/functions.php';

// Get JSON input or Form data
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit;
}

// Add to cart using helper
$success = add_to_cart($productId, $quantity);

if (!$success) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

// Calculate total items (simplified)
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
