<?php

namespace App\Filters;

class IPFilter extends Filter
{
    /**
     * Filter by IP Address.
     *
     * @param string $ip_address The IP Address.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereIPAddress(string $ip_address, string $boolean = null): static
    {
        return $this->whereString('ip_address', $ip_address, $boolean);
    }

    /**
     * Filter by Blacklisted
     *
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereBlacklisted(string $boolean = null): static
    {
        return $this->where('blacklisted', '=', true, $boolean);
    }

    /**
     * Filter by Whitelisted
     *
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereWhitelisted(string $boolean = null): static
    {
        return $this->where('blacklisted', '=', false, $boolean);
    }
}