# SelfTrack - 学習ペーサー v2.0

学習目標と実績のギャップを定量的に把握し、自律的な PDCA サイクルを支援する自己管理ツール。

## 📂 プロジェクト構成

```
selftrack/                      # ルート
├── selftrack/                  # PHP Backend
│   ├── .env
│   ├── .htaccess
│   ├── index.php               # エントリポイント
│   ├── composer.json
│   ├── src/
│   │   ├── autoload.php        # PSR-4 オートローダー
│   │   ├── Api/                # REST コントローラー
│   │   │   ├── SubjectController.php       [FR-001/002]
│   │   │   ├── StudyLogController.php      [FR-003/004]
│   │   │   ├── TodoController.php          [FR-012]
│   │   │   └── BackupController.php        [FR-007/008/015]
│   │   ├── Service/            # ビジネスロジック
│   │   │   ├── SubjectService.php          # 科目CRUD・cascade
│   │   │   ├── StudyLogService.php         # upsert・集計
│   │   │   ├── AchievementService.php      [FR-005] 達成率
│   │   │   ├── BackupService.php           # JSON export/import
│   │   │   └── ValidatorService.php        [NFR-002/003]
│   │   ├── Repository/         # データアクセス層
│   │   │   ├── LocalStorageRepository.php  [DR-004] シングルキー
│   │   │   ├── SubjectRepository.php
│   │   │   ├── StudyLogRepository.php
│   │   │   └── TodoRepository.php
│   │   ├── Model/              # エンティティ
│   │   │   ├── Subject.php
│   │   │   ├── StudyLog.php
│   │   │   ├── TodoTask.php
│   │   │   └── RootData.php
│   │   └── Middleware/
│   │       ├── CorsMiddleware.php
│   │       └── JsonResponseMiddleware.php
│   ├── storage/
│   │   └── selftrack_data.json [DR-005] データ永続化先
│   └── tests/
│       ├── SubjectServiceTest.php
│       ├── StudyLogServiceTest.php
│       └── ValidatorServiceTest.php
│
└── frontend/                   # React Frontend
    ├── package.json
    ├── vite.config.js
    ├── index.html
    └── src/
        ├── main.jsx
        ├── App.jsx
        ├── pages/              # 画面単位
        │   ├── Dashboard.jsx           [FR-005/006/016]
        │   ├── GoalSetting.jsx         [FR-001/002]
        │   ├── StudyLog.jsx            [FR-003/004/011]
        │   ├── Todo.jsx                [FR-012]
        │   └── Backup.jsx              [FR-007/008/015]
        ├── components/
        │   ├── layout/  (Header.jsx, GlobalNav.jsx)
        │   ├── dashboard/  (SubjectProgress / ProgressChart / TodayReminder / WeeklySummary)
        │   ├── timer/  (TimerOverlay / TimerRing / TimerLauncher) [FR-009/010]
        │   └── ui/  (Button / Card / Toast / ColorPicker / ProgressBar)
        ├── hooks/              # カスタムフック
        │   ├── useLocalStorage.js      [DR-001] 永続化
        │   ├── useStudyData.js         # 全データ管理・CRUD
        │   ├── useTimer.js             [FR-009/010]
        │   ├── useAchievement.js       [FR-005]
        │   ├── useChart.js             [FR-006]
        │   └── useTheme.js
        ├── store/              # 状態管理
        │   ├── studyDataStore.js
        │   └── timerStore.js
        ├── utils/
        │   ├── dateUtils.js
        │   ├── validator.js            [NFR-003]
        │   ├── uuid.js
        │   └── backupUtils.js
        └── styles/
            ├── global.css
            └── components.css
```

## 🚀 起動方法

### Backend (PHP)

```bash
cd selftrack
php -S localhost:8000
# → http://localhost:8000/api/subjects などにアクセス可能
```

### Frontend (React + Vite)

```bash
cd frontend
npm install
npm run dev
# → http://localhost:5173 で SPA が起動
# /api/* は localhost:8000 (PHP) にプロキシされます
```

### テスト実行 (PHP)

```bash
cd selftrack
composer install
./vendor/bin/phpunit tests
```

## 🧩 主要機能

| 機能ID | 機能名 | 優先度 | 担当ファイル |
|--------|--------|--------|--------------|
| FR-001/002 | 週次目標設定 | Must | GoalSetting.jsx, SubjectController.php |
| FR-003/004 | 学習実績記録 | Must | StudyLog.jsx, StudyLogController.php |
| FR-005     | 達成率計算 | Must | useAchievement.js, AchievementService.php |
| FR-006     | 進捗グラフ | Must | ProgressChart.jsx, useChart.js |
| FR-007/008 | バックアップ | Must | Backup.jsx, BackupController.php |
| FR-009/010 | ポモドーロ | Should | TimerOverlay.jsx, useTimer.js |
| FR-012     | ToDo | Should | Todo.jsx, TodoController.php |
| DR-001/004/005 | LocalStorage 永続化 | Must | useLocalStorage.js, LocalStorageRepository.php |
| NFR-002/003 | 入力検証・エラー表示 | Must | validator.js, ValidatorService.php |

## 🏛 アーキテクチャ

- **シングルキー・シングルオブジェクト方式**：全データを `study_pacer_data` キー1つで管理
- **完全スタンドアロン**：外部DB不要。React は LocalStorage、PHP は JSON ファイルに永続化
- **オフライン動作**：ネット未接続でも全機能が動作
- **責務分離**：Page → Hook → Store → API → Service → Repository → Storage
