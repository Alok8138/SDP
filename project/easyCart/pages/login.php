<?php
require '../includes/init.php';
require '../includes/header.php';



// If already logged in
if (isset($_SESSION['user'])) {
  header("Location: index.php");
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if ($email === '' || $password === '') {
    $error = "All fields are required.";
  } else {
    // Simple session-based login check
    if (isset($_SESSION['user']) && $_SESSION['user']['email'] === $email) {
      header("Location: index.php");
      exit;
    } else {
      $error = "Invalid credentials.";
    }
  }
}
?>

<section class="container auth-page">
  <h2>Login</h2>

  <?php if ($error): ?>
    <p class="error"><?= $error ?></p>
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

<?php require '../includes/footer.php'; ?>