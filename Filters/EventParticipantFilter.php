<?php

namespace Filters;

class EventParticipantFilter extends Filter
{
    /**
     * Filter by participant id.
     *
     * @param int $id The participant id.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereParticipant(int $id, string $boolean = null): static
    {
        return $this->where('participant_id', '=', $id, $boolean);
    }

    /**
     * Filter by event id.
     *
     * @param int $id The event id.
     * @param string|null $boolean The boolean operator (AND/OR).
     * @return static
     */
    public function whereEvent(int $id, string $boolean = null): static
    {
        return $this->where('event_id', '=', $id, $boolean);
    }
}