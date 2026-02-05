<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$controller = new AuthController();
$data = $controller->signup();

$error = $data['error'];
$success = $data['success'];

require_once '../app/helpers/functions.php';
require_once '../resources/views/header.php';
?>

<section class="container auth-page">
  <h2>Sign Up</h2>

  <?php if ($error): ?>
    <p class="error" style="color: red; margin-bottom: 1em;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <!-- Added ID for JS Validation -->
  <form id="signupForm" method="POST" class="auth-form" style="display: flex; flex-direction: column; gap: 1em; max-width: 400px;">
    <!-- Added IDs for inputs -->
    <input type="text" id="signupFirstname" name="firstname" placeholder="First Name" required style="padding: 10px; border: 1px solid #ddd;" />
    <input type="text" id="signupLastname" name="lastname" placeholder="Last Name" required style="padding: 10px; border: 1px solid #ddd;" />
    <input type="email" id="signupEmail" name="email" placeholder="Email" required style="padding: 10px; border: 1px solid #ddd;" />
    <input type="phone" name="phone" placeholder="Phone (Optional)" style="padding: 10px; border: 1px solid #ddd;" />
    <input type="password" id="signupPassword" name="password" placeholder="Password" required style="padding: 10px; border: 1px solid #ddd;" />
    
    <!-- Added Confirm Password Field -->
    <input type="password" id="signupConfirmPassword" name="confirm_password" placeholder="Confirm Password" required style="padding: 10px; border: 1px solid #ddd;" />

    <button type="submit" style="padding: 12px; background: #333; color: #fff; border: none; cursor: pointer;">Create Account</button>
  </form>

  <p style="margin-top: 1em;">
    Already have an account?
    <a href="login" style="color: #333; font-weight: bold;">Login</a>
  </p>
</section>

<!-- Validation Script -->
<script src="<?= BASE_URL ?>/assets/js/auth-validation.js"></script>

<?php require '../resources/views/footer.php'; ?>