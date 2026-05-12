import { useEffect, useState, useCallback } from 'react';
import { timerStore } from '../store/timerStore.js';

/**
 * ポモドーロタイマーフック（FR-009 / FR-010）
 * 25分カウントダウン。ストップ時に経過時間を返却して実績登録に使う
 */
export function useTimer() {
  const [state, setState] = useState(timerStore.getState());

  useEffect(() => timerStore.subscribe(setState), []);

  const start = useCallback((subjectId) => timerStore.start(subjectId), []);
  const pause = useCallback(() => timerStore.pause(), []);
  const stop = useCallback(() => timerStore.stop(), []);
  const reset = useCallback(() => timerStore.reset(), []);

  /** 経過分数（切り捨て）を取得して、必要なら呼び出し側でログ登録に使う */
  const commitElapsedMinutes = useCallback(() => {
    const minutes = timerStore.getElapsedMinutes();
    return minutes;
  }, []);

  const remaining = Math.max(0, state.targetSec - state.elapsed);
  const minutes = Math.floor(remaining / 60);
  const seconds = remaining % 60;

  return {
    ...state,
    remaining,
    display: `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`,
    progress: state.targetSec > 0 ? state.elapsed / state.targetSec : 0,
    start,
    pause,
    stop,
    reset,
    commitElapsedMinutes,
  };
}
