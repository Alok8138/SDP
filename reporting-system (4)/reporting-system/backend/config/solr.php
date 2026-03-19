<?php
return [
    'host'       => $_ENV['SOLR_HOST']       ?? 'solr',
    'port'       => $_ENV['SOLR_PORT']       ?? '8983',
    'collection' => $_ENV['SOLR_COLLECTION'] ?? 'report_data',
];
