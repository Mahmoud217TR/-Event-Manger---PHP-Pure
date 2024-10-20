<?php

namespace Repositories\Database;

use Entities\Participant;
use Repositories\ParticipantRepository;

class DatabaseParticipantRepository extends DatabaseBaseRepository implements ParticipantRepository
{
    protected function class(): string
    {
        return Participant::class;
    }
}