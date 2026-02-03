<?php
/**
 * CheckoutController.php
 */

require_once __DIR__ . '/../helpers/functions.php';

class CheckoutController {
    public function index() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: cart.php");
            exit;
        }

        $cart = $_SESSION['cart'];

        // Calculate initial subtotal
        $initialSubtotal = 0;
        foreach ($cart as $item) {
            $initialSubtotal += $item['price'] * $item['qty'];
        }

        // Validate shipping
        $savedShipping = $_SESSION['delivery_type'] ?? '';
        if (!is_shipping_allowed($initialSubtotal, $savedShipping)) {
            $savedShipping = ''; 
        }

        $totals = get_cart_totals($cart, $savedShipping);
        
        $data = [
            'cart' => $cart,
            'subtotal' => $totals['subtotal'],
            'shipping' => $totals['shipping'],
            'tax' => $totals['tax'],
            'finalTotal' => $totals['finalTotal'],
            'deliveryType' => $totals['shippingType']
        ];

        // Handle POST (Order submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->placeOrder($data['subtotal']);
            if (isset($result['error'])) {
                $data['error'] = $result['error'];
            }
        }

        return $data;
    }

    private function placeOrder($subtotal) {
        $cart = $_SESSION['cart'];
        
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
        } elseif (!is_shipping_allowed($subtotal, $deliveryType)) {
            return ['error' => "Invalid shipping option selected for your order total."];
        } else {
            $totals = get_cart_totals($cart, $deliveryType);
            
            $orders = require __DIR__ . '/../models/Order.php';
            $orders[] = [
                "id" => "#ORD" . rand(1000, 9999),
                "date" => date("d M Y"),
                "items" => count($cart),
                "subtotal" => round($totals['subtotal'], 2),
                "shipping" => round($totals['shipping'], 2),
                "tax" => round($totals['tax'], 2),
                "total" => round($totals['finalTotal'], 2),
                "status" => "Placed",
                "first_name" => $firstName,
                "last_name" => $lastName,
                "email" => $email,
                "contact" => $contactNumber,
                "address" => $address,
                "city" => $city,
                "postal_code" => $postalCode,
                "shipping_type" => $deliveryType
            ];

            $_SESSION['cart'] = [];
            $_SESSION['orders'] = $orders;
            unset($_SESSION['delivery_type']);

            header("Location: myOrders.php");
            exit;
        }
    }
}
