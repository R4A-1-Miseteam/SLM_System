<?php
declare(strict_types=1);
namespace SelfTrack\Model;

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

    public function __construct()
    {
        $this->version     = '1.1';
        $this->lastUpdated = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);
        $this->subjects    = [];
        $this->logs        = [];
        $this->todos       = [];
    }

    public static function fromArray(array $data): self
    {
        $root              = new self();
        $root->version     = $data['version']     ?? '1.1';
        $root->lastUpdated = $data['lastUpdated']  ?? $root->lastUpdated;
        $root->subjects    = array_map(fn($s) => Subject::fromArray($s),  $data['subjects'] ?? []);
        $root->logs        = array_map(fn($l) => StudyLog::fromArray($l), $data['logs']     ?? []);
        $root->todos       = array_map(fn($t) => TodoTask::fromArray($t), $data['todos']    ?? []);
        return $root;
    }

    public function toArray(): array
    {
        return [
            'version'     => $this->version,
            'lastUpdated' => $this->lastUpdated,
            'subjects'    => array_map(fn($s) => $s->toArray(), $this->subjects),
            'logs'        => array_map(fn($l) => $l->toArray(), $this->logs),
            'todos'       => array_map(fn($t) => $t->toArray(), $this->todos),
        ];
    }
}
