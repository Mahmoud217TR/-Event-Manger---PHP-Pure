<?php

namespace Repositories\Database;

use Entities\IP;
use Repositories\IPRepository;

class DatabaseIPRepository extends DatabaseBaseRepository implements IPRepository
{
    protected function class(): string
    {
        return IP::class;
    }
}