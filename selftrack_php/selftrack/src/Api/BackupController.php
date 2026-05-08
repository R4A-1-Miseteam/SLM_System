<?php
declare(strict_types=1);
namespace SelfTrack\Api;

use SelfTrack\Repository\LocalStorageRepository;
use SelfTrack\Service\BackupService;

class BackupController
{
    private BackupService $service;

    public function __construct(string $dataFile)
    {
        $store         = new LocalStorageRepository($dataFile);
        $this->service = new BackupService($store);
    }

    /** GET /api/backup/export */
    public function export(): void
    {
        $json     = $this->service->export();
        $filename = 'selftrack_' . date('Y-m-d') . '.json';

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($json));
        echo $json;
    }

    /** POST /api/backup/import */
    public function import(): void
    {
        $body   = file_get_contents('php://input');
        $result = $this->service->import($body);
        http_response_code(isset($result['errors']) ? 422 : 200);
        echo json_encode($result);
    }
}
