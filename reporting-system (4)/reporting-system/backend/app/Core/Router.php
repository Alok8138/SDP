<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private Request $request;
    private Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    public function get(string $path, string $handler): void
    {
        $this->routes[] = ['method' => 'GET', 'path' => $path, 'handler' => $handler];
    }

    public function post(string $path, string $handler): void
    {
        $this->routes[] = ['method' => 'POST', 'path' => $path, 'handler' => $handler];
    }

    public function put(string $path, string $handler): void
    {
        $this->routes[] = ['method' => 'PUT', 'path' => $path, 'handler' => $handler];
    }

    public function delete(string $path, string $handler): void
    {
        $this->routes[] = ['method' => 'DELETE', 'path' => $path, 'handler' => $handler];
    }

    public function dispatch(): void
    {
        $method = $this->request->method();
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                [$controllerName, $action] = explode('@', $route['handler']);
                $class = "App\\Controllers\\{$controllerName}";

                if (!class_exists($class)) {
                    $this->response->json(['error' => "Controller {$class} not found"], 500);
                    return;
                }

                $controller = new $class($this->request, $this->response);
                $controller->$action(...$matches);
                return;
            }
        }

        $this->response->json(['error' => 'Route not found'], 404);
    }
}
