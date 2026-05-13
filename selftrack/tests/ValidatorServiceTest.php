<?php
declare(strict_types=1);

namespace SelfTrack\Tests;

use PHPUnit\Framework\TestCase;
use SelfTrack\Service\ValidatorService;

/**
 * ValidatorService 単体テスト（NFR-002 / NFR-003）
 */
class ValidatorServiceTest extends TestCase
{
    private ValidatorService $validator;

    protected function setUp(): void
    {
        $this->validator = new ValidatorService();
    }

    public function testValidateTime(): void
    {
        $this->assertTrue($this->validator->validateTime(0));
        $this->assertTrue($this->validator->validateTime(1440));
        $this->assertFalse($this->validator->validateTime(-1));
        $this->assertFalse($this->validator->validateTime(1441));
    }

    public function testValidatePage(): void
    {
        $this->assertTrue($this->validator->validatePage(0));
        $this->assertTrue($this->validator->validatePage(9999));
        $this->assertFalse($this->validator->validatePage(-5));
        $this->assertFalse($this->validator->validatePage(10000));
    }

    public function testValidateDate(): void
    {
        $this->assertTrue($this->validator->validateDate('2026-05-08'));
        $this->assertFalse($this->validator->validateDate('2026/05/08'));
        $this->assertFalse($this->validator->validateDate('invalid'));
    }

    public function testValidateImportJsonDetectsMissingKey(): void
    {
        $errors = $this->validator->validateImportJson(['version' => '2.0.0']);
        $this->assertNotEmpty($errors);
    }

    public function testValidateImportJsonAcceptsValid(): void
    {
        $errors = $this->validator->validateImportJson([
            'version'  => '2.0.0',
            'subjects' => [],
            'logs'     => [],
            'todos'    => [],
        ]);
        $this->assertEmpty($errors);
    }
}
