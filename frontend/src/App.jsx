import React, { useState } from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import Header from './components/layout/Header';
import GlobalNav from './components/layout/GlobalNav.jsx';
import Toast from './components/ui/Toast.jsx';
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
  const [toast, setToast] = useState({ message: '', type: 'info' });

  const showToast = (message, type = 'info') => {
    setToast({ message, type });
  };

  const hideToast = () => {
    setToast({ message: '', type: 'info' });
  };

  return (
    <div className={`app theme-${theme}`}>
      <Header title="Dashboard" theme={theme}/>
      <GlobalNav />
      <main className="app-main">
        <Routes>
          <Route path="/" element={<Navigate to="/dashboard" replace />} />
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/goals" element={<GoalSetting showToast={showToast} />} />
          <Route path="/logs" element={<StudyLog showToast={showToast} />} />
          <Route path="/todos" element={<Todo />} />
          <Route path="/backup" element={<Backup showToast={showToast} />} />
          <Route path="*" element={<div>Page Not Found</div>} />
        </Routes>
      </main>
      <Toast
        message={toast.message}
        type={toast.type}
        onClose={hideToast}
      />
    </div>
  );
}
