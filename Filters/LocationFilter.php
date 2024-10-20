<?php

namespace Filters;

class LocationFilter extends Filter
{
    /**
     * Filter by location name (LIKE search).
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
     * Filter by location address (LIKE search).
     *
     * @param string $address The address to search.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereAddress(string $address, string $boolean = null): static
    {
        return $this->whereStringHas('address', $address, $boolean);
    }
    
    /**
     * Filter by location available capacity.
     *
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereAvailable(string $boolean = null): static
    {
        return $this->where('capacity', '>', '0', $boolean);
    }
}