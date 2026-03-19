<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class ColumnConfigModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function get(int $userId, string $reportId = 'default'): array
    {
        $stmt = $this->db->prepare(
            'SELECT config FROM column_config WHERE user_id = ? AND report_id = ?'
        );
        $stmt->execute([$userId, $reportId]);
        $row = $stmt->fetch();
        return $row ? json_decode($row['config'], true) : [];
    }

    public function save(int $userId, string $reportId, array $config): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO column_config (user_id, report_id, config, updated_at)
             VALUES (?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE config = VALUES(config), updated_at = NOW()'
        );
        $stmt->execute([$userId, $reportId, json_encode($config)]);
    }
}
