<?php
return [
    'host'     => $_ENV['REDIS_HOST']     ?? 'redis',
    'port'     => (int)($_ENV['REDIS_PORT']     ?? 6379),
    'password' => $_ENV['REDIS_PASSWORD'] ?? null,
];
