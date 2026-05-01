<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Middleware/CorsMiddleware.php';
require_once __DIR__ . '/src/Middleware/JsonResponseMiddleware.php';
require_once __DIR__ . '/src/Model/RootData.php';
require_once __DIR__ . '/src/Model/Subject.php';
require_once __DIR__ . '/src/Model/StudyLog.php';
require_once __DIR__ . '/src/Model/TodoTask.php';
require_once __DIR__ . '/src/Repository/LocalStorageRepository.php';
require_once __DIR__ . '/src/Repository/SubjectRepository.php';
require_once __DIR__ . '/src/Repository/StudyLogRepository.php';
require_once __DIR__ . '/src/Repository/TodoRepository.php';
require_once __DIR__ . '/src/Service/ValidatorService.php';
require_once __DIR__ . '/src/Service/SubjectService.php';
require_once __DIR__ . '/src/Service/StudyLogService.php';
require_once __DIR__ . '/src/Service/AchievementService.php';
require_once __DIR__ . '/src/Service/BackupService.php';
require_once __DIR__ . '/src/Api/SubjectController.php';
require_once __DIR__ . '/src/Api/StudyLogController.php';
require_once __DIR__ . '/src/Api/TodoController.php';
require_once __DIR__ . '/src/Api/BackupController.php';

use SelfTrack\Middleware\CorsMiddleware;
use SelfTrack\Middleware\JsonResponseMiddleware;
use SelfTrack\Api\SubjectController;
use SelfTrack\Api\StudyLogController;
use SelfTrack\Api\TodoController;
use SelfTrack\Api\BackupController;

// ミドルウェア
CorsMiddleware::handle();
JsonResponseMiddleware::setHeaders();

// ルーティング
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

// データファイルパス
$dataFile = __DIR__ . '/storage/selftrack_data.json';

try {
    match (true) {
        // 科目
        $uri === '/api/subjects' && $method === 'GET'    => (new SubjectController($dataFile))->index(),
        $uri === '/api/subjects' && $method === 'POST'   => (new SubjectController($dataFile))->store(),
        preg_match('#^/api/subjects/([^/]+)$#', $uri, $m) && $method === 'PUT'    => (new SubjectController($dataFile))->update($m[1]),
        preg_match('#^/api/subjects/([^/]+)$#', $uri, $m) && $method === 'DELETE' => (new SubjectController($dataFile))->destroy($m[1]),

        // 実績ログ
        $uri === '/api/logs' && $method === 'GET'  => (new StudyLogController($dataFile))->index(),
        $uri === '/api/logs' && $method === 'POST' => (new StudyLogController($dataFile))->upsert(),

        // ToDo
        $uri === '/api/todos' && $method === 'GET'  => (new TodoController($dataFile))->index(),
        $uri === '/api/todos' && $method === 'POST' => (new TodoController($dataFile))->store(),
        preg_match('#^/api/todos/([^/]+)$#', $uri, $m) && $method === 'PUT'    => (new TodoController($dataFile))->update($m[1]),
        preg_match('#^/api/todos/([^/]+)$#', $uri, $m) && $method === 'DELETE' => (new TodoController($dataFile))->destroy($m[1]),

        // バックアップ
        $uri === '/api/backup/export' && $method === 'GET'  => (new BackupController($dataFile))->export(),
        $uri === '/api/backup/import' && $method === 'POST' => (new BackupController($dataFile))->import(),

        default => http_response_code(404) && print json_encode(['error' => 'Not Found']),
    };
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
