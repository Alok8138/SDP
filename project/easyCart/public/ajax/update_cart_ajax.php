<?php
require_once '../../app/config/database.php';
require_once '../../app/helpers/functions.php';
require_once '../../app/helpers/auth_helper.php';
require_once '../../app/models/Cart.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$cartRecord = Cart::getActiveCart($userId);

if (!$cartRecord) {
    echo json_encode(['success' => false, 'message' => 'No active cart found']);
    exit;
}

$cartId = $cartRecord['entity_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';
$productId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Invalid Product ID']);
    exit;
}

if ($action === 'remove') {
    Cart::updateItemQuantity($cartId, $productId, 0);
} elseif ($action === 'update') {
    Cart::updateItemQuantity($cartId, $productId, max(1, $qty));
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Action']);
    exit;
}

$items = Cart::getItems($cartId);

// Transform for get_cart_totals
$cartForTotals = [];
$itemTotal = 0;
foreach ($items as $item) {
    $cartForTotals[] = ['price' => $item['price'], 'qty' => $item['qty']];
    if ($item['id'] == $productId) {
        $itemTotal = $item['price'] * $item['qty'];
    }
}

$totals = get_cart_totals($cartForTotals, 'standard'); 

echo json_encode([
    'success' => true,
    'isEmpty' => empty($items),
    'itemTotal' => number_format($itemTotal, 2),
    'subtotal' => number_format($totals['subtotal'], 2),
    'tax' => number_format($totals['tax'], 2), 
    'grandTotal' => number_format($totals['finalTotal'], 2),
    'cartCount' => $totals['totalItems']
]);
