<?php
declare(strict_types=1);

namespace SelfTrack\Api;

use SelfTrack\Service\BackupService;

/**
 * バックアップ REST エンドポイント（FR-007 / FR-008 / FR-015）
 *  GET  /api/backup/export  : JSONダウンロード
 *  POST /api/backup/import  : JSONアップロード（リストア）
 */
class BackupController
{
    private BackupService $service;

    public function __construct(?BackupService $service = null)
    {
        $this->service = $service ?? new BackupService();
    }

    /** JSONファイルをダウンロードレスポンスとして返す */
    public function export(): void
    {
        $json = $this->service->exportJSON();
        $filename = 'selftrack_backup_' . date('Ymd_His') . '.json';
        // JSONレスポンスミドルウェアで application/json が既に設定されているが、
        // ここではダウンロードとして扱うためヘッダーを上書き
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($json));
        echo $json;
        exit;
    }

    public function import(array $body): array
    {
        try {
            $this->service->importJSON($body);
            return [
                'success' => true,
                'data'    => ['message' => 'インポートに成功しました'],
            ];
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
