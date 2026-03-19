<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class SavedViewModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function allForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM saved_views WHERE user_id = ? OR JSON_CONTAINS(shared_with, ?) ORDER BY created_at DESC'
        );
        $stmt->execute([$userId, json_encode($userId)]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM saved_views WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO saved_views (user_id, name, columns_config, filters, sort_config, is_default, shared_with, version)
             VALUES (?, ?, ?, ?, ?, ?, ?, 1)'
        );
        $stmt->execute([
            $data['user_id'],
            $data['name'],
            json_encode($data['columns']     ?? []),
            json_encode($data['filters']     ?? []),
            json_encode($data['sort']        ?? []),
            (int)($data['is_default']        ?? 0),
            json_encode($data['shared_with'] ?? []),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE saved_views SET name=?, columns_config=?, filters=?, sort_config=?, is_default=?,
             shared_with=?, version=version+1, updated_at=NOW() WHERE id=?'
        );
        $stmt->execute([
            $data['name'],
            json_encode($data['columns']     ?? []),
            json_encode($data['filters']     ?? []),
            json_encode($data['sort']        ?? []),
            (int)($data['is_default']        ?? 0),
            json_encode($data['shared_with'] ?? []),
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM saved_views WHERE id = ?');
        $stmt->execute([$id]);
    }
}
