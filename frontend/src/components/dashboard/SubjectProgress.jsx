import React from 'react';
import Card from '../ui/Card.jsx';
import ProgressBar from '../ui/ProgressBar.jsx';

/**
 * 教科別の達成率カード
 * 時間とページ数の達成率をプログレスバーで表示
 */
export default function SubjectProgress({ achievement }) {
  const {
    title,
    targetTime,
    targetPage,
    totalTime,
    totalPages,
    timeRate,
    pageRate,
  } = achievement;

  return (
    <Card title={`📘 ${title}`} className="subject-progress">
      <div className="subject-progress__row">
        <ProgressBar
          label={`時間 (${totalTime} / ${targetTime} 分)`}
          value={totalTime}
          max={targetTime || 1}
          color="#3b82f6"
        />
      </div>
      <div className="subject-progress__row">
        <ProgressBar
          label={`ページ (${totalPages} / ${targetPage} ページ)`}
          value={totalPages}
          max={targetPage || 1}
          color="#10b981"
        />
      </div>
      <div className="subject-progress__rates">
        <span>時間達成率: <strong>{timeRate}%</strong></span>
        <span>ページ達成率: <strong>{pageRate}%</strong></span>
      </div>
    </Card>
  );
}
