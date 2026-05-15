<?php
declare(strict_types=1);

namespace SelfTrack\Model;

/**
 * ToDoタスク エンティティ
 * 学習タスクの管理用
 */
class TodoTask
{
    public string $id;
    public string $taskName;
    public bool   $isCompleted;
    public string $createdAt;

    public function __construct(
        string $id,
        string $taskName,
        bool   $isCompleted = false,
        string $createdAt = ''
    ) {
        $this->id          = $id;
        $this->taskName    = $taskName;
        $this->isCompleted = $isCompleted;
        $this->createdAt   = $createdAt ?: date('c');
    }

    /** 完了状態を反転する */
    public function toggleStatus(): void
    {
        $this->isCompleted = !$this->isCompleted;
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'taskName'    => $this->taskName,
            'isCompleted' => $this->isCompleted,
            'createdAt'   => $this->createdAt,
        ];
    }

    public static function fromArray(array $a): self
    {
        return new self(
            $a['id']          ?? '',
            $a['taskName']    ?? '',
            (bool)($a['isCompleted'] ?? false),
            $a['createdAt']   ?? ''
        );
    }
}
