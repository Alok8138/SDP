<?php
/**
 * Category Model
 */

require_once __DIR__ . '/../config/database.php';

class Category {
    
    /**
     * Fetch all categories from the database
     */
    public static function getAll() {
        try {
            $db = Database::connect();
            // Fetch categories that have products associated with them
            $sql = "SELECT DISTINCT c.* FROM catalog_category_entity c 
                    JOIN catalog_category_products cp ON c.entity_id = cp.category_id 
                    ORDER BY c.name ASC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}