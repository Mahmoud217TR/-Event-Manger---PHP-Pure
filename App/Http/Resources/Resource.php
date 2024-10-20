<?php

namespace App\Http\Resources;

use Closure;
use App\Entities\Entity;
use JsonSerializable;

abstract class Resource implements JsonSerializable
{
    protected array|Entity $resource;
    protected array $with = [];

    public function __construct(array|Entity $resource)
    {
        $this->resource = $resource;
    }

    public static function make(array|Entity $resource): static
    {
        return new static($resource);
    }

    public function with(array $with): static
    {
        $this->with = $with;
        return $this;
    }

    public function has(string $key): bool
    {
        return in_array($key, $this->with);
    }

    /**
     * Transform the entity into an array.
     */
    abstract public function toArray($resource): array;

    /**
     * Transform the entity into an array based on type.
     */
    public function resourceArray(): array
    {
        $array = [];
        if (is_array($this->resource)) {
            foreach ($this->resource as $item) {
                $array[] = array_filter($this->toArray($item), fn($value) => !is_null($value));
            }
        } else {
            $array = array_filter($this->toArray($this->resource), fn($value) => !is_null($value));
        }
        return $array;
    }

    /**
     * Convert to JSON format.
     */
    public function jsonSerialize(): mixed
    {
        return $this->resourceArray();   
    }
}
