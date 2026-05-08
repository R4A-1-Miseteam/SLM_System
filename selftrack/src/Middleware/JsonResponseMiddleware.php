<?php
declare(strict_types=1);

namespace SelfTrack\Middleware;

/**
 * JSON レスポンスミドルウェア
 * すべてのレスポンスを application/json として返す
 */
class JsonResponseMiddleware
{
    public static function handle(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
    }
}
