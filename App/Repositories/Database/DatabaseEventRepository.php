<?php

namespace App\Repositories\Database;

use App\Entities\Event;
use App\Repositories\EventRepository;

class DatabaseEventRepository extends DatabaseBaseRepository implements EventRepository
{
    protected function class(): string
    {
        return Event::class;
    }
}