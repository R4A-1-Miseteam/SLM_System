import React, { useState } from 'react';
import Card from '../components/ui/Card.jsx';
import Button from '../components/ui/Button.jsx';
import Toast from '../components/ui/Toast.jsx';
import { useStudyData } from '../hooks/useStudyData.js';

/**
 * ToDoリスト画面（FR-012）
 * 学習タスクの追加・完了切替・削除
 */
export default function Todo() {
  const { todos, addTodo, toggleTodo, deleteTodo } = useStudyData();
  const [taskName, setTaskName] = useState('');
  const [filter, setFilter] = useState('all'); // all | active | completed
  const [toast, setToast] = useState({ message: '', type: 'info' });

  const handleSubmit = (e) => {
    e.preventDefault();
    try {
      addTodo(taskName);
      setTaskName('');
      setToast({ message: 'タスクを追加しました', type: 'success' });
    } catch (err) {
      setToast({ message: err.message, type: 'error' });
    }
  };

  const handleDelete = (id) => {
    if (window.confirm('このタスクを削除しますか？')) {
      deleteTodo(id);
    }
  };

  const filtered = todos.filter((t) => {
    if (filter === 'active')    return !t.isCompleted;
    if (filter === 'completed') return t.isCompleted;
    return true;
  });

  const completedCount = todos.filter((t) => t.isCompleted).length;

  return (
    <div className="page page--todos">
      <div className="page__header">
        <h2>✅ ToDoリスト</h2>
      </div>

      <Card title="新しいタスクを追加">
        <form className="form form--inline" onSubmit={handleSubmit}>
          <input
            type="text"
            value={taskName}
            onChange={(e) => setTaskName(e.target.value)}
            placeholder="例：英文法の章末問題を解く"
            required
          />
          <Button type="submit" variant="primary">追加</Button>
        </form>
      </Card>

      <Card
        title={`タスク一覧 (完了 ${completedCount} / ${todos.length})`}
        actions={
          <div className="filter-tabs">
            <button
              className={filter === 'all' ? 'is-active' : ''}
              onClick={() => setFilter('all')}
            >すべて</button>
            <button
              className={filter === 'active' ? 'is-active' : ''}
              onClick={() => setFilter('active')}
            >未完了</button>
            <button
              className={filter === 'completed' ? 'is-active' : ''}
              onClick={() => setFilter('completed')}
            >完了済み</button>
          </div>
        }
      >
        {filtered.length === 0 ? (
          <p className="muted">該当するタスクはありません</p>
        ) : (
          <ul className="todo-list">
            {filtered.map((t) => (
              <li key={t.id} className={`todo-item ${t.isCompleted ? 'is-completed' : ''}`}>
                <label className="todo-item__check">
                  <input
                    type="checkbox"
                    checked={t.isCompleted}
                    onChange={() => toggleTodo(t.id)}
                  />
                  <span className="todo-item__name">{t.taskName}</span>
                </label>
                <Button size="sm" variant="danger" onClick={() => handleDelete(t.id)}>削除</Button>
              </li>
            ))}
          </ul>
        )}
      </Card>

      <Toast
        message={toast.message}
        type={toast.type}
        onClose={() => setToast({ message: '', type: 'info' })}
      />
    </div>
  );
}
