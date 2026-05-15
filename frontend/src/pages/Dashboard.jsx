import React, { useState } from 'react';
import SubjectProgress from '../components/dashboard/SubjectProgress.jsx';
import ProgressChart from '../components/dashboard/ProgressChart.jsx';
import TodayReminder from '../components/dashboard/TodayReminder.jsx';
import WeeklySummary from '../components/dashboard/WeeklySummary.jsx';
import TimerLauncher from '../components/timer/TimerLauncher.jsx';
import { useAchievement } from '../hooks/useAchievement.js';

/**
 * ダッシュボード画面（FR-005 / FR-006 / FR-016）
 * 全教科の達成率、今週の進捗推移、本日のリマインダーを表示
 */
export default function Dashboard() {
  const achievements = useAchievement();
  const [chartMode, setChartMode] = useState('bar');

  return (
    <div className="page page--dashboard">
      <div className="page__header">
        <h2>📊 ダッシュボード</h2>
        <TimerLauncher />
      </div>

      <div className="dashboard-grid">
        <div className="dashboard-grid__col">
          <TodayReminder />
          <WeeklySummary />
        </div>

        <div className="dashboard-grid__col">
          <ProgressChart mode={chartMode} />
          <div className="chart-controls">
            <button 
              className={chartMode === 'line' ? 'active' : ''} 
              onClick={() => setChartMode('line')}
            >
              折れ線グラフ
            </button>
            <button 
              className={chartMode === 'bar' ? 'active' : ''} 
              onClick={() => setChartMode('bar')}
            >
              棒グラフ
            </button>
          </div>
        </div>
      </div>

      <h3 className="section-title">教科別 週次達成率</h3>
      {achievements.length === 0 ? (
        <p className="muted">「週次目標」画面で科目を登録してください</p>
      ) : (
        <div className="subject-progress-grid">
          {achievements.map((a) => (
            <SubjectProgress key={a.subjectId} achievement={a} />
          ))}
        </div>
      )}
    </div>
  );
}
