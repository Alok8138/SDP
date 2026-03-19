<?php

namespace App\Services;

class SolrQueryBuilder
{
    private array $fq      = [];
    private string $q      = '*:*';
    private string $sort   = 'id asc';
    private int    $rows   = 25;
    private int    $start  = 0;
    private string $cursor = '*';
    private bool   $facet  = false;
    private array  $facetFields = [];
    private array  $fl     = ['*'];

    public function setQuery(string $q): self
    {
        $this->q = $q;
        return $this;
    }

    public function setRows(int $rows): self
    {
        $this->rows = $rows;
        return $this;
    }

    public function setStart(int $start): self
    {
        $this->start = $start;
        return $this;
    }

    public function setCursor(string $cursor): self
    {
        $this->cursor = $cursor;
        return $this;
    }

    public function setSort(string $field, string $dir = 'asc'): self
    {
        $dir = strtolower($dir) === 'desc' ? 'desc' : 'asc';
        // Always add a deterministic tiebreaker (uniqueKey) for cursor pagination
        $this->sort = "{$field} {$dir}, id asc";
        return $this;
    }

    public function setFields(array $fields): self
    {
        $this->fl = $fields;
        return $this;
    }

    /**
     * Apply a filter based on type:
     * text, dropdown, number_range, date_range, boolean, nested
     */
    public function addFilter(array $filter): self
    {
        $fq = $this->buildFilterClause($filter);
        if ($fq) {
            $this->fq[] = $fq;
        }
        return $this;
    }

    /**
     * Apply a top-level AND/OR filter tree
     * e.g. {operator: 'AND', rules: [{type:'dropdown', field:'category', value:['chair','table']}, ...]}
     */
    public function applyFilterTree(array $tree): self
    {
        $clause = $this->buildTree($tree);
        if ($clause) {
            $this->fq[] = $clause;
        }
        return $this;
    }

    private function buildTree(array $node): string
    {
        if (isset($node['rules'])) {
            $operator = strtoupper($node['operator'] ?? 'AND');
            $parts = [];
            foreach ($node['rules'] as $rule) {
                $part = isset($rule['rules'])
                    ? $this->buildTree($rule)
                    : $this->buildFilterClause($rule);
                if ($part) $parts[] = $part;
            }
            if (empty($parts)) return '';
            return '(' . implode(" {$operator} ", $parts) . ')';
        }
        return $this->buildFilterClause($node);
    }

    private function buildFilterClause(array $filter): string
    {
        $field = $filter['field'] ?? '';
        $type  = $filter['type']  ?? 'text';
        $value = $filter['value'] ?? null;

        if (!$field || $value === null || $value === '') return '';

        return match ($type) {
            'text'         => "{$field}:*" . $this->escape($value) . '*',
            'dropdown'     => $this->buildDropdown($field, (array)$value),
            'number_range' => $this->buildRange($field, $value['from'] ?? '*', $value['to'] ?? '*'),
            'date_range'   => $this->buildDateRange($field, $value['from'] ?? null, $value['to'] ?? null),
            'boolean'      => "{$field}:" . ($value ? 'true' : 'false'),
            default        => "{$field}:" . $this->escape((string)$value),
        };
    }

    private function buildDropdown(string $field, array $values): string
    {
        $escaped = array_map(fn($v) => '"' . $this->escape($v) . '"', $values);
        return "{$field}:(" . implode(' OR ', $escaped) . ')';
    }

    private function buildRange(string $field, mixed $from, mixed $to): string
    {
        $from = $from !== null ? $from : '*';
        $to   = $to   !== null ? $to   : '*';
        return "{$field}:[{$from} TO {$to}]";
    }

    private function buildDateRange(string $field, ?string $from, ?string $to): string
    {
        $from = $from ? $this->toSolrDate($from) : '*';
        $to   = $to   ? $this->toSolrDate($to)   : '*';
        return "{$field}:[{$from} TO {$to}]";
    }

    private function toSolrDate(string $date): string
    {
        $ts = strtotime($date);
        return date('Y-m-d', $ts) . 'T00:00:00Z';
    }

    private function escape(string $value): string
    {
        return addslashes($value);
    }

    public function enableFacets(array $fields): self
    {
        $this->facet       = true;
        $this->facetFields = $fields;
        return $this;
    }

    public function build(): array
    {
        $params = [
            'q'          => $this->q,
            'sort'       => $this->sort,
            'rows'       => $this->rows,
            'start'      => $this->start,
            'fl'         => implode(',', $this->fl),
            'wt'         => 'json',
            'cursorMark' => $this->cursor,
        ];

        if (!empty($this->fq)) {
            $params['fq'] = $this->fq;
        }

        if ($this->facet && !empty($this->facetFields)) {
            $params['facet']           = 'true';
            $params['facet.field']     = $this->facetFields;
            $params['facet.mincount']  = 1;
            $params['facet.limit']     = 20;
        }

        return $params;
    }
}
