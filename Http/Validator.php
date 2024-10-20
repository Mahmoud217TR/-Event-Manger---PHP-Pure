<?php

namespace Http;

use Closure;
use DateTime;
use Entities\Event;

class Validator
{
    protected $data;
    protected $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Create an instance of validator
     * 
     * @return Validator
     */
    public static function make(array $data): static
    {
        return new static($data);
    }

    /**
     * Validate if a field is required
     *
     * @param string $field
     * @param string|null $customMessage
     * @return static
     */
    public function required(string $field, string $customMessage = null)
    {
        if (empty($this->data[$field])) {
            $this->addError($field, $customMessage ?? "$field is required.");
        }

        return $this;
    }

    /**
     * Validate if a field is string
     *
     * @param string $field
     * @param string|null $customMessage
     * @return static
     */
    public function string(string $field, string $customMessage = null)
    {
        if (isset($this->data[$field]) && !is_string($this->data[$field])) {
            $this->addError($field, $customMessage ?? "$field must be a valid string.");
        }

        return $this;
    }

    /**
     * Validate if a field is a valid email
     *
     * @param string $field
     * @param string|null $customMessage
     * @return static
     */
    public function email(string $field, string $customMessage = null)
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $customMessage ?? "$field must be a valid email.");
        }

        return $this;
    }

    /**
     * Validate if a field is a valid IP address
     *
     * @param string $field
     * @param string|null $customMessage
     * @return static
     */
    public function ip(string $field, string $customMessage = null)
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_IP)) {
            $this->addError($field, $customMessage ?? "$field must be a valid IP address.");
        }

        return $this;
    }

    /**
     * Validate if a field is a valid boolean
     *
     * @param string $field
     * @param string|null $customMessage
     * @return static
     */
    public function boolean(string $field, string $customMessage = null)
    {
        if (isset($this->data[$field]) && is_null(filter_var($this->data[$field], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {
            $this->addError($field, $customMessage ?? "$field must be a valid boolean.");
        }

        return $this;
    }

    /**
     * Validate if a field is a valid integer
     *
     * @param string $field
     * @param string|null $customMessage
     * @return static
     */
    public function integer(string $field, string $customMessage = null)
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
            $this->addError($field, $customMessage ?? "$field must be a valid integer.");
        }

        return $this;
    }

    /**
     * Validate if a field is a valid numeric
     *
     * @param string $field
     * @param string|null $customMessage
     * @return static
     */
    public function numeric(string $field, string $customMessage = null)
    {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->addError($field, $customMessage ?? "$field must be a valid numeric.");
        }

        return $this;
    }

    /**
     * Validate if a field is within a specified length
     *
     * @param string $field
     * @param int $min
     * @param int $max
     * @param string|null $customMessage
     * @return static
     */
    public function length(string $field, int $min, int $max, string $customMessage = null)
    {
        if (isset($this->data[$field])) {
            $length = strlen($this->data[$field]);
            if ($length < $min || $length > $max) {
                $this->addError($field, $customMessage ?? "$field must be between $min and $max characters.");
            }
        }

        return $this;
    }

    /**
     * Validate if a field matches a regex pattern
     *
     * @param string $field
     * @param string $pattern
     * @param string|null $customMessage
     * @return static
     */
    public function regex(string $field, string $pattern, string $customMessage = null)
    {
        if (isset($this->data[$field]) && !preg_match($pattern, $this->data[$field])) {
            $this->addError($field, $customMessage ?? "$field is not valid.");
        }

        return $this;
    }

    /**
     * Validate if a field is a valid date
     *
     * @param string $field
     * @param string|null $format (optional) - The expected date format (default: 'Y-m-d')
     * @param string|null $customMessage
     * @return static
     */
    public function date(string $field, string $format = 'Y-m-d', string $customMessage = null)
    {
        if (isset($this->data[$field])) {
            $date = DateTime::createFromFormat($format, $this->data[$field]);
            if (!$date || $date->format($format) !== $this->data[$field]) {
                $this->addError($field, $customMessage ?? "$field must be a valid date in the format $format.");
            }
        }

        return $this;
    }

    /**
     * Validate if a record exists in database based on table and column
     *
     * @param string $field
     * @param string $table
     * @param string|null $column
     * @param string|null $customMessage
     * @return static
     */
    public function exists(string $field, string $table, string $column = 'id', string $customMessage = null)
    {
        if (isset($this->data[$field])) {
            $statement = pdo()->prepare("SELECT * FROM {$table} WHERE {$column} = ?");
            $statement->execute([$this->data[$field]]);
            if (!$statement->fetch()) {
                $this->addError($field, $customMessage ?? "$field is not valid.");
            }
        }

        return $this;
    }

    /**
     * Validate if a record is unique in database based on table and column
     *
     * @param string $field
     * @param string $table
     * @param string $column
     * @param string|null $except
     * @param mixed|null $value
     * @param string|null $customMessage
     * @return static
     */
    public function unique(
        string $field,
        string $table,
        string $column,
        string $except = null,
        mixed $value = null,
        string $customMessage = null
    ) {
        if (isset($this->data[$field])) {
            $query = "{$column} = ?";
            $values =[ $this->data[$field]];
            if ($except && $value) {
                $query .= " AND {$except} != ?";
                $values[] = $value;
            }
            $statement = pdo()->prepare("SELECT * FROM {$table} WHERE {$query}");
            $statement->execute($values);
            if ($statement->fetch()) {
                $this->addError($field, $customMessage ?? "$field already exists.");
            }
        }

        return $this;
    }

    /**
     * Validate if a record is unique in database based on table and multiple columns
     *
     * @param array $fields
     * @param string $table
     * @param array $columns
     * @param string|null $except
     * @param mixed|null $value
     * @param string|null $customMessage
     * @return static
     */
    public function uniqueOn(
        array $fields,
        string $table,
        array $columns,
        string $except = null,
        mixed $value = null,
        string $customMessage = null
    ) {
        $values = [];
        foreach ($fields as $field) {
            if (!isset($this->data[$field])) {
                return $this;
            }
            $values[] = $this->data[$field];
        }

        $query = '';
        foreach ($columns as $column) {
            $query .= "{$column} = ? AND ";
        }
        $query = rtrim($query, 'AND ');

        if ($except && $value) {
            $query .= "AND {$except} != ?";
            $values[] = $value;
        }

        $statement = pdo()->prepare("SELECT * FROM {$table} WHERE {$query}");
        $statement->execute($values);
        if ($statement->fetch()) {
            $field = implode(', ', $fields);
            $this->addError($field, $customMessage ?? "$field already exists.");
        }

        return $this;
    }

    /**
     * Validate based on count query
     *
     * @param string $key
     * @param string $entity
     * @param string $query
     * @param array $values
     * @param Closure $comparison
     * @param string $customMessage
     * @return static
     */
    public function count(
        string $key,
        string $entity,
        string $query,
        array $values,
        Closure $comparison,
        string $customMessage
    ) {
        $count = $entity::count($query, $values);
        if (!$comparison($count)) {
            $this->addError($key, $customMessage);
        }

        return $this;
    }

    /**
     * Check if there are any validation errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Retrieve all validation errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add an error message for a specific field
     *
     * @param string $field
     * @param string $message
     */
    protected function addError(string $field, string $message)
    {
        $this->errors[$field][] = $message;
    }
}
