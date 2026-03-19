<?php

namespace App\Services;

class DateCompareService
{
    public function __construct(
        private SolrService $solr,
        private SolrQueryBuilder $builder
    ) {}

    /**
     * @param string $field         date field name in Solr
     * @param string $periodStart   Y-m-d
     * @param string $periodEnd     Y-m-d
     * @param string $compareMode   previous_period | same_period_last_year
     * @param array  $baseFilters   other active filters to apply to both queries
     * @param array  $metrics       numeric fields to aggregate (sum/avg/count)
     */
    public function compare(
        string $field,
        string $periodStart,
        string $periodEnd,
        string $compareMode,
        array  $baseFilters = [],
        array  $metrics = ['price', 'quantity']
    ): array {
        [$compStart, $compEnd] = $this->resolveComparePeriod(
            $periodStart, $periodEnd, $compareMode
        );

        $params1 = $this->buildParams($field, $periodStart, $periodEnd, $baseFilters, $metrics);
        $params2 = $this->buildParams($field, $compStart,   $compEnd,   $baseFilters, $metrics);

        [$result1, $result2] = $this->solr->queryParallel($params1, $params2);

        $current  = $this->extractStats($result1, $metrics);
        $previous = $this->extractStats($result2, $metrics);

        $diff = [];
        foreach ($metrics as $m) {
            $cur = $current[$m] ?? 0;
            $prv = $previous[$m] ?? 0;
            $abs = $cur - $prv;
            $pct = $prv != 0 ? round(($abs / $prv) * 100, 2) : null;
            $diff[$m] = ['absolute' => $abs, 'percent' => $pct];
        }

        return [
            'period'       => ['start' => $periodStart, 'end' => $periodEnd],
            'compare'      => ['start' => $compStart,   'end' => $compEnd],
            'compare_mode' => $compareMode,
            'current'      => $current,
            'previous'     => $previous,
            'diff'         => $diff,
        ];
    }

    private function buildParams(
        string $field, string $from, string $to,
        array $baseFilters, array $metrics
    ): array {
        $builder = clone $this->builder;
        $builder->setRows(0);

        $dateFq = "{$field}:[{$from}T00:00:00Z TO {$to}T23:59:59Z]";
        $builder->addFilter(['type' => 'raw', 'field' => $field, 'value' => $dateFq]);

        foreach ($baseFilters as $f) {
            $builder->addFilter($f);
        }

        $params = $builder->build();

        // Add stats for numeric fields
        $params['stats']        = 'true';
        $params['stats.field']  = $metrics;

        return $params;
    }

    private function extractStats(array $result, array $metrics): array
    {
        $statsFields = $result['stats']['stats_fields'] ?? [];
        $out = ['count' => $result['response']['numFound'] ?? 0];
        foreach ($metrics as $m) {
            $out[$m . '_sum'] = $statsFields[$m]['sum'] ?? 0;
            $out[$m . '_avg'] = $statsFields[$m]['mean'] ?? 0;
        }
        return $out;
    }

    private function resolveComparePeriod(
        string $start, string $end, string $mode
    ): array {
        $startTs = strtotime($start);
        $endTs   = strtotime($end);
        $diff    = $endTs - $startTs;

        if ($mode === 'same_period_last_year') {
            return [
                date('Y-m-d', strtotime('-1 year', $startTs)),
                date('Y-m-d', strtotime('-1 year', $endTs)),
            ];
        }

        // previous_period: shift back by the same number of days
        return [
            date('Y-m-d', $startTs - $diff - 86400),
            date('Y-m-d', $startTs - 86400),
        ];
    }
}
