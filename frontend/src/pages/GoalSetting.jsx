import React, { useState } from 'react';
import Card from '../components/ui/Card.jsx';
import Button from '../components/ui/Button.jsx';
import Toast from '../components/ui/Toast.jsx';
import { useStudyData } from '../hooks/useStudyData.js';

/**
 * 週次目標設定画面（FR-001 / FR-002）
 * 科目の追加・編集・削除と週次目標(時間/ページ)の設定
 */
export default function GoalSetting() {
  const { subjects, addSubject, updateSubject, deleteSubject } = useStudyData();
  const [form, setForm] = useState({ title: '', targetTime: 0, targetPage: 0, memo: '' });
  const [editingId, setEditingId] = useState(null);
  const [toast, setToast] = useState({ message: '', type: 'info' });

  const resetForm = () => {
    setForm({ title: '', targetTime: 0, targetPage: 0, memo: '' });
    setEditingId(null);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    try {
      if (editingId) {
        updateSubject(editingId, form);
        setToast({ message: '科目を更新しました', type: 'success' });
      } else {
        addSubject(form);
        setToast({ message: '科目を登録しました', type: 'success' });
      }
      resetForm();
    } catch (err) {
      setToast({ message: err.message, type: 'error' });
    }
  };

  const handleEdit = (subject) => {
    setEditingId(subject.id);
    setForm({
      title: subject.title,
      targetTime: subject.targetTime,
      targetPage: subject.targetPage,
      memo: subject.memo,
    });
  };

  const handleDelete = (id, title) => {
    if (window.confirm(`「${title}」を削除しますか？関連する学習ログも削除されます。`)) {
      deleteSubject(id);
      setToast({ message: '科目を削除しました', type: 'success' });
      if (editingId === id) resetForm();
    }
  };

  return (
    <div className="page page--goals">
      <div className="page__header">
        <h2>🎯 週次目標設定</h2>
      </div>

      <Card title={editingId ? '科目を編集' : '新しい科目を追加'}>
        <form className="form" onSubmit={handleSubmit}>
          <div className="form__row">
            <label>科目名 *</label>
            <input
              type="text"
              value={form.title}
              onChange={(e) => setForm({ ...form, title: e.target.value })}
              required
              placeholder="例：英語"
            />
          </div>
          <div className="form__row form__row--half">
            <div>
              <label>週次目標時間（分）</label>
              <input
                type="number"
                min="0"
                max="1440"
                value={form.targetTime}
                onChange={(e) => setForm({ ...form, targetTime: e.target.value })}
              />
            </div>
            <div>
              <label>週次目標ページ数</label>
              <input
                type="number"
                min="0"
                max="9999"
                value={form.targetPage}
                onChange={(e) => setForm({ ...form, targetPage: e.target.value })}
              />
            </div>
          </div>
          <div className="form__row">
            <label>メモ</label>
            <textarea
              value={form.memo}
              onChange={(e) => setForm({ ...form, memo: e.target.value })}
              rows="3"
              placeholder="使用教材や勉強方針など"
            />
          </div>
          <div className="form__actions">
            <Button type="submit" variant="primary">
              {editingId ? '更新' : '登録'}
            </Button>
            {editingId && (
              <Button type="button" variant="ghost" onClick={resetForm}>キャンセル</Button>
            )}
          </div>
        </form>
      </Card>

      <Card title={`登録済み科目 (${subjects.length})`}>
        {subjects.length === 0 ? (
          <p className="muted">まだ科目が登録されていません</p>
        ) : (
          <table className="data-table">
            <thead>
              <tr>
                <th>科目名</th>
                <th>目標時間</th>
                <th>目標ページ</th>
                <th>メモ</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
              {subjects.map((s) => (
                <tr key={s.id}>
                  <td><strong>{s.title}</strong></td>
                  <td>{s.targetTime} 分</td>
                  <td>{s.targetPage} ページ</td>
                  <td className="memo-cell">{s.memo || '-'}</td>
                  <td>
                    <Button size="sm" variant="secondary" onClick={() => handleEdit(s)}>編集</Button>
                    <Button size="sm" variant="danger" onClick={() => handleDelete(s.id, s.title)}>削除</Button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
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
