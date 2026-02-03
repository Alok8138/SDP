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

  <form method="POST" class="auth-form" style="display: flex; flex-direction: column; gap: 1em; max-width: 400px;">
    <input type="text" name="firstname" placeholder="First Name" required style="padding: 10px; border: 1px solid #ddd;" />
    <input type="text" name="lastname" placeholder="Last Name" required style="padding: 10px; border: 1px solid #ddd;" />
    <input type="email" name="email" placeholder="Email" required style="padding: 10px; border: 1px solid #ddd;" />
    <input type="phone" name="phone" placeholder="Phone (Optional)" style="padding: 10px; border: 1px solid #ddd;" />
    <input type="password" name="password" placeholder="Password" required style="padding: 10px; border: 1px solid #ddd;" />

    <button type="submit" style="padding: 12px; background: #333; color: #fff; border: none; cursor: pointer;">Create Account</button>
  </form>

  <p style="margin-top: 1em;">
    Already have an account?
    <a href="login.php" style="color: #333; font-weight: bold;">Login</a>
  </p>
</section>

<?php require '../resources/views/footer.php'; ?>