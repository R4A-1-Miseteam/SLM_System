import { useState, useEffect, useCallback } from 'react';

/**
 * LocalStorage 永続化フック（DR-001）
 * F5 リロードしてもデータが保持されること
 */
export function useLocalStorage(key, initialValue) {
  const [value, setValue] = useState(() => {
    try {
      const item = window.localStorage.getItem(key);
      return item ? JSON.parse(item) : initialValue;
    } catch (err) {
      console.error(`[useLocalStorage] read error: ${key}`, err);
      return initialValue;
    }
  });

  const setStoredValue = useCallback(
    (newValue) => {
      try {
        const valueToStore = newValue instanceof Function ? newValue(value) : newValue;
        setValue(valueToStore);
        window.localStorage.setItem(key, JSON.stringify(valueToStore));
      } catch (err) {
        console.error(`[useLocalStorage] write error: ${key}`, err);
      }
    },
    [key, value]
  );

  const remove = useCallback(() => {
    try {
      window.localStorage.removeItem(key);
      setValue(initialValue);
    } catch (err) {
      console.error(`[useLocalStorage] remove error: ${key}`, err);
    }
  }, [key, initialValue]);

  // 他タブとの同期
  useEffect(() => {
    const handler = (e) => {
      if (e.key === key && e.newValue) {
        try {
          setValue(JSON.parse(e.newValue));
        } catch {}
      }
    };
    window.addEventListener('storage', handler);
    return () => window.removeEventListener('storage', handler);
  }, [key]);

  return [value, setStoredValue, remove];
}
