<?php

namespace Framework\Routing;

class Route
{
    private static array $routes = [];

    public static function get(string $path, array $handler): void
    {
        self::$routes[] = ['GET', $path, $handler[0], $handler[1]];
    }

    public static function post(string $path, array $handler): void
    {
        self::$routes[] = ['POST', $path, $handler[0], $handler[1]];
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path   = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';

        foreach (self::$routes as [$routeMethod, $routePath, $class, $action]) {
            if ($method === $routeMethod && $path === $routePath) {
                (new $class)->callAction($action);
                return;
            }
        }

        http_response_code(404);
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>404</title></head><body><h2>404 — Page not found</h2></body></html>';
    }
}
