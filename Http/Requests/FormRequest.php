<?php

namespace Http\Requests;

use Filters\Filter;
use Http\Request;
use Http\Validator;

abstract class FormRequest
{
    protected Request $request;

    public function __construct()
    {
        $this->request = Request::make();
    }

    /**
     * Create an instance of form request
     * 
     * @return static
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * Validate the incoming request based on the defined rules.
     *
     * @return Validator
     */
    public static function validate()
    {
        $formRequest = new static();
        $validator = $formRequest->validator($formRequest->request->all());

        if ($validator->hasErrors()) {
            static::respondWithErrors($validator->getErrors());
        }
    }

    /**
     * Get all request data.
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->request->all();
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
        return $this->request->get($key, $default);
    }

    /**
     * Retrieve a specific boolean field from the request.
     * 
     * @param string $key
     * @return bool
     */
    public function boolean($key): bool
    {
        return $this->request->boolean($key);
    }

    /**
     * Check if the request has a given key.
     * 
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->request->has($key);
    }

    /**
     * Create a new Validator instance for this request's data.
     *
     * @return Validator
     */
    abstract public static function validator(array $data): Validator;

    /**
     * Return the validation response
     *
     * @return void
     */
    abstract public static function respondWithErrors(array $errors): void;

    /**
     * Return a custom filter built from response (Optional)
     *
     * @return Filter
     */
    public function filter(): Filter
    {
        return Filter::make();
    }
}
