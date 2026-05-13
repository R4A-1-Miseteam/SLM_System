<?php
declare(strict_types=1);

namespace SelfTrack\Repository;

use SelfTrack\Model\StudyLog;

/**
 * 学習実績ログへのアクセスを担当
 */
class StudyLogRepository
{
    private LocalStorageRepository $storage;

    public function __construct(?LocalStorageRepository $storage = null)
    {
        $this->storage = $storage ?? new LocalStorageRepository();
    }

    /** @return StudyLog[] */
    public function findAll(): array
    {
        return $this->storage->read()->logs;
    }

    /** @return StudyLog[] */
    public function findBySubject(string $subjectId): array
    {
        return array_values(array_filter(
            $this->findAll(),
            fn(StudyLog $l) => $l->subjectId === $subjectId
        ));
    }

    /** 期間指定で取得（YYYY-MM-DD） */
    public function findByDateRange(string $start, string $end): array
    {
        return array_values(array_filter(
            $this->findAll(),
            fn(StudyLog $l) => $l->date >= $start && $l->date <= $end
        ));
    }

    public function save(StudyLog $log): void
    {
        $root = $this->storage->read();
        $found = false;
        foreach ($root->logs as $i => $l) {
            if ($l->id === $log->id) {
                $root->logs[$i] = $log;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $root->logs[] = $log;
        }
        $this->storage->write($root);
    }

    public function delete(string $id): void
    {
        $root = $this->storage->read();
        $root->logs = array_values(array_filter(
            $root->logs,
            fn(StudyLog $l) => $l->id !== $id
        ));
        $this->storage->write($root);
    }

    /** 教科に紐づくログを一括削除（cascade） */
    public function deleteBySubject(string $subjectId): int
    {
        $root = $this->storage->read();
        $before = count($root->logs);
        $root->logs = array_values(array_filter(
            $root->logs,
            fn(StudyLog $l) => $l->subjectId !== $subjectId
        ));
        $deleted = $before - count($root->logs);
        $this->storage->write($root);
        return $deleted;
    }
}
