<?php

require_once __DIR__ . '/../app/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'];
$path   = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';

$routes = [
    ['GET',  '/admin',                    \KhanNet\Controllers\DashboardController::class,        'index'],
    ['GET',  '/admin/login',              \KhanNet\Controllers\AuthController::class,              'login'],
    ['POST', '/admin/login',              \KhanNet\Controllers\AuthController::class,              'login'],
    ['GET',  '/admin/logout',             \KhanNet\Controllers\AuthController::class,              'logout'],
    ['GET',  '/admin/connections',        \KhanNet\Controllers\ConnectionController::class,        'index'],
    ['POST', '/admin/connections',        \KhanNet\Controllers\ConnectionController::class,        'updateStatus'],
    ['GET',  '/admin/connections/export', \KhanNet\Controllers\ConnectionController::class,        'export'],
    ['GET',  '/admin/quotes',             \KhanNet\Controllers\QuoteController::class,             'index'],
    ['POST', '/admin/quotes',             \KhanNet\Controllers\QuoteController::class,             'updateStatus'],
    ['GET',  '/admin/quotes/export',      \KhanNet\Controllers\QuoteController::class,             'export'],
    ['POST', '/api/submit',               \KhanNet\Controllers\Api\ConnectionApiController::class, 'submit'],
    ['POST', '/api/quote',                \KhanNet\Controllers\Api\QuoteApiController::class,      'submit'],
];

foreach ($routes as [$routeMethod, $routePath, $class, $action]) {
    if ($method === $routeMethod && $path === $routePath) {
        (new $class)->callAction($action);
        return;
    }
}

http_response_code(404);
echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>404</title></head><body><h2>404 — Page not found</h2></body></html>';
