<?php

namespace Services;

use DateTime;
use Entities\Event;
use Entities\EventParticipant;
use Entities\Participant;
use Filters\EventParticipantFilter;
use Repositories\Database\DatabaseEventParticipantRepository;
use Repositories\EventParticipantRepository;

class EventParticipantService
{
    protected EventParticipantRepository $eventParticipants;
    
    public function __construct()
    {
        $this->eventParticipants = new DatabaseEventParticipantRepository();
    }

    /**
     * Get all eventParticipants based on optional filters.
     *
     * @param EventParticipantFilter|null $filter The filter to apply (optional).
     * @return array<EventParticipant>
     */
    public function get(EventParticipantFilter $filter = null): array
    {
        $conditions = [];
        if ($filter) {
            $conditions = $filter->build();
        }

        return $this->eventParticipants->get($conditions);
    }

    /**
     * Get an event participants based on id.
     *
     * @param int $id The eventParticipant id.
     * @return EventParticipant
     */
    public function find(int $id): ?EventParticipant
    {
        return $this->eventParticipants->find($id);
    }
    
    /**
     * Create a new event participant.
     * 
     * @param Event $event The event id
     * @param Participant $participant The participant id
     * @return EventParticipant
     */
    public function create(
        Event $event,
        Participant $participant
    ): EventParticipant {
        return $this->eventParticipants->create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
        ]);
    }

    /**
     * Delete an existing event participant.
     *
     * @param EventParticipant $eventParticipant The eventParticipant to be deleted
     * @return bool
     */
    public function delete(EventParticipant $eventParticipant): bool
    {
        return $this->eventParticipants->delete($eventParticipant->id);
    }
}
