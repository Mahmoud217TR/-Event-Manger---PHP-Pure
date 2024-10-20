<?php

namespace App\Entities;

use DateTime;

class Location extends Entity
{
    public string $name;
    public string $address;
    public int $capacity;
    public DateTime $created_at;
    public ?array $events = null;
    
    public function __construct(
        int $id,
        string $name,
        string $address,
        int $capacity,
        DateTime $created_at,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->capacity = $capacity;
        $this->created_at = $created_at;
    }

    public static function fromRecord(array $record): static
    {
        return new static(
            $record['id'],
            $record['name'],
            $record['address'],
            $record['capacity'],
            new DateTime($record['created_at']),
        );
    }

    public static function getTableName(): string
    {
        return 'locations';
    }

    public function events(): array
    {
        if (is_null($this->events)) {
            return $this->hasMany('events', Event::class, 'location_id');
        }
        return $this->events;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'capacity' => $this->capacity,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}