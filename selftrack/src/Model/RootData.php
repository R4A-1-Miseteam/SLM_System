<?php
declare(strict_types=1);

namespace SelfTrack\Model;

/**
 * ルートデータオブジェクト
 * LocalStorage（JSON）に格納される全データの最上位構造
 * シングルキー・シングルオブジェクト方式
 */
class RootData
{
    public string $version;
    public string $lastUpdated;
    /** @var Subject[] */
    public array $subjects;
    /** @var StudyLog[] */
    public array $logs;
    /** @var TodoTask[] */
    public array $todos;

    public function __construct(
        string $version = '2.0.0',
        string $lastUpdated = '',
        array  $subjects = [],
        array  $logs = [],
        array  $todos = []
    ) {
        $this->version     = $version;
        $this->lastUpdated = $lastUpdated ?: date('c');
        $this->subjects    = $subjects;
        $this->logs        = $logs;
        $this->todos       = $todos;
    }

    /** 空の初期データを生成 */
    public static function createEmpty(): self
    {
        return new self('2.0.0', date('c'), [], [], []);
    }

    public function toArray(): array
    {
        return [
            'version'     => $this->version,
            'lastUpdated' => $this->lastUpdated,
            'subjects'    => array_map(fn(Subject $s)  => $s->toArray(), $this->subjects),
            'logs'        => array_map(fn(StudyLog $l) => $l->toArray(), $this->logs),
            'todos'       => array_map(fn(TodoTask $t) => $t->toArray(), $this->todos),
        ];
    }

    public static function fromArray(array $a): self
    {
        return new self(
            $a['version']     ?? '2.0.0',
            $a['lastUpdated'] ?? date('c'),
            array_map(fn($s) => Subject::fromArray($s),  $a['subjects'] ?? []),
            array_map(fn($l) => StudyLog::fromArray($l), $a['logs']     ?? []),
            array_map(fn($t) => TodoTask::fromArray($t), $a['todos']    ?? [])
        );
    }
}
