<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../helpers/functions.php';

class ProductController {
    
    /**
     * Method for getting random products for homepage (Task 2)
     */
    public function getHomeProducts() {
        return Product::getRandomProducts(6);
    }
    
    /**
     * Handle Product Listing Page (PLP)
     */
    public function index() {
        $allProducts = Product::getAll();

        // echo '<pre>';
        // print_r($allProducts);
        
        $brands = Product::getBrands();
        $categories = Category::getAll();

        $allProducts = is_array($allProducts) ? $allProducts : [];
        $brands = is_array($brands) ? $brands : [];
        $categories = is_array($categories) ? $categories : [];

        $products = $allProducts;

        // Apply Brand Filter
        if (isset($_GET['brand']) && is_array($_GET['brand'])) {
            $selectedBrands = $_GET['brand'];
            $products = array_filter($products, function ($product) use ($selectedBrands) {
                return in_array($product['brand'], $selectedBrands);
            });
        }

        // Apply Category Filter (Task 1)
        if (isset($_GET['category']) && is_array($_GET['category'])) {
            $selectedCategories = $_GET['category'];
            
            // We need to know which products belong to which categories
            $db = Database::connect();
            $placeholders = implode(',', array_fill(0, count($selectedCategories), '?'));
            $stmt = $db->prepare("SELECT product_id FROM catalog_category_products WHERE category_id IN ($placeholders)");
            $stmt->execute($selectedCategories);
            $productIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $products = array_filter($products, function ($product) use ($productIds) {
                return in_array($product['id'], $productIds);
            });
        }

        // Apply Price Filter
        if (isset($_GET['maxPrice']) && $_GET['maxPrice'] !== '') {
            $maxPrice = (int) $_GET['maxPrice'];
            $products = array_filter($products, function ($product) use ($maxPrice) {
                return $product['price'] <= $maxPrice;
            });
        }

        // Pagination Setup
        $productsPerPage = 6;
        $totalProducts = count($products);
        $totalPages = max(1, ceil($totalProducts / $productsPerPage));

        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $currentPage = max(1, min($currentPage, $totalPages));

        $offset = ($currentPage - 1) * $productsPerPage;
        $paginatedProducts = array_slice($products, $offset, $productsPerPage);

        return [
            'paginatedProducts' => $paginatedProducts,
            'brands' => $brands,
            'categories' => $categories,
            'totalProducts' => $totalProducts,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage
        ];
    }

    /**
     * Handle Product Detail Page (PDP) by slug
     *
     * @param string $slug
     * @return array
     */
    public function show(string $slug) {
        // Basic slug validation & sanitization
        $slug = trim($slug);

        if ($slug === '') {
            http_response_code(404);
            return ['error' => 'Product not found.'];
        }

        // Optionally enforce a simple slug pattern (letters, numbers, dashes)
        if (!preg_match('/^[a-zA-Z0-9\-]+$/', $slug)) {
            http_response_code(404);
            return ['error' => 'Product not found.'];
        }

        $result = Product::getBySlug($slug);

        if ($result === null) {
            http_response_code(404);
            return ['error' => 'Product not found.'];
        }

        $product = $result['product'];
        $images = $result['images'] ?? [];
        $attributes = $result['attributes'] ?? [];

        // Build slider images with proper fallbacks (no business logic in views)
        $sliderImages = $images;

        // Fallback to main image if gallery is empty
        if (empty($sliderImages) && !empty($product['image'])) {
            $sliderImages = [$product['image']];
        }

        // Fallback to placeholder if still empty
        if (empty($sliderImages)) {
            $sliderImages = ['assets/images/no-image-placeholder.png'];
        }

        // Extract feature list from attributes
        $features = [];
        foreach ($attributes as $attribute) {
            if (
                isset($attribute['attribute_name'], $attribute['attribute_value']) &&
                $attribute['attribute_name'] === 'Feature'
            ) {
                $features[] = $attribute['attribute_value'];
            }
        }

        // Current quantity in cart (if any) for this product
        $cartQuantity = 0;
        if (isset($_SESSION['cart'][$product['id']]['qty'])) {
            $cartQuantity = (int) $_SESSION['cart'][$product['id']]['qty'];
        }

        return [
            'product' => $product,
            'sliderImages' => $sliderImages,
            'features' => $features,
            'cartQuantity' => $cartQuantity,
        ];
    }

    /**
     * Handle CSV Import
     */
    public function importCSV() {
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            header("Location: index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['product_csv'])) {
            $file = $_FILES['product_csv'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                return Product::importFromCSV($file['tmp_name']);
            }
        }
        return null;
    }

    /**
     * Handle CSV Export
     */
    public function exportCSV() {
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            header("Location: index");
            exit;
        }
        Product::exportToCSV();
    }
}
