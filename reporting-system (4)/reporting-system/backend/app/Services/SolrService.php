<?php

namespace App\Services;

class SolrService
{
    private string $baseUrl;

    public function __construct()
    {
        $host       = $_ENV['SOLR_HOST']       ?? 'solr';
        $port       = $_ENV['SOLR_PORT']       ?? '8983';
        $collection = $_ENV['SOLR_COLLECTION'] ?? 'report_data';
        $this->baseUrl = "http://{$host}:{$port}/solr/{$collection}";
    }

    public function query(array $params): array
    {
        $url = $this->baseUrl . '/select?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);

        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false || $code !== 200) {
            throw new \RuntimeException("Solr query failed (HTTP {$code})");
        }

        return json_decode($raw, true);
    }

    /**
     * Run two queries in parallel using curl_multi
     */
    public function queryParallel(array $params1, array $params2): array
    {
        $mh = curl_multi_init();
        $handles = [];

        foreach ([$params1, $params2] as $i => $params) {
            $url = $this->baseUrl . '/select?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            $ch  = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            ]);
            curl_multi_add_handle($mh, $ch);
            $handles[$i] = $ch;
        }

        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        $results = [];
        foreach ($handles as $i => $ch) {
            $raw = curl_multi_getcontent($ch);
            $results[$i] = json_decode($raw, true);
            curl_multi_remove_handle($mh, $ch);
        }
        curl_multi_close($mh);

        return $results;
    }

    public function index(array $doc): bool
    {
        $url  = $this->baseUrl . '/update?commitWithin=1000&wt=json';
        $body = json_encode([$doc]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code === 200;
    }

    public function indexBatch(array $docs): bool
    {
        $url  = $this->baseUrl . '/update?commitWithin=1000&wt=json';
        $body = json_encode($docs);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code === 200;
    }

    public function getFacets(string $field, string $prefix = ''): array
    {
        $params = [
            'q'              => '*:*',
            'rows'           => 0,
            'facet'          => 'true',
            'facet.field'    => $field,
            'facet.mincount' => 1,
            'facet.limit'    => 20,
            'wt'             => 'json',
        ];
        if ($prefix !== '') {
            $params['facet.prefix'] = $prefix;
        }

        $result = $this->query($params);
        $raw    = $result['facet_counts']['facet_fields'][$field] ?? [];

        // Solr returns [value, count, value, count, ...]
        $facets = [];
        for ($i = 0; $i < count($raw); $i += 2) {
            $facets[] = ['value' => $raw[$i], 'count' => $raw[$i + 1]];
        }
        return $facets;
    }
}
