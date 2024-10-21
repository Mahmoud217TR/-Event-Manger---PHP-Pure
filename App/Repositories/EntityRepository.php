<?php

namespace App\Repositories;

use App\Entities\Entity;

interface EntityRepository
{
    /**
     * Retrieve a list of entities based on specified conditions.
     *
     * This method allows you to fetch entities from the repository
     * with optional filtering conditions. If no conditions are provided,
     * it returns all entities.
     *
     * @param array $conditions An associative array of conditions to filter entities.
     * @return array An array of Entity objects that match the given conditions.
     */
    public function get(array $conditions = []): array;

    /**
     * Find a single entity by its unique identifier (ID).
     * 
     * @param int $id The id of the entity to find.
     * @return Entity|null The found entity or null if no entity matches the given ID.
     */
    public function find(int $id): ?Entity;

    /**
     * Create a new entity in the repository.
     *
     * @param array $options An associative array of options to create the entity.
     * @return Entity The created Entity object.
     */
    public function create(array $options): Entity;

    /**
     * Update an existing entity identified by its unique identifier (ID).
     *
     * @param int $id The id of the entity to update.
     * @param array $options An associative array of options to update the entity.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update(int $id, array $options): bool;

    /**
     * Delete an entity identified by its unique identifier (ID).
     * 
     * @param int $id The id of the entity to delete.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function delete(int $id): bool;
}