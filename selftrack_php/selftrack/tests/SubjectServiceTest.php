<?php
declare(strict_types=1);

use SelfTrack\Model\RootData;
use SelfTrack\Repository\LocalStorageRepository;
use SelfTrack\Repository\SubjectRepository;
use SelfTrack\Service\SubjectService;

/**
 * SubjectService 簡易テスト（PHPUnit不要・php tests/SubjectServiceTest.php で実行）
 */

require_once __DIR__ . '/../src/Middleware/CorsMiddleware.php';
require_once __DIR__ . '/../src/Middleware/JsonResponseMiddleware.php';
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

$tmpFile = sys_get_temp_dir() . '/selftrack_test_' . uniqid() . '.json';
$store   = new LocalStorageRepository($tmpFile);
$repo    = new SubjectRepository($store);
$service = new SubjectService($repo);

$pass = 0; $fail = 0;

function assert_eq(string $label, mixed $expected, mixed $actual): void {
    global $pass, $fail;
    if ($expected === $actual) {
        echo "  ✅ PASS: {$label}\n"; $pass++;
    } else {
        echo "  ❌ FAIL: {$label}\n     期待値: " . json_encode($expected) . "\n     実際値: " . json_encode($actual) . "\n";
        $fail++;
    }
}

echo "\n[SubjectService テスト]\n\n";

// 1. 正常追加
$r = $service->create(['title' => '英語', 'targetTime' => 300, 'targetPage' => 50, 'color' => '#059669', 'todayFlag' => true]);
assert_eq('科目追加が成功する', true, isset($r['data']));
assert_eq('タイトルが正しい', '英語', $r['data']['title'] ?? null);
assert_eq('todayFlagが保存される', true, $r['data']['todayFlag'] ?? null);

// 2. 重複タイトルはエラー
$r2 = $service->create(['title' => '英語', 'targetTime' => 100, 'targetPage' => 10]);
assert_eq('重複タイトルはエラー', true, isset($r2['errors']));

// 3. 空タイトルはエラー
$r3 = $service->create(['title' => '', 'targetTime' => 100, 'targetPage' => 10]);
assert_eq('空タイトルはエラー', true, isset($r3['errors']));

// 4. targetTime=0 はエラー
$r4 = $service->create(['title' => '数学', 'targetTime' => 0, 'targetPage' => 10]);
assert_eq('targetTime=0 はエラー', true, isset($r4['errors']));

// 5. 取得
$all = $service->getAll();
assert_eq('1件取得できる', 1, count($all));

// 6. 更新
$id  = $all[0]['id'];
$r5  = $service->update($id, ['title' => '英語（更新）', 'todayFlag' => false]);
assert_eq('更新が成功する', '英語（更新）', $r5['data']['title'] ?? null);
assert_eq('todayFlag更新が反映', false, $r5['data']['todayFlag'] ?? null);

// 7. 削除
$r6 = $service->delete($id);
assert_eq('削除メッセージあり', true, isset($r6['message']));
assert_eq('削除後0件', 0, count($service->getAll()));

@unlink($tmpFile);
echo "\n結果: {$pass}件成功 / {$fail}件失敗\n\n";
exit($fail > 0 ? 1 : 0);
