import React, { useRef, useState } from 'react';
import Card from '../components/ui/Card.jsx';
import Button from '../components/ui/Button.jsx';
import { useStudyData } from '../hooks/useStudyData.js';
import { exportToFile, importFromFile } from '../utils/backupUtils.js';

/**
 * バックアップ画面（FR-007 / FR-008 / FR-015）
 * LocalStorage の全データを JSON 形式で書き出し / 復元する
 */
export default function Backup({ showToast }) {
  const { rawData, replaceAll, subjects, logs, todos, lastUpdated } = useStudyData();
  const fileInputRef = useRef(null);

  const handleExport = () => {
    try {
      exportToFile(rawData);
      showToast('JSONファイルをダウンロードしました', 'success');
    } catch (err) {
      showToast(`エクスポートに失敗しました: ${err.message}`, 'error');
    }
  };

  const handleImport = async (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    if (!window.confirm('現在のデータが上書きされます。続行しますか？')) {
      e.target.value = '';
      return;
    }
    try {
      const data = await importFromFile(file);
      replaceAll(data);
      showToast('インポートに成功しました', 'success');
    } catch (err) {
      showToast(err.message, 'error');
    } finally {
      e.target.value = '';
    }
  };

  return (
    <div className="page page--backup">
      <div className="page__header">
        <h2>💾 バックアップ</h2>
      </div>

      <Card title="現在のデータ概要">
        <ul className="info-list">
          <li>登録科目数: <strong>{subjects.length}</strong></li>
          <li>学習ログ件数: <strong>{logs.length}</strong></li>
          <li>ToDo件数: <strong>{todos.length}</strong></li>
          <li>最終更新: <strong>{lastUpdated}</strong></li>
        </ul>
      </Card>

      <Card title="📤 エクスポート（JSON書き出し）">
        <p>
          現在の全データを JSON ファイルとしてダウンロードします。<br />
          PC 変更時や定期バックアップにご利用ください。
        </p>
        <div className="form__actions">
          <Button variant="primary" onClick={handleExport}>
            JSON ファイルをダウンロード
          </Button>
        </div>
      </Card>

      <Card title="📥 インポート（JSON読み込み）">
        <p>
          以前にエクスポートした JSON ファイルを読み込み、データを復元します。<br />
          <span className="warning">⚠️ 現在のデータは完全に上書きされます。</span>
        </p>
        <div className="form__actions">
          <input
            ref={fileInputRef}
            type="file"
            accept="application/json,.json"
            onChange={handleImport}
            style={{ display: 'none' }}
          />
          <Button variant="secondary" onClick={() => fileInputRef.current?.click()}>
            JSON ファイルを選択
          </Button>
        </div>
      </Card>
    </div>
  );
}
