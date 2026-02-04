<?php
/**
 * Database Connection Configuration
 * 
 * Uses Singleton pattern to ensure only one connection is open.
 */

// Start session for the application
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../helpers/env_loader.php';

// Global Site Configuration
define('SITE_NAME', getenv('SITE_NAME') ?: 'EasyCart');

// Robust BASE_URL fallback for clean URL Support
if (!defined('BASE_URL')) {
    define('BASE_URL', '/Internship/project/easyCart/public');
}

class Database {
    // Hold the class instance
    private static $instance = null;
    private $pdo;

    // Database Credentials from environment
    private $host;
    private $port;
    private $dbname;
    private $user;
    private $pass;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->host   = getenv('DB_HOST') ?: 'localhost';
        $this->port   = getenv('DB_PORT') ?: '5432';
        $this->dbname = getenv('DB_NAME') ?: 'easycart';
        $this->user   = getenv('DB_USER') ?: 'postgres';
        $this->pass   = getenv('DB_PASS') ?: '';

        try {
            // PostgreSQL DSN
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            
            // Create PDO instance
            $this->pdo = new PDO($dsn, $this->user, $this->pass);

            // Set Error Mode to Exception
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Set default fetch mode to associative array
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * Main connection method using Singleton
     */
    public static function connect() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
