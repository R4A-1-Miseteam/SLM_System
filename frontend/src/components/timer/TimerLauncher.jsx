import React, { useState } from 'react';
import Button from '../ui/Button.jsx';
import TimerOverlay from './TimerOverlay.jsx';
import { useTimer } from '../../hooks/useTimer.js';

/**
 * ダッシュボード埋込み用のタイマー起動ボタン
 * クリックで全画面オーバーレイを開く
 */
export default function TimerLauncher() {
  const [open, setOpen] = useState(false);
  const timer = useTimer();

  return (
    <>
      <Button variant="primary" size="lg" onClick={() => setOpen(true)}>
        🍅 ポモドーロを開始
        {timer.isRunning && <span className="timer-launcher__badge">{timer.display}</span>}
      </Button>
      <TimerOverlay open={open} onClose={() => setOpen(false)} />
    </>
  );
}
