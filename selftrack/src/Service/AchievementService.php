<?php
declare(strict_types=1);

namespace SelfTrack\Service;

use SelfTrack\Repository\SubjectRepository;
use SelfTrack\Repository\StudyLogRepository;

/**
 * 達成率計算サービス（FR-005）
 * 週次目標に対する累計実績の達成率を算出
 */
class AchievementService
{
    private SubjectRepository  $subjectRepo;
    private StudyLogRepository $logRepo;

    public function __construct(
        ?SubjectRepository  $subjectRepo = null,
        ?StudyLogRepository $logRepo = null
    ) {
        $this->subjectRepo = $subjectRepo ?? new SubjectRepository();
        $this->logRepo     = $logRepo     ?? new StudyLogRepository();
    }

    /**
     * 指定教科の今週達成率を計算
     * @return array{timeRate: float, pageRate: float, totalTime: int, totalPages: int}
     */
    public function calcRate(string $subjectId, ?string $weekStart = null): array
    {
        $subject = $this->subjectRepo->findById($subjectId);
        if ($subject === null) {
            throw new \RuntimeException("Subject not found: {$subjectId}");
        }

        [$start, $end] = $this->getWeekRange($weekStart);
        $logs = array_filter(
            $this->logRepo->findBySubject($subjectId),
            fn($l) => $l->date >= $start && $l->date <= $end
        );

        $totalTime  = 0;
        $totalPages = 0;
        foreach ($logs as $log) {
            $totalTime  += $log->duration;
            $totalPages += $log->pageCount;
        }

        return [
            'subjectId'  => $subjectId,
            'weekStart'  => $start,
            'weekEnd'    => $end,
            'totalTime'  => $totalTime,
            'totalPages' => $totalPages,
            'timeRate'   => $subject->targetTime > 0 ? round($totalTime  / $subject->targetTime * 100, 1) : 0.0,
            'pageRate'   => $subject->targetPage > 0 ? round($totalPages / $subject->targetPage * 100, 1) : 0.0,
        ];
    }

    /** 全教科の達成率を一括取得 */
    public function calcAllRates(?string $weekStart = null): array
    {
        $result = [];
        foreach ($this->subjectRepo->findAll() as $subject) {
            $result[] = $this->calcRate($subject->id, $weekStart);
        }
        return $result;
    }

    /** 月曜起点の週範囲を返す（YYYY-MM-DD, YYYY-MM-DD） */
    private function getWeekRange(?string $base = null): array
    {
        $date = $base ? new \DateTime($base) : new \DateTime('today');
        $dow = (int)$date->format('N'); // 1(Mon)〜7(Sun)
        $start = (clone $date)->modify('-' . ($dow - 1) . ' days');
        $end   = (clone $start)->modify('+6 days');
        return [$start->format('Y-m-d'), $end->format('Y-m-d')];
    }
}
