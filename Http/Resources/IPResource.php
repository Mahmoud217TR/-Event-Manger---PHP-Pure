<?php

namespace Http\Resources;

use Entities\IP;

class IPResource extends Resource
{
    public function __construct(array|IP $resource)
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
            'ip_address' => $resource->ip_address,
            'blacklisted_at' => $resource->isBlacklisted() ? $resource->created_at->format('Y-m-d') : null,
            'whitelisted_at' => $resource->isWhitelisted() ? $resource->created_at->format('Y-m-d') : null,
        ];
    }

}
