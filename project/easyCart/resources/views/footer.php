<footer class="footer">
    <div class="footer-container">
        <!-- Brand Section -->
        <div class="footer-section">
            <h3 class="footer-brand">EasyCart</h3>
            <p class="footer-description">Your one-stop shop for quality electronics, fashion, and accessories.</p>
        </div>

        <!-- Quick Links -->
        <div class="footer-section">
            <h4 class="footer-title">Quick Links</h4>
            <ul class="footer-links">
                <li><a href="<?= BASE_URL ?>/">Home</a></li>
                <li><a href="<?= BASE_URL ?>/plp">Shop</a></li>
                <li><a href="<?= BASE_URL ?>/cart">Cart</a></li>
                <li><a href="<?= BASE_URL ?>/orders">My Orders</a></li>
                <li><a href="<?= BASE_URL ?>/login">Login</a></li>
                <li><a href="<?= BASE_URL ?>/signup">Sign Up</a></li>
            </ul>
        </div>

        <!-- Customer Support -->
        <div class="footer-section">
            <h4 class="footer-title">Customer Support</h4>
            <ul class="footer-links">
                <li><a href="#">Help Center</a></li>
                <li><a href="#">Returns & Refunds</a></li>
                <li><a href="#">Shipping Information</a></li>
                <li><a href="#">Contact Us</a></li>
            </ul>
        </div>

        <!-- Contact Information -->
        <div class="footer-section">
            <h4 class="footer-title">Contact Us</h4>
            <ul class="footer-contact">
                <li>Email: <a href="mailto:support@easycart.com">support@easycart.com</a></li>
                <li>Phone: <a href="tel:+919876543210">+91 98765 43210</a></li>
                <li>Location: India</li>
            </ul>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> EasyCart. All rights reserved.</p>
    </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/cart_ajax.js"></script>
</body>

</html>