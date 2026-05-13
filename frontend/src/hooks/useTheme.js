import { useCallback } from 'react';
import { useLocalStorage } from './useLocalStorage.js';

/**
 * テーマ切替フック（ライト/ダーク）
 */
export function useTheme() {
  const [theme, setTheme] = useLocalStorage('selftrack_theme', 'light');

  const toggle = useCallback(() => {
    setTheme((prev) => (prev === 'light' ? 'dark' : 'light'));
  }, [setTheme]);

  return { theme, setTheme, toggle };
}
