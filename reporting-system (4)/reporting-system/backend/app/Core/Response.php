<?php

namespace App\Core;

class Response
{
    public function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public function success(mixed $data, string $message = 'OK', int $status = 200): void
    {
        $this->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    public function error(string $message, int $status = 400): void
    {
        $this->json(['success' => false, 'error' => $message], $status);
    }
}
