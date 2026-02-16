<?php

/**
 * Simple PSR-4 style autoloader and application bootstrap.
 *
 * Responsibilities:
 * - Start session
 * - Load environment (.env)
 * - Define global SITE_NAME and BASE_URL
 * - Register a PSR-4-like autoloader for app classes
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables
require_once __DIR__ . '/../helpers/env_loader.php';

// Global Site Configuration
if (!defined('SITE_NAME')) {
    define('SITE_NAME', getenv('SITE_NAME') ?: 'EasyCart');
}

// BASE_URL fallback for clean URLs (can still be overridden elsewhere if needed)
if (!defined('BASE_URL')) {
    define('BASE_URL', '/Internship/project/easyCart/public');
}

class Autoloader
{
    /**
     * Register the autoloader.
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    /**
     * PSR-4 style autoloading for the app namespace and legacy flat classes.
     *
     * - Supports namespaced classes like App\Controllers\ProductController
     * - Falls back to searching common app folders for non-namespaced classes
     */
    public static function autoload(string $class): void
    {
        // First, handle namespaced classes using PSR-4 mapping rooted at app/
        $baseDir = __DIR__ . '/../';
        $normalizedClass = ltrim($class, '\\');
        $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $normalizedClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }

        // Fallback for legacy non-namespaced classes (e.g., ProductController, Cart, Database)
        $legacyDirs = [
            __DIR__ . '/../controllers/',
            __DIR__ . '/../models/',
            __DIR__ . '/../core/',
            __DIR__ . '/../services/',
            __DIR__ . '/../queries/',
            __DIR__ . '/../helpers/',
        ];

        foreach ($legacyDirs as $dir) {
            $legacyFile = $dir . $class . '.php';
            if (file_exists($legacyFile)) {
                require_once $legacyFile;
                return;
            }
        }
    }
}

