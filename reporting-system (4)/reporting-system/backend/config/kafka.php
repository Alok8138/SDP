<?php
return [
    'broker'         => $_ENV['KAFKA_BROKER'] ?? 'kafka:9092',
    'topic'          => 'report_data_topic',
    'dlq_topic'      => 'report_data_dlq',
    'consumer_group' => 'report_consumer_group',
    'chunk_size'     => 500,
];
