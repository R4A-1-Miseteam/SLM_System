<?php
declare(strict_types=1);

namespace SelfTrack\Tests;

use PHPUnit\Framework\TestCase;
use SelfTrack\Service\StudyLogService;
use SelfTrack\Repository\StudyLogRepository;
use SelfTrack\Repository\LocalStorageRepository;

/**
 * StudyLogService 単体テスト
 */
class StudyLogServiceTest extends TestCase
{
    private string $tmpFile;
    private StudyLogService $service;

    protected function setUp(): void
    {
        $this->tmpFile = sys_get_temp_dir() . '/selftrack_test_' . uniqid() . '.json';
        $storage = new LocalStorageRepository($this->tmpFile);
        $this->service = new StudyLogService(new StudyLogRepository($storage));
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tmpFile)) {
            unlink($this->tmpFile);
        }
    }

    public function testUpsertCreatesNewLog(): void
    {
        $log = $this->service->upsertLog([
            'subjectId' => 'subj-001',
            'date'      => '2026-05-08',
            'duration'  => 60,
            'pageCount' => 10,
        ]);
        $this->assertSame('subj-001', $log->subjectId);
        $this->assertSame(60, $log->duration);
    }

    public function testUpsertUpdatesExistingLogSameDay(): void
    {
        $first  = $this->service->upsertLog([
            'subjectId' => 'subj-001', 'date' => '2026-05-08',
            'duration'  => 30, 'pageCount' => 5,
        ]);
        $second = $this->service->upsertLog([
            'subjectId' => 'subj-001', 'date' => '2026-05-08',
            'duration'  => 90, 'pageCount' => 15,
        ]);
        $this->assertSame($first->id, $second->id, '同日同教科のログは更新される');
        $this->assertSame(90, $second->duration);
    }

    public function testInvalidDurationThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->upsertLog([
            'subjectId' => 'subj-001',
            'date'      => '2026-05-08',
            'duration'  => 1500, // > 1440
        ]);
    }
}
