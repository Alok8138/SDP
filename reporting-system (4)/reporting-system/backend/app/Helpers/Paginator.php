<?php

namespace App\Helpers;

class Paginator
{
    public static function cursorParams(
        string $cursor = '*',
        int $rows = 25,
        string $sortField = 'id',
        string $sortDir = 'asc'
    ): array {
        return [
            'cursorMark' => $cursor,
            'rows'       => min($rows, 100),
            'sort'       => "{$sortField} {$sortDir}",
        ];
    }

    public static function response(array $solrResult, int $rows): array
    {
        return [
            'rows'       => $solrResult['response']['docs']     ?? [],
            'total'      => $solrResult['response']['numFound'] ?? 0,
            'nextCursor' => $solrResult['nextCursorMark']       ?? null,
            'pageSize'   => $rows,
        ];
    }
}
