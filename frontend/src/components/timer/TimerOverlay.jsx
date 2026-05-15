import React, { useState } from 'react';
import TimerRing from './TimerRing.jsx';
import Button from '../ui/Button.jsx';
import { useTimer } from '../../hooks/useTimer.js';
import { useStudyData } from '../../hooks/useStudyData.js';
import { today } from '../../utils/dateUtils.js';

/**
 * 全画面タイマーオーバーレイ（FR-009 / FR-010）
 * ポモドーロ実行中、画面いっぱいに表示
 * 終了時に経過分数を学習ログへ反映する補助動線を提供する
 */
export default function TimerOverlay({ open, onClose }) {
  const timer = useTimer();
  const { subjects, saveLog } = useStudyData();
  const [selectedSubjectId, setSelectedSubjectId] = useState(timer.subjectId ?? '');
  const [savedMessage, setSavedMessage] = useState('');

  if (!open) return null;

  const handleStart = () => {
    if (!selectedSubjectId) {
      alert('科目を選択してください');
      return;
    }
    timer.start(selectedSubjectId);
  };

  const handleFinish = () => {
    const minutes = timer.commitElapsedMinutes();
    if (minutes > 0 && selectedSubjectId) {
      try {
        saveLog({
          subjectId: selectedSubjectId,
          date: today(),
          duration: minutes,
          pageCount: 0,
          comment: 'タイマーから自動登録',
        });
        setSavedMessage(`✅ ${minutes}分を実績として記録しました`);
      } catch (err) {
        setSavedMessage(`⚠️ ${err.message}`);
      }
    }
    timer.reset();
  };

  return (
    <div className="timer-overlay">
      <div className="timer-overlay__inner">
        <button className="timer-overlay__close" onClick={onClose} aria-label="閉じる">×</button>
        <h2>🍅 ポモドーロタイマー</h2>

        <div className="timer-overlay__subject">
          <label>科目: </label>
          <select
            value={selectedSubjectId}
            onChange={(e) => setSelectedSubjectId(e.target.value)}
            disabled={timer.isRunning}
          >
            <option value="">-- 選択してください --</option>
            {subjects.map((s) => (
              <option key={s.id} value={s.id}>{s.title}</option>
            ))}
          </select>
        </div>

        <TimerRing progress={timer.progress} display={timer.display} size={300} />

        <div className="timer-overlay__controls">
          {!timer.isRunning ? (
            <Button variant="primary" size="lg" onClick={handleStart}>▶ スタート</Button>
          ) : (
            <Button variant="secondary" size="lg" onClick={timer.pause}>⏸ 一時停止</Button>
          )}
          <Button variant="danger" size="lg" onClick={handleFinish}>⏹ 終了して記録</Button>
          <Button variant="ghost" size="lg" onClick={timer.reset}>↺ リセット</Button>
        </div>

        {savedMessage && <p className="timer-overlay__message">{savedMessage}</p>}
      </div>
    </div>
  );
}
