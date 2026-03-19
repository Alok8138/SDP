<?php

namespace App\Services;

use Predis\Client;

class CacheService
{
    private Client $redis;
    private int $defaultTtl = 300; // 5 minutes

    public function __construct()
    {
        $this->redis = new Client([
            'scheme'   => 'tcp',
            'host'     => $_ENV['REDIS_HOST']     ?? 'redis',
            'port'     => (int)($_ENV['REDIS_PORT']     ?? 6379),
            'password' => $_ENV['REDIS_PASSWORD'] ?? null,
        ]);
    }

    public function get(string $key): mixed
    {
        $value = $this->redis->get($key);
        return $value ? json_decode($value, true) : null;
    }

    public function set(string $key, mixed $value, int $ttl = 0): void
    {
        $ttl = $ttl ?: $this->defaultTtl;
        $this->redis->setex($key, $ttl, json_encode($value));
    }

    public function delete(string $key): void
    {
        $this->redis->del([$key]);
    }

    public function remember(string $key, callable $callback, int $ttl = 0): mixed
    {
        $cached = $this->get($key);
        if ($cached !== null) return $cached;

        $value = $callback();
        $this->set($key, $value, $ttl);
        return $value;
    }

    public function cacheKey(string $prefix, array $params): string
    {
        return $prefix . ':' . md5(json_encode($params));
    }
}
