<?php
declare(strict_types=1);

namespace SelfTrack\Api;

use SelfTrack\Service\SubjectService;

/**
 * 教科 REST エンドポイント（FR-001 / FR-002）
 *  GET    /api/subjects        : 一覧取得
 *  POST   /api/subjects        : 新規作成
 *  PUT    /api/subjects/{id}   : 更新
 *  DELETE /api/subjects/{id}   : 削除（cascade）
 */
class SubjectController
{
    private SubjectService $service;

    public function __construct(?SubjectService $service = null)
    {
        $this->service = $service ?? new SubjectService();
    }

    public function index(): array
    {
        return [
            'success' => true,
            'data'    => array_map(fn($s) => $s->toArray(), $this->service->listAll()),
        ];
    }

    public function store(array $body): array
    {
        try {
            $subject = $this->service->createSubject($body);
            http_response_code(201);
            return ['success' => true, 'data' => $subject->toArray()];
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function update(string $id, array $body): array
    {
        try {
            $subject = $this->service->updateSubject($id, $body);
            return ['success' => true, 'data' => $subject->toArray()];
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            return ['success' => false, 'error' => $e->getMessage()];
        } catch (\RuntimeException $e) {
            http_response_code(404);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function destroy(string $id): array
    {
        try {
            $result = $this->service->deleteSubjectCascade($id);
            return ['success' => true, 'data' => $result];
        } catch (\RuntimeException $e) {
            http_response_code(404);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
