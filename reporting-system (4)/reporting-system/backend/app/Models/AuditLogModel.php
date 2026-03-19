<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class AuditLogModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function log(int $userId, string $action, ?string $reportId = null, array $payload = []): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO audit_logs (user_id, action, report_id, payload, created_at)
             VALUES (?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$userId, $action, $reportId, json_encode($payload)]);
    }

    public function forUser(int $userId, int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM audit_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?'
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
}
