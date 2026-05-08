<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Service/ValidatorService.php';

use SelfTrack\Service\ValidatorService;

$pass = 0; $fail = 0;

function assert_eq3(string $label, mixed $expected, mixed $actual): void {
    global $pass, $fail;
    if ($expected === $actual) { echo "  ✅ PASS: {$label}\n"; $pass++; }
    else { echo "  ❌ FAIL: {$label}\n     期待: " . json_encode($expected) . "\n     実際: " . json_encode($actual) . "\n"; $fail++; }
}

echo "\n[ValidatorService テスト]\n\n";

// Subject
assert_eq3('正常な科目入力はエラーなし',    0, count(ValidatorService::subject(['title'=>'英語','targetTime'=>100,'targetPage'=>10])));
assert_eq3('空タイトルはエラー',            1, count(ValidatorService::subject(['title'=>'','targetTime'=>100,'targetPage'=>10])));
assert_eq3('targetTime=0はエラー',          1, count(ValidatorService::subject(['title'=>'英語','targetTime'=>0,'targetPage'=>10])));
assert_eq3('targetPage=-1はエラー',         1, count(ValidatorService::subject(['title'=>'英語','targetTime'=>100,'targetPage'=>-1])));

// StudyLog
assert_eq3('正常な実績入力はエラーなし',    0, count(ValidatorService::studyLog(['subjectId'=>'id','date'=>'2026-04-28','duration'=>60,'pageCount'=>10,'src'=>'manual'])));
assert_eq3('duration=1440はOK',             0, count(ValidatorService::studyLog(['subjectId'=>'id','date'=>'2026-04-28','duration'=>1440,'pageCount'=>0,'src'=>'timer'])));
assert_eq3('duration=1441はエラー',         1, count(ValidatorService::studyLog(['subjectId'=>'id','date'=>'2026-04-28','duration'=>1441,'pageCount'=>0,'src'=>'manual'])));
assert_eq3('duration=-1はエラー',           1, count(ValidatorService::studyLog(['subjectId'=>'id','date'=>'2026-04-28','duration'=>-1,'pageCount'=>0,'src'=>'manual'])));
assert_eq3('不正日付形式はエラー',          1, count(ValidatorService::studyLog(['subjectId'=>'id','date'=>'2026/04/28','duration'=>30,'pageCount'=>0,'src'=>'manual'])));
assert_eq3('不正srcはエラー',               1, count(ValidatorService::studyLog(['subjectId'=>'id','date'=>'2026-04-28','duration'=>30,'pageCount'=>0,'src'=>'auto'])));

// Import
assert_eq3('正常なインポートデータ',        0, count(ValidatorService::importData(['version'=>'1.1','subjects'=>[],'logs'=>[],'todos'=>[]])));
assert_eq3('versionなしはエラー',           1, count(ValidatorService::importData(['subjects'=>[],'logs'=>[],'todos'=>[]])));
assert_eq3('非配列はエラー',               1, count(ValidatorService::importData('not array')));

echo "\n結果: {$pass}件成功 / {$fail}件失敗\n\n";
exit($fail > 0 ? 1 : 0);
