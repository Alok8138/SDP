<?php
/**
 * Test Database Connection
 */

// Include the database configuration
require_once __DIR__ . '/../app/config/database.php';

try {
    // Attempt to connect
    $db = Database::connect();

    // Run a simple query to verify connection
    $stmt = $db->query("SELECT 1");
    $result = $stmt->fetch();

    if ($result) {
        echo "<h2>Database Connected Successfully</h2>";
        echo "<p>PHP is now connected to PostgreSQL (easycart) using the singleton pattern.</p>";
    }

} catch (Exception $e) {
    echo "<h2>Database Connection Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
