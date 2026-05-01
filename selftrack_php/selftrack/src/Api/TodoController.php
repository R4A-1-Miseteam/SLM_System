<?php
declare(strict_types=1);
namespace SelfTrack\Api;

use SelfTrack\Repository\LocalStorageRepository;
use SelfTrack\Repository\TodoRepository;
use SelfTrack\Model\TodoTask;
use SelfTrack\Service\ValidatorService;

class TodoController
{
    private TodoRepository $repo;

    public function __construct(string $dataFile)
    {
        $store      = new LocalStorageRepository($dataFile);
        $this->repo = new TodoRepository($store);
    }

    /** GET /api/todos */
    public function index(): void
    {
        $todos = array_map(fn($t) => $t->toArray(), $this->repo->findAll());
        echo json_encode(['data' => $todos]);
    }

    /** POST /api/todos */
    public function store(): void
    {
        $body   = $this->getBody();
        $errors = ValidatorService::todo($body);
        if ($errors) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            return;
        }

        $todo              = new TodoTask();
        $todo->id          = $this->generateUuid();
        $todo->taskName    = trim($body['taskName']);
        $todo->isCompleted = false;
        $todo->createdAt   = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

        $this->repo->save($todo);
        http_response_code(201);
        echo json_encode(['data' => $todo->toArray()]);
    }

    /** PUT /api/todos/{id} （isCompleted トグル or taskName 更新） */
    public function update(string $id): void
    {
        $todos = $this->repo->findAll();
        $todo  = null;
        foreach ($todos as $t) {
            if ($t->id === $id) { $todo = $t; break; }
        }

        if (!$todo) {
            http_response_code(404);
            echo json_encode(['errors' => ['ToDoが見つかりません']]);
            return;
        }

        $body = $this->getBody();
        if (isset($body['isCompleted'])) $todo->isCompleted = (bool) $body['isCompleted'];
        if (isset($body['taskName']))    $todo->taskName    = trim($body['taskName']);

        $this->repo->save($todo);
        echo json_encode(['data' => $todo->toArray()]);
    }

    /** DELETE /api/todos/{id} */
    public function destroy(string $id): void
    {
        $this->repo->delete($id);
        echo json_encode(['message' => '削除しました']);
    }

    private function getBody(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    private function generateUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
