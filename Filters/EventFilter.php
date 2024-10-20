<?php

namespace Filters;

use DateTime;

class EventFilter extends Filter
{
    /**
     * Filter by event name (LIKE search).
     *
     * @param string $name The name to search.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereName(string $name, string $boolean = null): static
    {
        return $this->whereStringHas('name', $name, $boolean);
    }

    /**
     * Filter by exact event date.
     *
     * @param DateTime|string $date The specific date to filter by.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereDate(DateTime|string $date, string $boolean = null): static
    {
        return $this->whereDateTime('date', $date, $boolean);
    }

    /**
     * Filter events before a specific date.
     *
     * @param DateTime|string $date The date before which events should be returned.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereBeforeDate(DateTime|string $date, string $boolean = null): static
    {
        return $this->whereBeforeDateTime('date', $date, $boolean);
    }

    /**
     * Filter events after a specific date.
     *
     * @param DateTime|string $date The date after which events should be returned.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereAfterDate(DateTime|string $date, string $boolean = null): static
    {
        return $this->whereAfterDateTime('date', $date, $boolean);
    }

    /**
     * Filter by location id.
     *
     * @param int $locationId The specific location id to filter by.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereLocation(int $locationId, string $boolean = null): static
    {
        return $this->where('location_id', '=', $locationId, $boolean);
    }
}