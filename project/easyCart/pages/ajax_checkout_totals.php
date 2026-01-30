<?php
require_once '../includes/init.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Get the delivery type from the request
$input = json_decode(file_get_contents('php://input'), true);
$deliveryType = isset($input['type']) ? $input['type'] : ($_POST['type'] ?? 'standard');

// Calculate all totals using centralized logic
$totals = get_cart_totals($_SESSION['cart'], $deliveryType);

// Validate if requested type was allowed (if it was swapped by helper)
if ($totals['shippingType'] !== $deliveryType) {
    echo json_encode(['success' => false, 'message' => 'Shipping option not allowed for this subtotal']);
    exit;
}

// Persist the choice
$_SESSION['delivery_type'] = $deliveryType;
session_write_close(); // Ensure session is saved immediately

echo json_encode([
    'success' => true,
    'subtotal' => number_format($totals['subtotal'], 2),
    'shipping' => number_format($totals['shipping'], 2),
    'tax' => number_format($totals['tax'], 2),
    'total' => number_format($totals['finalTotal'], 2)
]);
