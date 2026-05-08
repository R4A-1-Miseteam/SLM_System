<?php
declare(strict_types=1);
namespace SelfTrack\Service;

use SelfTrack\Model\Subject;
use SelfTrack\Repository\SubjectRepository;

class SubjectService
{
    public function __construct(private SubjectRepository $repo) {}

    public function getAll(): array
    {
        return array_map(fn($s) => $s->toArray(), $this->repo->findAll());
    }

    public function create(array $data): array
    {
        $errors = ValidatorService::subject($data);
        if ($errors) return ['errors' => $errors];

        // タイトル重複チェック
        $existing = array_filter($this->repo->findAll(), fn($s) => $s->title === trim($data['title']));
        if ($existing) return ['errors' => ['その科目名はすでに登録されています']];

        $subject             = new Subject();
        $subject->id         = \Ramsey\Uuid\Uuid::uuid4()->toString(); // なければ uniqid 代替
        $subject->title      = trim($data['title']);
        $subject->targetTime = (int) $data['targetTime'];
        $subject->targetPage = (int) $data['targetPage'];
        $subject->color      = $data['color']    ?? '#059669';
        $subject->todayFlag  = (bool)($data['todayFlag'] ?? false);
        $subject->memo       = $data['memo']     ?? null;
        $subject->createdAt  = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

        // UUID fallback
        if (function_exists('com_create_guid')) {
            $subject->id = trim(com_create_guid(), '{}');
        } else {
            $subject->id = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
        }

        $this->repo->save($subject);
        return ['data' => $subject->toArray()];
    }

    public function update(string $id, array $data): array
    {
        $subject = $this->repo->findById($id);
        if (!$subject) return ['errors' => ['科目が見つかりません']];

        if (isset($data['title']))      $subject->title      = trim($data['title']);
        if (isset($data['targetTime'])) $subject->targetTime = (int) $data['targetTime'];
        if (isset($data['targetPage'])) $subject->targetPage = (int) $data['targetPage'];
        if (isset($data['color']))      $subject->color      = $data['color'];
        if (isset($data['todayFlag']))  $subject->todayFlag  = (bool) $data['todayFlag'];
        if (array_key_exists('memo', $data)) $subject->memo  = $data['memo'];

        $this->repo->save($subject);
        return ['data' => $subject->toArray()];
    }

    public function delete(string $id): array
    {
        $this->repo->delete($id); // DR-008 cascade
        return ['message' => '削除しました'];
    }
}
