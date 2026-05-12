<?php
declare(strict_types=1);

namespace SelfTrack\Service;

use SelfTrack\Model\StudyLog;
use SelfTrack\Repository\StudyLogRepository;

/**
 * 学習実績ログの upsert・集計を担当
 */
class StudyLogService
{
    private StudyLogRepository $logRepo;
    private ValidatorService   $validator;

    public function __construct(
        ?StudyLogRepository $logRepo = null,
        ?ValidatorService   $validator = null
    ) {
        $this->logRepo   = $logRepo   ?? new StudyLogRepository();
        $this->validator = $validator ?? new ValidatorService();
    }

    /** @return StudyLog[] */
    public function listAll(): array
    {
        return $this->logRepo->findAll();
    }

    /** 同日・同教科のログがあれば更新、なければ新規作成 */
    public function upsertLog(array $dto): StudyLog
    {
        $errors = $this->validator->validateLogDto($dto);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' / ', $errors));
        }

        // 既存ログ検索
        $existing = null;
        foreach ($this->logRepo->findBySubject($dto['subjectId']) as $log) {
            if ($log->date === $dto['date']) {
                $existing = $log;
                break;
            }
        }

        if ($existing !== null) {
            $existing->duration  = (int)($dto['duration']  ?? $existing->duration);
            $existing->pageCount = (int)($dto['pageCount'] ?? $existing->pageCount);
            $existing->comment   = $dto['comment'] ?? $existing->comment;
            $this->logRepo->save($existing);
            return $existing;
        }

        $log = new StudyLog(
            $this->generateId(),
            $dto['subjectId'],
            $dto['date'],
            (int)($dto['duration']  ?? 0),
            (int)($dto['pageCount'] ?? 0),
            $dto['comment'] ?? ''
        );
        $this->logRepo->save($log);
        return $log;
    }

    public function deleteLog(string $id): void
    {
        $this->logRepo->delete($id);
    }

    /** 教科ごとの累計集計 */
    public function aggregate(string $subjectId): array
    {
        $logs = $this->logRepo->findBySubject($subjectId);
        $totalTime  = 0;
        $totalPages = 0;
        foreach ($logs as $log) {
            $totalTime  += $log->duration;
            $totalPages += $log->pageCount;
        }
        return [
            'subjectId'  => $subjectId,
            'totalTime'  => $totalTime,
            'totalPages' => $totalPages,
            'logCount'   => count($logs),
        ];
    }

    private function generateId(): string
    {
        return bin2hex(random_bytes(8));
    }
}
