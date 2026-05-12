<?php
declare(strict_types=1);

namespace SelfTrack\Api;

use SelfTrack\Service\StudyLogService;
use SelfTrack\Service\AchievementService;

/**
 * 学習実績ログ REST エンドポイント（FR-003 / FR-004）
 *  GET    /api/logs            : 一覧取得
 *  POST   /api/logs            : upsert（同日同教科は更新）
 *  DELETE /api/logs/{id}       : 削除
 */
class StudyLogController
{
    private StudyLogService    $service;
    private AchievementService $achievement;

    public function __construct(
        ?StudyLogService    $service = null,
        ?AchievementService $achievement = null
    ) {
        $this->service     = $service     ?? new StudyLogService();
        $this->achievement = $achievement ?? new AchievementService();
    }

    public function index(): array
    {
        return [
            'success' => true,
            'data'    => array_map(fn($l) => $l->toArray(), $this->service->listAll()),
            'rates'   => $this->achievement->calcAllRates(),
        ];
    }

    public function upsert(array $body): array
    {
        try {
            $log = $this->service->upsertLog($body);
            return ['success' => true, 'data' => $log->toArray()];
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function destroy(string $id): array
    {
        $this->service->deleteLog($id);
        return ['success' => true, 'data' => ['deletedId' => $id]];
    }
}
