<?php

namespace App\Entities;

use Closure;
use DateTime;
use JsonSerializable;
use PDO;

abstract class Entity implements JsonSerializable
{
    public int $id;
    protected static array $joins = [];
    
    public abstract static function fromRecord(array $record): static;
    public abstract static function getTableName(): string;
    public abstract function toArray(): array;

    public static function all(): array
    {
        return static::where([]);
    }

    public static function where(array $conditions = [], string $operator = 'AND'): array
    {
        $query = "SELECT * FROM ".static::getTableName();
        $values = [];

        if (!empty(static::$joins)) {
            foreach (static::$joins as $join) {
                $query .= " {$join['type']} JOIN {$join['table']} ON {$join['on']}";
            }
        }

        if (!empty($conditions)) {
            $query .= " WHERE ";
            $query .= static::buildConditions($conditions, $operator, $values);
        }

        $statement = pdo()->prepare($query);
        $statement->execute($values);

        $records = $statement->fetchAll(PDO::FETCH_NAMED);

        return static::coerce($records);
    }

    public static function count(string $whereQuery = '', array $values = []): int
    {
        return static::query(
            $whereQuery,
            $values,
            "SELECT COUNT(*) FROM",
            PDO::FETCH_ASSOC,
            fn($result) => isset($result[0]['COUNT(*)']) ? $result[0]['COUNT(*)'] : 0
        );
    }

    public static function query(
        string $whereQuery,
        array $values = [],
        string $select = "SELECT * FROM",
        int $fetch = PDO::FETCH_NAMED,
        Closure $resolve = null
    ) {
        $query = "{$select} ".static::getTableName();

        if (!empty(static::$joins)) {
            foreach (static::$joins as $join) {
                $query .= " {$join['type']} JOIN {$join['table']} ON {$join['on']}";
            }
        }
        
        if (!empty($whereQuery)) {
            $query .= " {$whereQuery}";
        }

        $statement = pdo()->prepare($query);
        $statement->execute($values);

        $records = $statement->fetchAll($fetch);
        
        if ($resolve) {
            return $resolve($records);
        }
        return static::coerce($records);
    }

    public static function coerce($records): array
    {
        $entities = [];
        foreach ($records as $record) {
            $entity = static::fromRecord($record);
            $entities[] = $entity;
        }

        return $entities;
    }

    public static function with(array $relateds): void
    {
        foreach ($relateds as $related) {
            static::$related();
        }
    }

    public static function find(mixed $id): ?static
    {
        return static::findBy($id);
    }

    public static function findBy(mixed $value, string $column = 'id', string $operator = '='): ?static
    {
        $results = static::query("WHERE {$column} {$operator} ?", [$value]);
        return isset($results[0]) ? $results[0] : null;
    }

    /**
     * Returns a fresh entity instance from database.
     * 
     * @return static
     */
    public function fresh(): static
    {
        return $this->find($this->id);
    }

    /**
     * Create a relationship: hasOne.
     * 
     * This will now return a single related entity.
     * 
     * @param string $attribute Attribute to store the related entity (e.g., 'profile').
     * @param string $relatedEntity The related entity class (e.g., Profile::class).
     * @param string $foreignKey The foreign key on the related entity table (e.g., user_id).
     * @param string $localKey The local entity key (e.g., id for users).
     * @return ?object The related entity or null if not found.
     */
    public function hasOne(string $attribute, string $relatedEntity, string $foreignKey, string $localKey = 'id'): ?Entity
    {
        $relatedTable = $relatedEntity::getTableName();
        $query = "SELECT * FROM {$relatedTable} WHERE {$foreignKey} = ? LIMIT 1";

        $statement = pdo()->prepare($query);
        $statement->execute([$this->$localKey]);

        $record = $statement->fetch(PDO::FETCH_ASSOC);
        
        if ($record) {
            $relatedObject = $relatedEntity::fromRecord($record);
            $this->$attribute = $relatedObject;
            return $relatedObject;
        }

        $this->$attribute = null;
        return null;
    }


    /**
     * Create a relationship: belongsTo.
     * 
     * This will now return the parent entity in a belongs-to relationship.
     * 
     * @param string $attribute Attribute to store the related entity (e.g., 'user').
     * @param string $relatedEntity The related entity class (e.g., User::class).
     * @param string $foreignKey The foreign key in this entity's table (e.g., user_id).
     * @param string $ownerKey The key on the related entity table (e.g., id).
     * @return ?object The related entity or null if not found.
     */
    public function belongsTo(string $attribute, string $relatedEntity, string $foreignKey, string $ownerKey = 'id'): ?Entity
    {
        $relatedTable = $relatedEntity::getTableName();
        $query = "SELECT * FROM {$relatedTable} WHERE {$ownerKey} = ? LIMIT 1";

        $statement = pdo()->prepare($query);
        $statement->execute([$this->$foreignKey]);

        $record = $statement->fetch(PDO::FETCH_ASSOC);
        
        if ($record) {
            $relatedObject = $relatedEntity::fromRecord($record);
            $this->$attribute = $relatedObject;
            return $relatedObject;
        }

        $this->$attribute = null;
        return null;
    }

    /**
     * Create a relationship: hasMany.
     * 
     * This method establishes a one-to-many relationship between entities. 
     * It will now return an array of related entities.
     * 
     * @param string $attribute The name of the property where the related entities will be stored.
     * @param string $relatedEntity The class name of the related entity.
     * @param string $foreignKey The foreign key in the related entity's table that links to the current entity.
     * @param string $localKey The primary key in the current entity's table. Defaults to 'id'.
     * @return array
     */
    public function hasMany(string $attribute, string $relatedEntity, string $foreignKey, string $localKey = 'id'): array
    {
        $relatedTable = $relatedEntity::getTableName();
        $query = "SELECT * FROM {$relatedTable} WHERE {$foreignKey} = ?";
        
        $statement = pdo()->prepare($query);
        $statement->execute([$this->$localKey]);

        $records = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $relatedEntities = [];
        foreach ($records as $record) {
            $relatedEntities[] = $relatedEntity::fromRecord($record);
        }

        $this->$attribute = $relatedEntities;

        return $relatedEntities;
    }


    /**
     * Create a relationship: belongsToMany.
     * 
     * This handles a many-to-many relationship via a pivot table.
     * 
     * @param string $attribute Attribute to store the related entities (e.g., 'participants').
     * @param string $relatedEntity The related entity class (e.g., Participant::class).
     * @param string $pivotTable The name of the pivot table (e.g., event_participants).
     * @param string $foreignKey The foreign key on the pivot table for this entity (e.g., event_id).
     * @param string $relatedKey The foreign key on the pivot table for the related entity (e.g., participant_id).
     * @param string $localKey The local entity key (e.g., id for events).
     * @param string $relatedEntityKey The key on the related entity (e.g., id for participants).
     * 
     * @return array
     */
    public function belongsToMany(
        string $attribute, 
        string $relatedEntity, 
        string $pivotTable, 
        string $foreignKey, 
        string $relatedKey, 
        string $localKey = 'id', 
        string $relatedEntityKey = 'id'
    ): array {
        $query = "
            SELECT {$relatedEntity::getTableName()}.* 
            FROM {$pivotTable}
            JOIN {$relatedEntity::getTableName()} 
            ON {$pivotTable}.{$relatedKey} = {$relatedEntity::getTableName()}.{$relatedEntityKey}
            WHERE {$pivotTable}.{$foreignKey} = ?
        ";

        $statement = pdo()->prepare($query);
        $statement->execute([$this->$localKey]);

        $records = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $relatedEntities = [];
        foreach ($records as $record) {
            $relatedEntities[] = $relatedEntity::fromRecord($record);
        }

        $this->$attribute = $relatedEntities;

        return $relatedEntities;
    }


    public static function create(array $options): static
    {
        $query = "INSERT INTO ".static::getTableName()." (";
        
        $options['created_at'] = (new DateTime())->format('Y-m-d');

        foreach ($options as $key => $value) {
            $query .= "{$key}, ";    
        }

        $query = rtrim($query, ', ');
        $query .= ") VALUES (";
        $query .= static::addVariables(count($options));
        $query .= ")";

        $statement = pdo()->prepare($query);
        $statement->execute(array_values($options));

        return static::fromRecord(array_merge(
            $options,
            ['id' => pdo()->lastInsertId()]
        ));
    }

    public function update(array $options): true
    {
        $query = "UPDATE ".static::getTableName()." SET ";
        
        foreach ($options as $key => $value) {
            $query .= "$key = ? , ";
            $values[] = $value;
        }
        
        $query = rtrim($query, ' ,');
        $query .= " WHERE id = ?";
        $values[] = $this->id;

        $statement = pdo()->prepare($query);

        $statement->execute($values);

        return true;
    }

    public function delete(): bool
    {
        if (isset($this->id)) {
            $statement = pdo()->prepare("DELETE FROM ".static::getTableName()." WHERE id = ?");
            $statement->execute([
                $this->id
            ]);
        }
        return true;
    }

    public function jsonSerialize(): mixed
    {
        return array_filter($this->toArray(), fn($value) => !is_null($value));   
    }

    protected static function addVariables(string $count, string $variable = "?, "): string
    {
        return rtrim(str_repeat($variable, $count), ", ");
    }

    protected static function buildConditions(array $conditions, string $operator, array &$values): string
    {
        $queryParts = [];

        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $queryParts[] = "{$key} {$value[0]} ?";
                $values[] = $value[1];
            } else {
                $queryParts[] = "{$key} = ?";
                $values[] = $value;
            }
        }

        return implode(" {$operator} ", $queryParts);
    }
}