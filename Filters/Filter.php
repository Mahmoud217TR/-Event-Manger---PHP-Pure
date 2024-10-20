<?php

namespace Filters;

use Closure;
use DateTime;

class Filter
{
    protected array $conditions = [];
    protected array $values = [];

    public static function make(): static
    {
        return new static();
    }

    /**
     * Add a condition to the filter with the specified operator (AND/OR).
     * 
     * @param string $field The database column name.
     * @param string $operator The comparison operator (=, <, >, etc.).
     * @param mixed $value The value to compare against.
     * @param string $boolean The boolean operator (AND/OR), default is 'AND'.
     * @return static
     */
    public function where(string $field, string $operator, $value, string $boolean = null): static
    {
        if (is_null($boolean)) {
            $boolean = 'AND';
        }

        if (!empty($this->conditions)) {
            $this->conditions[] = "{$boolean} {$field} {$operator} ?";
        } else {
            $this->conditions[] = "{$field} {$operator} ?";
        }

        $this->values[] = $value;
        
        return $this;
    }

    /**
     * Add a condition to the filter with the AND operator
     * 
     * @param string $field The database column name.
     * @param string $operator The comparison operator (=, <, >, etc.).
     * @param mixed $value The value to compare against.
     * @return static
     */
    public function andWhere(string $field, string $operator, $value): static
    {
        return $this->where($field, $operator, $value, "AND");
    }

    /**
     * Add a condition to the filter with the Or operator
     * 
     * @param string $field The database column name.
     * @param string $operator The comparison operator (=, <, >, etc.).
     * @param mixed $value The value to compare against.
     * @return static
     */
    public function orWhere(string $field, string $operator, $value): static
    {
        return $this->where($field, $operator, $value, "OR");
    }

    /**
     * Filter by string has keyword (LIKE search).
     *
     * @param string $field The database column name.
     * @param string $keyword The keyword to search.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereStringHas(string $field, string $keyword, string $boolean = null): static
    {
        return $this->whereString($field, "%$keyword%", $boolean);
    }

    /**
     * Filter by string starting with a keyword (LIKE search).
     *
     * @param string $field The database column name.
     * @param string $keyword The keyword to search.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereStringStartWith(string $field, string $keyword, string $boolean = null): static
    {
        return $this->whereString($field, "$keyword%", $boolean);
    }

    /**
     * Filter by string ends with a keyword (LIKE search).
     *
     * @param string $field The database column name.
     * @param string $keyword The keyword to search.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereStringEndsWith(string $field, string $keyword, string $boolean = null): static
    {
        return $this->whereString($field, "%$keyword", $boolean);
    }

    /**
     * Filter by string equals keyword (LIKE search).
     *
     * @param string $field The database column name.
     * @param string $keyword The keyword to search.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereString(string $field, string $keyword, string $boolean = null): static
    {
        return $this->where($field, 'LIKE', $keyword, $boolean);
    }

    /**
     * Filter by datetime.
     * 
     * @param string $field The database column name.
     * @param DateTime|string $date The specific date to filter by.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereDateTime(string $field, DateTime|string $date, string $boolean = null): static
    {
        if ($date instanceof DateTime) {
            $date = $this->formatDateTime($date);
        }

        return $this->where($field, '=', $date, $boolean);
    }

    /**
     * Filter entities before a specific date.
     *
     * @param string $field The database column name.
     * @param DateTime|string $date The date before which entities should be returned.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereBeforeDateTime(string $field, DateTime|string $date, string $boolean = null): static
    {
        if ($date instanceof DateTime) {
            $date = $this->formatDateTime($date);
        }

        return $this->where($field, '<', $date, $boolean);
    }

    /**
     * Filter entities after a specific date.
     * 
     * @param string $field The database column name.
     * @param DateTime|string $date The date after which entities should be returned.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereAfterDateTime(string $field, DateTime|string $date, string $boolean = null): static
    {
        if ($date instanceof DateTime) {
            $date = $this->formatDateTime($date);
        }

        return $this->where($field, '>', $date, $boolean);
    }

    /**
     * Filter entities based on null field.
     * 
     * @param string $field The database column name.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereNull(string $field, string $boolean = null): static
    {
        return $this->where($field, 'IS', "NULL", $boolean);
    }

    /**
     * Filter entities based on fields with values.
     * 
     * @param string $field The database column name.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereNotNull(string $field, string $boolean = null): static
    {
        return $this->where($field, 'IS NOT', "NULL", $boolean);
    }

    /**
     * Add a condition to the filter with the specified operator (AND/OR).
     * 
     * @param string $field The database column name.
     * @param array $values The values to serach in.
     * @param string $boolean The boolean operator (AND/OR), default is 'AND'.
     * @return static
     */
    public function whereIn(string $field, array $values, string $boolean = 'AND'): static
    {
        if (!empty($values)) {
            $placeholders = implode(', ', array_fill(0, count($values), '?'));

            if (!empty($this->conditions)) {
                $this->conditions[] = "{$boolean} {$field} IN ({$placeholders})";
            } else {
                $this->conditions[] = "{$field} IN ({$placeholders})";
            }

            $this->values = array_merge($this->values, $values);
        }

        return $this;
    }

    /**
     * Build the WHERE clause and return the query string and values.
     * 
     * @return array An array with the query string and the corresponding values.
     */
    public function build(): array
    {
        $query = !empty($this->conditions) ? "WHERE " . implode(' ', $this->conditions) : '';
        return [$query, $this->values];
    }

    /**
     * Conditionally apply filters using a closure.
     * 
     * @param bool $condition Whether the closure should be applied.
     * @param \Closure $callback The callback where you can add more conditions.
     * @param \Closure|null $default The default callback if the condition is false.
     * @return $this
     */
    public function when(bool $condition, Closure $callback, Closure $default = null): self
    {
        if ($condition) {
            $callback($this);
        } elseif ($default) {
            $default($this);
        }

        return $this;
    }

    /**
     * Format datetime attributes for database search.
     * 
     * @param DateTime $date The to be formatted.
     * @return string the fomatted datetime.
     */
    protected function formatDateTime(DateTime $dateTime): string
    {
        return $dateTime->format('Y-m-d');
    }
}