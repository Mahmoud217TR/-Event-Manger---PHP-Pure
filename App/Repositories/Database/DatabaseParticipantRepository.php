<?php

namespace App\Repositories\Database;

use App\Entities\Participant;
use App\Repositories\ParticipantRepository;

class DatabaseParticipantRepository extends DatabaseBaseRepository implements ParticipantRepository
{
    protected function class(): string
    {
        return Participant::class;
    }
}