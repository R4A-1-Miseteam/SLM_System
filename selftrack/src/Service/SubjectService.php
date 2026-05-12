<?php
declare(strict_types=1);

namespace SelfTrack\Service;

use SelfTrack\Model\Subject;
use SelfTrack\Repository\SubjectRepository;
use SelfTrack\Repository\StudyLogRepository;

/**
 * 教科のCRUDおよびcascade削除を担当
 */
class SubjectService
{
    private SubjectRepository  $subjectRepo;
    private StudyLogRepository $logRepo;
    private ValidatorService   $validator;

    public function __construct(
        ?SubjectRepository  $subjectRepo = null,
        ?StudyLogRepository $logRepo = null,
        ?ValidatorService   $validator = null
    ) {
        $this->subjectRepo = $subjectRepo ?? new SubjectRepository();
        $this->logRepo     = $logRepo     ?? new StudyLogRepository();
        $this->validator   = $validator   ?? new ValidatorService();
    }

    /** @return Subject[] */
    public function listAll(): array
    {
        return $this->subjectRepo->findAll();
    }

    public function createSubject(array $dto): Subject
    {
        $errors = $this->validator->validateSubjectDto($dto);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' / ', $errors));
        }

        $subject = new Subject(
            $this->generateId(),
            $dto['title'],
            (int)($dto['targetTime'] ?? 0),
            (int)($dto['targetPage'] ?? 0),
            $dto['memo'] ?? '',
            date('c')
        );
        $this->subjectRepo->save($subject);
        return $subject;
    }

    public function updateSubject(string $id, array $dto): Subject
    {
        $subject = $this->subjectRepo->findById($id);
        if ($subject === null) {
            throw new \RuntimeException("Subject not found: {$id}");
        }
        $errors = $this->validator->validateSubjectDto($dto);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' / ', $errors));
        }

        $subject->title = $dto['title'];
        $subject->updateTarget(
            (int)($dto['targetTime'] ?? $subject->targetTime),
            (int)($dto['targetPage'] ?? $subject->targetPage)
        );
        $subject->memo = $dto['memo'] ?? $subject->memo;
        $this->subjectRepo->save($subject);
        return $subject;
    }

    /** 教科削除：紐づく学習ログも cascade で削除 */
    public function deleteSubjectCascade(string $id): array
    {
        $subject = $this->subjectRepo->findById($id);
        if ($subject === null) {
            throw new \RuntimeException("Subject not found: {$id}");
        }
        $deletedLogs = $this->logRepo->deleteBySubject($id);
        $this->subjectRepo->delete($id);
        return [
            'deletedSubjectId' => $id,
            'deletedLogCount'  => $deletedLogs,
        ];
    }

    private function generateId(): string
    {
        return bin2hex(random_bytes(8));
    }
}
