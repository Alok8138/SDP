<?php
/**
 * AuthController.php
 */

class AuthController {
    
    /**
     * Handle Login
     */
    public function login() {
        if (isset($_SESSION['user'])) {
             header("Location: index.php");
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($email === '' || $password === '') {
                $error = "All fields are required.";
            } else {
                if (isset($_SESSION['user']) && $_SESSION['user']['email'] === $email) {
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Invalid credentials.";
                }
            }
        }

        return ['error' => $error];
    }

    /**
     * Handle Signup
     */
    public function signup() {
        if (isset($_SESSION['user'])) {
            header("Location: index.php");
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($name === '' || $email === '' || $password === '') {
                $error = "All fields are required.";
            } else {
                $_SESSION['user'] = [
                    'name' => $name,
                    'email' => $email
                ];
                header("Location: login.php");
                exit;
            }
        }

        return ['error' => $error, 'success' => $success];
    }
}
