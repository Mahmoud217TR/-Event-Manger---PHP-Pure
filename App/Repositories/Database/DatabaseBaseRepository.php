<?php

namespace App\Repositories\Database;

use App\Entities\Entity;
use App\Repositories\EntityRepository;

abstract class DatabaseBaseRepository implements EntityRepository
{
    abstract protected function class(): string;

    public function get(array $conditions = []): array
    {
        $query = isset($conditions[0]) ? $conditions[0] : '';
        $values = isset($conditions[1]) ? $conditions[1] : [];
        return $this->class()::query($query, $values);
    }

    public function find(int $id): ?Entity
    {
        return $this->class()::find($id);
    }

    public function create(array $options): Entity
    {
        return $this->class()::create($options);
    }

    public function update(int $id, array $options): bool
    {
        $entity = $this->find($id);
        if (is_null($entity)) {
            return false;
        }
        return $entity->update($options);
    }

    public function delete(int $id): bool
    {
        $entity = $this->find($id);
        if (is_null($entity)) {
            return false;
        }
        return $entity->delete($id);
    }
}