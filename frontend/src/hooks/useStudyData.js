import { useEffect, useState, useCallback } from 'react';
import { studyDataStore } from '../store/studyDataStore.js';
import { generateId } from '../utils/uuid.js';
import { validateTime, validatePage, validateNonEmpty } from '../utils/validator.js';

/**
 * 全データの管理・CRUD を提供するフック
 * 画面コンポーネントは原則このフック経由でデータを操作する
 */
export function useStudyData() {
  const [state, setState] = useState(studyDataStore.getState());

  useEffect(() => {
    return studyDataStore.subscribe(setState);
  }, []);

  // ===== Subject =====
  const addSubject = useCallback((dto) => {
    if (!validateNonEmpty(dto.title)) throw new Error('科目名は必須です');
    if (!validateTime(dto.targetTime ?? 0)) throw new Error('目標時間は0〜1440分で指定してください');
    if (!validatePage(dto.targetPage ?? 0)) throw new Error('目標ページは0〜9999で指定してください');

    const subject = {
      id: generateId(),
      title: dto.title,
      targetTime: Number(dto.targetTime ?? 0),
      targetPage: Number(dto.targetPage ?? 0),
      memo: dto.memo ?? '',
      createdAt: new Date().toISOString(),
    };
    studyDataStore.setState((s) => ({ ...s, subjects: [...s.subjects, subject] }));
    return subject;
  }, []);

  const updateSubject = useCallback((id, dto) => {
    if (!validateTime(dto.targetTime ?? 0)) throw new Error('目標時間は0〜1440分で指定してください');
    if (!validatePage(dto.targetPage ?? 0)) throw new Error('目標ページは0〜9999で指定してください');
    studyDataStore.setState((s) => ({
      ...s,
      subjects: s.subjects.map((sub) =>
        sub.id === id ? { ...sub, ...dto } : sub
      ),
    }));
  }, []);

  const deleteSubject = useCallback((id) => {
    // cascade: 関連 logs も削除
    studyDataStore.setState((s) => ({
      ...s,
      subjects: s.subjects.filter((sub) => sub.id !== id),
      logs: s.logs.filter((l) => l.subjectId !== id),
    }));
  }, []);

  // ===== StudyLog (upsert: 同日同教科は更新) =====
  const saveLog = useCallback((dto) => {
    if (!dto.subjectId) throw new Error('subjectId は必須です');
    if (!validateTime(dto.duration ?? 0)) throw new Error('時間は0〜1440分で指定してください');
    if (!validatePage(dto.pageCount ?? 0)) throw new Error('ページ数は0〜9999で指定してください');

    studyDataStore.setState((s) => {
      const existing = s.logs.find(
        (l) => l.subjectId === dto.subjectId && l.date === dto.date
      );
      if (existing) {
        return {
          ...s,
          logs: s.logs.map((l) =>
            l.id === existing.id
              ? { ...l, duration: Number(dto.duration), pageCount: Number(dto.pageCount), comment: dto.comment ?? l.comment }
              : l
          ),
        };
      }
      return {
        ...s,
        logs: [
          ...s.logs,
          {
            id: generateId(),
            subjectId: dto.subjectId,
            date: dto.date,
            duration: Number(dto.duration ?? 0),
            pageCount: Number(dto.pageCount ?? 0),
            comment: dto.comment ?? '',
          },
        ],
      };
    });
  }, []);

  const deleteLog = useCallback((id) => {
    studyDataStore.setState((s) => ({
      ...s,
      logs: s.logs.filter((l) => l.id !== id),
    }));
  }, []);

  // ===== Todo =====
  const addTodo = useCallback((taskName) => {
    if (!validateNonEmpty(taskName)) throw new Error('タスク名は必須です');
    const todo = {
      id: generateId(),
      taskName,
      isCompleted: false,
      createdAt: new Date().toISOString(),
    };
    studyDataStore.setState((s) => ({ ...s, todos: [...s.todos, todo] }));
    return todo;
  }, []);

  const toggleTodo = useCallback((id) => {
    studyDataStore.setState((s) => ({
      ...s,
      todos: s.todos.map((t) =>
        t.id === id ? { ...t, isCompleted: !t.isCompleted } : t
      ),
    }));
  }, []);

  const deleteTodo = useCallback((id) => {
    studyDataStore.setState((s) => ({
      ...s,
      todos: s.todos.filter((t) => t.id !== id),
    }));
  }, []);

  // ===== バックアップ用：全データ置換 =====
  const replaceAll = useCallback((newData) => {
    studyDataStore.replaceAll(newData);
  }, []);

  return {
    subjects: state.subjects,
    logs: state.logs,
    todos: state.todos,
    version: state.version,
    lastUpdated: state.lastUpdated,
    addSubject,
    updateSubject,
    deleteSubject,
    saveLog,
    deleteLog,
    addTodo,
    toggleTodo,
    deleteTodo,
    replaceAll,
    rawData: state,
  };
}
