<?php

/**
 * DeleteQuery
 *
 * Fluent builder for DELETE statements.
 */

class DeleteQuery extends BaseQuery
{
    private string $table;
    private array $wheres = [];
    private array $bindings = [];

    public static function table(string $table): self
    {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    /**
     * Add simple AND conditions.
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

    public function toSql(): string
    {
        $sql = 'DELETE FROM ' . $this->table;

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
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

