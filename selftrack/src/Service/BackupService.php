<?php
declare(strict_types=1);

namespace SelfTrack\Service;

use SelfTrack\Model\RootData;
use SelfTrack\Repository\LocalStorageRepository;

/**
 * バックアップサービス（FR-007 / FR-008）
 * JSON形式でのエクスポート / インポートを担当
 */
class BackupService
{
    private LocalStorageRepository $storage;
    private ValidatorService       $validator;

    public function __construct(
        ?LocalStorageRepository $storage = null,
        ?ValidatorService       $validator = null
    ) {
        $this->storage   = $storage   ?? new LocalStorageRepository();
        $this->validator = $validator ?? new ValidatorService();
    }

    /** 全データを JSON 文字列としてエクスポート */
    public function exportJSON(): string
    {
        $data = $this->storage->read();
        $json = json_encode(
            $data->toArray(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        if ($json === false) {
            throw new \RuntimeException('JSON encode failed');
        }
        return $json;
    }

    /**
     * インポート処理
     * @throws \InvalidArgumentException 不正なJSON構造の場合
     */
    public function importJSON(array $data): bool
    {
        $errors = $this->validator->validateImportJson($data);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' / ', $errors));
        }
        $root = RootData::fromArray($data);
        $this->storage->write($root);
        return true;
    }
}
