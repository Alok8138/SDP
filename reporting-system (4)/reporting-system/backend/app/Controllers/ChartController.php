<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\SolrService;
use App\Services\SolrQueryBuilder;
use App\Services\CacheService;

class ChartController
{
    public function __construct(
        private Request  $request,
        private Response $response
    ) {}

    /**
     * GET /api/charts?field=category&metric=price&agg=sum&filters=...
     */
    public function index(): void
    {
        $params  = $this->request->all();
        $field   = $params['field']   ?? 'category';
        $metric  = $params['metric']  ?? 'price';
        $agg     = $params['agg']     ?? 'sum';
        $filters = json_decode($params['filters'] ?? '[]', true) ?? [];

        $solr    = new SolrService();
        $builder = new SolrQueryBuilder();
        $cache   = new CacheService();

        foreach ($filters as $f) {
            $builder->addFilter($f);
        }

        $solrParams = array_merge($builder->build(), [
            'rows'          => 0,
            'facet'         => 'true',
            'facet.field'   => $field,
            'facet.mincount'=> 1,
            'facet.limit'   => 50,
            'stats'         => 'true',
            'stats.field'   => $metric,
        ]);

        $cacheKey = (new CacheService())->cacheKey('chart', $solrParams);

        $result = $cache->remember($cacheKey, fn() => $solr->query($solrParams), 60);

        $rawFacets = $result['facet_counts']['facet_fields'][$field] ?? [];
        $labels    = [];
        $values    = [];
        for ($i = 0; $i < count($rawFacets); $i += 2) {
            $labels[] = $rawFacets[$i];
            $values[] = $rawFacets[$i + 1];
        }

        $this->response->success([
            'labels' => $labels,
            'values' => $values,
            'field'  => $field,
            'metric' => $metric,
            'agg'    => $agg,
        ]);
    }
}
