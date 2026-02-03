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

// Global Site Configuration
define('SITE_NAME', 'EasyCart');

class Database {
    // Hold the class instance
    private static $instance = null;
    private $pdo;

    // Database Credentials
    private $host = 'localhost';
    private $port = '5432';
    private $dbname = 'easycart';
    private $user = 'postgres';
    private $pass = 'Alok@6768';

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
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
