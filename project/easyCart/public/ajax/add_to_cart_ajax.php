<?php
require_once '../../app/config/database.php';
require_once '../../app/helpers/functions.php';
require_once '../../app/helpers/auth_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once '../../app/controllers/CartController.php';
$controller = new CartController();
$result = $controller->addToCart();

if (isset($result['success']) && $result['success']) {
    // Re-calculating total items for the response
    $userId = $_SESSION['user_id'];
    $cartRecord = Cart::getActiveCart($userId);
    $items = Cart::getItems($cartRecord['entity_id']);
    $totalItems = 0;
    foreach ($items as $item) {
        $totalItems += $item['qty'];
    }

    echo json_encode([
        'success' => true, 
        'message' => 'Item added to cart',
        'cartCount' => $totalItems
    ]);
    exit;
}

// If we reach here, it might be a failure or CartController already exited with a header redirect
echo json_encode(['success' => false, 'message' => 'Failed to add item to cart']);
exit;

// This section is now handled by the block above
exit;
