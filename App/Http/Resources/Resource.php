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

    /**
     * Static factory method to create a new Resource instance.
     * 
     * This method creates a new instance of the Resource class, initializing it with the provided
     * resource, which can either be a single Entity or an array of Entity objects.
     * 
     * @param array|Entity $resource The resource or resources to be wrapped by the Resource class.
     * @return static A new instance of the Resource class.
     */
    public static function make(array|Entity $resource): static
    {
        return new static($resource);
    }


    /**
     * Add additional data to be included with the resource response.
     * 
     * This method sets additional data (specified by the `$with` array) that will be included
     * with the resource when it's serialized or converted to an array.
     * 
     * @param array $with An array of keys specifying additional data to include with the resource.
     * @return static The current instance of the Resource class, allowing for method chaining.
     */
    public function with(array $with): static
    {
        $this->with = $with;
        return $this;
    }

    /**
     * Check if a specific key exists in the additional data to be included.
     * 
     * This method checks whether a particular key exists in the `$with` array, which contains
     * additional data that is to be included with the resource when it's transformed.
     * 
     * @param string $key The key to check for in the additional data.
     * @return bool Returns `true` if the key exists in the `$with` array, `false` otherwise.
     */
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
