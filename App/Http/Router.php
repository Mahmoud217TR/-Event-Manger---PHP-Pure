<?php

namespace App\Http;

class Router
{
    protected $routes = [];

    /**
     * Register a GET route.
     *
     * @param string $path
     * @param callable $callback
     * @param array $middleware
     * @return void
     */
    public function get(string $path, $callback, array $middleware = [])
    {
        $this->add("GET", $path, $callback, $middleware);
    }

    /**
     * Register a POST route.
     *
     * @param string $path
     * @param callable $callback
     * @param array $middleware
     * @return void
     */
    public function post(string $path, $callback, array $middleware = [])
    {
        $this->add("POST", $path, $callback, $middleware);
    }

    /**
     * Register a PUT route.
     *
     * @param string $path
     * @param callable $callback
     * @param array $middleware
     * @return void
     */
    public function put(string $path, $callback, array $middleware = [])
    {
        $this->add("PUT", $path, $callback, $middleware);
    }

    /**
     * Register a PATCH route.
     *
     * @param string $path
     * @param callable $callback
     * @param array $middleware
     * @return void
     */
    public function patch(string $path, $callback, array $middleware = [])
    {
        $this->add("PATCH", $path, $callback, $middleware);
    }

    /**
     * Register a DELETE route.
     *
     * @param string $path
     * @param callable $callback
     * @param array $middleware
     * @return void
     */
    public function delete(string $path, $callback, array $middleware = [])
    {
        $this->add("DELETE", $path, $callback, $middleware);
    }

    /**
     * Add a route to the router.
     *
     * @param string $method
     * @param string $path
     * @param callable $callback
     * @param array $middleware
     * @return void
     */
    public function add(string $method, string $path, $callback, array $middleware = [])
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
            'middleware' => $middleware,
        ];
    }

    /**
     * Dispatch the request to the appropriate route.
     *
     * @return void
     */
    public function dispatch()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
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
     *
     * @param array $middlewareArray
     * @param callable $callback
     * @return void
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
                throw new \Exception("Middleware must extend the App\Http\Middleware class.");
            }
        };

        $next();
    }

    /**
     * Extract route parameters from the request URI based on the defined route path.
     *
     * @param string $routePath
     * @param string $requestUri
     * @return array
     */
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
    
    /**
     * Match the route path with the request URI.
     *
     * @param string $routePath
     * @param string $requestUri
     * @return bool
     */
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
