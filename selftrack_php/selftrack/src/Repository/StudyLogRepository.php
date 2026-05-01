<?php
declare(strict_types=1);
namespace SelfTrack\Repository;

use SelfTrack\Model\StudyLog;

class StudyLogRepository
{
    public function __construct(private LocalStorageRepository $store) {}

    /** @return StudyLog[] */
    public function findAll(): array
    {
        return $this->store->load()->logs;
    }

    public function findBySubjectAndDate(string $subjectId, string $date): ?StudyLog
    {
        foreach ($this->store->load()->logs as $l) {
            if ($l->subjectId === $subjectId && $l->date === $date) return $l;
        }
        return null;
    }

    /** 同日同科目は upsert（上書き） */
    public function upsert(StudyLog $log): void
    {
        $root = $this->store->load();
        $found = false;
        foreach ($root->logs as &$l) {
            if ($l->subjectId === $log->subjectId && $l->date === $log->date) {
                $l     = $log;
                $found = true;
                break;
            }
        }
        unset($l);
        if (!$found) $root->logs[] = $log;
        $this->store->save($root);
    }
}
