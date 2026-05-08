<?php
declare(strict_types=1);
namespace SelfTrack\Service;

/**
 * NFR-002 不正JSON検証 / NFR-003 入力制限バリデーション
 */
class ValidatorService
{
    /** Subject 入力検証 */
    public static function subject(array $d): array
    {
        $errors = [];
        if (empty(trim($d['title'] ?? '')))     $errors[] = '科目名は必須です';
        if (($d['targetTime'] ?? 0) < 1)        $errors[] = '目標時間は1分以上で入力してください';
        if (($d['targetPage'] ?? -1) < 0)       $errors[] = '目標ページ数は0以上で入力してください';
        return $errors;
    }

    /** StudyLog 入力検証 */
    public static function studyLog(array $d): array
    {
        $errors = [];
        if (empty($d['subjectId'] ?? ''))       $errors[] = '科目IDは必須です';
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $d['date'] ?? ''))
                                                $errors[] = '日付形式が不正です (YYYY-MM-DD)';
        $dur = (int)($d['duration'] ?? -1);
        if ($dur < 0 || $dur > 1440)            $errors[] = '実施時間は0〜1440分で入力してください';
        if (($d['pageCount'] ?? -1) < 0)        $errors[] = '完了ページ数は0以上で入力してください';
        if (!in_array($d['src'] ?? '', ['manual', 'timer'], true))
                                                $errors[] = 'srcは manual または timer のみ有効です';
        return $errors;
    }

    /** TodoTask 入力検証 */
    public static function todo(array $d): array
    {
        $errors = [];
        if (empty(trim($d['taskName'] ?? '')))  $errors[] = 'タスク内容は必須です';
        return $errors;
    }

    /** インポートJSONのスキーマ検証 (NFR-002) */
    public static function importData(mixed $data): array
    {
        $errors = [];
        if (!is_array($data))                           { $errors[] = 'JSONの形式が不正です'; return $errors; }
        if (empty($data['version']))                    $errors[] = 'version フィールドが存在しません';
        if (!isset($data['subjects']) || !is_array($data['subjects'])) $errors[] = 'subjects が不正です';
        if (!isset($data['logs'])     || !is_array($data['logs']))     $errors[] = 'logs が不正です';
        if (!isset($data['todos'])    || !is_array($data['todos']))    $errors[] = 'todos が不正です';
        return $errors;
    }
}
