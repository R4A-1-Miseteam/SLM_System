<?php
declare(strict_types=1);
namespace SelfTrack\Repository;

use SelfTrack\Model\RootData;

/**
 * DR-004 シングルキー・シングルオブジェクト方式
 * storage/selftrack_data.json を単一JSONとして読み書きする
 */
class LocalStorageRepository
{
    public function __construct(private string $filePath) {}

    public function load(): RootData
    {
        if (!file_exists($this->filePath)) {
            return new RootData(); // DR-003 初期化
        }
        $json = file_get_contents($this->filePath);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return new RootData();
        }
        return RootData::fromArray($data);
    }

    public function save(RootData $root): void
    {
        $root->lastUpdated = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);
        $json = json_encode($root->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($this->filePath, $json, LOCK_EX);
    }
}
