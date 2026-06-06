<?php

declare(strict_types=1);

namespace Palmed\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->normalizePath($path),
            'handler' => $handler,
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = $this->normalizePath(parse_url($uri, PHP_URL_PATH) ?: '/');

        foreach ($this->routes as $route) {
            $params = $this->match($route['path'], $uri);
            if ($params !== false && $route['method'] === $method) {
                call_user_func($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        view('partials.404');
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }

    private function match(string $routePath, string $uri): array|false
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $uri, $matches)) {
            return false;
        }

        array_shift($matches);
        preg_match_all('/\{([a-zA-Z_]+)\}/', $routePath, $paramNames);
        $params = [];
        foreach ($paramNames[1] as $i => $name) {
            $params[$name] = $matches[$i] ?? null;
        }

        return $params;
    }
}
