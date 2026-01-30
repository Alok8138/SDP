<?php
require '../includes/init.php';
require '../includes/header.php';

/**
 * Load data
 */
$allProducts = require '../data/products.php';
$brands = require '../data/brands.php';

/**
 * Apply filters
 */
$products = $allProducts;

/**
 * Total before filtering (for display if needed)
 */
$productCount = count($allProducts);

/**
 * BRAND FILTER
 */
if (isset($_GET['brand']) && is_array($_GET['brand'])) {
  $selectedBrands = $_GET['brand'];

  $products = array_filter($products, function ($product) use ($selectedBrands) {
    return in_array($product['brand'], $selectedBrands);
  });
}

/**
 * PRICE FILTER
 */
if (isset($_GET['maxPrice']) && $_GET['maxPrice'] !== '') {
  $maxPrice = (int) $_GET['maxPrice'];

  $products = array_filter($products, function ($product) use ($maxPrice) {
    return $product['price'] <= $maxPrice;
  });
}

/**
 * PAGINATION SETUP
 */
$productsPerPage = 6;
$totalProducts = count($products);
$totalPages = max(1, ceil($totalProducts / $productsPerPage));

$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages));

$offset = ($currentPage - 1) * $productsPerPage;
$products = array_slice($products, $offset, $productsPerPage);
?>

<section class="plp-page">
  <div class="plp-layout">

    <!-- FILTER SIDEBAR -->
    <aside class="filters">
      <h3>Filters</h3>

      <form method="GET">

        <!-- Brand Filter -->
        <div class="filter-group">
          <h4>Brand</h4>

          <?php foreach ($brands as $brand): ?>
            <label>
              <input
                type="checkbox"
                name="brand[]"
                value="<?= htmlspecialchars($brand) ?>"
            
                <?= (isset($_GET['brand']) && in_array($brand, $_GET['brand'])) ? 'checked' : '' ?>>
              <?= htmlspecialchars($brand) ?>

            </label>
          <?php endforeach; ?>
        </div>

        <!-- Price Filter -->
        <div class=" filter-group">
          <h4>Max Price</h4>
          <input
            type="number"
            name="maxPrice"
            min="0"
            step="1"
            placeholder="Enter price"
            value="<?= isset($_GET['maxPrice']) ? htmlspecialchars($_GET['maxPrice']) : '' ?>"
          >
        </div>

        <button type="submit">Apply Filters</button>
      </form>
    </aside>

    <!-- PRODUCT LIST -->
    <section class="products">
      <h2>All Products</h2>
      <p class="product-count"><?= $totalProducts ?> products found</p>

      <?php if (empty($products)): ?>
        <p>No products found.</p>
      <?php else: ?>
        <div class="grid three">
          <?php foreach ($products as $product): ?>
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
                  <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                  <input type="hidden" name="quantity" value="1">
                  <button type="submit" class="card-cart-btn" aria-label="Quick add to cart" title="Add to Cart">
                    <img src="../images/cart.jpg" alt="Add to Cart">
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- PAGINATION LINKS -->
      <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php
            $queryParams = $_GET;
            $queryParams['page'] = $i;
            $url = '?' . http_build_query($queryParams);
            ?>
            <a href="<?= $url ?>" class="<?= ($i == $currentPage) ? 'active' : '' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>

    </section>

  </div>
</section>

<script src="../javascript/plp.js"></script>

<?php require '../includes/footer.php'; ?>