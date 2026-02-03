<?php
/**
 * logout.php
 */

require_once __DIR__ . '/../app/controllers/AuthController.php';

// Initialize controller and call logout
$authController = new AuthController();
$authController->logout();
