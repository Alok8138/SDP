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
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" class="auth-form">
    <input type="text" name="name" placeholder="Full Name" required />
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />

    <button type="submit">Create Account</button>
  </form>

  <p>
    Already have an account?
    <a href="login.php">Login</a>
  </p>
</section>

<?php require '../resources/views/footer.php'; ?>