<?php
declare(strict_types=1);

namespace SelfTrack\Repository;

use SelfTrack\Model\Subject;

/**
 * 教科データへのアクセスを担当
 * 内部的には LocalStorageRepository 経由でJSONファイルにアクセス
 */
class SubjectRepository
{
    private LocalStorageRepository $storage;

    public function __construct(?LocalStorageRepository $storage = null)
    {
        $this->storage = $storage ?? new LocalStorageRepository();
    }

    /** @return Subject[] */
    public function findAll(): array
    {
        return $this->storage->read()->subjects;
    }

    public function findById(string $id): ?Subject
    {
        foreach ($this->findAll() as $s) {
            if ($s->id === $id) return $s;
        }
        return null;
    }

    public function save(Subject $subject): void
    {
        $root = $this->storage->read();
        $found = false;
        foreach ($root->subjects as $i => $s) {
            if ($s->id === $subject->id) {
                $root->subjects[$i] = $subject;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $root->subjects[] = $subject;
        }
        $this->storage->write($root);
    }

    public function delete(string $id): void
    {
        $root = $this->storage->read();
        $root->subjects = array_values(array_filter(
            $root->subjects,
            fn(Subject $s) => $s->id !== $id
        ));
        $this->storage->write($root);
    }
}
