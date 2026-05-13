<?php
declare(strict_types=1);

namespace SelfTrack\Tests;

use PHPUnit\Framework\TestCase;
use SelfTrack\Service\SubjectService;
use SelfTrack\Repository\SubjectRepository;
use SelfTrack\Repository\StudyLogRepository;
use SelfTrack\Repository\LocalStorageRepository;

/**
 * SubjectService 単体テスト
 */
class SubjectServiceTest extends TestCase
{
    private string $tmpFile;
    private SubjectService $service;

    protected function setUp(): void
    {
        $this->tmpFile = sys_get_temp_dir() . '/selftrack_test_' . uniqid() . '.json';
        $storage = new LocalStorageRepository($this->tmpFile);
        $this->service = new SubjectService(
            new SubjectRepository($storage),
            new StudyLogRepository($storage)
        );
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

    public function testCreateSubject(): void
    {
        $subject = $this->service->createSubject([
            'title'      => '英語',
            'targetTime' => 300,
            'targetPage' => 50,
        ]);
        $this->assertSame('英語', $subject->title);
        $this->assertSame(300, $subject->targetTime);
        $this->assertSame(50, $subject->targetPage);
    }

    public function testCreateSubjectWithInvalidTimeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->createSubject([
            'title'      => '数学',
            'targetTime' => 9999, // > 1440
        ]);
    }

    public function testDeleteSubjectCascadeReturnsResult(): void
    {
        $s = $this->service->createSubject(['title' => '物理', 'targetTime' => 60, 'targetPage' => 10]);
        $result = $this->service->deleteSubjectCascade($s->id);
        $this->assertSame($s->id, $result['deletedSubjectId']);
        $this->assertSame(0, $result['deletedLogCount']);
    }
}
