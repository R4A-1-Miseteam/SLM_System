<?php
declare(strict_types=1);
namespace SelfTrack\Repository;

use SelfTrack\Model\TodoTask;

class TodoRepository
{
    public function __construct(private LocalStorageRepository $store) {}

    /** @return TodoTask[] */
    public function findAll(): array
    {
        return $this->store->load()->todos;
    }

    public function save(TodoTask $todo): void
    {
        $root = $this->store->load();
        $idx  = array_search($todo->id, array_column(
            array_map(fn($t) => $t->toArray(), $root->todos), 'id'
        ));
        if ($idx === false) {
            $root->todos[] = $todo;
        } else {
            $root->todos[$idx] = $todo;
        }
        $this->store->save($root);
    }

    public function delete(string $id): void
    {
        $root        = $this->store->load();
        $root->todos = array_values(array_filter($root->todos, fn($t) => $t->id !== $id));
        $this->store->save($root);
    }
}
