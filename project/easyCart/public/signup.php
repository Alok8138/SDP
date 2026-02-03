<?php
require '../app/config/database.php';
require '../app/helpers/functions.php';
require '../resources/views/header.php';

// If already logged in
if (isset($_SESSION['user'])) {
  header("Location: index.php");
  exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  if ($name === '' || $email === '' || $password === '') {
    $error = "All fields are required.";
  } else {
    // Save user in session (mock signup)
    $_SESSION['user'] = [
      'name' => $name,
      'email' => $email
    ];

    $success = "Signup successful. You can now login.";
    header("Location: login.php");
    exit;
  }
}
?>

<section class="container auth-page">
  <h2>Sign Up</h2>

  <?php if ($error): ?>
    <p class="error"><?= $error ?></p>
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