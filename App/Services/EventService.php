<?php

namespace App\Services;

use DateTime;
use App\Entities\Event;
use App\Filters\EventFilter;
use App\Repositories\Database\DatabaseEventRepository;
use App\Repositories\EventRepository;

class EventService
{
    protected EventRepository $events;
    
    public function __construct()
    {
        $this->events = new DatabaseEventRepository();
    }

    /**
     * Get all events based on optional filters.
     *
     * @param EventFilter|null $filter The filter to apply (optional).
     * @return array<Event>
     */
    public function get(EventFilter $filter = null): array
    {
        $conditions = [];
        if ($filter) {
            $conditions = $filter->build();
        }

        return $this->events->get($conditions);
    }

    /**
     * Get an events based on id.
     *
     * @param int $id The event id.
     * @return Event
     */
    public function find(int $id): ?Event
    {
        return $this->events->find($id);
    }
    
    /**
     * Create a new event.
     * 
     * @param string $name The event name
     * @param DateTime $date The event date
     * @param int $location_id The event location id
     * @return Event
     */
    public function create(
        string $name,
        DateTime $date,
        int $location_id,
    ): Event {
        return $this->events->create([
            'name' => $name,
            'date' => $date->format('Y-m-d'),
            'location_id' => $location_id,
        ]);
    }

    /**
     * Update an existing event.
     * 
     * @param Event $event The event to be updated
     * @param string $name The event name
     * @param DateTime $date The event date
     * @param int $location_id The event location id
     * @return Event
     */
    public function update(
        Event $event,
        string $name,
        DateTime $date,
        int $location_id,
    ): Event {
        $this->events->update(
            $event->id,
            [
                'name' => $name,
                'date' => $date->format('Y-m-d'),
                'location_id' => $location_id,
            ]
        );

        return $event->fresh();
    }

    /**
     * Delete an existing event.
     *
     * @param Event $event The event to be deleted
     * @return bool
     */
    public function delete(Event $event): bool
    {
        return $this->events->delete($event->id);
    }
}
