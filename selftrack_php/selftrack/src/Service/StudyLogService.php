<?php
declare(strict_types=1);
namespace SelfTrack\Service;

use SelfTrack\Model\StudyLog;
use SelfTrack\Repository\StudyLogRepository;
use SelfTrack\Repository\SubjectRepository;

class StudyLogService
{
    public function __construct(
        private StudyLogRepository $logRepo,
        private SubjectRepository  $subjectRepo
    ) {}

    public function getAll(): array
    {
        return array_map(fn($l) => $l->toArray(), $this->logRepo->findAll());
    }

    /** FR-003/FR-010/FR-011: 同日同科目は upsert */
    public function upsert(array $data): array
    {
        $errors = ValidatorService::studyLog($data);
        if ($errors) return ['errors' => $errors];

        // 科目存在チェック
        if (!$this->subjectRepo->findById($data['subjectId'])) {
            return ['errors' => ['指定された科目が存在しません']];
        }

        $existing = $this->logRepo->findBySubjectAndDate($data['subjectId'], $data['date']);

        $log                 = new StudyLog();
        $log->id             = $existing?->id ?? $this->generateUuid();
        $log->subjectId      = $data['subjectId'];
        $log->date           = $data['date'];
        $log->duration       = (int) $data['duration'];
        $log->pageCount      = (int) $data['pageCount'];
        $log->src            = $data['src'] ?? 'manual';
        $log->reflectionMemo = $data['reflectionMemo'] ?? null;
        $log->savedAt        = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

        $this->logRepo->upsert($log);

        return [
            'data'    => $log->toArray(),
            'updated' => $existing !== null,
        ];
    }

    private function generateUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
