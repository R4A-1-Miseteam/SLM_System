import React from 'react';
import { useTheme } from '../../hooks/useTheme.js';

/**
 * アプリヘッダー
 * タイトル表示とテーマ切替ボタン
 */
export default function Header() {
  const { theme, toggle } = useTheme();

  return (
    <header className="app-header">
      <div className="app-header__inner">
        <h1 className="app-title">📚 SelfTrack</h1>
        <span className="app-subtitle">学習ペーサー v2.0</span>
        <button className="theme-toggle" onClick={toggle} aria-label="テーマ切替">
          {theme === 'light' ? '🌙' : '☀️'}
        </button>
      </div>
    </header>
  );
}
