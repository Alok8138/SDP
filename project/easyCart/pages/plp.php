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
 * product count 
 */
$productCount = count($allProducts);


// Brand filter
if (isset($_GET['brand']) && is_array($_GET['brand'])) {
  $selectedBrands = $_GET['brand'];

  $products = array_filter($products, function ($product) use ($selectedBrands) {
    return in_array($product['brand'], $selectedBrands);
  });
}

// Price filter
if (isset($_GET['maxPrice']) && $_GET['maxPrice'] !== '') {
  $maxPrice = (int) $_GET['maxPrice'];

  $products = array_filter($products, function ($product) use ($maxPrice) {
    return $product['price'] <= $maxPrice;
  });
}
?>

<section class="plp-page">
  <div class="plp-layout">

    <!-- LEFT FILTER SIDEBAR -->
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
                value="<?= $brand ?>"
              <?= $brand ?>>
            </label>
          <?php endforeach; ?>
        </div>

        <!--name="brand[]" tell the browser:Multiple values can be selected, send them as an array -->

        <!-- Price Filter -->
        <div class="filter-group">
          <h4>Max Price</h4>
          <input
            type="number"
            name="maxPrice"
            id="maxPriceInput"
            min="0"
            step="1"
            placeholder="Enter price"
            value="<?= isset($_GET['maxPrice']) ? htmlspecialchars($_GET['maxPrice']) : '' ?>" />
        </div>

        <button type="submit">Apply Filters</button>
      </form>
    </aside>

    <!-- PRODUCT LIST -->
    <section class="products">
      <h2>All Products</h2>
      <p class="product-count"><?= count($products) ?> of <?= $productCount ?> products found</p>
      <?php if (empty($products)): ?>
        <p>No products found.</p>
      <?php else: ?>
        <div class="grid three">
          <?php foreach ($products as $product): ?>
            <div class="card">
              <div class="card-image-wrapper">
                <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
              </div>
              <h3><?= $product['name'] ?></h3>
              <p class="price">$<?= $product['price'] ?></p>
              <div class="product-actions">
                <a href="pdp.php?id=<?= $product['id'] ?>">
                  <button>View Product</button>
                </a>
                <form method="POST" action="pdp.php?id=<?= $product['id'] ?>" class="quick-add-form">
                  <input type="hidden" name="quantity" value="1">
                  <button type="submit" class="card-cart-btn" aria-label="Quick add to cart" title="Add to Cart">
                    <img src="../images/cart.jpg" alt="Add to Cart" />
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

  </div>
</section>

<script src="../javascript/plp.js"></script>

<?php require '../includes/footer.php'; ?>