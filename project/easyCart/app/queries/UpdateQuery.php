<?php

/**
 * UpdateQuery
 *
 * Fluent builder for UPDATE statements.
 */

class UpdateQuery extends BaseQuery
{
    private string $table;
    private array $data = [];
    private array $wheres = [];
    private array $bindings = [];

    public static function table(string $table): self
    {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    /**
     * Set column => value pairs to update.
     */
    public function set(array $data): self
    {
        foreach ($data as $column => $value) {
            $param = $this->makeParamName('set_' . $column, count($this->bindings));
            $this->data[$column] = $param;
            $this->bindings[$param] = $value;
        }
        return $this;
    }

    /**
     * Add simple AND conditions.
     */
    public function where(array $conditions): self
    {
        foreach ($conditions as $column => $value) {
            $param = $this->makeParamName('where_' . $column, count($this->bindings));
            $this->wheres[] = sprintf('%s = :%s', $column, $param);
            $this->bindings[$param] = $value;
        }
        return $this;
    }

    public function toSql(): string
    {
        $sets = [];
        foreach ($this->data as $column => $param) {
            $sets[] = sprintf('%s = :%s', $column, $param);
        }

        $sql = sprintf(
            'UPDATE %s SET %s',
            $this->table,
            implode(', ', $sets)
        );

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

