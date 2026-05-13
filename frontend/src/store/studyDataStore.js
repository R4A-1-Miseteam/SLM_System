/**
 * 全データのグローバルストア
 * シンプルな pub/sub 実装（外部ライブラリ非依存）
 */
const STORAGE_KEY = 'study_pacer_data';

const createInitialData = () => ({
  version: '2.0.0',
  lastUpdated: new Date().toISOString(),
  subjects: [],
  logs: [],
  todos: [],
});

class StudyDataStore {
  constructor() {
    this.listeners = new Set();
    this.state = this.load();
  }

  load() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) {
        const init = createInitialData();
        localStorage.setItem(STORAGE_KEY, JSON.stringify(init));
        return init;
      }
      return JSON.parse(raw);
    } catch (err) {
      console.error('[studyDataStore] load failed', err);
      return createInitialData();
    }
  }

  persist() {
    this.state.lastUpdated = new Date().toISOString();
    localStorage.setItem(STORAGE_KEY, JSON.stringify(this.state));
  }

  getState() {
    return this.state;
  }

  setState(updater) {
    this.state =
      typeof updater === 'function' ? updater(this.state) : { ...this.state, ...updater };
    this.persist();
    this.listeners.forEach((l) => l(this.state));
  }

  subscribe(listener) {
    this.listeners.add(listener);
    return () => this.listeners.delete(listener);
  }

  /** インポート時のデータ全置換 */
  replaceAll(newData) {
    this.state = { ...createInitialData(), ...newData };
    this.persist();
    this.listeners.forEach((l) => l(this.state));
  }
}

export const studyDataStore = new StudyDataStore();
export { STORAGE_KEY };
