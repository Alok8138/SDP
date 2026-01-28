<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$productId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Invalid Product ID']);
    exit;
}

// Perform Action
if ($action === 'remove') {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
} elseif ($action === 'update') {
    if (isset($_SESSION['cart'][$productId])) {
        // Enforce min qty 1
        $newQty = max(1, $qty);
        $_SESSION['cart'][$productId]['qty'] = $newQty;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Action']);
    exit;
}

// Recalculate Totals
$subtotal = 0;
$totalItems = 0;
$itemTotal = 0;

foreach ($_SESSION['cart'] as $item) {
    $itemSub = $item['price'] * $item['qty'];
    $subtotal += $itemSub;
    $totalItems += $item['qty'];
    
    // Get total for the specific updated item
    if ($item['id'] === $productId) {
        $itemTotal = $itemSub;
    }
}

$tax = $subtotal * 0.1;
$grandTotal = $subtotal * 1.1;

echo json_encode([
    'success' => true,
    'isEmpty' => empty($_SESSION['cart']),
    'itemTotal' => number_format($itemTotal, 2),
    'subtotal' => number_format($subtotal, 2),
    'tax' => number_format($tax, 2),
    'grandTotal' => number_format($grandTotal, 2),
    'cartCount' => $totalItems
]);
