<?php

namespace App\Entities;

use DateTime;

class Participant extends Entity
{
    public string $name;
    public string $email;
    public DateTime $created_at;
    public ?array $events = null;
    
    public function __construct(
        int $id,
        string $name,
        string $email,
        DateTime $created_at
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->created_at = $created_at;
    }

    public static function fromRecord(array $record): static
    {
        return new static(
            $record['id'],
            $record['name'],
            $record['email'],
            new DateTime($record['created_at']),
        );
    }

    public static function getTableName(): string
    {
        return 'participants';
    }

    /**
     * Retrieves the events associated with the participant.
     * 
     * This method establishes a many-to-many relationship between the participant and the events
     * they are part of. It will return an array of Event entities.
     * 
     * @return array An array of Event entities associated with the participant.
     */
    public function events(): array
    {
        if (is_null($this->events)) {
            return $this->belongsToMany(
                'events',
                Event::class,
                'event_participants',
                'participant_id',
                'event_id'
            );
        }
        return $this->events;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}