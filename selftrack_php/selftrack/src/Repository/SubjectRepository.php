<?php
declare(strict_types=1);
namespace SelfTrack\Repository;

use SelfTrack\Model\Subject;

class SubjectRepository
{
    public function __construct(private LocalStorageRepository $store) {}

    /** @return Subject[] */
    public function findAll(): array
    {
        return $this->store->load()->subjects;
    }

    public function findById(string $id): ?Subject
    {
        foreach ($this->store->load()->subjects as $s) {
            if ($s->id === $id) return $s;
        }
        return null;
    }

    public function save(Subject $subject): void
    {
        $root = $this->store->load();
        $idx  = array_search($subject->id, array_column(
            array_map(fn($s) => $s->toArray(), $root->subjects), 'id'
        ));
        if ($idx === false) {
            $root->subjects[] = $subject;
        } else {
            $root->subjects[$idx] = $subject;
        }
        $this->store->save($root);
    }

    /** DR-008 カスケード削除: 関連する StudyLog も同時削除 */
    public function delete(string $id): void
    {
        $root          = $this->store->load();
        $root->subjects = array_values(array_filter($root->subjects, fn($s) => $s->id !== $id));
        $root->logs     = array_values(array_filter($root->logs,     fn($l) => $l->subjectId !== $id));
        $this->store->save($root);
    }
}
