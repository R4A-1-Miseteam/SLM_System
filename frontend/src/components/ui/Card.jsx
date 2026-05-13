import React from 'react';

/**
 * カードコンポーネント
 * 情報をグルーピングして表示する汎用コンテナ
 */
export default function Card({ title, children, className = '', actions = null }) {
  return (
    <section className={`card ${className}`.trim()}>
      {(title || actions) && (
        <header className="card__header">
          {title && <h3 className="card__title">{title}</h3>}
          {actions && <div className="card__actions">{actions}</div>}
        </header>
      )}
      <div className="card__body">{children}</div>
    </section>
  );
}
