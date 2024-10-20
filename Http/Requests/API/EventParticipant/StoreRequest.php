<?php

namespace Http\Requests\API\EventParticipant;

use DateTime;
use Entities\Event;
use Entities\EventParticipant;
use Entities\Participant;
use Http\Requests\API\APIFormRequest;
use Http\Validator;

class StoreRequest extends APIFormRequest
{
    /**
     * Create a new Validator instance for this request's data.
     *
     * @return Validator
     */
    public static function validator(array $data): Validator
    {
        $event = Event::find($data['event_id']);
        $capacity = PHP_INT_MAX; 
        if ($event) {
            $capacity = $event->getCapacity();
        }
        return Validator::make($data)
            ->required('event_id')
            ->integer('event_id')
            ->exists('event_id', 'events')
            ->required('participant_id')
            ->integer('participant_id')
            ->exists('participant_id', 'participants')
            ->uniqueOn(
                ['event_id', 'participant_id'],
                'event_participants',
                ['event_id', 'participant_id'],
                null,
                null,
                "Already reserved a seat for the event"
            )
            ->count(
                'capacity',
                EventParticipant::class,
                "WHERE event_id = ?",
                [$data['event_id']],
                fn($count) => $count+1 < $capacity,
                "The event reached it's maximum capacity"
            );
    }

    public function getEvent(): Event
    {
        return Event::find($this->get('event_id'));
    }

    public function getParticipant(): Participant
    {
        return Participant::find($this->get('participant_id'));
    }
}
