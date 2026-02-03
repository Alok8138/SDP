<?php
/**
 * Order Model
 */

require_once __DIR__ . '/../config/database.php';

class Order {
    
    /**
     * Create a new order and transfer items from an active cart (Transactions)
     */
    public static function convertCartToOrder($userId, $cartId, $orderData) {
        try {
            $db = Database::connect();
            $db->beginTransaction();

            // 1. Create the order record
            $sql = "INSERT INTO sales_order (user_id, total_amount, status, shipping_method) 
                    VALUES (:user_id, :total_amount, 'Placed', :shipping_method) 
                    RETURNING entity_id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'user_id'         => $userId,
                'total_amount'    => $orderData['total'] ?? $orderData['total_amount'],
                'shipping_method' => $orderData['shipping_method']
            ]);
            
            $orderId = $stmt->fetchColumn();

            // 2. Insert Shipping Address into order_address (matching schema.sql)
            $addrSql = "INSERT INTO order_address (order_id, full_name, phone, address, city, pincode) 
                        VALUES (:order_id, :full_name, :phone, :address, :city, :pincode)";
            $addrStmt = $db->prepare($addrSql);
            $addrStmt->execute([
                'order_id'  => $orderId,
                'full_name' => $orderData['first_name'] . ' ' . $orderData['last_name'],
                'phone'     => $orderData['phone'],
                'address'   => $orderData['address'],
                'city'      => $orderData['city'],
                'pincode'   => $orderData['postal_code']
            ]);

            // 3. Fetch cart items to snapshot them into order products
            $cartItemsSql = "SELECT cp.*, p.price FROM sales_cart_product cp 
                             JOIN catalog_product_entity p ON cp.product_id = p.entity_id 
                             WHERE cp.cart_id = :cart_id";
            $cartItemsStmt = $db->prepare($cartItemsSql);
            $cartItemsStmt->execute(['cart_id' => $cartId]);
            $items = $cartItemsStmt->fetchAll();

            // 4. Insert each item into sales_order_product
            $insertItemSql = "INSERT INTO sales_order_product (order_id, product_id, quantity, price) 
                              VALUES (:order_id, :product_id, :quantity, :price)";
            $insertItemStmt = $db->prepare($insertItemSql);

            foreach ($items as $item) {
                $insertItemStmt->execute([
                    'order_id'   => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price']
                ]);
            }

            // 5. Update cart status to converted
            $updateCartSql = "UPDATE sales_cart SET status = 'converted' WHERE entity_id = :cart_id";
            $updateCartStmt = $db->prepare($updateCartSql);
            $updateCartStmt->execute(['cart_id' => $cartId]);

            $db->commit();
            return $orderId;

        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return false;
        }
    }

    /**
     * Fetch orders for a specific user
     */
    public static function getByUserId($userId) {
        try {
            $db = Database::connect();
            $sql = "SELECT o.*, 
                           a.full_name as first_name, '' as last_name, 
                           a.address, a.city, a.pincode as postal_code
                    FROM sales_order o
                    LEFT JOIN order_address a ON o.entity_id = a.order_id
                    WHERE o.user_id = :user_id 
                    ORDER BY o.created_at DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
