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
     * Handle Product Detail Page (PDP)
     */
    public function show() {
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            return ['error' => 'Product ID is missing.'];
        }

        $productId = (int) $_GET['id'];
        $product = Product::getById($productId);

        if (!$product) {
            return ['error' => 'Product not found.'];
        }

        // Handle POST (Add to Cart fallback)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $qty = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
            $id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : $product['id'];

            if (add_to_cart($id, $qty)) {
                header("Location: cart.php");
                exit;
            }
        }

        return ['product' => $product];
    }
}
