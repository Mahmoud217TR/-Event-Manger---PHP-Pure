<?php

namespace Repositories;

use Entities\Entity;

interface EntityRepository
{
    public function get(array $conditions = []): array;
    public function find(int $id): ?Entity;
    public function create(array $options): Entity;
    public function update(int $id, array $options): bool;
    public function delete(int $id): bool;
}