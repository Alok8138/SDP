<?php
// Expecting $data array from ProductController::show($slug)
$product = $data['product'];
$sliderImages = $data['sliderImages'];
$features = $data['features'];
$cartQuantity = $data['cartQuantity'];
?>

<section class="container pdp">
    <div class="pdp-layout">

        <!-- Image Slider Section -->
        <div class="pdp-image" id="pdpImageContainer">
            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($sliderImages[0]) ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="mainImage">
            
            <button class="slider-btn prev-btn" id="prevBtn" aria-label="Previous image">&#10094;</button>
            <button class="slider-btn next-btn" id="nextBtn" aria-label="Next image">&#10095;</button>
            
            <div class="slider-dots" id="sliderDots">
                <?php foreach ($sliderImages as $index => $img): ?>
                    <span class="dot <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>"></span>
                <?php endforeach; ?>
            </div>

            <script>
                window.pdpData = {
                    sliderImages: <?= json_encode($sliderImages, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
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

            <?php if (!empty($features)): ?>
                <ul class="features">
                    <?php foreach ($features as $feature): ?>
                        <li><?= htmlspecialchars($feature) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST" id="addToCartForm" class="pdp-cart-form ajax-cart-form">
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
                            <?= (int) $cartQuantity ?>
                        </span>
                    </div>
                </div>

                <button type="submit" id="addToCartBtn">Add to Cart</button>
            </form>
        </div>

    </div>
</section>

<script src="<?= BASE_URL ?>/assets/js/pdp.js"></script>

