<?php

/**
 * SelectQuery
 *
 * Fluent builder for SELECT statements.
 */

class SelectQuery extends BaseQuery
{
    private string $table;
    private array $columns = ['*'];
    private array $wheres = [];
    private array $joins = [];
    private ?string $orderBy = null;
    private ?int $limit = null;
    private array $bindings = [];

    public static function table(string $table): self
    {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    public function columns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Add simple AND conditions.
     *
     * Example: ->where(['user_id' => $userId, 'status' => 'active'])
     */
    public function where(array $conditions): self
    {
        foreach ($conditions as $column => $value) {
            $param = $this->makeParamName($column, count($this->bindings));
            $this->wheres[] = sprintf('%s = :%s', $column, $param);
            $this->bindings[$param] = $value;
        }
        return $this;
    }

    /**
     * Add a JOIN clause.
     *
     * @param string $type INNER, LEFT, RIGHT, etc.
     * @param string $table Table name with optional alias (e.g. "catalog_category_products cp")
     * @param string $on Raw ON condition (e.g. "c.entity_id = cp.category_id")
     */
    public function join(string $type, string $table, string $on): self
    {
        $this->joins[] = [
            'type' => strtoupper($type),
            'table' => $table,
            'on' => $on,
        ];
        return $this;
    }

    /**
     * Set ORDER BY clause (raw, but controlled by server-side code).
     */
    public function orderBy(string $orderBy): self
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * Set LIMIT for the query.
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function toSql(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . ' FROM ' . $this->table;

        foreach ($this->joins as $join) {
            $sql .= sprintf(
                ' %s JOIN %s ON %s',
                $join['type'],
                $join['table'],
                $join['on']
            );
        }

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        if ($this->orderBy !== null) {
            $sql .= ' ORDER BY ' . $this->orderBy;
        }

        if ($this->limit !== null) {
            // Use a named parameter for LIMIT to keep it prepared
            $param = '__limit';
            $sql .= ' LIMIT :' . $param;
            $this->bindings[$param] = $this->limit;
        }

        return $sql;
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }

    private function makeParamName(string $column, int $index): string
    {
        $base = preg_replace('/[^a-zA-Z0-9_]/', '_', $column) ?: 'param';
        return strtolower($base . '_' . $index);
    }
}

