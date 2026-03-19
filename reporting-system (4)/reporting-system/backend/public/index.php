<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;
use App\Core\Response;

// Load env
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
}

// CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    http_response_code(204);
    exit;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$request  = new Request();
$response = new Response();
$router   = new Router($request, $response);

// Register routes
$router->get('/api/reports',             'ReportController@index');
$router->get('/api/reports/schema',      'ReportController@schema');
$router->get('/api/facets/{field}',      'ReportController@facets');
$router->get('/api/charts',              'ChartController@index');
$router->get('/api/saved-views',         'SavedViewController@index');
$router->post('/api/saved-views',        'SavedViewController@store');
$router->put('/api/saved-views/{id}',    'SavedViewController@update');
$router->delete('/api/saved-views/{id}', 'SavedViewController@destroy');
$router->get('/api/column-config',       'ColumnConfigController@show');
$router->put('/api/column-config',       'ColumnConfigController@update');
$router->post('/api/date-compare',       'DateCompareController@compare');

try {
    $router->dispatch();
} catch (Throwable $e) {
    $response->json(['error' => $e->getMessage()], 500);
}
