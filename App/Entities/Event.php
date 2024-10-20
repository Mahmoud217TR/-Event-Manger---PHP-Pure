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

    public function location(): ?Location
    {
        if (is_null($this->location)) {
            return $this->belongsTo('location', Location::class, 'location_id');
        }
        return $this->location;
    }

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

    public function getCapacity(): int
    {
        return $this->location()->capacity;
    }
    
    public function getParticipantsCount(): int
    {
        return count($this->participants());
    }

    public function getCapacityRate(): float
    {
        return $this->getParticipantsCount() * 100 / $this->getCapacity();
    }

    public function getCapacityRatePercentage(): string
    {
        return ceil($this->getParticipantsCount() * 100 / $this->getCapacity())."%";
    }

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