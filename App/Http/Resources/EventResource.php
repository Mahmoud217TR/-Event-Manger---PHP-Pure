<?php

namespace App\Http\Resources;

use App\Entities\Event;

class EventResource extends Resource
{
    public function __construct(array|Event $resource)
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
            'name' => $resource->name,
            'date' => $resource->date->format('Y-m-d'),
            'location_id' => $resource->location_id,
            'created_at' => $resource->created_at->format('Y-m-d'),
            'location' => $this->has('location') ? LocationResource::make($resource->location()) : null,
            'participants' => $this->has('participants') ? ParticipantResource::make($resource->participants()) : null,
            'visitors' => $this->has('visitors') ? $resource->getEventParticipantsCount() : null,
        ];
    }

}
