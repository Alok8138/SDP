<?php

require '../../app/config/database.php';

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


require_once '../../app/helpers/functions.php';

// ... (code for parsing ID/Action is fine)

// Recalculate Totals using centralized logic
// Default to standard shipping for estimation or just 0 if we want to hide it.
// However, since we plan to hide Tax/Total on Cart page, these values might be unused,
// but let's return them consistent with Phase 4 rules mechanism.
$totals = get_cart_totals($_SESSION['cart'], 'standard'); 

// Calculate specific item total for the UI update
$itemTotal = 0;
foreach ($_SESSION['cart'] as $item) {
    if ($item['id'] === $productId) {
        $itemTotal = $item['price'] * $item['qty'];
        break;
    }
}

echo json_encode([
    'success' => true,
    'isEmpty' => empty($_SESSION['cart']),
    'itemTotal' => number_format($itemTotal, 2),
    'subtotal' => number_format($totals['subtotal'], 2),
    'tax' => number_format($totals['tax'], 2), 
    'grandTotal' => number_format($totals['finalTotal'], 2),
    'cartCount' => $totals['totalItems']
]);
