<?php
declare(strict_types=1);
namespace SelfTrack\Service;

use SelfTrack\Model\Subject;
use SelfTrack\Model\StudyLog;

/**
 * FR-005: 達成率リアルタイム計算
 * FR-006: 日別・週別・月別集計
 */
class AchievementService
{
    /**
     * 指定科目の週次達成率を計算
     * @param StudyLog[] $logs
     */
    public function calcWeekly(Subject $subject, array $logs, string $weekStart): array
    {
        $days      = $this->getWeekDays($weekStart);
        $weekLogs  = array_filter($logs, fn($l) =>
            $l->subjectId === $subject->id && in_array($l->date, $days, true)
        );

        $totalTime = array_sum(array_map(fn($l) => $l->duration,  $weekLogs));
        $totalPage = array_sum(array_map(fn($l) => $l->pageCount, $weekLogs));

        $pctTime = $subject->targetTime > 0
            ? min(100, round($totalTime / $subject->targetTime * 100))
            : 0;
        $pctPage = $subject->targetPage > 0
            ? min(100, round($totalPage / $subject->targetPage * 100))
            : 0;
        $pct = $subject->targetPage > 0
            ? round(($pctTime + $pctPage) / 2)
            : $pctTime;

        return [
            'subjectId'  => $subject->id,
            'totalTime'  => $totalTime,
            'totalPage'  => $totalPage,
            'pctTime'    => $pctTime,
            'pctPage'    => $pctPage,
            'pct'        => $pct,
        ];
    }

    /**
     * FR-006: 日別グラフ用データ（1日の時間帯別）
     * @param StudyLog[] $logs
     */
    public function getDayData(array $logs, string $date): array
    {
        $dayLogs = array_filter($logs, fn($l) => $l->date === $date);
        return array_map(fn($l) => $l->toArray(), array_values($dayLogs));
    }

    /**
     * FR-006: 週別グラフ用データ（月〜日 × 科目）
     * @param Subject[]  $subjects
     * @param StudyLog[] $logs
     */
    public function getWeekData(array $subjects, array $logs, string $weekStart): array
    {
        $days = $this->getWeekDays($weekStart);
        $result = [];
        foreach ($subjects as $s) {
            $data = [];
            foreach ($days as $day) {
                $log    = $this->findLog($logs, $s->id, $day);
                $data[] = ['date' => $day, 'duration' => $log?->duration ?? 0, 'pageCount' => $log?->pageCount ?? 0];
            }
            $result[] = ['subject' => $s->toArray(), 'days' => $data];
        }
        return $result;
    }

    /**
     * FR-006: 月別グラフ用データ
     * @param Subject[]  $subjects
     * @param StudyLog[] $logs
     */
    public function getMonthData(array $subjects, array $logs, string $yearMonth): array
    {
        $days   = $this->getMonthDays($yearMonth);
        $result = [];
        foreach ($subjects as $s) {
            $data = [];
            foreach ($days as $day) {
                $log    = $this->findLog($logs, $s->id, $day);
                $data[] = ['date' => $day, 'duration' => $log?->duration ?? 0, 'pageCount' => $log?->pageCount ?? 0];
            }
            $result[] = ['subject' => $s->toArray(), 'days' => $data];
        }
        return $result;
    }

    // ---------- ユーティリティ ----------

    /** @return string[] YYYY-MM-DD × 7 */
    private function getWeekDays(string $weekStart): array
    {
        $start = new \DateTimeImmutable($weekStart);
        $days  = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $start->modify("+{$i} days")->format('Y-m-d');
        }
        return $days;
    }

    /** @return string[] YYYY-MM-DD × 月の日数 */
    private function getMonthDays(string $yearMonth): array
    {
        $start = new \DateTimeImmutable($yearMonth . '-01');
        $last  = (int) $start->format('t');
        $days  = [];
        for ($i = 0; $i < $last; $i++) {
            $days[] = $start->modify("+{$i} days")->format('Y-m-d');
        }
        return $days;
    }

    /** @param StudyLog[] $logs */
    private function findLog(array $logs, string $subjectId, string $date): ?StudyLog
    {
        foreach ($logs as $l) {
            if ($l->subjectId === $subjectId && $l->date === $date) return $l;
        }
        return null;
    }
}
