<?php

namespace App\Entities;

use DateTime;

class Event extends Entity
{
    public string $name;
    public DateTime $date;
    public int $location_id;
    public DateTime $created_at;
    public ?Location $location = null;
    public ?array $participants = null;
    
    public function __construct(
        int $id,
        string $name,
        DateTime $date,
        int $location_id,
        DateTime $created_at
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->date = $date;
        $this->location_id = $location_id;
        $this->created_at = $created_at;
    }

    public static function fromRecord(array $record): static
    {
        return new static(
            $record['id'],
            $record['name'],
            new DateTime($record['date']),
            $record['location_id'],
            new DateTime($record['created_at']),
        );
    }

    public static function getTableName(): string
    {
        return 'events';
    }


    /**
     * Retrieves the associated Location entity for the event.
     * 
     * Uses a belongs-to relationship between Event and Location.
     * 
     * @return ?Location The related Location entity or null if not found.
     */
    public function location(): ?Location
    {
        if (is_null($this->location)) {
            return $this->belongsTo('location', Location::class, 'location_id');
        }
        return $this->location;
    }

    /**
     * Retrieves an array of Participant entities related to the event.
     * 
     * Uses a many-to-many relationship via the pivot table 'event_participants'.
     * 
     * @return array An array of related Participant entities.
     */
    public function participants(): array
    {
        if (is_null($this->participants)) {
            return $this->belongsToMany(
                'participants',
                Participant::class,
                'event_participants',
                'event_id',
                'participant_id'
            );
        }
        return $this->participants;
    }

    /**
     * Retrieves the capacity of the event's location.
     * 
     * This method fetches the capacity from the related Location entity.
     * 
     * @return int The capacity of the event's location.
     */
    public function getCapacity(): int
    {
        return $this->location()->capacity;
    }
    
    
    /**
     * Retrieves the number of participants for the event.
     * 
     * This counts the related participants for the event.
     * 
     * @return int The number of participants attending the event.
     */
    public function getParticipantsCount(): int
    {
        return count($this->participants());
    }

    /**
     * Calculates the rate of capacity usage for the event as a float.
     * 
     * This method divides the number of participants by the total capacity.
     * 
     * @return float The percentage of the capacity filled by participants.
     */
    public function getCapacityRate(): float
    {
        return $this->getParticipantsCount() * 100 / $this->getCapacity();
    }

    /**
     * Calculates the rate of capacity usage for the event as a formatted string percentage.
     * 
     * The result is a string percentage, with the value rounded up.
     * 
     * @return string The capacity usage rate in percentage format (e.g., "85%").
     */
    public function getCapacityRatePercentage(): string
    {
        return ceil($this->getParticipantsCount() * 100 / $this->getCapacity())."%";
    }

    /**
     * Retrieves the count of event participants from the pivot table 'event_participants'.
     * 
     * This uses a custom query to count the number of participants for the event.
     * 
     * @return int The number of participants associated with the event.
     */
    public function getEventParticipantsCount(): int
    {
        return EventParticipant::count("WHERE event_id = ?", [$this->id]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->date->format('Y-m-d'),
            'location_id' => $this->location_id,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}