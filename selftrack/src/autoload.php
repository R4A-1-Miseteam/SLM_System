<?php
/**
 * シンプルな PSR-4 オートローダー
 * Composer 不要で動作するよう手動実装
 */
spl_autoload_register(function (string $class): void {
    $prefix = 'SelfTrack\\';
    $baseDir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
