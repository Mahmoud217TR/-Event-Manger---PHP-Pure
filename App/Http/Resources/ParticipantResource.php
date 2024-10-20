<?php

namespace App\Http\Resources;

use App\Entities\Participant;

class ParticipantResource extends Resource
{
    public function __construct(array|Participant $resource)
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
            'email' => $resource->email,
            'created_at' => $resource->created_at->format('Y-m-d'),
        ];
    }

}
