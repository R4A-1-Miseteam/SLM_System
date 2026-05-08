import React from 'react';
import Card from '../ui/Card.jsx';
import { useChart } from '../../hooks/useChart.js';

/**
 * 週次サマリー
 * 全教科の今週の目標・実績・差分を一覧表示
 */
export default function WeeklySummary() {
  const { weeklySummary } = useChart();

  if (weeklySummary.length === 0) {
    return (
      <Card title="📋 今週のサマリー">
        <p className="muted">教科を登録するとここに表示されます</p>
      </Card>
    );
  }

  return (
    <Card title="📋 今週のサマリー">
      <table className="summary-table">
        <thead>
          <tr>
            <th>科目</th>
            <th>時間 (実績/目標)</th>
            <th>ページ (実績/目標)</th>
            <th>状態</th>
          </tr>
        </thead>
        <tbody>
          {weeklySummary.map((row) => {
            const timeOk = row.targetTime > 0 && row.actualTime >= row.targetTime;
            const pageOk = row.targetPage > 0 && row.actualPage >= row.targetPage;
            return (
              <tr key={row.subjectId}>
                <td>{row.title}</td>
                <td>
                  {row.actualTime} / {row.targetTime} 分
                  {timeOk && <span className="badge badge--success">達成</span>}
                </td>
                <td>
                  {row.actualPage} / {row.targetPage} ページ
                  {pageOk && <span className="badge badge--success">達成</span>}
                </td>
                <td>
                  {timeOk && pageOk ? '🎉 完全達成' : timeOk || pageOk ? '🟡 部分達成' : '🔵 進行中'}
                </td>
              </tr>
            );
          })}
        </tbody>
      </table>
    </Card>
  );
}
