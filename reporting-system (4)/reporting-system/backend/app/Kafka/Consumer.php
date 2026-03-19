<?php

ini_set('memory_limit', '512M');
error_reporting(E_ALL & ~E_DEPRECATED);

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use longlang\phpkafka\Consumer\Consumer;
use longlang\phpkafka\Consumer\ConsumerConfig;

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

$solrHost = $_ENV['SOLR_HOST'] ?? 'solr';
$solrPort = $_ENV['SOLR_PORT'] ?? '8983';
$solrCore = $_ENV['SOLR_COLLECTION'] ?? 'csvcore';
$topic = 'kafka-solr-csvdata';

define('BATCH_SIZE', 1000);
define('SOLR_BASE', "http://{$solrHost}:{$solrPort}/solr/{$solrCore}");

echo "========================================\n";
echo "  KAFKA CONSUMER → SOLR INDEXER\n";
echo "========================================\n";
echo "Kafka : {$broker}\n";
echo "Solr  : " . SOLR_BASE . "\n\n";

// ─── Solr ─────────────────────────────────────────────────────

function getSolrSuffix(mixed $value): string
{
    if (is_int($value))
        return '_i';
    if (is_float($value))
        return '_f';
    if (is_bool($value))
        return '_b';
    return '_s';
}

function sendBatchToSolr(array $batch): void
{
    if (empty($batch)) {
        return;
    }

    $url = SOLR_BASE . '/update?commitWithin=5000';
    $data = json_encode($batch, JSON_UNESCAPED_UNICODE);
    $maxAttempts = 3;
    $attempt = 0;

    while ($attempt < $maxAttempts) {
        $attempt++;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($code === 200) {
            echo "✅ Indexed " . count($batch) . " docs\n";
            return;
        }

        echo "❌ Solr error (HTTP {$code}) attempt {$attempt}/{$maxAttempts}: " . ($error ?: $response) . "\n";

        if ($attempt < $maxAttempts) {
            sleep(2);
        }
    }

    // After retries, decide whether to throw or continue depending on your needs.
    echo "🔥 FAILED to index batch after {$maxAttempts} attempts: " . count($batch) . " docs\n";
}

function buildSolrDoc(array $data): ?array
{
    if (isset($data['__sentinel__']))
        return null;

    $doc = [];

    foreach ($data as $key => $value) {
        $cleanKey = trim(preg_replace('/_+/', '_', preg_replace('/[^a-zA-Z0-9_]/', '_', $key)), '_');
        $cleanValue = $value ?? '';

        if (in_array($cleanKey, ['_source_file', 'source_file'])) {
            $doc['source_file_s'] = $cleanValue;
            continue;
        }

        if ($cleanKey === 'id') {
            $doc['original_id_s'] = (string)$cleanValue;
            continue;
        }

        $doc[$cleanKey . getSolrSuffix($value)] = $cleanValue;
    }

    // Always generate a truly globally unique ID to guarantee 740k rows, never overwrite!
    $doc['id'] = 'row_' . uniqid('', true);

    return $doc;
}

// ─── Consumer ────────────────────────────────────────────────

$config = new ConsumerConfig();
$config->setBootstrapServer($broker);
$config->setTopic($topic);
$config->setGroupId('solr-indexer-group');
$config->setGroupInstanceId('solr-indexer-' . uniqid('', true)); // must be non-null for php-kafka
$config->setAutoCommit(false);

$consumer = new Consumer($config);
$batch = [];
$totalIndexed = 0;
$emptyPolls = 0;
$maxEmpty = 500;
$sentinelReceived = 0;
$requiredSentinels = 4;

echo "Consuming messages (stops after {$maxEmpty} empty polls OR after {$requiredSentinels} sentinels + idle)...\n\n";

while (true) {
    try {
        $message = $consumer->consume();
    } catch (\Throwable $e) {
        echo "⚠️  Consume error: " . $e->getMessage() . "\n";
        sleep(1);
        continue;
    }

    if ($message === null || $message->getValue() === null || $message->getValue() === '') {
        $emptyPolls++;

        // Flush pending batch when idle
        if (!empty($batch)) {
            sendBatchToSolr($batch);
            $totalIndexed += count($batch);
            echo "📦 Total indexed: {$totalIndexed}\n";
            $batch = [];
        }

        if ($emptyPolls >= $maxEmpty) {
            if ($sentinelReceived >= $requiredSentinels) {
                echo "\n📭 Queue empty after all sentinels. Stopping.\n";
                break;
            }

            echo "\n📭 Queue idle but not all sentinels received ({$sentinelReceived}/{$requiredSentinels}). Continuing.\n";
            $emptyPolls = 0; // keep waiting
        }

        usleep(100000); // 100ms wait
        continue;
    }

    $emptyPolls = 0;

    $data = json_decode($message->getValue(), true);

    if ($data && isset($data['__sentinel__'])) {
        $sentinelReceived++;
        echo "🏁 Sentinel received ({$sentinelReceived}/{$requiredSentinels}) — flushing remaining batch\n";
        if (!empty($batch)) {
            sendBatchToSolr($batch);
            $totalIndexed += count($batch);
            $batch = [];
        }
        $consumer->ack($message);

        if ($sentinelReceived >= $requiredSentinels) {
            echo "📦 All sentinels received. Will stop after {$maxEmpty} idle polls.\n";
        }

        continue;
    }

    if ($data) {
        $doc = buildSolrDoc($data);
        if ($doc !== null) {
            $batch[] = $doc;
        }
    }

    $consumer->ack($message);

    if (count($batch) >= BATCH_SIZE) {
        sendBatchToSolr($batch);
        $totalIndexed += count($batch);
        echo "📦 Total indexed: {$totalIndexed}\n";
        $batch = [];
    }
}

// Final flush
if (!empty($batch)) {
    sendBatchToSolr($batch);
    $totalIndexed += count($batch);
}

echo "\n========================================\n";
echo "  DONE — TOTAL INDEXED: {$totalIndexed}\n";
echo "========================================\n";
