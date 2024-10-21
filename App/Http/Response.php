<?php

namespace App\Http;

class Response
{
    protected int $statusCode = 200;
    protected string $message = '';

    public function __construct() {}

    /**
     * Creates a response instance
     *
     * @param int $statusCode
     * @return Response
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * Set response status code
     *
     * @param int $statusCode
     * @return Response
     */
    public function code(int $statusCode): static
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Set response message
     *
     * @param string $message
     * @return Response
     */
    public function message(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Add custom headers
     *
     * @param array $headers
     * @return Response
     */
    public function headers(array $headers): static
    {
        foreach($headers as $header) {
            $this->header($header);
        }
        return $this;
    }

    /**
     * Add custom header
     *
     * @param string $header
     * @return Response
     */
    public function header(string $header): static
    {
        header($header);
        return $this;
    }

    /**
     * Send a JSON response
     *
     * @param array|object $data
     * @return void
     */
    public function json(array|object $data = null)
    {
        if (is_null($data)) {
            $data = [
                'message' => $this->message
            ];
        }
        http_response_code($this->statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Render a View
     * 
     * @param string $view
     * @param array|null $data
     * @return void
     */
    public function view(string $view, array $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../../views/' . $view . '.php';
        exit;
    }

    /**
     * Redirect to a given URL
     *
     * @param string $url
     * @return void
     */
    public function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }
    
    /**
     * Send a raw content
     *
     * @param mixed $content
     * @param int $statusCode
     * @return void
     */
    public function raw($content = null)
    {
        if (is_null($content)) {
            $content = $this->message;
        }
        http_response_code($this->statusCode);
        header('Content-Type: text/plain');
        echo $content;
        exit;
    }

    /**
     * Create a response indicating that the requested resource was not found (404).
     * 
     * @param string $message Custom message to include in the response. Default is 'Item Not Found'.
     * @return Response Returns a new instance of the Response class with a 404 status code and the provided message.
     */
    public static function notFound(string $message = 'Item Not Found')
    {
        return static::make()
            ->code(404)
            ->message($message);
    }

    /**
     * Create a response indicating that the request is forbidden (403).
     * 
     * @param string $message Custom message to include in the response. Default is 'Unauthorized'.
     * @return Response Returns a new instance of the Response class with a 403 status code and the provided message.
     */
    public static function forbidden(string $message = 'Unauthorized')
    {
        return static::make()
            ->code(403)
            ->message($message);
    }
}