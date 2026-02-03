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
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

$items = Cart::getItems($cartRecord['entity_id']);

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Get the delivery type from the request
$input = json_decode(file_get_contents('php://input'), true);
$deliveryType = isset($input['type']) ? $input['type'] : ($_POST['type'] ?? 'standard');

// Transform for get_cart_totals
$cartForTotals = [];
foreach ($items as $item) {
    $cartForTotals[] = ['price' => $item['price'], 'qty' => $item['qty']];
}

// Calculate all totals using centralized logic
$totals = get_cart_totals($cartForTotals, $deliveryType);

// Validate if requested type was allowed (if it was swapped by helper)
if ($totals['shippingType'] !== $deliveryType) {
    echo json_encode(['success' => false, 'message' => 'Shipping option not allowed for this subtotal']);
    exit;
}

// Persist the choice
$_SESSION['delivery_type'] = $deliveryType;

echo json_encode([
    'success' => true,
    'subtotal' => number_format($totals['subtotal'], 2),
    'shipping' => number_format($totals['shipping'], 2),
    'tax' => number_format($totals['tax'], 2),
    'total' => number_format($totals['finalTotal'], 2)
]);
