<?php
declare(strict_types=1);
namespace SelfTrack\Api;

use SelfTrack\Repository\LocalStorageRepository;
use SelfTrack\Repository\SubjectRepository;
use SelfTrack\Service\SubjectService;

class SubjectController
{
    private SubjectService $service;

    public function __construct(string $dataFile)
    {
        $store         = new LocalStorageRepository($dataFile);
        $repo          = new SubjectRepository($store);
        $this->service = new SubjectService($repo);
    }

    /** GET /api/subjects */
    public function index(): void
    {
        echo json_encode(['data' => $this->service->getAll()]);
    }

    /** POST /api/subjects */
    public function store(): void
    {
        $body   = $this->getBody();
        $result = $this->service->create($body);
        http_response_code(isset($result['errors']) ? 422 : 201);
        echo json_encode($result);
    }

    /** PUT /api/subjects/{id} */
    public function update(string $id): void
    {
        $body   = $this->getBody();
        $result = $this->service->update($id, $body);
        http_response_code(isset($result['errors']) ? 422 : 200);
        echo json_encode($result);
    }

    /** DELETE /api/subjects/{id} */
    public function destroy(string $id): void
    {
        $result = $this->service->delete($id);
        echo json_encode($result);
    }

    private function getBody(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
}
