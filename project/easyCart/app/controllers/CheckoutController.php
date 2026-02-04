<?php
/**
 * CheckoutController.php
 */

require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../helpers/auth_helper.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';

class CheckoutController {
    
    public function index() {
        // Restricted to logged-in users only
        requireLogin();

        $userId = $_SESSION['user_id'];
        $cartRecord = Cart::getActiveCart($userId);
        $items = $cartRecord ? Cart::getItems($cartRecord['entity_id']) : [];

        if (empty($items)) {
            header("Location: cart");
            exit;
        }

        // Calculate initial subtotal from DB items
        $initialSubtotal = 0;
        foreach ($items as $item) {
            $initialSubtotal += $item['price'] * $item['qty'];
        }

        // Validate shipping
        $savedShipping = $_SESSION['delivery_type'] ?? '';
        if (!is_shipping_allowed($initialSubtotal, $savedShipping)) {
            $savedShipping = ''; 
        }

        // Use standard get_cart_totals (adapted for DB item property 'quantity')
        $totals = $this->calculateTotals($items, $savedShipping);
        
        $data = [
            'cart' => $items,
            'subtotal' => $totals['subtotal'],
            'shipping' => $totals['shipping'],
            'tax' => $totals['tax'],
            'finalTotal' => $totals['finalTotal'],
            'deliveryType' => $totals['shippingType']
        ];

        // Handle POST (Order submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->placeOrder($userId, $cartRecord['entity_id'], $data);
            if (isset($result['error'])) {
                $data['error'] = $result['error'];
            }
        }

        return $data;
    }

    /**
     * Helper to bridge DB items to the shipping calculation logic
     */
    private function calculateTotals($items, $shippingType) {
        $cartForTotals = [];
        foreach ($items as $item) {
            $cartForTotals[] = [
                'price' => $item['price'],
                'qty' => $item['qty']
            ];
        }
        return get_cart_totals($cartForTotals, $shippingType);
    }

    /**
     * Handle the transition from persistent Cart to Order via Transaction
     */
    private function placeOrder($userId, $cartId, $totalsData) {
        $firstName     = trim($_POST['first_name'] ?? '');
        $lastName      = trim($_POST['last_name'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $contactNumber = trim($_POST['contact_number'] ?? '');
        $address       = trim($_POST['address'] ?? '');
        $city          = trim($_POST['city'] ?? '');
        $postalCode    = trim($_POST['postal_code'] ?? '');
        $deliveryType  = $_POST['delivery_type'] ?? 'standard';

        if (empty($firstName) || empty($lastName) || empty($email) ||
            empty($contactNumber) || empty($address) ||
            empty($city) || empty($postalCode)) {
            return ['error' => "Please fill in all required fields."];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => "Please enter a valid email address."];
        } elseif (empty($deliveryType)) {
            return ['error' => "Please select a shipping method."];
        } elseif (!is_shipping_allowed($totalsData['subtotal'], $deliveryType)) {
            return ['error' => "Invalid shipping option selected for your order total."];
        } else {
            // Precise totals for selected delivery type
            $cartItems = Cart::getItems($cartId);
            $totals = $this->calculateTotals($cartItems, $deliveryType);
            
            $orderData = [
                'subtotal'        => $totals['subtotal'],
                'tax'             => $totals['tax'],
                'tax_rate'        => $totals['tax_rate'],
                'shipping_cost'   => $totals['shipping'],
                'total'           => $totals['finalTotal'],
                'first_name'      => $firstName,
                'last_name'       => $lastName,
                'email'           => $email,
                'phone'           => $contactNumber,
                'address'         => $address,
                'city'            => $city,
                'postal_code'     => $postalCode,
                'shipping_method' => $deliveryType
            ];

            // Use the transaction-based conversion in the Order model
            if (Order::convertCartToOrder($userId, $cartId, $orderData)) {
                // Clear the temporary shipping selection
                unset($_SESSION['delivery_type']);

                header("Location: orders?order=success");
                exit;
            } else {
                return ['error' => "Could not place order. Transaction failed."];
            }
        }
    }
}
