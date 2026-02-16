<?php

/**
 * BaseQuery
 *
 * Holds common pieces for all query types and exposes the
 * SQL string + bound parameters for execution.
 */

abstract class BaseQuery
{
    /**
     * Return the final SQL string for this query.
     */
    abstract public function toSql(): string;

    /**
     * Return the bound parameters for this query.
     *
     * @return array<string, mixed>
     */
    abstract public function getBindings(): array;
}

