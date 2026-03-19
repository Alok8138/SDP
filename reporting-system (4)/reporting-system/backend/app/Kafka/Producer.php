<?php
ini_set('memory_limit', '512M');
error_reporting(E_ALL & ~E_DEPRECATED);

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use longlang\phpkafka\Producer\Producer;
use longlang\phpkafka\Producer\ProducerConfig;

$folder     = dirname(__DIR__, 2) . '/csvfiles/';
$topic      = 'kafka-solr-csvdata';
$batchSize  = 1000;
$partitions = 4;

// Prefer container host first; fallback to host-accessible broker if unavailable.
$broker = $_ENV['KAFKA_BROKER'] ?? 'kafka:9092';

if (str_contains($broker, 'kafka:9092')) {
    $socket = @fsockopen('kafka', 9092, $errno, $errstr, 1);
    if (!$socket) {
        echo "⚠️  kafka host not resolvable; falling back to localhost:29092\n";
        $broker = 'localhost:29092';
    } else {
        fclose($socket);
    }
}

echo "========================================\n";
echo "  KAFKA PRODUCER - CSV INDEXER\n";
echo "========================================\n\n";
echo "Broker : {$broker}\n";
echo "Folder : {$folder}\n\n";

$config = new ProducerConfig();
$config->setBootstrapServer($broker);
$config->setAcks(-1);
$config->setConnectTimeout(5);
$config->setSendTimeout(5);

$producer = new Producer($config);

$files = glob($folder . '*.csv');
if (!$files) {
    echo "❌ No CSV files found in {$folder}\n";
    echo "   Copy your CSV files into: backend/csvfiles/\n";
    exit(1);
}

$fileCount = count($files);
echo "Found {$fileCount} CSV file(s)\n\n";

$total     = 0;
$fileIndex = 0;

foreach ($files as $file) {
    $fileIndex++;
    $fileName = basename($file);
    echo "[{$fileIndex}/{$fileCount}] Processing: {$fileName}\n";

    $handle = fopen($file, 'r');
    if (!$handle) {
        echo "  ⚠️  Cannot open — skipping\n";
        continue;
    }

    $headers = fgetcsv($handle);
    if (!$headers) {
        echo "  ⚠️  Empty header — skipping\n";
        fclose($handle);
        continue;
    }

    $headers   = array_map('trim', $headers);
    $rowNumber = 0;
    $batch     = [];

    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) !== count($headers)) continue;

        $raw  = array_combine($headers, $row);
        $data = [];

        foreach ($raw as $key => $value) {
            $data[trim($key)] = autocast((string)$value);
        }

        $data['_source_file'] = $fileName;

        $batch[] = ['data' => $data, 'row' => $rowNumber];
        $rowNumber++;
        $total++;

        if (count($batch) >= $batchSize) {
            sendBatch($producer, $topic, $batch, $fileName);
            $batch = [];
        }
    }

    if (!empty($batch)) {
        sendBatch($producer, $topic, $batch, $fileName);
    }

    fclose($handle);
    echo "  ✅ Done — {$rowNumber} rows sent\n\n";
}

// Sentinel signals so consumer knows all data is sent
echo "Sending sentinel signals...\n";
for ($p = 0; $p < $partitions; $p++) {
    $producer->send(
        $topic,
        json_encode(['__sentinel__' => true]),
        '__sentinel_' . $p . '__'
    );
}

$producer->close();

echo "\n========================================\n";
echo "  DONE — TOTAL ROWS SENT: {$total}\n";
echo "========================================\n";

// ─── Helpers ─────────────────────────────────────────────────

function autocast(string $value): mixed
{
    $value = trim($value);
    if ($value === '') return null;
    $clean = str_replace(',', '', $value);
    if (ctype_digit($clean)) return (int)$clean;
    if (preg_match('/^-\d+$/', $clean)) return (int)$clean;
    if (is_numeric($clean) && str_contains($clean, '.')) return (float)$clean;
    if (strtolower($value) === 'true')  return true;
    if (strtolower($value) === 'false') return false;
    return $value;
}

function sendBatch(Producer $producer, string $topic, array $batch, string $fileName): void
{
    foreach ($batch as $item) {
        $key = md5($fileName . ':' . $item['row'] . ':' . uniqid('', true));
        $producer->send($topic, json_encode($item['data'], JSON_UNESCAPED_UNICODE), $key);
    }
    echo "  Sent batch of " . count($batch) . " rows\n";
}
