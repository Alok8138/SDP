<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';

$controller = new ProductController();
$data = $controller->show();

require_once '../app/helpers/functions.php';
require_once '../resources/views/header.php';

if (isset($data['error'])) {
    echo "<section class='container'><p>" . htmlspecialchars($data['error']) . "</p></section>";
    require '../resources/views/footer.php';
    exit;
}

$product = $data['product'];
$sliderImages = !empty($product['gallery']) ? $product['gallery'] : [$product['image']];
$hasMultipleImages = count($sliderImages) > 1;
?>

<section class="container pdp">
    <div class="pdp-layout">

        <!-- Image Slider Section -->
        <div class="pdp-image" id="pdpImageContainer">
            <img src="<?= htmlspecialchars($sliderImages[0]) ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="mainImage">
            
            <?php if ($hasMultipleImages): ?>
                <button class="slider-btn prev-btn" id="prevBtn" aria-label="Previous image">&#10094;</button>
                <button class="slider-btn next-btn" id="nextBtn" aria-label="Next image">&#10095;</button>
                
                <div class="slider-dots" id="sliderDots">
                    <?php foreach ($sliderImages as $index => $img): ?>
                        <span class="dot <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>"></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <script>
                window.pdpData = {
                    sliderImages: <?= json_encode($sliderImages) ?>
                };
            </script>
        </div>

        <!-- Product Details Section -->
        <div class="pdp-details">
            <h1><?= htmlspecialchars($product['name']) ?></h1>

            <p class="price">
                $<?= htmlspecialchars($product['price']) ?>
                <?php if (!empty($product['old_price'])): ?>
                    <span class="old-price">$<?= htmlspecialchars($product['old_price']) ?></span>
                <?php endif; ?>
            </p>

            <p class="description"><?= htmlspecialchars($product['description']) ?></p>

            <?php if (!empty($product['features'])): ?>
                <ul class="features">
                    <?php foreach ($product['features'] as $feature): ?>
                        <li><?= htmlspecialchars($feature) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST" id="addToCartForm" class="ajax-cart-form">
                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                <input type="hidden" name="quantity" id="quantityInput" value="1">
                
                <div class="quantity-wrapper">
                    <span class="qty-label">Quantity:</span>
                    <div class="quantity-box">
                        <button type="button" id="decrementBtn" aria-label="Decrease quantity">−</button>
                        <span class="qty-value" id="quantityValue">1</span>
                        <button type="button" id="incrementBtn" aria-label="Increase quantity">+</button>
                    </div>
                </div>

                <div class="quantity-wrapper">
                    <span class="qty-label">Current in Cart:</span>
                    <div class="quantity-box">
                        <span class="qty-value qty-cart" id="sessionQuantity">
                            <?= isset($_SESSION['cart'][$product['id']]) ? (int) $_SESSION['cart'][$product['id']]['qty'] : 0 ?>
                        </span>
                    </div>
                </div>

                <button type="submit" id="addToCartBtn">Add to Cart</button>
            </form>
        </div>

    </div>
</section>

<script src="assets/js/pdp.js"></script>

<?php require '../resources/views/footer.php'; ?>