<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once '../app/helpers/functions.php';

// 1. Validate that slug exists in the query string
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if ($slug === '') {
    http_response_code(404);
    require '../resources/views/header.php';
    require '../resources/views/404.php';
    require '../resources/views/footer.php';
    exit;
}

// 2. Delegate request handling to the controller
$controller = new ProductController();
$data = $controller->show($slug);

// 3. Load the appropriate response view
require '../resources/views/header.php';

if (isset($data['error'])) {
    require '../resources/views/404.php';
    require '../resources/views/footer.php';
    exit;
}

require '../resources/views/pdp.php';
require '../resources/views/footer.php';