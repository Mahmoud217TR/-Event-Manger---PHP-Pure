<?php

namespace App\Repositories\Database;

use App\Entities\Location;
use App\Repositories\LocationRepository;

class DatabaseLocationRepository extends DatabaseBaseRepository implements LocationRepository
{
    protected function class(): string
    {
        return Location::class;
    }
}