<?php
declare(strict_types=1);

namespace SelfTrack\Middleware;

/**
 * CORS ミドルウェア
 * フロントエンド (Vite dev server: http://localhost:5173) からの
 * クロスオリジンリクエストを許可する
 */
class CorsMiddleware
{
    public static function handle(): void
    {
        $allowedOrigin = $_ENV['CORS_ALLOWED_ORIGIN'] ?? '*';
        header('Access-Control-Allow-Origin: ' . $allowedOrigin);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Credentials: true');

        // プリフライトリクエストへの対応
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
