/**
 * 入力検証ユーティリティ（NFR-003）
 */
export const MAX_MINUTES_PER_DAY = 1440;
export const MAX_PAGES_PER_DAY = 9999;

export function validateTime(min) {
  const n = Number(min);
  return Number.isFinite(n) && n >= 0 && n <= MAX_MINUTES_PER_DAY;
}

export function validatePage(page) {
  const n = Number(page);
  return Number.isFinite(n) && n >= 0 && n <= MAX_PAGES_PER_DAY;
}

export function validateNonEmpty(str) {
  return typeof str === 'string' && str.trim().length > 0;
}

/** インポートJSONの構造検証 */
export function validateImportJson(data) {
  const errors = [];
  if (!data || typeof data !== 'object') {
    errors.push('不正なファイルです: JSONがオブジェクトではありません');
    return errors;
  }
  for (const key of ['version', 'subjects', 'logs', 'todos']) {
    if (!(key in data)) {
      errors.push(`不正なファイルです: 必須キー '${key}' が存在しません`);
    }
  }
  if (data.subjects && !Array.isArray(data.subjects)) errors.push('不正なファイルです: subjects は配列である必要があります');
  if (data.logs && !Array.isArray(data.logs)) errors.push('不正なファイルです: logs は配列である必要があります');
  if (data.todos && !Array.isArray(data.todos)) errors.push('不正なファイルです: todos は配列である必要があります');
  return errors;
}
