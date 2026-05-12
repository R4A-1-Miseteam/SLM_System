<?php
declare(strict_types=1);

namespace SelfTrack\Service;

/**
 * 入力値の検証サービス（NFR-002 / NFR-003）
 * - 不正な数値（負数・過大値）を弾く
 * - 不正なJSON構造を検出する
 */
class ValidatorService
{
    public const MAX_MINUTES_PER_DAY = 1440; // 24時間
    public const MAX_PAGES_PER_DAY   = 9999;

    /** 時間（分）の検証：0〜1440 */
    public function validateTime(int $minutes): bool
    {
        return $minutes >= 0 && $minutes <= self::MAX_MINUTES_PER_DAY;
    }

    /** ページ数の検証：0〜9999 */
    public function validatePage(int $pages): bool
    {
        return $pages >= 0 && $pages <= self::MAX_PAGES_PER_DAY;
    }

    /** 日付フォーマットの検証（YYYY-MM-DD） */
    public function validateDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /** Subject 入力DTOの検証 */
    public function validateSubjectDto(array $dto): array
    {
        $errors = [];
        if (empty($dto['title']) || !is_string($dto['title'])) {
            $errors[] = 'title は必須かつ文字列である必要があります';
        }
        if (isset($dto['targetTime']) && !$this->validateTime((int)$dto['targetTime'])) {
            $errors[] = 'targetTime は 0〜1440 分の範囲で指定してください';
        }
        if (isset($dto['targetPage']) && !$this->validatePage((int)$dto['targetPage'])) {
            $errors[] = 'targetPage は 0〜9999 の範囲で指定してください';
        }
        return $errors;
    }

    /** StudyLog 入力DTOの検証 */
    public function validateLogDto(array $dto): array
    {
        $errors = [];
        if (empty($dto['subjectId'])) {
            $errors[] = 'subjectId は必須です';
        }
        if (empty($dto['date']) || !$this->validateDate($dto['date'])) {
            $errors[] = 'date は YYYY-MM-DD 形式で指定してください';
        }
        if (isset($dto['duration']) && !$this->validateTime((int)$dto['duration'])) {
            $errors[] = 'duration は 0〜1440 分の範囲で指定してください';
        }
        if (isset($dto['pageCount']) && !$this->validatePage((int)$dto['pageCount'])) {
            $errors[] = 'pageCount は 0〜9999 の範囲で指定してください';
        }
        return $errors;
    }

    /** インポートJSONの構造検証 */
    public function validateImportJson($data): array
    {
        $errors = [];
        if (!is_array($data)) {
            $errors[] = '不正なファイルです: JSONがオブジェクトではありません';
            return $errors;
        }
        foreach (['version', 'subjects', 'logs', 'todos'] as $key) {
            if (!array_key_exists($key, $data)) {
                $errors[] = "不正なファイルです: 必須キー '{$key}' が存在しません";
            }
        }
        if (isset($data['subjects']) && !is_array($data['subjects'])) {
            $errors[] = '不正なファイルです: subjects は配列である必要があります';
        }
        if (isset($data['logs']) && !is_array($data['logs'])) {
            $errors[] = '不正なファイルです: logs は配列である必要があります';
        }
        if (isset($data['todos']) && !is_array($data['todos'])) {
            $errors[] = '不正なファイルです: todos は配列である必要があります';
        }
        return $errors;
    }
}
