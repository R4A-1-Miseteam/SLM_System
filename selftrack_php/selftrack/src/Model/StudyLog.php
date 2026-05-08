<?php
declare(strict_types=1);
namespace SelfTrack\Model;

class StudyLog
{
    public string  $id;
    public string  $subjectId;     // FK → Subject.id (DR-008)
    public string  $date;          // YYYY-MM-DD (FR-004)
    public int     $duration;      // 0〜1440分 (NFR-003)
    public int     $pageCount;     // 0以上
    public string  $src;           // 'manual' | 'timer' (FR-010/011)
    public ?string $reflectionMemo; // FR-014 振り返りメモ (Should)
    public string  $savedAt;

    public static function fromArray(array $d): self
    {
        $l                 = new self();
        $l->id             = $d['id'];
        $l->subjectId      = $d['subjectId'];
        $l->date           = $d['date'];
        $l->duration       = (int) $d['duration'];
        $l->pageCount      = (int) $d['pageCount'];
        $l->src            = $d['src']            ?? 'manual';
        $l->reflectionMemo = $d['reflectionMemo'] ?? null;
        $l->savedAt        = $d['savedAt']        ?? (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);
        return $l;
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'subjectId'      => $this->subjectId,
            'date'           => $this->date,
            'duration'       => $this->duration,
            'pageCount'      => $this->pageCount,
            'src'            => $this->src,
            'reflectionMemo' => $this->reflectionMemo,
            'savedAt'        => $this->savedAt,
        ];
    }
}
