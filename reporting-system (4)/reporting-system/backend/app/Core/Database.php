<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? 'mysql';
            $name = $_ENV['DB_NAME'] ?? 'reporting_db';
            $user = $_ENV['DB_USER'] ?? 'report_user';
            $pass = $_ENV['DB_PASS'] ?? '';

            try {
                self::$instance = new PDO(
                    "mysql:host={$host};dbname={$name};charset=utf8mb4",
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                throw new \RuntimeException('DB connection failed: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
