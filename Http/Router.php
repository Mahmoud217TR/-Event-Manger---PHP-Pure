<?php

namespace Http;

class Router
{
    protected $routes = [];

    public function get(string $path, $callback, array $middleware = [])
    {
        $this->add("GET", $path, $callback, $middleware);
    }

    public function post(string $path, $callback, array $middleware = [])
    {
        $this->add("POST", $path, $callback, $middleware);
    }

    public function put(string $path, $callback, array $middleware = [])
    {
        $this->add("PUT", $path, $callback, $middleware);
    }

    public function patch(string $path, $callback, array $middleware = [])
    {
        $this->add("PATCH", $path, $callback, $middleware);
    }

    public function delete(string $path, $callback, array $middleware = [])
    {
        $this->add("DELETE", $path, $callback, $middleware);
    }

    public function add(string $method, string $path, $callback, array $middleware = [])
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
            'middleware' => $middleware,
        ];
    }

    public function dispatch()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Use parse_url to extract only the path portion, ignoring the query string
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); 

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $this->matchRoute($route['path'], $requestUri)) {
                $this->handleMiddlewares($route['middleware'], function() use ($route, $requestUri) {
                    $params = $this->getRouteParams($route['path'], $requestUri);

                    if (is_callable($route['callback'])) {
                        call_user_func($route['callback'], $this->getRouteParams($route['path'], $requestUri));
                    } else if (is_array($route['callback'])) {
                        $controller = new $route['callback'][0]();
                        $method = $route['callback'][1];
                        call_user_func([$controller, $method], ...$this->getRouteParams($route['path'], $requestUri));
                    }
                });
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }

     /**
     * Process each middleware in sequence before handling the request.
     */
    protected function handleMiddlewares(array $middlewareArray, callable $callback)
    {
        $next = function() use (&$middlewareArray, &$callback, &$next) {
            if (empty($middlewareArray)) {
                $callback();
                return;
            }

            $middleware = array_shift($middlewareArray);

            if ($middleware instanceof Middleware) {
                $middleware->handle(fn() => $next());
            } else {
                throw new \Exception("Middleware must extend the Http\Middleware class.");
            }
        };

        $next();
    }


    protected function getRouteParams($routePath, $requestUri)
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($requestUri, '/'));

        $params = [];
        foreach ($routeParts as $index => $part) {
            if (strpos($part, ':') === 0) {
                $params[] = $uriParts[$index] ?? null;
                request()->addParam(substr($part, 1), $uriParts[$index]);
            }
        }


        return $params;
    }

    protected function matchRoute($routePath, $requestUri)
    {
        $requestUri = parse_url($requestUri, PHP_URL_PATH);

        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($requestUri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        foreach ($routeParts as $index => $part) {
            if (strpos($part, ':') === 0) {
                continue;
            }

            if ($part !== $uriParts[$index]) {
                return false;
            }
        }

        return true;
    }
}
