<?php

namespace Repositories\Database;

use Entities\Event;
use Repositories\EventRepository;

class DatabaseEventRepository extends DatabaseBaseRepository implements EventRepository
{
    protected function class(): string
    {
        return Event::class;
    }
}