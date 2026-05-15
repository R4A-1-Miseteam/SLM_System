/**
 * タイマー状態のグローバルストア（FR-009 / FR-010）
 * 画面遷移しても計測が継続するように単一インスタンスで管理
 */
class TimerStore {
  constructor() {
    this.listeners = new Set();
    this.state = {
      isRunning: false,
      isPaused: false,
      elapsed: 0,         // 経過秒
      targetSec: 25 * 60, // ポモドーロ 25分
      subjectId: null,
      startedAt: null,
    };
    this.intervalId = null;
  }

  getState() {
    return this.state;
  }

  subscribe(listener) {
    this.listeners.add(listener);
    return () => this.listeners.delete(listener);
  }

  notify() {
    this.listeners.forEach((l) => l(this.state));
  }

  start(subjectId = null) {
    if (this.state.isRunning) return;
    this.state = {
      ...this.state,
      isRunning: true,
      isPaused: false,
      subjectId: subjectId ?? this.state.subjectId,
      startedAt: this.state.startedAt ?? Date.now(),
    };
    this.intervalId = setInterval(() => this.tick(), 1000);
    this.notify();
  }

  pause() {
    if (!this.state.isRunning) return;
    clearInterval(this.intervalId);
    this.intervalId = null;
    this.state = { ...this.state, isRunning: false, isPaused: true };
    this.notify();
  }

  stop() {
    clearInterval(this.intervalId);
    this.intervalId = null;
    this.state = { ...this.state, isRunning: false, isPaused: false };
    this.notify();
  }

  tick() {
    this.state = { ...this.state, elapsed: this.state.elapsed + 1 };
    if (this.state.elapsed >= this.state.targetSec) {
      this.stop();
    }
    this.notify();
  }

  reset() {
    clearInterval(this.intervalId);
    this.intervalId = null;
    this.state = {
      isRunning: false,
      isPaused: false,
      elapsed: 0,
      targetSec: 25 * 60,
      subjectId: this.state.subjectId,
      startedAt: null,
    };
    this.notify();
  }

  /** 経過時間を分単位で取得（切り捨て） */
  getElapsedMinutes() {
    return Math.floor(this.state.elapsed / 60);
  }
}

export const timerStore = new TimerStore();
