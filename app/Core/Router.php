<?php
namespace App\Core;

class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, $handler, array $roles = []): void
    {
        $this->routes['GET'][$path] = ['handler' => $handler, 'roles' => $roles];
    }

    public function post(string $path, $handler, array $roles = []): void
    {
        $this->routes['POST'][$path] = ['handler' => $handler, 'roles' => $roles];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Hapus base folder jika app di subdir; sesuaikan jika perlu
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        if ($scriptDir !== '' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }
        $uri = '/' . trim($uri, '/');
        if ($uri === '/') $uri = '/dashboard'; // default

        $route = $this->routes[$method][$uri] ?? null;
        if (!$route) {
            http_response_code(404);
            echo '404 - Halaman tidak ditemukan: ' . htmlspecialchars($uri);
            return;
        }
        // Cek autentikasi & otorisasi
        if (!empty($route['roles'])) {
            Auth::requireLogin();
            Auth::requireRole($route['roles']);
        }
        [$controller, $action] = $route['handler'];
        (new $controller())->$action();
    }
}
