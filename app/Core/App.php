<?php

declare (strict_types = 1);

namespace App\Core;

final class App
{
    /** @var array<string, array<string, array<int, string>>> */
    private array $routes;

    public function __construct()
    {
        $this->routes = require BASE_PATH . '/routes/web.php';
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        $normalizedUri = $this->normalizeUri($uri);
        require BASE_PATH . '/middleware/auth_check.php';
        enforceAccess($normalizedUri);
        [$action, $routeParams] = $this->resolveRoute($method, $normalizedUri);

        if ($action === null || count($action) !== 2) {
            $this->sendNotFound();
            return;
        }

        [$controllerName, $controllerMethod] = $action;
        $className                           = 'App\\Controllers\\' . $controllerName;

        if (! class_exists($className)) {
            $this->sendNotFound();
            return;
        }

        $controller = new $className();

        if (! method_exists($controller, $controllerMethod)) {
            $this->sendNotFound();
            return;
        }

        $controller->{$controllerMethod}(...$routeParams);
    }

    /**
     * @return array{0: array<int, string>|null, 1: array<int, int|string>}
     */
    private function resolveRoute(string $method, string $uri): array
    {
        $methodRoutes = $this->routes[$method] ?? [];

        if (isset($methodRoutes[$uri])) {
            return [$methodRoutes[$uri], []];
        }

        foreach ($methodRoutes as $pattern => $action) {
            [$isMatch, $params] = $this->matchDynamicRoute($pattern, $uri);

            if ($isMatch) {
                return [$action, $params];
            }
        }

        return [null, []];
    }

    /**
     * @return array{0: bool, 1: array<int, int|string>}
     */
    private function matchDynamicRoute(string $pattern, string $uri): array
    {
        if (strpos($pattern, '{') === false) {
            return [false, []];
        }

        $patternSegments = explode('/', trim($pattern, '/'));
        $uriSegments = explode('/', trim($uri, '/'));

        if (count($patternSegments) !== count($uriSegments)) {
            return [false, []];
        }

        $params = [];

        foreach ($patternSegments as $index => $patternSegment) {
            $uriSegment = $uriSegments[$index];

            if (preg_match('/^\{[a-zA-Z_][a-zA-Z0-9_]*\}$/', $patternSegment) === 1) {
                $params[] = ctype_digit($uriSegment) ? (int) $uriSegment : $uriSegment;
                continue;
            }

            if ($patternSegment !== $uriSegment) {
                return [false, []];
            }
        }

        return [true, $params];
    }

    private function normalizeUri(string $uri): string
    {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        if ($scriptDir !== '/' && $scriptDir !== '.' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir)) ?: '/';
        }

        $trimmed = trim($uri, '/');

        return $trimmed === '' ? '/' : '/' . $trimmed;
    }

    private function sendNotFound(): void
    {
        http_response_code(404);
        echo '404 - Page not found';
    }
}
