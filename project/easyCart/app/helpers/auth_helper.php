<?php
/**
 * Session protection helper
 */

/**
 * Check if a user is currently logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect guests to login page if they try to access protected pages
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login");
        exit;
    }
}

/**
 * Redirect logged-in users away from auth pages (login/signup)
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: ./");
        exit;
    }
}
