<?php

namespace Filters;

class ParticipantFilter extends Filter
{
    /**
     * Filter by participant name (LIKE search).
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
     * Filter by participant email.
     *
     * @param string $email The email to search.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereEmail(string $email, string $boolean = null): static
    {
        return $this->whereString('email', $email, $boolean);
    }
}