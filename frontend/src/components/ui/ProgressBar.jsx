import React from 'react';

/**
 * プログレスバーコンポーネント
 * 達成率の可視化に使用
 */
export default function ProgressBar({ value = 0, max = 100, label = '', color = '#3b82f6' }) {
  const pct = Math.min(100, Math.max(0, (value / max) * 100));
  const overflow = value > max;

  return (
    <div className="progress-bar">
      {label && (
        <div className="progress-bar__label">
          <span>{label}</span>
          <span className="progress-bar__value">{Math.round(pct)}%</span>
        </div>
      )}
      <div className="progress-bar__track">
        <div
          className={`progress-bar__fill ${overflow ? 'is-overflow' : ''}`}
          style={{ width: `${pct}%`, backgroundColor: overflow ? '#10b981' : color }}
        />
      </div>
    </div>
  );
}
