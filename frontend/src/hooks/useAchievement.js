import { useMemo } from 'react';
import { useStudyData } from './useStudyData.js';
import { getWeek } from '../utils/dateUtils.js';

/**
 * 達成率計算フック（FR-005）
 * 週次目標(100%)に対する累計実績の達成率をリアルタイムで計算する
 */
export function useAchievement(baseDate = new Date()) {
  const { subjects, logs } = useStudyData();

  const result = useMemo(() => {
    const { start, end } = getWeek(baseDate);

    return subjects.map((subject) => {
      const targetLogs = logs.filter(
        (l) => l.subjectId === subject.id && l.date >= start && l.date <= end
      );
      const totalTime  = targetLogs.reduce((acc, l) => acc + (l.duration ?? 0), 0);
      const totalPages = targetLogs.reduce((acc, l) => acc + (l.pageCount ?? 0), 0);

      return {
        subjectId:  subject.id,
        title:      subject.title,
        weekStart:  start,
        weekEnd:    end,
        targetTime: subject.targetTime,
        targetPage: subject.targetPage,
        totalTime,
        totalPages,
        timeRate: subject.targetTime > 0
          ? Math.round((totalTime / subject.targetTime) * 1000) / 10
          : 0,
        pageRate: subject.targetPage > 0
          ? Math.round((totalPages / subject.targetPage) * 1000) / 10
          : 0,
      };
    });
  }, [subjects, logs, baseDate]);

  return result;
}
