<?php
/**
 * Product Model
 */

require_once __DIR__ . '/../config/database.php';

class Product {
    
    /**
     * Fetch all products with their main image
     */
    public static function getAll() {
        try { 
            $db = Database::connect();
            
            // SQL Query to join products with their main image
            $sql = "SELECT p.*, p.entity_id as id, i.image_path as image 
                    FROM catalog_product_entity p 
                    LEFT JOIN catalog_product_image i 
                    ON p.entity_id = i.product_id AND i.is_main = true";
            
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Fetch random products (for homepage)
     */
    public static function getRandomProducts($limit = 6) {
        try {
            $db = Database::connect();
            $sql = "SELECT p.*, p.entity_id as id, i.image_path as image 
                    FROM catalog_product_entity p 
                    LEFT JOIN catalog_product_image i 
                    ON p.entity_id = i.product_id AND i.is_main = true 
                    ORDER BY RANDOM() 
                    LIMIT :limit";
            
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Fetch all unique brands from the database
     */
    public static function getBrands() {
        try {
            $db = Database::connect();
            $sql = "SELECT DISTINCT brand FROM catalog_product_entity WHERE brand IS NOT NULL ORDER BY brand ASC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Fetch a single product by ID (to support PDP later)
     */
    public static function getById($id) {
        try {
            $db = Database::connect();
            
            // 1. Get main product info
            $sql = "SELECT p.*, p.entity_id as id, i.image_path as image 
                    FROM catalog_product_entity p 
                    LEFT JOIN catalog_product_image i 
                    ON p.entity_id = i.product_id AND i.is_main = true 
                    WHERE p.entity_id = :id";
            
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id]);
            $product = $stmt->fetch();

            if ($product) {
                // 2. Fetch Gallery
                $imgSql = "SELECT image_path FROM catalog_product_image WHERE product_id = :id ORDER BY sort_order ASC";
                $imgStmt = $db->prepare($imgSql);
                $imgStmt->execute(['id' => $id]);
                $product['gallery'] = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

                // 3. Fetch Features (from catalog_product_attribute)
                $attrSql = "SELECT attribute_value FROM catalog_product_attribute 
                            WHERE product_id = :id AND attribute_name = 'Feature'";
                $attrStmt = $db->prepare($attrSql);
                $attrStmt->execute(['id' => $id]);
                $product['features'] = $attrStmt->fetchAll(PDO::FETCH_COLUMN);
            }

            return $product;
            
        } catch (PDOException $e) {
            return null;
        }
    }
}
