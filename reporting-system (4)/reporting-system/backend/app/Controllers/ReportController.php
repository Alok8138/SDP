<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\SolrService;
use App\Services\SolrQueryBuilder;
use App\Services\CacheService;

class ReportController
{
    private SolrService      $solr;
    private SolrQueryBuilder $builder;
    private CacheService     $cache;

    public function __construct(
        private Request  $request,
        private Response $response
    ) {
        $this->solr    = new SolrService();
        $this->builder = new SolrQueryBuilder();
        $this->cache   = new CacheService();
    }

    /**
     * GET /api/reports
     * Query params: filters (JSON), columns[], sort_field, sort_dir, rows, cursor
     */
    public function index(): void
    {
        $params    = $this->request->all();
        $filters   = json_decode($params['filters'] ?? '[]', true) ?? [];
        $columns   = isset($params['columns']) ? array_filter(explode(',', $params['columns'])) : ['*'];
        $sortField = $params['sort_field'] ?? 'product_id_i';
        $sortDir   = $params['sort_dir']   ?? 'asc';
        // Guard against 0/negative rows so we always return docs
        $rows      = max(1, min((int)($params['rows'] ?? 25), 100));
        $cursor    = $params['cursor']       ?? '*';

        $this->builder
            ->setRows(min($rows, 100))
            ->setCursor($cursor)
            ->setSort($sortField, $sortDir)
            ->setFields($columns);

        if (!empty($filters)) {
            $this->builder->applyFilterTree($filters);
        }

        $cacheKey = $this->cache->cacheKey('report', $this->builder->build());

        $result = $this->cache->remember($cacheKey, function () {
            return $this->solr->query($this->builder->build());
        }, 60);

        $this->response->success([
            'rows'       => $result['response']['docs']    ?? [],
            'total'      => $result['response']['numFound'] ?? 0,
            'nextCursor' => $result['nextCursorMark']       ?? null,
        ]);
    }

    /**
     * GET /api/reports/schema
     * Returns server-driven column schema
     */
    public function schema(): void
    {
        $schema = [
            ['key' => 'product_id_i',   'label' => 'Product ID',    'type' => 'number',  'sortable' => true,  'filterable' => true],
            ['key' => 'Product_Name_s', 'label' => 'Product Name',  'type' => 'text',    'sortable' => true,  'filterable' => true],
            ['key' => 'Brand_Name_s',   'label' => 'Brand',         'type' => 'text',    'sortable' => true,  'filterable' => true, 'facet' => true],
            ['key' => 'Price_f',        'label' => 'Price',         'type' => 'number',  'sortable' => true,  'filterable' => true],
            ['key' => 'AF_PRICE_f',     'label' => 'AF Price',      'type' => 'number',  'sortable' => true,  'filterable' => true],
            ['key' => 'Quantity_i',     'label' => 'Quantity',      'type' => 'number',  'sortable' => true,  'filterable' => true],
            ['key' => 'Stock_s',        'label' => 'Stock',         'type' => 'text',    'sortable' => true,  'filterable' => true, 'facet' => true],
            ['key' => 'Type_s',         'label' => 'Type',          'type' => 'text',    'sortable' => true,  'filterable' => true],
            ['key' => 'Default_SKU_s',  'label' => 'Default SKU',   'type' => 'text',    'sortable' => true,  'filterable' => true],
            ['key' => 'source_file_s',  'label' => 'Source File',   'type' => 'text',    'sortable' => true,  'filterable' => true],
            ['key' => 'AF_URL_s',       'label' => 'Product URL',   'type' => 'text',    'sortable' => false, 'filterable' => false],
        ];
        $this->response->success($schema);
    }

    /**
     * GET /api/facets/{field}?q=prefix
     */
    public function facets(string $field): void
    {
        $prefix = $this->request->get('q', '');
        $cacheKey = "facets:{$field}:{$prefix}";

        $facets = $this->cache->remember($cacheKey, function () use ($field, $prefix) {
            return $this->solr->getFacets($field, $prefix);
        }, 120);

        $this->response->success($facets);
    }
}
