/**
 * 日付関連ユーティリティ
 * 週・月の範囲計算など
 */

/** YYYY-MM-DD 形式の今日の日付 */
export function today() {
  const d = new Date();
  return formatDate(d);
}

/** Date → YYYY-MM-DD */
export function formatDate(d) {
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${y}-${m}-${day}`;
}

/** 月曜起点の週範囲を返す */
export function getWeek(base = new Date()) {
  const d = new Date(base);
  const dow = d.getDay() === 0 ? 7 : d.getDay(); // 月=1, 日=7
  const start = new Date(d);
  start.setDate(d.getDate() - (dow - 1));
  const end = new Date(start);
  end.setDate(start.getDate() + 6);
  return { start: formatDate(start), end: formatDate(end) };
}

/** 月初〜月末を返す */
export function getMonth(base = new Date()) {
  const d = new Date(base);
  const start = new Date(d.getFullYear(), d.getMonth(), 1);
  const end = new Date(d.getFullYear(), d.getMonth() + 1, 0);
  return { start: formatDate(start), end: formatDate(end) };
}

/** 週の各日付を配列で返す（月〜日） */
export function getWeekDays(base = new Date()) {
  const { start } = getWeek(base);
  const startDate = new Date(start);
  const days = [];
  for (let i = 0; i < 7; i++) {
    const d = new Date(startDate);
    d.setDate(startDate.getDate() + i);
    days.push(formatDate(d));
  }
  return days;
}
