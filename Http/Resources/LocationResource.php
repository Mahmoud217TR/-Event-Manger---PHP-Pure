<?php

namespace Http\Resources;

use Entities\Location;

class LocationResource extends Resource
{
    public function __construct(array|Location $resource)
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
            'address' => $resource->address,
            'capacity' => $resource->capacity,
            'created_at' => $resource->created_at->format('Y-m-d')
        ];
    }

}
