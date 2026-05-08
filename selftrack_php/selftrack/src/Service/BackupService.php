<?php
declare(strict_types=1);
namespace SelfTrack\Service;

use SelfTrack\Model\RootData;
use SelfTrack\Repository\LocalStorageRepository;

/**
 * FR-007: JSONエクスポート
 * FR-008: JSONインポート（NFR-002 不正JSON検証付き）
 */
class BackupService
{
    public function __construct(private LocalStorageRepository $store) {}

    /** エクスポート: 現在のデータをJSON文字列で返す */
    public function export(): string
    {
        $root = $this->store->load();
        return json_encode($root->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /** インポート: JSONを検証してストアに上書き保存 */
    public function import(string $jsonString): array
    {
        $data = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['errors' => ['JSONの解析に失敗しました: ' . json_last_error_msg()]];
        }

        $errors = ValidatorService::importData($data);
        if ($errors) return ['errors' => $errors];

        $root = RootData::fromArray($data);
        $this->store->save($root);

        return ['message' => 'インポートしました'];
    }
}
