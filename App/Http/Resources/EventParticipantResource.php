<?php

namespace App\Http\Resources;

use App\Entities\EventParticipant;

class EventParticipantResource extends Resource
{
    public function __construct(array|EventParticipant $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the entity into an array.
     */
    public function toArray($resource): array
    {
        return [
            'id' => $resource->id,
            'event' => $this->has('event') ? EventResource::make($resource->event()) : null,
            'participant' => $this->has('participant') ? ParticipantResource::make($resource->participant()) : null,
            'registered_at' => $resource->created_at->format('Y-m-d'),
        ];
    }

}
