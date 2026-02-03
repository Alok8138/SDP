<?php
/**
 * Cart Model
 */

require_once __DIR__ . '/../config/database.php';

class Cart {
    
    /**
     * Get the active cart for a user or create a new one
     */
    public static function getActiveCart($userId) {
        try {
            $db = Database::connect();
            
            // Try to find an active cart
            $sql = "SELECT * FROM sales_cart WHERE user_id = :user_id AND status = 'active' LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            $cart = $stmt->fetch();
            
            if (!$cart) {
                // Create a new cart if none exists
                $sql = "INSERT INTO sales_cart (user_id, session_id, status) VALUES (:user_id, :session_id, 'active') RETURNING entity_id";
                $stmt = $db->prepare($sql);
                $stmt->execute(['user_id' => $userId, 'session_id' => session_id()]);
                $cartId = $stmt->fetchColumn();
                
                return ['entity_id' => $cartId, 'user_id' => $userId, 'status' => 'active'];
            }
            
            return $cart;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Add or update an item in the cart
     */
    public static function addItem($cartId, $productId, $quantity) {
        try {
            $db = Database::connect();
            
            // Check if item already exists in the cart
            $sql = "SELECT * FROM sales_cart_product WHERE cart_id = :cart_id AND product_id = :product_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
            $existingItem = $stmt->fetch();
            
            if ($existingItem) {
                // Update quantity
                $sql = "UPDATE sales_cart_product SET quantity = quantity + :quantity WHERE cart_id = :cart_id AND product_id = :product_id";
            } else {
                // Insert new item
                $sql = "INSERT INTO sales_cart_product (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, :quantity)";
            }
            
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get all items in a cart with product details
     */
    public static function getItems($cartId) {
        try {
            $db = Database::connect();
            $sql = "SELECT cp.product_id as id, cp.quantity as qty, p.name, p.price, p.sku, i.image_path as image 
                    FROM sales_cart_product cp 
                    JOIN catalog_product_entity p ON cp.product_id = p.entity_id 
                    LEFT JOIN catalog_product_image i ON p.entity_id = i.product_id AND i.is_main = true 
                    WHERE cp.cart_id = :cart_id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['cart_id' => $cartId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Update the quantity of a specific item in the cart
     */
    public static function updateItemQuantity($cartId, $productId, $quantity) {
        try {
            $db = Database::connect();
            if ($quantity <= 0) {
                $sql = "DELETE FROM sales_cart_product WHERE cart_id = :cart_id AND product_id = :product_id";
                $stmt = $db->prepare($sql);
                return $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
            } else {
                $sql = "UPDATE sales_cart_product SET quantity = :quantity WHERE cart_id = :cart_id AND product_id = :product_id";
                $stmt = $db->prepare($sql);
                return $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId, 'quantity' => $quantity]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Convert cart status to converted (after order)
     */
    public static function markAsConverted($cartId) {
        try {
            $db = Database::connect();
            $sql = "UPDATE sales_cart SET status = 'converted' WHERE entity_id = :cart_id";
            $stmt = $db->prepare($sql);
            return $stmt->execute(['cart_id' => $cartId]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
