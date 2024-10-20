<?php

namespace Repositories\Database;

use Entities\Location;
use Repositories\LocationRepository;

class DatabaseLocationRepository extends DatabaseBaseRepository implements LocationRepository
{
    protected function class(): string
    {
        return Location::class;
    }
}