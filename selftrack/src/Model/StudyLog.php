<?php
declare(strict_types=1);

namespace SelfTrack\Model;

/**
 * 学習実績ログ エンティティ
 * 日付ごとの学習時間とページ数を記録
 */
class StudyLog
{
    public string $id;
    public string $subjectId;
    public string $date;       // YYYY-MM-DD
    public int    $duration;   // 実施時間（分）
    public int    $pageCount;  // 完了ページ数
    public string $comment;

    public function __construct(
        string $id,
        string $subjectId,
        string $date,
        int    $duration = 0,
        int    $pageCount = 0,
        string $comment = ''
    ) {
        $this->id        = $id;
        $this->subjectId = $subjectId;
        $this->date      = $date;
        $this->duration  = $duration;
        $this->pageCount = $pageCount;
        $this->comment   = $comment;
    }

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'subjectId' => $this->subjectId,
            'date'      => $this->date,
            'duration'  => $this->duration,
            'pageCount' => $this->pageCount,
            'comment'   => $this->comment,
        ];
    }

    public static function fromArray(array $a): self
    {
        return new self(
            $a['id']        ?? '',
            $a['subjectId'] ?? '',
            $a['date']      ?? date('Y-m-d'),
            (int)($a['duration']  ?? 0),
            (int)($a['pageCount'] ?? 0),
            $a['comment']   ?? ''
        );
    }
}
