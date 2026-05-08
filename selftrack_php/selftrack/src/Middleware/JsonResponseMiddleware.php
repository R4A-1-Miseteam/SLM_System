<?php
declare(strict_types=1);
namespace SelfTrack\Middleware;

class JsonResponseMiddleware
{
    public static function setHeaders(): void
    {
        header('Content-Type: application/json; charset=utf-8');
    }
}
