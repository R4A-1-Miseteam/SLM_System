/**
 * バックアップ関連ユーティリティ（FR-007 / FR-008）
 * JSON エクスポート / インポート処理
 */
import { validateImportJson } from './validator.js';

/** データを JSON ファイルとしてダウンロードさせる */
export function exportToFile(data, filename = null) {
  const json = JSON.stringify(data, null, 2);
  const blob = new Blob([json], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename || `selftrack_backup_${formatTimestamp()}.json`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

/** File オブジェクトから JSON を読み込み・検証する */
export function importFromFile(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      try {
        const data = JSON.parse(e.target.result);
        const errors = validateImportJson(data);
        if (errors.length > 0) {
          reject(new Error(errors.join(' / ')));
          return;
        }
        resolve(data);
      } catch (err) {
        reject(new Error('不正なファイルです: JSONとして解析できません'));
      }
    };
    reader.onerror = () => reject(new Error('ファイル読み込みに失敗しました'));
    reader.readAsText(file);
  });
}

function formatTimestamp() {
  const d = new Date();
  const pad = (n) => String(n).padStart(2, '0');
  return `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}_${pad(d.getHours())}${pad(d.getMinutes())}${pad(d.getSeconds())}`;
}
