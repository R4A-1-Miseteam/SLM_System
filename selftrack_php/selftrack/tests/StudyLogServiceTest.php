<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Model/RootData.php';
require_once __DIR__ . '/../src/Model/Subject.php';
require_once __DIR__ . '/../src/Model/StudyLog.php';
require_once __DIR__ . '/../src/Model/TodoTask.php';
require_once __DIR__ . '/../src/Repository/LocalStorageRepository.php';
require_once __DIR__ . '/../src/Repository/SubjectRepository.php';
require_once __DIR__ . '/../src/Repository/StudyLogRepository.php';
require_once __DIR__ . '/../src/Repository/TodoRepository.php';
require_once __DIR__ . '/../src/Service/ValidatorService.php';
require_once __DIR__ . '/../src/Service/SubjectService.php';
require_once __DIR__ . '/../src/Service/StudyLogService.php';

$tmpFile    = sys_get_temp_dir() . '/selftrack_log_test_' . uniqid() . '.json';
$store      = new SelfTrack\Repository\LocalStorageRepository($tmpFile);
$subRepo    = new SelfTrack\Repository\SubjectRepository($store);
$logRepo    = new SelfTrack\Repository\StudyLogRepository($store);
$subService = new SelfTrack\Service\SubjectService($subRepo);
$logService = new SelfTrack\Service\StudyLogService($logRepo, $subRepo);

$pass = 0; $fail = 0;

function assert_eq2(string $label, mixed $expected, mixed $actual): void {
    global $pass, $fail;
    if ($expected === $actual) { echo "  ✅ PASS: {$label}\n"; $pass++; }
    else { echo "  ❌ FAIL: {$label}\n     期待: " . json_encode($expected) . "\n     実際: " . json_encode($actual) . "\n"; $fail++; }
}

echo "\n[StudyLogService テスト]\n\n";

// 科目を先に作成
$sub = $subService->create(['title' => '数学', 'targetTime' => 300, 'targetPage' => 40]);
$sid = $sub['data']['id'];

// 1. 正常保存
$r = $logService->upsert(['subjectId' => $sid, 'date' => '2026-04-28', 'duration' => 60, 'pageCount' => 10, 'src' => 'manual']);
assert_eq2('実績保存が成功する', true, isset($r['data']));
assert_eq2('updated=false（新規）', false, $r['updated'] ?? null);

// 2. 同日同科目は upsert
$r2 = $logService->upsert(['subjectId' => $sid, 'date' => '2026-04-28', 'duration' => 90, 'pageCount' => 15, 'src' => 'timer']);
assert_eq2('upsertが成功する', true, isset($r2['data']));
assert_eq2('updated=true（上書き）', true, $r2['updated'] ?? null);
assert_eq2('durationが更新される', 90, $r2['data']['duration'] ?? null);
assert_eq2('srcがtimerに更新', 'timer', $r2['data']['src'] ?? null);

// 3. duration 範囲外
$r3 = $logService->upsert(['subjectId' => $sid, 'date' => '2026-04-29', 'duration' => 1500, 'pageCount' => 0, 'src' => 'manual']);
assert_eq2('duration>1440 はエラー', true, isset($r3['errors']));

// 4. 存在しない科目ID
$r4 = $logService->upsert(['subjectId' => 'no-such-id', 'date' => '2026-04-29', 'duration' => 30, 'pageCount' => 0, 'src' => 'manual']);
assert_eq2('存在しない科目IDはエラー', true, isset($r4['errors']));

// 5. 日付形式不正
$r5 = $logService->upsert(['subjectId' => $sid, 'date' => '2026/04/29', 'duration' => 30, 'pageCount' => 0, 'src' => 'manual']);
assert_eq2('日付形式不正はエラー', true, isset($r5['errors']));

@unlink($tmpFile);
echo "\n結果: {$pass}件成功 / {$fail}件失敗\n\n";
exit($fail > 0 ? 1 : 0);
