<?php

namespace App\Entities;

use DateTime;

class EventParticipant extends Entity
{
    public int $event_id;
    public int $participant_id;
    public DateTime $created_at;
    public ?Event $event = null;
    public ?Participant $participant = null;
    
    public function __construct(
        int $id,
        int $event_id,
        int $participant_id,
        DateTime $created_at
    ) {
        $this->id = $id;
        $this->event_id = $event_id;
        $this->participant_id = $participant_id;
        $this->created_at = $created_at;
    }

    public static function fromRecord(array $record): static
    {
        return new static(
            $record['id'],
            $record['event_id'],
            $record['participant_id'],
            new DateTime($record['created_at']),
        );
    }

    public static function getTableName(): string
    {
        return 'event_participants';
    }

    /**
     * Retrieves the associated Event entity for the event participant.
     * 
     * Uses a belongs-to relationship between EventParticipant and Event.
     * 
     * @return ?Event The related Event entity or null if not found.
     */
    public function event(): ?Event
    {
        if (is_null($this->event)) {
            return $this->belongsTo('event', Event::class, 'event_id');
        }
        return $this->event;
    }

    /**
     * Retrieves the associated Participant entity for the event participant.
     * 
     * Uses a belongs-to relationship between EventParticipant and Participant.
     * 
     * @return ?Participant The related Participant entity or null if not found.
     */
    public function participant(): ?Participant
    {
        if (is_null($this->participant)) {
            return $this->belongsTo('participant', Participant::class, 'participant_id');
        }
        return $this->participant;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'participant_id' => $this->participant_id,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}