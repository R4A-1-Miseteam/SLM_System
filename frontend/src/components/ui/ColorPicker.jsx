import React from 'react';

/**
 * カラーピッカーコンポーネント
 * 教科ごとのテーマカラー設定などに使用
 */
const PRESET_COLORS = [
  '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
  '#8b5cf6', '#ec4899', '#14b8a6', '#6366f1',
];

export default function ColorPicker({ value, onChange }) {
  return (
    <div className="color-picker">
      {PRESET_COLORS.map((color) => (
        <button
          key={color}
          type="button"
          className={`color-picker__swatch ${value === color ? 'is-selected' : ''}`}
          style={{ backgroundColor: color }}
          onClick={() => onChange && onChange(color)}
          aria-label={`色 ${color}`}
        />
      ))}
    </div>
  );
}
