<?php
declare(strict_types=1);
namespace SelfTrack\Model;

class Subject
{
    public string  $id;
    public string  $title;
    public int     $targetTime;  // 分 (FR-001)
    public int     $targetPage;  // ページ (FR-001)
    public string  $color;       // #rrggbb
    public bool    $todayFlag;   // FR-016 リマインド
    public ?string $memo;        // FR-013 教科別補足メモ (Should)
    public string  $createdAt;

    public static function fromArray(array $d): self
    {
        $s             = new self();
        $s->id         = $d['id'];
        $s->title      = $d['title'];
        $s->targetTime = (int) $d['targetTime'];
        $s->targetPage = (int) $d['targetPage'];
        $s->color      = $d['color']     ?? '#059669';
        $s->todayFlag  = (bool) ($d['todayFlag'] ?? false);
        $s->memo       = $d['memo']      ?? null;
        $s->createdAt  = $d['createdAt'] ?? (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);
        return $s;
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'targetTime' => $this->targetTime,
            'targetPage' => $this->targetPage,
            'color'      => $this->color,
            'todayFlag'  => $this->todayFlag,
            'memo'       => $this->memo,
            'createdAt'  => $this->createdAt,
        ];
    }
}
