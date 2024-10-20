<?php

namespace App\Services;

use App\Entities\Location;
use App\Filters\LocationFilter;
use App\Repositories\Database\DatabaseLocationRepository;
use App\Repositories\LocationRepository;

class LocationService
{
    protected LocationRepository $locations;
    
    public function __construct()
    {
        $this->locations = new DatabaseLocationRepository();
    }

    /**
     * Get all locations based on optional filters.
     *
     * @param LocationFilter|null $filter The filter to apply (optional).
     * @return array<Location>
     */
    public function get(LocationFilter $filter = null): array
    {
        $conditions = [];
        if ($filter) {
            $conditions = $filter->build();
        }
        return $this->locations->get($conditions);
    }

    /**
     * Get an locations based on id.
     *
     * @param int $id The event id.
     * @return Location
     */
    public function find(int $id): ?Location
    {
        return $this->locations->find($id);
    }
    
    /**
     * Create a new location.
     * 
     * @param string $name The location name
     * @param string $address The location address
     * @param int $capacity The location capacity
     * @return Location
     */
    public function create(
        string $name,
        string $address,
        int $capacity
    ): Location {
        return $this->locations->create([
            'name' => $name,
            'address' => $address,
            'capacity' => $capacity,
        ]);
    }

    /**
     * Update an existing location.
     * 
     * @param Location $location The location to be updated
     * @param string $name The location name
     * @param string $address The location address
     * @param int $capacity The location capacity
     * @return Location
     */
    public function update(
        Location $location,
        string $name,
        string $address,
        int $capacity
    ): Location {
        $this->locations->update(
            $location->id,
            [
                'name' => $name,
                'address' => $address,
                'capacity' => $capacity,
            ]
        );

        return $location->fresh();
    }

    /**
     * Delete an existing location.
     *
     * @param Location $location The location to be deleted
     * @return bool
     */
    public function delete(Location $location): bool
    {
        $events = new EventService();
        foreach($location->events() as $event) {
            $events->delete($event);
        }
        return $this->locations->delete($location->id);
    }
}
