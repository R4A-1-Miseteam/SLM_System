<?php
declare(strict_types=1);

namespace SelfTrack\Model;

/**
 * 教科（学習科目）エンティティ
 * 週次目標時間・目標ページ数を保持
 */
class Subject
{
    public string $id;
    public string $title;
    public int    $targetTime;   // 週次目標時間（分）
    public int    $targetPage;   // 週次目標ページ数
    public string $memo;
    public string $createdAt;    // ISO8601

    public function __construct(
        string $id,
        string $title,
        int    $targetTime = 0,
        int    $targetPage = 0,
        string $memo = '',
        string $createdAt = ''
    ) {
        $this->id         = $id;
        $this->title      = $title;
        $this->targetTime = $targetTime;
        $this->targetPage = $targetPage;
        $this->memo       = $memo;
        $this->createdAt  = $createdAt ?: date('c');
    }

    /** 目標を更新する */
    public function updateTarget(int $time, int $page): void
    {
        $this->targetTime = $time;
        $this->targetPage = $page;
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'targetTime' => $this->targetTime,
            'targetPage' => $this->targetPage,
            'memo'       => $this->memo,
            'createdAt'  => $this->createdAt,
        ];
    }

    public static function fromArray(array $a): self
    {
        return new self(
            $a['id']         ?? '',
            $a['title']      ?? '',
            (int)($a['targetTime'] ?? 0),
            (int)($a['targetPage'] ?? 0),
            $a['memo']       ?? '',
            $a['createdAt']  ?? ''
        );
    }
}
