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

    public function addParam(string $key, mixed $value): void
    {
        $this->params[$key] = $value;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function hasParam(string $key): bool
    {
        return isset($this->params[$key]);
    }

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
