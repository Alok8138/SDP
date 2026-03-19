<?php

namespace App\Core;

class Middleware
{
    public static function cors(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    public static function json(): void
    {
        header('Content-Type: application/json');
    }

    /**
     * Basic auth check — extend with JWT or session as needed
     */
    public static function auth(): void
    {
        // Placeholder: add token validation here
        // $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        // if (!$token) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }
    }
}
