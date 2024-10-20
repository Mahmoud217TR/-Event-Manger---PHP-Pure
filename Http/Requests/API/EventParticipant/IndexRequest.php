<?php

namespace Http\Requests\API\EventParticipant;

use Filters\EventParticipantFilter;
use Http\Requests\API\APIFormRequest;
use Http\Validator;

class IndexRequest extends APIFormRequest
{
    /**
     * Create a new Validator instance for this request's data.
     *
     * @return Validator
     */
    public static function validator(array $data): Validator
    {
        return Validator::make($data)
            ->integer('event_id')
            ->exists('event_id', 'events')
            ->integer('participant_id')
            ->exists('participant_id', 'participants');
    }

    public function filter(): EventParticipantFilter
    {
        return EventParticipantFilter::make()
            ->when(
                $this->has('event_id'),
                fn (EventParticipantFilter $filter) => $filter->whereEvent($this->get('event_id'))
            )
            ->when(
                $this->has('participant_id'),
                fn (EventParticipantFilter $filter) => $filter->whereParticipant($this->get('participant_id'))
            );
    }
}
