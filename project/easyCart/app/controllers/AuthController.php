<?php
/**
 * AuthController.php
 */

require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../helpers/auth_helper.php';

class AuthController {
    
    /**
     * Handle Login
     */
    public function login() {
        redirectIfLoggedIn();

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($email === '' || $password === '') {
                $error = "All fields are required.";
            } else {
                $customer = Customer::getByEmail($email);
                
                if ($customer && password_verify($password, $customer['password_hash'])) {
                    // Start Session and store user info
                    $_SESSION['user_id'] = $customer['entity_id'];
                    $_SESSION['user_email'] = $customer['email'];
                    $_SESSION['user_name'] = $customer['firstname'] . ' ' . $customer['lastname'];
                    
                    // Handle redirect URL
                    $redirect = $_GET['redirect'] ?? 'index';
                    $location = ($redirect === 'cart') ? BASE_URL . '/cart' : BASE_URL . '/';
                    
                    header("Location: $location");
                    exit;
                } else {
                    $error = "Invalid email or password.";
                }
            }
        }

        return ['error' => $error];
    }

    /**
     * Handle Signup
     */
    public function signup() {
        redirectIfLoggedIn();

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstname = trim($_POST['firstname'] ?? '');
            $lastname  = trim($_POST['lastname'] ?? '');
            $email     = trim($_POST['email'] ?? '');
            $password  = trim($_POST['password'] ?? '');
            $phone     = trim($_POST['phone'] ?? '');

            if ($firstname === '' || $lastname === '' || $email === '' || $password === '') {
                $error = "Please fill in all required fields.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address.";
            } elseif (Customer::emailExists($email)) {
                $error = "This email is already registered.";
            } else {
                $data = [
                    'firstname' => $firstname,
                    'lastname'  => $lastname,
                    'email'     => $email,
                    'password'  => $password,
                    'phone'     => $phone
                ];

                if (Customer::create($data)) {
                    $success = "Registration successful! You can now log in.";
                    // Optionally auto-login or redirect
                    header("Location: " . BASE_URL . "/login?signup=success");
                    exit;
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        }

        return ['error' => $error, 'success' => $success];
    }

    /**
     * Handle Logout
     */
    public function logout() {
        // Unset all session variables
        $_SESSION = [];

        // Destroy the session
        if (session_id() !== "" || isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        session_destroy();

        // Start a new session and regenerate ID to prevent fixation
        session_start();
        session_regenerate_id(true);
        session_destroy(); // Destroy again to ensure it's clean for the redirect

        header("Location: " . BASE_URL . "/login");
        exit;
    }
}
