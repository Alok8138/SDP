<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$controller = new AuthController();
$data = $controller->login();

$error = $data['error'];

require_once '../app/helpers/functions.php';
require_once '../resources/views/header.php';
?>

<section class="container auth-page">
  <h2>Login</h2>

  <?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" class="auth-form">
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />

    <button type="submit">Login</button>
  </form>

  <p>
    Don’t have an account?
    <a href="signup.php">Sign Up</a>
  </p>
</section>

<?php require '../resources/views/footer.php'; ?>