<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\DateCompareService;
use App\Services\SolrService;
use App\Services\SolrQueryBuilder;

class DateCompareController
{
    public function __construct(
        private Request  $request,
        private Response $response
    ) {}

    public function compare(): void
    {
        $body        = $this->request->body();
        $field       = $body['field']        ?? 'created_at';
        $start       = $body['period_start'] ?? date('Y-m-01');
        $end         = $body['period_end']   ?? date('Y-m-d');
        $mode        = $body['compare_mode'] ?? 'previous_period';
        $baseFilters = $body['filters']      ?? [];
        $metrics     = $body['metrics']      ?? ['price', 'quantity'];

        $service = new DateCompareService(new SolrService(), new SolrQueryBuilder());
        $result  = $service->compare($field, $start, $end, $mode, $baseFilters, $metrics);

        $this->response->success($result);
    }
}
