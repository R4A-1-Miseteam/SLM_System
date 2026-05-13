import { useMemo } from 'react';
import { useStudyData } from './useStudyData.js';
import { getWeek, getWeekDays, getMonth } from '../utils/dateUtils.js';

/**
 * グラフデータ生成フック（FR-006）
 * 日別・週別の進捗推移データを生成
 */
export function useChart(subjectId = null, baseDate = new Date()) {
  const { subjects, logs } = useStudyData();

  /** 今週（月〜日）の日別データ */
  const weeklyData = useMemo(() => {
    const days = getWeekDays(baseDate);
    return days.map((date) => {
      const filtered = subjectId
        ? logs.filter((l) => l.subjectId === subjectId && l.date === date)
        : logs.filter((l) => l.date === date);
      const duration = filtered.reduce((a, l) => a + (l.duration ?? 0), 0);
      const pages    = filtered.reduce((a, l) => a + (l.pageCount ?? 0), 0);
      return {
        date,
        label: date.slice(5), // MM-DD
        duration,
        pages,
      };
    });
  }, [logs, subjectId, baseDate]);

  /** 週次合計（目標との対比用） */
  const weeklySummary = useMemo(() => {
    const { start, end } = getWeek(baseDate);
    const subjectsToShow = subjectId
      ? subjects.filter((s) => s.id === subjectId)
      : subjects;
    return subjectsToShow.map((s) => {
      const ls = logs.filter(
        (l) => l.subjectId === s.id && l.date >= start && l.date <= end
      );
      const time = ls.reduce((a, l) => a + (l.duration ?? 0), 0);
      const page = ls.reduce((a, l) => a + (l.pageCount ?? 0), 0);
      return {
        subjectId:   s.id,
        title:       s.title,
        targetTime:  s.targetTime,
        targetPage:  s.targetPage,
        actualTime:  time,
        actualPage:  page,
      };
    });
  }, [logs, subjects, subjectId, baseDate]);

  /** 月次推移 */
  const monthlyData = useMemo(() => {
    const { start, end } = getMonth(baseDate);
    const filtered = logs.filter(
      (l) => l.date >= start && l.date <= end &&
        (!subjectId || l.subjectId === subjectId)
    );
    const grouped = {};
    filtered.forEach((l) => {
      grouped[l.date] = grouped[l.date] || { date: l.date, duration: 0, pages: 0 };
      grouped[l.date].duration += l.duration ?? 0;
      grouped[l.date].pages    += l.pageCount ?? 0;
    });
    return Object.values(grouped).sort((a, b) => a.date.localeCompare(b.date));
  }, [logs, subjectId, baseDate]);

  return { weeklyData, weeklySummary, monthlyData };
}
