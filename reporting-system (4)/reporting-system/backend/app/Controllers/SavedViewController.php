<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;

class SavedViewController
{
    private \PDO $db;

    public function __construct(
        private Request  $request,
        private Response $response
    ) {
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $userId = $this->request->get('user_id', 1);
        $stmt   = $this->db->prepare(
            'SELECT * FROM saved_views WHERE user_id = ? OR JSON_CONTAINS(shared_with, ?) ORDER BY created_at DESC'
        );
        $stmt->execute([$userId, json_encode($userId)]);
        $this->response->success($stmt->fetchAll());
    }

    public function store(): void
    {
        $body = $this->request->body();
        $stmt = $this->db->prepare(
            'INSERT INTO saved_views (user_id, name, columns_config, filters, sort_config, is_default, shared_with, version)
             VALUES (?, ?, ?, ?, ?, ?, ?, 1)'
        );
        $stmt->execute([
            $body['user_id']  ?? 1,
            $body['name']     ?? 'Untitled View',
            json_encode($body['columns'] ?? []),
            json_encode($body['filters'] ?? []),
            json_encode($body['sort']    ?? []),
            (int)($body['is_default']    ?? 0),
            json_encode($body['shared_with'] ?? []),
        ]);
        $id = $this->db->lastInsertId();
        $this->response->success(['id' => $id], 'View saved', 201);
    }

    public function update(string $id): void
    {
        $body = $this->request->body();
        $stmt = $this->db->prepare(
            'UPDATE saved_views SET name=?, columns_config=?, filters=?, sort_config=?, is_default=?, shared_with=?, version=version+1, updated_at=NOW()
             WHERE id=?'
        );
        $stmt->execute([
            $body['name']        ?? 'Untitled View',
            json_encode($body['columns']     ?? []),
            json_encode($body['filters']     ?? []),
            json_encode($body['sort']        ?? []),
            (int)($body['is_default']        ?? 0),
            json_encode($body['shared_with'] ?? []),
            $id,
        ]);
        $this->response->success(['id' => $id], 'View updated');
    }

    public function destroy(string $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM saved_views WHERE id = ?');
        $stmt->execute([$id]);
        $this->response->success(null, 'View deleted');
    }
}
