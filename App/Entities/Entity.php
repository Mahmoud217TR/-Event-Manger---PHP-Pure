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
    
    /**
     * Creates and returns an entity instance from a database record.
     * 
     * @param array $record An associative array representing a database record.
     * 
     * @return static The entity instance created from the given record.
     */
    public abstract static function fromRecord(array $record): static;

    /**
     * Returns the name of the database table associated with the entity.
     * 
     * @return string The table name for the entity.
     */
    public abstract static function getTableName(): string;

    /**
     * Converts the current entity instance to an associative array.
     * 
     * @return array An array representation of the entity instance, typically used for serialization or output.
     */
    public abstract function toArray(): array;

    /**
     * Retrieves all records from the database table for the entity.
     * 
     * @return array An array of entity instances representing all records in the table.
     */
    public static function all(): array
    {
        return static::where([]);
    }

    /**
     * Retrieves records from the database based on specified conditions.
     * 
     * @param array $conditions An associative array of conditions where the key is the column and the value is the condition.
     * @param string $operator Logical operator to combine conditions (default is 'AND').
     * 
     * @return array An array of entity instances that match the specified conditions.
     */
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

    /**
     * Counts the number of records in the database based on a given condition.
     * 
     * @param string $whereQuery An optional WHERE clause for filtering records.
     * @param array $values Values to be bound to the query.
     * 
     * @return int The count of records matching the condition.
     */
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

    /**
     * Executes a custom query and returns the result.
     * 
     * @param string $whereQuery Optional WHERE clause or any additional SQL query parts.
     * @param array $values Values to be bound to the query.
     * @param string $select The SELECT clause (default is "SELECT * FROM").
     * @param int $fetch The fetch style for the PDO statement (default is PDO::FETCH_NAMED).
     * @param Closure|null $resolve An optional closure to handle the result set before returning.
     * 
     * @return mixed The result of the query, either an array of entities or a processed result if a closure is provided.
     */
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

    /**
     * Converts database records into entity instances.
     * 
     * @param array $records An array of database records to convert.
     * 
     * @return array An array of entity instances based on the given records.
     */
    public static function coerce($records): array
    {
        $entities = [];
        foreach ($records as $record) {
            $entity = static::fromRecord($record);
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     * Eager loads related entities for the current entity by specifying relationships.
     * 
     * @param array $relateds An array of relationships to load.
     * 
     * @return void
     */
    public static function with(array $relateds): void
    {
        foreach ($relateds as $related) {
            static::$related();
        }
    }

    /**
     * Finds a record by its primary key.
     * 
     * @param mixed $id The primary key value.
     * 
     * @return ?static The entity instance if found, or null if not.
     */
    public static function find(mixed $id): ?static
    {
        return static::findBy($id);
    }

    /**
     * Finds a record by a specified column and value.
     * 
     * @param mixed $value The value to search for.
     * @param string $column The column to search in (default is 'id').
     * @param string $operator The comparison operator (default is '=').
     * 
     * @return ?static The entity instance if found, or null if not.
     */
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

    /**
     * Creates a new record in the database and returns the corresponding entity instance.
     * 
     * @param array $options An associative array of column names and values for the new record.
     * 
     * @return static The newly created entity instance.
     */
    public static function create(array $options): static
    {
        $query = "INSERT INTO ".static::getTableName()." (";
        
        $options['created_at'] = (new DateTime())->format('Y-m-d H:i:s');

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

    /**
     * Updates the current entity's record in the database with new values.
     * 
     * @param array $options An associative array of column names and values to update.
     * 
     * @return true Returns true if the update is successful.
     */
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

    /**
     * Deletes the current entity's record from the database.
     * 
     * @return bool True if the deletion is successful, false otherwise.
     */
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

    /**
     * Serializes the entity to a JSON-compatible format.
     * 
     * @return mixed A JSON-serializable representation of the entity, with null values removed.
     */
    public function jsonSerialize(): mixed
    {
        return array_filter($this->toArray(), fn($value) => !is_null($value));   
    }

    /**
     * Adds placeholders for SQL query variables.
     * 
     * @param string $count The number of placeholders to add.
     * @param string $variable The placeholder format, default is "?, ". 
     *                         Each placeholder is separated by a comma and space.
     * 
     * @return string A string of placeholders (e.g., "?, ?, ?, " for a count of 3).
     */
    protected static function addVariables(string $count, string $variable = "?, "): string
    {
        return rtrim(str_repeat($variable, $count), ", ");
    }

    /**
     * Builds a SQL condition string from an associative array of conditions.
     * 
     * @param array $conditions An associative array where the key is the column name and the value is the condition.
     *              If the value is an array, the first element should be the operator (e.g., '=', '>', etc.)
     *              and the second element should be the value for the condition.
     * @param string $operator The logical operator (e.g., 'AND', 'OR') used to combine conditions.
     * @param array &$values This array will be populated with the values that correspond to the conditions.
     * 
     * @return string The constructed SQL condition string.
     */
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