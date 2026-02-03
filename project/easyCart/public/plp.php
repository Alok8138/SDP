<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';

$controller = new ProductController();
$data = $controller->index();

$paginatedProducts = $data['paginatedProducts'];
$brands = $data['brands'];
$totalProducts = $data['totalProducts'];
$totalPages = $data['totalPages'];
$currentPage = $data['currentPage'];

require_once '../app/helpers/functions.php';
require_once '../resources/views/header.php';
?>

<section class="plp-page">
    <div class="plp-layout">

        <!-- Sidebar Filters -->
        <aside class="filters">
            <h3>Filters</h3>
            <form method="GET">
                <!-- Brand Filter -->
                <div class="filter-group">
                    <h4>Brand</h4>
                    <?php foreach ($brands as $brand): ?>
                        <label>
                            <input type="checkbox" name="brand[]" value="<?= htmlspecialchars($brand) ?>"
                                <?= (isset($_GET['brand']) && in_array($brand, (array)$_GET['brand'])) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($brand) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <!-- Price Filter -->
                <div class="filter-group">
                    <h4>Max Price</h4>
                    <input type="number" name="maxPrice" min="0" step="1" placeholder="Enter price"
                        value="<?= isset($_GET['maxPrice']) ? htmlspecialchars($_GET['maxPrice']) : '' ?>">
                </div>

                <button type="submit">Apply Filters</button>
            </form>
        </aside>

        <!-- Product List -->
        <section class="products">
            <h2>All Products</h2>
            <p class="product-count"><?= (int)$totalProducts ?> products found</p>

            <?php if (empty($paginatedProducts)): ?>
                <p>No products found matching your criteria.</p>
            <?php else: ?>
                <div class="grid three">
                    <?php foreach ($paginatedProducts as $product): ?>
                        <div class="card">
                            <div class="card-image-wrapper">
                                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            </div>

                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="price">$<?= htmlspecialchars($product['price']) ?></p>

                            <div class="product-actions">
                                <a href="pdp.php?id=<?= urlencode($product['id']) ?>">
                                    <button>View Product</button>
                                </a>

                                <form method="POST" action="pdp.php?id=<?= urlencode($product['id']) ?>" class="quick-add-form ajax-cart-form">
                                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="card-cart-btn" aria-label="Quick add to cart" title="Add to Cart">
                                        <img src="assets/images/cart.jpg" alt="Add to Cart">
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Pagination Links -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php
                        $queryParams = $_GET;
                        $queryParams['page'] = $i;
                        $url = '?' . http_build_query($queryParams);
                        ?>
                        <a href="<?= htmlspecialchars($url) ?>" class="<?= ($i == $currentPage) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        </section>

    </div>
</section>

<script src="assets/js/plp.js"></script>

<?php require '../resources/views/footer.php'; ?>