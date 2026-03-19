<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

class ColumnConfigController
{
    private \PDO $db;

    public function __construct(
        private Request  $request,
        private Response $response
    ) {
        $this->db = Database::getInstance();
    }

    public function show(): void
    {
        $userId   = $this->request->get('user_id', 1);
        $reportId = $this->request->get('report_id', 'default');
        $stmt     = $this->db->prepare(
            'SELECT config FROM column_config WHERE user_id = ? AND report_id = ?'
        );
        $stmt->execute([$userId, $reportId]);
        $row = $stmt->fetch();
        $this->response->success($row ? json_decode($row['config'], true) : []);
    }

    public function update(): void
    {
        $body     = $this->request->body();
        $userId   = $body['user_id']   ?? 1;
        $reportId = $body['report_id'] ?? 'default';
        $config   = json_encode($body['config'] ?? []);

        $stmt = $this->db->prepare(
            'INSERT INTO column_config (user_id, report_id, config, updated_at)
             VALUES (?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE config = VALUES(config), updated_at = NOW()'
        );
        $stmt->execute([$userId, $reportId, $config]);
        $this->response->success(null, 'Config saved');
    }
}
