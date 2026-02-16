<?php

/**
 * InsertQuery
 *
 * Fluent builder for INSERT statements.
 */

class InsertQuery extends BaseQuery
{
    private string $table;
    private array $data = [];
    private ?string $returning = null;

    public static function table(string $table): self
    {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    /**
     * Set column => value pairs to insert.
     */
    public function values(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Optional PostgreSQL RETURNING clause.
     */
    public function returning(string $column): self
    {
        $this->returning = $column;
        return $this;
    }

    public function toSql(): string
    {
        $columns = array_keys($this->data);
        $params = [];

        foreach ($columns as $index => $column) {
            $params[] = ':' . $this->makeParamName($column, $index);
        }

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $params)
        );

        if ($this->returning !== null) {
            $sql .= ' RETURNING ' . $this->returning;
        }

        return $sql;
    }

    public function getBindings(): array
    {
        $bindings = [];
        $index = 0;
        foreach ($this->data as $column => $value) {
            $param = $this->makeParamName($column, $index++);
            $bindings[$param] = $value;
        }
        return $bindings;
    }

    private function makeParamName(string $column, int $index): string
    {
        $base = preg_replace('/[^a-zA-Z0-9_]/', '_', $column) ?: 'param';
        return strtolower($base . '_' . $index);
    }
}

