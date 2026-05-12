<?php
declare(strict_types=1);

namespace SelfTrack\Api;

use SelfTrack\Model\TodoTask;
use SelfTrack\Repository\TodoRepository;

/**
 * ToDo REST エンドポイント（FR-012）
 *  GET    /api/todos           : 一覧取得
 *  POST   /api/todos           : 新規作成
 *  PUT    /api/todos/{id}      : 完了状態のトグル
 *  DELETE /api/todos/{id}      : 削除
 */
class TodoController
{
    private TodoRepository $repo;

    public function __construct(?TodoRepository $repo = null)
    {
        $this->repo = $repo ?? new TodoRepository();
    }

    public function index(): array
    {
        return [
            'success' => true,
            'data'    => array_map(fn($t) => $t->toArray(), $this->repo->findAll()),
        ];
    }

    public function store(array $body): array
    {
        if (empty($body['taskName']) || !is_string($body['taskName'])) {
            http_response_code(400);
            return ['success' => false, 'error' => 'taskName は必須です'];
        }
        $todo = new TodoTask(
            bin2hex(random_bytes(8)),
            $body['taskName'],
            false,
            date('c')
        );
        $this->repo->save($todo);
        http_response_code(201);
        return ['success' => true, 'data' => $todo->toArray()];
    }

    public function toggle(string $id): array
    {
        $todo = $this->repo->findById($id);
        if ($todo === null) {
            http_response_code(404);
            return ['success' => false, 'error' => 'Todo not found'];
        }
        $todo->toggleStatus();
        $this->repo->save($todo);
        return ['success' => true, 'data' => $todo->toArray()];
    }

    public function destroy(string $id): array
    {
        $this->repo->delete($id);
        return ['success' => true, 'data' => ['deletedId' => $id]];
    }
}
