<?php

namespace App\Repositories\Database;

use App\Entities\IP;
use App\Repositories\IPRepository;

class DatabaseIPRepository extends DatabaseBaseRepository implements IPRepository
{
    protected function class(): string
    {
        return IP::class;
    }
}