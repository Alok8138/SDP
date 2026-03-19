<?php
return [
    'env'      => $_ENV['APP_ENV'] ?? 'development',
    'debug'    => ($_ENV['APP_ENV'] ?? 'development') === 'development',
];
