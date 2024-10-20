<?php

namespace Http;

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
     * @param int $statusCode
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

    public static function notFound(string $message = 'Item Not Found')
    {
        return static::make()
            ->code(404)
            ->message($message);
    }
}