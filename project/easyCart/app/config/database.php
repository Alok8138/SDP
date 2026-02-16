<?php

/**
 * Backwards-compatible bootstrap for older code that still includes this file.
 *
 * New code should rely on app/core/Autoloader.php and app/core/Database.php.
 */

require_once __DIR__ . '/../core/Autoloader.php';
Autoloader::register();

