<?php

namespace App\Repositories\Database;

use App\Entities\EventParticipant;
use App\Repositories\EventParticipantRepository;

class DatabaseEventParticipantRepository extends DatabaseBaseRepository implements EventParticipantRepository
{
    protected function class(): string
    {
        return EventParticipant::class;
    }
}