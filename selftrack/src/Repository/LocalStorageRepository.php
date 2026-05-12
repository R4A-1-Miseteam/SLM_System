<?php
declare(strict_types=1);

namespace SelfTrack\Repository;

use SelfTrack\Model\RootData;

/**
 * LocalStorage Repository（DR-004 シングルキー）
 * 
 * storage/selftrack_data.json への読み書きを一元管理する
 * シングルキー・シングルオブジェクト方式により、
 * 全データを1つのJSONファイルとして永続化する
 */
class LocalStorageRepository
{
    private string $filePath;

    public function __construct(?string $filePath = null)
    {
        $this->filePath = $filePath ?? __DIR__ . '/../../storage/selftrack_data.json';
        $this->ensureStorageExists();
    }

    /** 初期化処理：ファイルが存在しない場合は空のデータ構造を作成 */
    private function ensureStorageExists(): void
    {
        $dir = dirname($this->filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!file_exists($this->filePath)) {
            $empty = RootData::createEmpty();
            $this->write($empty);
        }
    }

    /** 全データを読み込む */
    public function read(): RootData
    {
        $json = file_get_contents($this->filePath);
        if ($json === false || $json === '') {
            return RootData::createEmpty();
        }
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return RootData::createEmpty();
        }
        return RootData::fromArray($data);
    }

    /** 全データを書き込む（lastUpdated を自動更新） */
    public function write(RootData $data): void
    {
        $data->lastUpdated = date('c');
        $json = json_encode(
            $data->toArray(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        if ($json === false) {
            throw new \RuntimeException('JSON encode failed');
        }
        // アトミックな書き込み（一時ファイル経由）
        $tmp = $this->filePath . '.tmp';
        file_put_contents($tmp, $json, LOCK_EX);
        rename($tmp, $this->filePath);
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
