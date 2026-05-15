import React from 'react';
import { NavLink } from 'react-router-dom';

/**
 * グローバルナビゲーション
 * 主要画面間の遷移リンクを提供
 */
const NAV_ITEMS = [
  { to: '/dashboard', label: 'ダッシュボード', icon: '📊' },
  { to: '/goals',     label: '週次目標',       icon: '🎯' },
  { to: '/logs',      label: '実績記録',       icon: '✏️' },
  { to: '/todos',     label: '学習記録',       icon: '📚' },
  { to: '/backup',    label: 'バックアップ',   icon: '💾' },
];

export default function GlobalNav() {
  return (
    <nav className="global-nav">
      <ul className="global-nav__list">
        {NAV_ITEMS.map((item) => (
          <li key={item.to} className="global-nav__item">
            <NavLink
              to={item.to}
              className={({ isActive }) =>
                'global-nav__link' + (isActive ? ' is-active' : '')
              }
            >
              <span className="global-nav__icon">{item.icon}</span>
              <span className="global-nav__label">{item.label}</span>
            </NavLink>
          </li>
        ))}
      </ul>
    </nav>
  );
}
