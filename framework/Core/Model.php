<?php

namespace Framework\Core;

use PDO;
use PDOException;
use Framework\Core\SoftDeletes;
use Framework\Database\Connection;
use Framework\Events\EventDispatcher;

abstract class Model
{
    use SoftDeletes;
    protected static $dispatcher;

    public static function setEventDispatcher(EventDispatcher $dispatcher): void
    {
        self::$dispatcher = $dispatcher;
    }

    protected function fireEvent(string $event, ...$args): void
    {
        if (self::$dispatcher) {
            self::$dispatcher->dispatch($event, ...$args);
        }
    }

    
    protected $table;

    protected $query = '';
    protected $bindings = [];
    protected $wheres = [];
    protected $limit = null;
    protected $offset = null;
    protected $selects = ['*'];
    protected $eagerLoads = [];
    protected $joins = []; // New property for join clauses
    protected $orders = []; // New property for orderBy
    protected $groups = []; // New property for groupBy
    protected $havings = []; // New property for having
    protected $subqueries = []; // New property for subqueries
    protected $unionQueries = []; // New property for union queries

    public function __construct()
    {
        $this->resetQuery();
    }

    protected function getPdo(): PDO
    {
        return Connection::getInstance();
    }

    protected function resetQuery(): void
    {
        $this->query = '';
        $this->bindings = [];
        $this->wheres = [];
        $this->limit = null;
        $this->offset = null;
        $this->selects = ['*'];
        $this->eagerLoads = [];
        $this->orders = []; // Reset orderBy
        $this->groups = []; // Reset groupBy;
        $this->havings = []; // Reset having
        $this->subqueries = []; // Reset subqueries
        $this->unionQueries = []; // Reset union queries
    }

    public function select(array $columns = ['*']): self
    {
        $this->selects = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->wheres[] = ['type' => 'basic', 'column' => $column, 'operator' => $operator, 'value' => $value];
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $this->wheres[] = ['type' => 'in', 'column' => $column, 'values' => $values];
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->orders[] = ['column' => $column, 'direction' => strtoupper($direction)];
        return $this;
    }

    public function groupBy(string ...$columns): self
    {
        $this->groups = array_merge($this->groups, $columns);
        return $this;
    }

    /**
     * Specify relationships to be eager loaded.
     * @param string|array $relations
     * @return $this
     */
    public function with($relations): self
    {
        $this->eagerLoads = array_merge($this->eagerLoads, (array) $relations);
        return $this;
    }

    public function having(string $column, string $operator, $value): self
    {
        $this->havings[] = compact('column', 'operator', 'value');
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self
    {
        $this->joins[] = compact('type', 'table', 'first', 'operator', 'second');
        return $this;
    }

    public function whereExists(callable $callback): self
    {
        $subqueryModel = new static();
        $callback($subqueryModel);
        $this->wheres[] = ['type' => 'exists', 'query' => $subqueryModel->buildQuery(), 'bindings' => $subqueryModel->bindings];
        return $this;
    }

    public function union(self $query): self
    {
        $this->unionQueries[] = $query;
        return $this;
    }

    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function whereRaw(string $sql, array $bindings = []): self
    {
        $this->wheres[] = ['type' => 'raw', 'sql' => $sql, 'bindings' => $bindings];
        return $this;
    }

    protected function buildQuery(): string
    {
        $mainQuery = "SELECT " . implode(', ', $this->selects) . " FROM {$this->table}";

        foreach ($this->joins as $join) {
            $mainQuery .= " {$join['type']} JOIN {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
        }

        $whereClauses = [];
        $this->bindings = []; // Reset bindings for each build

        // Handle soft deletes
        if (property_exists($this, 'deleted_at')) {
            if ($this->onlyTrashed) {
                $whereClauses[] = "deleted_at IS NOT NULL";
            } elseif (!$this->withTrashed) {
                $whereClauses[] = "deleted_at IS NULL";
            }
        }

        foreach ($this->wheres as $where) {
            if ($where['type'] === 'basic') {
                $paramName = ':' . $where['column'] . uniqid(); // Unique param name
                $whereClauses[] = "{$where['column']} {$where['operator']} {$paramName}";
                $this->bindings[$paramName] = $where['value'];
            } elseif ($where['type'] === 'in') {
                $placeholders = implode(', ', array_fill(0, count($where['values']), '?'));
                $whereClauses[] = "{$where['column']} IN ({$placeholders})";
                $this->bindings = array_merge($this->bindings, $where['values']);
            } elseif ($where['type'] === 'exists') {
                $whereClauses[] = "EXISTS ({$where['query']})";
                $this->bindings = array_merge($this->bindings, $where['bindings']);
            } elseif ($where['type'] === 'raw') {
                $whereClauses[] = $where['sql'];
                $this->bindings = array_merge($this->bindings, $where['bindings']);
            }
        }

        if (!empty($whereClauses)) {
            $mainQuery .= " WHERE " . implode(' AND ', $whereClauses);
        }

        if (!empty($this->groups)) {
            $mainQuery .= " GROUP BY " . implode(', ', $this->groups);
        }

        if (!empty($this->havings)) {
            $mainQuery .= " HAVING ";
            $havingClauses = [];
            foreach ($this->havings as $index => $having) {
                $paramName = ':having' . $index;
                $havingClauses[] = "{$having['column']} {$having['operator']} {$paramName}";
                $this->bindings[$paramName] = $having['value'];
            }
            $mainQuery .= implode(' AND ', $havingClauses);
        }

        if (!empty($this->orders)) {
            $mainQuery .= " ORDER BY ";
            $orderClauses = [];
            foreach ($this->orders as $order) {
                $orderClauses[] = "{$order['column']} {$order['direction']}";
            }
            $mainQuery .= implode(', ', $orderClauses);
        }

        if ($this->limit !== null) {
            $mainQuery .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $mainQuery .= " OFFSET {$this->offset}";
        }

        $fullQuery = $mainQuery;

        foreach ($this->unionQueries as $unionQuery) {
            $fullQuery .= " UNION ALL (" . $unionQuery->buildQuery() . ")";
            $this->bindings = array_merge($this->bindings, $unionQuery->bindings);
        }

        return $fullQuery;
    }

    /**
     * Execute the query and return hydrated model objects, with eager loading.
     * @return static[]
     */
    public function get(): array
    {
        $sql = $this->buildQuery();
        $stmt = $this->getPdo()->prepare($sql);

        // Determine if bindings are named or positional
        $isNamedBindings = array_keys($this->bindings) !== range(0, count($this->bindings) - 1);

        if ($isNamedBindings) {
            $stmt->execute($this->bindings);
        } else {
            // For positional bindings (like from whereIn), execute with values directly
            $stmt->execute(array_values($this->bindings));
        }

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->resetQuery();

        $hydratedResults = [];
        foreach ($results as $row) {
            $hydratedResults[] = (new static())->fill($row);
        }

        // Eager loading
        if (!empty($this->eagerLoads) && !empty($hydratedResults)) {
            foreach ($this->eagerLoads as $relationMethod) {
                if (method_exists($this, $relationMethod)) {
                    // Get the local keys from the parent models
                    $localKeys = array_column($hydratedResults, 'id'); // Assuming 'id' is the local key

                    if (empty($localKeys)) {
                        continue; // No parent models to eager load for
                    }

                    // Call the relationship method to get the related model query builder
                    /** @var Model $relationQueryBuilder */
                    $relationQueryBuilder = $this->$relationMethod();

                    if (!($relationQueryBuilder instanceof Model)) {
                        throw new \Exception("Relationship method '{$relationMethod}' must return an instance of Model.");
                    }

                    // Get foreign key from the relationData set by hasMany, hasOne or belongsTo
                    $foreignKey = $relationQueryBuilder->getRelationData()['foreign_key'] ?? null;
                    $relationType = $relationQueryBuilder->getRelationData()['type'] ?? null;

                    if (!$foreignKey) {
                        throw new \Exception("Foreign key not defined for relation '{$relationMethod}'.");
                    }

                    if ($relationType === 'belongsToMany') {
                        $pivotTable = $relationQueryBuilder->getRelationData()['pivot_table'];
                        $foreignPivotKey = $relationQueryBuilder->getRelationData()['foreign_pivot_key'];
                        $relatedPivotKey = $relationQueryBuilder->getRelationData()['related_pivot_key'];
                        $relatedKey = $relationQueryBuilder->getRelationData()['related_key'];

                        // Fetch pivot data
                        $pivotStmt = $this->pdo->prepare("SELECT * FROM {$pivotTable} WHERE {$foreignPivotKey} IN (" . implode(', ', array_fill(0, count($localKeys), '?')) . ")");
                        $pivotStmt->execute($localKeys);
                        $pivotData = $pivotStmt->fetchAll(PDO::FETCH_ASSOC);

                        $relatedIds = array_column($pivotData, $relatedPivotKey);
                        if (empty($relatedIds)) {
                            continue;
                        }

                        // Fetch related models
                        $relatedData = $relationQueryBuilder->whereIn($relatedKey, $relatedIds)->get();

                        $groupedRelated = [];
                        foreach ($relatedData as $relatedItem) {
                            $groupedRelated[$relatedItem->$relatedKey] = $relatedItem;
                        }

                        // Attach related models via pivot
                        foreach ($hydratedResults as $parentModel) {
                            $parentModel->$relationMethod = [];
                            foreach ($pivotData as $pivotRow) {
                                if ($pivotRow[$foreignPivotKey] === $parentModel->id && isset($groupedRelated[$pivotRow[$relatedPivotKey]])) {
                                    $parentModel->{$relationMethod}[] = $groupedRelated[$pivotRow[$relatedPivotKey]];
                                }
                            }
                        }
                    } else {
                        // Existing eager loading logic for hasMany/hasOne/belongsTo
                        $relatedData = $relationQueryBuilder->whereIn($foreignKey, $localKeys)->get();

                        $groupedRelated = [];
                        foreach ($relatedData as $relatedItem) {
                            if (property_exists($relatedItem, $foreignKey)) {
                                $groupedRelated[$relatedItem->$foreignKey][] = $relatedItem;
                            }
                        }

                        foreach ($hydratedResults as $parentModel) {
                            if (isset($relationQueryBuilder->getRelationData()['type']) && $relationQueryBuilder->getRelationData()['type'] === 'hasOne') {
                                $parentModel->$relationMethod = $groupedRelated[$parentModel->id][0] ?? null;
                            } else {
                                $parentModel->$relationMethod = $groupedRelated[$parentModel->id] ?? [];
                            }
                        }
                    }
                }
            }
        }

        return $hydratedResults;
    }

    /**
     * Execute the query and return the first hydrated model object.
     * @return static|null
     */
    public function first(): ?static
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * Fill the model with an array of attributes.
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    // Existing CRUD methods, adapted to use the new query builder where applicable
    /**
     * @return static[]
     */
    public static function all(): array
    {
        return (new static())->get();
    }

    /**
     * @param int $id
     * @return static|null
     */
    public static function find(int $id): ?static
    {
        return (new static())->where('id', '=', $id)->first();
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return static|null
     */
    public static function findBy(string $column, $value): ?static
    {
        return (new static())->where($column, '=', $value)->first();
    }

    /**
     * Define a one-to-many relationship.
     * @param string $relatedModel The class name of the related model.
     * @param string|null $foreignKey The foreign key on the related model (e.g., 'user_id'). Defaults to {current_model_name}_id.
     * @param string $localKey The local key on the current model (e.g., 'id').
     * @return Model
     */
    public function hasMany(string $relatedModel, string $foreignKey = null, string $localKey = 'id'): Model
    {
        // Infer foreign key if not provided
        if ($foreignKey === null) {
            $parts = explode('\\', static::class);
            $foreignKey = strtolower(end($parts)) . '_id';
        }

        /** @var Model $relatedInstance */
        $relatedInstance = new $relatedModel();
        // Store relationship details for eager loading
        $relatedInstance->setRelationData('hasMany', $this->table, $localKey, $foreignKey);
        return $relatedInstance;
    }

    /**
     * Define a one-to-one relationship.
     * @param string $relatedModel The class name of the related model.
     * @param string|null $foreignKey The foreign key on the related model (e.g., 'user_id'). Defaults to {current_model_name}_id.
     * @param string $localKey The local key on the current model (e.g., 'id').
     * @return Model
     */
    public function hasOne(string $relatedModel, string $foreignKey = null, string $localKey = 'id'): Model
    {
        // Infer foreign key if not provided
        if ($foreignKey === null) {
            $parts = explode('\\', static::class);
            $foreignKey = strtolower(end($parts)) . '_id';
        }

        /** @var Model $relatedInstance */
        $relatedInstance = new $relatedModel();
        // Store relationship details for eager loading
        $relatedInstance->setRelationData('hasOne', $this->table, $localKey, $foreignKey);
        return $relatedInstance->limit(1);
    }

    /**
     * Define an inverse one-to-one or many-to-one relationship.
     * @param string $relatedModel The class name of the related model.
     * @param string|null $foreignKey The foreign key on the current model (e.g., 'user_id'). Defaults to {related_model_name}_id.
     * @param string $ownerKey The key on the related model (e.g., 'id').
     * @return Model
     */
    public function belongsTo(string $relatedModel, string $foreignKey = null, string $ownerKey = 'id'): Model
    {
        // Infer foreign key if not provided
        if ($foreignKey === null) {
            $parts = explode('\\', $relatedModel);
            $foreignKey = strtolower(end($parts)) . '_id';
        }

        /** @var Model $relatedInstance */
        $relatedInstance = new $relatedModel();
        // Store relationship details for eager loading
        $relatedInstance->setRelationData('belongsTo', $this->table, $foreignKey, $ownerKey);
        return $relatedInstance->limit(1);
    }

    /**
     * Define a many-to-many relationship.
     * @param string $relatedModel The class name of the related model.
     * @param string|null $pivotTable The name of the intermediate table. Defaults to alphabetical order of model names.
     * @param string|null $foreignPivotKey The foreign key of the current model on the pivot table.
     * @param string|null $relatedPivotKey The foreign key of the related model on the pivot table.
     * @param string $parentKey The key on the parent model.
     * @param string $relatedKey The key on the related model.
     * @return Model
     */
    public function belongsToMany(string $relatedModel, string $pivotTable = null, string $foreignPivotKey = null, string $relatedPivotKey = null, string $parentKey = 'id', string $relatedKey = 'id'): Model
    {
        $baseModelName = strtolower(basename(str_replace('\\', '/', static::class)));
        $relatedModelName = strtolower(basename(str_replace('\\', '/', $relatedModel)));

        if ($pivotTable === null) {
            $tables = [$baseModelName, $relatedModelName];
            sort($tables);
            $pivotTable = implode('_', $tables);
        }

        if ($foreignPivotKey === null) {
            $foreignPivotKey = $baseModelName . '_id';
        }

        if ($relatedPivotKey === null) {
            $relatedPivotKey = $relatedModelName . '_id';
        }

        /** @var Model $relatedInstance */
        $relatedInstance = new $relatedModel();
        $relatedInstance->setRelationData('belongsToMany', $pivotTable, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey);
        return $relatedInstance;
    }

    // Helper to store relation data for eager loading
    protected $relationData = [];

    protected function setRelationData(string $type, string $parentTable, string $localKey, string $foreignKey): void
    {
        $this->relationData = [
            'type' => $type,
            'parent_table' => $parentTable,
            'local_key' => $localKey,
            'foreign_key' => $foreignKey,
        ];
    }

        public function getRelationData(): array
    {
        return $this->relationData;
    }

    public static function __callStatic($method, $args)
    {
        return (new static)->$method(...$args);
    }

    public static function create(array $data): bool
    {
        return (new static)->createInstance($data);
    }

    public function createInstance(array $data): bool
    {
        $this->fireEvent('creating', $this);
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->getPdo()->prepare($sql);
        $result = $stmt->execute($data);
        if ($result) {
            $this->fireEvent('created', $this);
        }
        return $result;
    }

    public static function update(int $id, array $data): bool
    {
        return (new static)->updateInstance($id, $data);
    }

    public function updateInstance(int $id, array $data): bool
    {
        $this->fireEvent('updating', $this);
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
        }
        $set = implode(', ', $set);
        $sql = "UPDATE {$this->table} SET $set WHERE id = :id";
        $stmt = $this->getPdo()->prepare("UPDATE {$this->table} SET $set WHERE id = :id");
        $data['id'] = $id;
        $result = $stmt->execute($data);
        if ($result) {
            $this->fireEvent('updated', $this);
        }
        return $result;
    }

    public static function delete(int $id): bool
    {
        return (new static)->deleteInstance($id);
    }

    public function deleteInstance(int $id): bool
    {
        $this->fireEvent('deleting', $this);
        $stmt = $this->getPdo()->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $result = $stmt->execute(['id' => $id]);
        if ($result) {
            $this->fireEvent('deleted', $this);
        }
        return $result;
    }

    public function __get(string $key)
    {
        $accessor = 'get' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key))) . 'Attribute';
        if (method_exists($this, $accessor)) {
            return $this->$accessor();

        if (property_exists($this, $key)) {
            return $this->$key;
        }

        return null;
    }
    }

    public function __set(string $key, $value)
    {
        $mutator = 'set' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key))) . 'Attribute';
        if (method_exists($this, $mutator)) {
            $this->$mutator($value);
        } else {
            $this->$key = $value;
        }
    }

    public function toArray(): array
    {
        $data = [];
        foreach (get_object_vars($this) as $key => $value) {
            // Exclude internal properties of the Model class
            if (!in_array($key, ['pdo', 'table', 'query', 'bindings', 'wheres', 'limit', 'offset', 'selects', 'eagerLoads', 'joins', 'orders', 'groups', 'havings', 'relationData'])) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    public static function truncateTable(string $tableName): void
    {
        $pdo = Connection::getInstance();
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("TRUNCATE TABLE {$tableName}");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    }
}