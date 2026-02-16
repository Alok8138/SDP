<?php

/**
 * Product Model
 */

require_once __DIR__ . '/../config/database.php';

class Product
{

    /**
     * Fetch all products with their main image
     */
    public static function getAll()
    {
        try {
            $db = Database::connect();

            // SQL Query to join products with their main image
            $sql = "SELECT p.*, p.entity_id as id, i.image_path as image 
                    FROM catalog_product_entity p 
                    LEFT JOIN catalog_product_image i 
                    ON p.entity_id = i.product_id AND i.is_main = true";
            // Att return by above query
            // {
            //   "entity_id": 1,
            //   "id": 1,
            //   "sku": "sku-001",
            //   "name": "Wireless Headphones",
            //   "price": "99.00",
            //   "old_price": "129.00",
            //   "description": "Premium wireless headphones...",
            //   "brand": "Sony",
            //   "delivery_type": "Express",
            //   "created_at": "2026-02-01 10:20:30",
            //   "image": "assets/images/headphone.jpg"
            // }


            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Fetch random products (for homepage)
     */
    public static function getRandomProducts($limit = 6)
    {
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
    public static function getBrands()
    {
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
    public static function getById($id)
    {
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

    /**
     * Fetch a single product by slug with related images and attributes
     *
     * @param string $slug
     * @return array|null
     */
    public static function getBySlug(string $slug): ?array
    {
        try {
            $db = Database::connect();

            // 1. Get main product info by slug
            $sql = "SELECT p.*, p.entity_id as id
                    FROM catalog_product_entity p
                    WHERE p.slug = :slug
                    LIMIT 1";

            $stmt = $db->prepare($sql);
            $stmt->execute(['slug' => $slug]);
            $product = $stmt->fetch();

            if (!$product) {
                return null;
            }

            $productId = (int) $product['id'];

            // 2. Fetch all images for the product (gallery)
            $imgSql = "SELECT image_path 
                       FROM catalog_product_image 
                       WHERE product_id = :id 
                       ORDER BY sort_order ASC";
            $imgStmt = $db->prepare($imgSql);
            $imgStmt->execute(['id' => $productId]);
            $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

            // 3. Fetch all attributes for the product
            $attrSql = "SELECT attribute_name, attribute_value 
                        FROM catalog_product_attribute 
                        WHERE product_id = :id";
            $attrStmt = $db->prepare($attrSql);
            $attrStmt->execute(['id' => $productId]);
            $attributes = $attrStmt->fetchAll();

            return [
                'product' => $product,
                'images' => $images,
                'attributes' => $attributes,
            ];
        } catch (PDOException $e) {
            // Do not leak internal DB errors
            return null;
        }
    }
    /**
     * Get total count of products
     */
    public static function getCount() {
        try {
            $db = Database::connect();
            return $db->query("SELECT COUNT(*) FROM catalog_product_entity")->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Import products from CSV
     */
    public static function importFromCSV($filePath) {
        $summary = ['total' => 0, 'inserted' => 0, 'skipped' => 0];
        if (!file_exists($filePath) || !($handle = fopen($filePath, "r"))) {
            return $summary;
        }

        $header = fgetcsv($handle); // Read header
        if (!$header) return $summary;

        // Map header to indices
        $cols = array_flip($header);
        $db = Database::connect();

        while (($row = fgetcsv($handle)) !== FALSE) {
            $summary['total']++;
            
            try {
                $name = $row[$cols['name']] ?? '';
                $price = $row[$cols['price']] ?? 0;
                $desc = $row[$cols['description']] ?? '';
                $qty = $row[$cols['quantity']] ?? 0;
                $catId = $row[$cols['category_id']] ?? null;
                $brandId = $row[$cols['brand_id']] ?? '';
                $status = $row[$cols['status']] ?? 'Active';

                if (empty($name) || !is_numeric($price)) {
                    $summary['skipped']++;
                    continue;
                }

                // Generate SKU from name if not present (simple slug)
                $sku = 'sku-' . strtolower(preg_replace('/[^A-Za-z0-9]/', '-', $name)) . '-' . time() . rand(10, 99);

                $db->beginTransaction();

                // Check for duplicates by name (or SKU if we had one)
                $stmt = $db->prepare("SELECT entity_id FROM catalog_product_entity WHERE name = :name LIMIT 1");
                $stmt->execute(['name' => $name]);
                if ($stmt->fetch()) {
                    $db->rollBack();
                    $summary['skipped']++;
                    continue;
                }

                // Insert into catalog_product_entity
                $sql = "INSERT INTO catalog_product_entity (sku, name, price, description, brand) 
                        VALUES (:sku, :name, :price, :desc, :brand) RETURNING entity_id";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'sku' => $sku,
                    'name' => $name,
                    'price' => $price,
                    'desc' => $desc,
                    'brand' => $brandId
                ]);
                $productId = $stmt->fetchColumn();

                // Insert Category Mapping
                if ($catId) {
                    $stmt = $db->prepare("INSERT INTO catalog_category_products (category_id, product_id) VALUES (:cat_id, :prod_id)");
                    $stmt->execute(['cat_id' => $catId, 'prod_id' => $productId]);
                }

                // Insert Attributes (Quantity, Status)
                $attrSql = "INSERT INTO catalog_product_attribute (product_id, attribute_name, attribute_value) VALUES (?, ?, ?)";
                $attrStmt = $db->prepare($attrSql);
                $attrStmt->execute([$productId, 'Quantity', $qty]);
                $attrStmt->execute([$productId, 'Status', $status]);

                $db->commit();
                $summary['inserted']++;

            } catch (Exception $e) {
                if ($db->inTransaction()) $db->rollBack();
                $summary['skipped']++;
            }
        }
        fclose($handle);
        return $summary;
    }

    /**
     * Export products to CSV
     */
    public static function exportToCSV() {
        $db = Database::connect();
        $sql = "SELECT p.name, p.description, p.price, 
                       COALESCE(aq.attribute_value, '0') as quantity, 
                       c.category_id, p.brand as brand_id, 
                       COALESCE(at_status.attribute_value, 'Active') as status
                FROM catalog_product_entity p
                LEFT JOIN catalog_category_products c ON p.entity_id = c.product_id
                LEFT JOIN catalog_product_attribute aq ON p.entity_id = aq.product_id AND aq.attribute_name = 'Quantity'
                LEFT JOIN catalog_product_attribute at_status ON p.entity_id = at_status.product_id AND at_status.attribute_name = 'Status'";
        
        $stmt = $db->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="products_export_' . date('Ymd_His') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['name', 'description', 'price', 'quantity', 'category_id', 'brand_id', 'status']);
        
        foreach ($products as $product) {
            fputcsv($output, $product);
        }
        fclose($output);
        exit;
    }
}
