<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$controller = new AuthController();
$data = $controller->login();

$error = $data['error'];
$signupSuccess = isset($_GET['signup']) && $_GET['signup'] === 'success';

require_once '../app/helpers/functions.php';
require_once '../resources/views/header.php';
?>

<section class="container auth-page">
  <h2>Login</h2>

  <?php if ($signupSuccess): ?>
    <p class="success" style="color: green; margin-bottom: 1em;">Registration successful! You can now log in.</p>
  <?php endif; ?>

  <?php if ($error): ?>
    <p class="error" style="color: red; margin-bottom: 1em;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" class="auth-form" style="display: flex; flex-direction: column; gap: 1em; max-width: 400px;">
    <input type="email" name="email" placeholder="Email" required style="padding: 10px; border: 1px solid #ddd;" />
    <input type="password" name="password" placeholder="Password" required style="padding: 10px; border: 1px solid #ddd;" />

    <button type="submit" style="padding: 12px; background: #333; color: #fff; border: none; cursor: pointer;">Login</button>
  </form>

  <p style="margin-top: 1em;">
    Don’t have an account?
    <a href="signup" style="color: #333; font-weight: bold;">Sign Up</a>
  </p>
</section>

<?php require '../resources/views/footer.php'; ?>