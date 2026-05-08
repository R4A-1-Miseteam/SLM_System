<?php
declare(strict_types=1);
namespace SelfTrack\Api;

use SelfTrack\Repository\LocalStorageRepository;
use SelfTrack\Repository\StudyLogRepository;
use SelfTrack\Repository\SubjectRepository;
use SelfTrack\Service\StudyLogService;
use SelfTrack\Service\AchievementService;

class StudyLogController
{
    private StudyLogService  $logService;
    private AchievementService $achieveService;
    private LocalStorageRepository $store;

    public function __construct(string $dataFile)
    {
        $this->store          = new LocalStorageRepository($dataFile);
        $logRepo              = new StudyLogRepository($this->store);
        $subjectRepo          = new SubjectRepository($this->store);
        $this->logService     = new StudyLogService($logRepo, $subjectRepo);
        $this->achieveService = new AchievementService();
    }

    /** GET /api/logs?mode=week&date=2026-04-28 */
    public function index(): void
    {
        $mode = $_GET['mode'] ?? 'all'; // all | week | month | day
        $date = $_GET['date'] ?? date('Y-m-d');

        $root     = $this->store->load();
        $logs     = $root->logs;
        $subjects = $root->subjects;

        $data = match($mode) {
            'week'  => $this->achieveService->getWeekData($subjects, $logs, $this->getMondayOf($date)),
            'month' => $this->achieveService->getMonthData($subjects, $logs, substr($date, 0, 7)),
            'day'   => $this->achieveService->getDayData($logs, $date),
            default => array_map(fn($l) => $l->toArray(), $logs),
        };

        echo json_encode(['data' => $data]);
    }

    /** POST /api/logs */
    public function upsert(): void
    {
        $body   = $this->getBody();
        $result = $this->logService->upsert($body);
        http_response_code(isset($result['errors']) ? 422 : 200);
        echo json_encode($result);
    }

    private function getMondayOf(string $date): string
    {
        $d   = new \DateTimeImmutable($date);
        $dow = (int) $d->format('N'); // 1=月 〜 7=日
        return $d->modify('-' . ($dow - 1) . ' days')->format('Y-m-d');
    }

    private function getBody(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
}
