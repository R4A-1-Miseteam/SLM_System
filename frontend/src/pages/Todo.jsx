import React from 'react';
import Card from '../components/ui/Card.jsx';
import ProgressChart from '../components/dashboard/ProgressChart.jsx';
import { useStudyData } from '../hooks/useStudyData.js';

/**
 * 学習記録画面
 * 学習進捗グラフと学習履歴を表示
 */
export default function Todo() {
  const { logs, subjects } = useStudyData();

  return (
    <div className="page page--todos">
      <div className="page__header">
        <h2>📚 学習記録</h2>
      </div>

      <Card title="学習進捗グラフ">
        <ProgressChart mode="line" />
      </Card>

      <Card title="学習履歴">
        {logs.length === 0 ? (
          <p className="muted">学習履歴がありません</p>
        ) : (
          <table className="data-table">
            <thead>
              <tr>
                <th>日付</th>
                <th>科目</th>
                <th>時間</th>
                <th>ページ</th>
                <th>コメント</th>
              </tr>
            </thead>
            <tbody>
              {logs.slice().reverse().map((log) => (
                <tr key={log.id}>
                  <td>{log.date}</td>
                  <td>{subjects.find(s => s.id === log.subjectId)?.title || '不明'}</td>
                  <td>{log.duration} 分</td>
                  <td>{log.pageCount} ページ</td>
                  <td>{log.comment || '-'}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </Card>
    </div>
  );
}
