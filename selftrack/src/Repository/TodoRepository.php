<?php
declare(strict_types=1);

namespace SelfTrack\Repository;

use SelfTrack\Model\TodoTask;

/**
 * ToDoタスクへのアクセスを担当
 */
class TodoRepository
{
    private LocalStorageRepository $storage;

    public function __construct(?LocalStorageRepository $storage = null)
    {
        $this->storage = $storage ?? new LocalStorageRepository();
    }

    /** @return TodoTask[] */
    public function findAll(): array
    {
        return $this->storage->read()->todos;
    }

    public function findById(string $id): ?TodoTask
    {
        foreach ($this->findAll() as $t) {
            if ($t->id === $id) return $t;
        }
        return null;
    }

    public function save(TodoTask $todo): void
    {
        $root = $this->storage->read();
        $found = false;
        foreach ($root->todos as $i => $t) {
            if ($t->id === $todo->id) {
                $root->todos[$i] = $todo;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $root->todos[] = $todo;
        }
        $this->storage->write($root);
    }

    public function delete(string $id): void
    {
        $root = $this->storage->read();
        $root->todos = array_values(array_filter(
            $root->todos,
            fn(TodoTask $t) => $t->id !== $id
        ));
        $this->storage->write($root);
    }
}
