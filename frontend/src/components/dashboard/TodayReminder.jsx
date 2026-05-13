import React from 'react';
import Card from '../ui/Card.jsx';
import { useStudyData } from '../../hooks/useStudyData.js';
import { today } from '../../utils/dateUtils.js';

/**
 * 本日の学習状況リマインダー（FR-016）
 * 当日の入力済み実績を一覧表示。未入力科目を強調する
 */
export default function TodayReminder() {
  const { subjects, logs } = useStudyData();
  const t = today();

  const todayLogs = logs.filter((l) => l.date === t);
  const loggedSubjectIds = new Set(todayLogs.map((l) => l.subjectId));
  const pending = subjects.filter((s) => !loggedSubjectIds.has(s.id));

  return (
    <Card title={`🔔 今日の学習 (${t})`}>
      {subjects.length === 0 ? (
        <p className="muted">まだ科目が登録されていません</p>
      ) : (
        <>
          <div className="today-reminder__section">
            <h4>✅ 入力済み ({todayLogs.length})</h4>
            {todayLogs.length === 0 ? (
              <p className="muted">本日の実績はまだ未入力です</p>
            ) : (
              <ul className="today-reminder__list">
                {todayLogs.map((l) => {
                  const s = subjects.find((sub) => sub.id === l.subjectId);
                  return (
                    <li key={l.id}>
                      <strong>{s?.title ?? '不明'}</strong>:
                      &nbsp;{l.duration}分 / {l.pageCount}ページ
                    </li>
                  );
                })}
              </ul>
            )}
          </div>
          {pending.length > 0 && (
            <div className="today-reminder__section">
              <h4>⏳ 未入力 ({pending.length})</h4>
              <ul className="today-reminder__list">
                {pending.map((s) => (
                  <li key={s.id} className="pending-item">{s.title}</li>
                ))}
              </ul>
            </div>
          )}
        </>
      )}
    </Card>
  );
}
