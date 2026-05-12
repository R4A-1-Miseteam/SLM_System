import React from 'react';

/**
 * 汎用ボタンコンポーネント
 * variant: primary | secondary | danger | ghost
 */
export default function Button({
  variant = 'primary',
  size = 'md',
  type = 'button',
  disabled = false,
  onClick,
  children,
  className = '',
  ...rest
}) {
  return (
    <button
      type={type}
      disabled={disabled}
      onClick={onClick}
      className={`btn btn--${variant} btn--${size} ${className}`.trim()}
      {...rest}
    >
      {children}
    </button>
  );
}
