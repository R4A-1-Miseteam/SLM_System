<?php
/**
 * SelfTrack Backend Entry Point
 * 全リクエストはこのファイルを経由してルーティングされる
 */

declare(strict_types=1);

// オートロード
require_once __DIR__ . '/src/autoload.php';

use SelfTrack\Middleware\CorsMiddleware;
use SelfTrack\Middleware\JsonResponseMiddleware;
use SelfTrack\Api\SubjectController;
use SelfTrack\Api\StudyLogController;
use SelfTrack\Api\TodoController;
use SelfTrack\Api\BackupController;

// ミドルウェア適用
CorsMiddleware::handle();
JsonResponseMiddleware::handle();

// .env 読み込み
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
        $_ENV[trim($k)] = trim($v);
    }
}

// シンプルなルーター
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path   = trim(str_replace('/index.php', '', $uri), '/');
$parts  = explode('/', $path);

// /api/{resource}/{id?}
if ($parts[0] !== 'api') {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found']);
    exit;
}

$resource = $parts[1] ?? '';
$id       = $parts[2] ?? null;

try {
    switch ($resource) {
        case 'subjects':
            $controller = new SubjectController();
            if ($method === 'GET')    echo json_encode($controller->index());
            elseif ($method === 'POST')   echo json_encode($controller->store(json_decode(file_get_contents('php://input'), true) ?? []));
            elseif ($method === 'PUT' && $id) echo json_encode($controller->update($id, json_decode(file_get_contents('php://input'), true) ?? []));
            elseif ($method === 'DELETE' && $id) echo json_encode($controller->destroy($id));
            else                           http_response_code(405);
            break;

        case 'logs':
            $controller = new StudyLogController();
            if ($method === 'GET')    echo json_encode($controller->index());
            elseif ($method === 'POST')   echo json_encode($controller->upsert(json_decode(file_get_contents('php://input'), true) ?? []));
            elseif ($method === 'DELETE' && $id) echo json_encode($controller->destroy($id));
            else                           http_response_code(405);
            break;

        case 'todos':
            $controller = new TodoController();
            if ($method === 'GET')    echo json_encode($controller->index());
            elseif ($method === 'POST')   echo json_encode($controller->store(json_decode(file_get_contents('php://input'), true) ?? []));
            elseif ($method === 'PUT' && $id)    echo json_encode($controller->toggle($id));
            elseif ($method === 'DELETE' && $id) echo json_encode($controller->destroy($id));
            else                           http_response_code(405);
            break;

        case 'backup':
            $controller = new BackupController();
            if ($parts[2] === 'export' && $method === 'GET')      $controller->export();
            elseif ($parts[2] === 'import' && $method === 'POST') echo json_encode($controller->import(json_decode(file_get_contents('php://input'), true) ?? []));
            else                                                  http_response_code(405);
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Resource not found: ' . $resource]);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error'   => 'Internal Server Error',
        'message' => $e->getMessage(),
    ]);
}
