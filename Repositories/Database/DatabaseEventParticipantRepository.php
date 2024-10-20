<?php

namespace Repositories\Database;

use Entities\EventParticipant;
use Repositories\EventParticipantRepository;

class DatabaseEventParticipantRepository extends DatabaseBaseRepository implements EventParticipantRepository
{
    protected function class(): string
    {
        return EventParticipant::class;
    }
}