import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import Header from './components/layout/Header.jsx';
import GlobalNav from './components/layout/GlobalNav.jsx';
import Dashboard from './pages/Dashboard.jsx';
import GoalSetting from './pages/GoalSetting.jsx';
import StudyLog from './pages/StudyLog.jsx';
import Todo from './pages/Todo.jsx';
import Backup from './pages/Backup.jsx';
import { useTheme } from './hooks/useTheme.js';

/**
 * アプリケーションルートコンポーネント
 * ルーティングとテーマ切替を担当
 */
export default function App() {
  const { theme } = useTheme();

  return (
    <div className={`app theme-${theme}`}>
      <Header />
      <GlobalNav />
      <main className="app-main">
        <Routes>
          <Route path="/" element={<Navigate to="/dashboard" replace />} />
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/goals" element={<GoalSetting />} />
          <Route path="/logs" element={<StudyLog />} />
          <Route path="/todos" element={<Todo />} />
          <Route path="/backup" element={<Backup />} />
          <Route path="*" element={<div>Page Not Found</div>} />
        </Routes>
      </main>
    </div>
  );
}
