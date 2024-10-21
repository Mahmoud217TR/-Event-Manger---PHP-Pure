<?php

namespace App\Http;

class Request
{
    protected $data = [];
    protected $params = [];
    protected static Request $request;

    protected function __construct()
    {
        $this->data = array_merge($_GET, $_POST, $this->getJsonBody());
    }

    /**
     * Create an instance of request
     * 
     * @return Request
     */
    public static function make(): static
    {
        if (!isset(static::$request)) {
            static::$request = new static();
        }
        return static::$request;
    }

    /**
     * Retrieve a specific field from the request.
     * 
     * @param string $key
     * @param mixed $default Default value if key is not present
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Retrieve a specific boolean field from the request.
     * 
     * @param string $key
     * @return bool
     */
    public function boolean($key): bool
    {
        return filter_var($this->get($key), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get all request data.
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Retrieve a JSON request body (for API requests).
     * 
     * @return array
     */
    protected function getJsonBody(): array
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }

    /**
     * Check if the request has a given key.
     * 
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Create a new Validator instance for this request's data.
     * 
     * @return Validator
     */
    public function validate(): Validator
    {
        return new Validator($this->data);
    }

    /**
     * Add a route parameter to the request.
     * 
     * @param string $key The name of the parameter (e.g., 'id').
     * @param mixed $value The value of the parameter (e.g., '123').
     * @return void
     */
    public function addParam(string $key, mixed $value): void
    {
        $this->params[$key] = $value;
    }


    /**
     * Retrieve all route parameters.
     * 
     * This method returns an array of all parameters that were extracted from the URL
     * (e.g., from a route like `events/:id`).
     * 
     * @return array An associative array of parameters (e.g., ['id' => 123]).
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Check if a specific route parameter exists.
     * 
     * This method checks if a given key is present in the parameters array, which contains
     * 
     * @param string $key The parameter key to check (e.g., 'id').
     * @return bool Returns `true` if the parameter exists, `false` otherwise.
     */
    public function hasParam(string $key): bool
    {
        return isset($this->params[$key]);
    }

    /**
     * Retrieve a specific route parameter.
     * 
     * This method retrieves the value of a particular parameter (such as 'id') from the `$params` array,
     * which contains values extracted from the URL. If the parameter does not exist, it returns `null`.
     * 
     * @param string $key The name of the parameter to retrieve (e.g., 'id').
     * @return mixed The value of the parameter, or `null` if not found.
     */
    public function getParam(string $key): mixed
    {
        return $this->hasParam($key) ? $this->params[$key] : null;
    }

    /**
     * Get the client's IP address from the $_SERVER superglobal.
     *
     * @return string The client's IP address.
     */
    public function getClientIp(): string
    {
        // Handle different server variables to retrieve the real IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

}
