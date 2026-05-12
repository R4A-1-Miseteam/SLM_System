import React, { useEffect } from 'react';

/**
 * トースト通知コンポーネント（NFR-002）
 * エラー / 成功メッセージを一定時間表示する
 */
export default function Toast({ message, type = 'info', duration = 3000, onClose }) {
  useEffect(() => {
    if (!message) return;
    const timer = setTimeout(() => onClose && onClose(), duration);
    return () => clearTimeout(timer);
  }, [message, duration, onClose]);

  if (!message) return null;

  return (
    <div className={`toast toast--${type}`} role="alert">
      <span className="toast__icon">
        {type === 'error' ? '⚠️' : type === 'success' ? '✅' : 'ℹ️'}
      </span>
      <span className="toast__message">{message}</span>
      <button className="toast__close" onClick={onClose} aria-label="閉じる">
        ×
      </button>
    </div>
  );
}
