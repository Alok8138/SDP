#!/usr/bin/env php
<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Kafka\Producer;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$file = $argv[1] ?? null;
if (!$file) {
    echo "Usage: php scripts/import_csv.php <path_to_csv>\n";
    exit(1);
}

(new Producer())->importCsv($file);
