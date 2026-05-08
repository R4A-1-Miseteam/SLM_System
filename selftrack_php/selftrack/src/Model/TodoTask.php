<?php
declare(strict_types=1);
namespace SelfTrack\Model;

class TodoTask
{
    public string $id;
    public string $taskName;
    public bool   $isCompleted;
    public string $createdAt;

    public static function fromArray(array $d): self
    {
        $t              = new self();
        $t->id          = $d['id'];
        $t->taskName    = $d['taskName'];
        $t->isCompleted = (bool) ($d['isCompleted'] ?? false);
        $t->createdAt   = $d['createdAt'] ?? (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);
        return $t;
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
}
