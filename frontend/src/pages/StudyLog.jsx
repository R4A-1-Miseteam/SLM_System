import React, { useState } from 'react';
import Card from '../components/ui/Card.jsx';
import Button from '../components/ui/Button.jsx';
import Toast from '../components/ui/Toast.jsx';
import { useStudyData } from '../hooks/useStudyData.js';
import { today } from '../utils/dateUtils.js';

/**
 * 学習実績記録画面（FR-003 / FR-004 / FR-011）
 * 日付・科目・時間・ページ数を手動入力して保存
 */
export default function StudyLog() {
  const { subjects, logs, saveLog, deleteLog } = useStudyData();
  const [form, setForm] = useState({
    subjectId: '',
    date: today(),
    duration: 0,
    pageCount: 0,
    comment: '',
  });
  const [toast, setToast] = useState({ message: '', type: 'info' });
  const [filterSubject, setFilterSubject] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    try {
      saveLog(form);
      setToast({ message: '実績を保存しました', type: 'success' });
      setForm({ ...form, duration: 0, pageCount: 0, comment: '' });
    } catch (err) {
      setToast({ message: err.message, type: 'error' });
    }
  };

  const handleDelete = (id) => {
    if (window.confirm('この実績を削除しますか？')) {
      deleteLog(id);
      setToast({ message: '実績を削除しました', type: 'success' });
    }
  };

  const filteredLogs = filterSubject
    ? logs.filter((l) => l.subjectId === filterSubject)
    : logs;
  const sortedLogs = [...filteredLogs].sort((a, b) => b.date.localeCompare(a.date));

  const subjectMap = Object.fromEntries(subjects.map((s) => [s.id, s.title]));

  return (
    <div className="page page--logs">
      <div className="page__header">
        <h2>✏️ 学習実績記録</h2>
      </div>

      <Card title="新しい実績を入力">
        {subjects.length === 0 ? (
          <p className="muted">先に「週次目標」画面で科目を登録してください</p>
        ) : (
          <form className="form" onSubmit={handleSubmit}>
            <div className="form__row form__row--half">
              <div>
                <label>科目 *</label>
                <select
                  value={form.subjectId}
                  onChange={(e) => setForm({ ...form, subjectId: e.target.value })}
                  required
                >
                  <option value="">-- 選択 --</option>
                  {subjects.map((s) => (
                    <option key={s.id} value={s.id}>{s.title}</option>
                  ))}
                </select>
              </div>
              <div>
                <label>日付 *</label>
                <input
                  type="date"
                  value={form.date}
                  onChange={(e) => setForm({ ...form, date: e.target.value })}
                  required
                />
              </div>
            </div>
            <div className="form__row form__row--half">
              <div>
                <label>実施時間（分）</label>
                <input
                  type="number"
                  min="0"
                  max="1440"
                  value={form.duration}
                  onChange={(e) => setForm({ ...form, duration: e.target.value })}
                />
              </div>
              <div>
                <label>完了ページ数</label>
                <input
                  type="number"
                  min="0"
                  max="9999"
                  value={form.pageCount}
                  onChange={(e) => setForm({ ...form, pageCount: e.target.value })}
                />
              </div>
            </div>
            <div className="form__row">
              <label>振り返りメモ</label>
              <textarea
                value={form.comment}
                onChange={(e) => setForm({ ...form, comment: e.target.value })}
                rows="2"
                placeholder="今日の学習で気づいたことなど"
              />
            </div>
            <div className="form__actions">
              <Button type="submit" variant="primary">保存</Button>
            </div>
            <p className="form__hint">
              ※ 同じ日付・同じ科目で再保存すると、既存の記録が上書きされます
            </p>
          </form>
        )}
      </Card>

      <Card
        title={`実績一覧 (${sortedLogs.length})`}
        actions={
          <select
            value={filterSubject}
            onChange={(e) => setFilterSubject(e.target.value)}
          >
            <option value="">全科目</option>
            {subjects.map((s) => (
              <option key={s.id} value={s.id}>{s.title}</option>
            ))}
          </select>
        }
      >
        {sortedLogs.length === 0 ? (
          <p className="muted">まだ実績がありません</p>
        ) : (
          <table className="data-table">
            <thead>
              <tr>
                <th>日付</th>
                <th>科目</th>
                <th>時間</th>
                <th>ページ</th>
                <th>メモ</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
              {sortedLogs.map((l) => (
                <tr key={l.id}>
                  <td>{l.date}</td>
                  <td>{subjectMap[l.subjectId] ?? '不明'}</td>
                  <td>{l.duration} 分</td>
                  <td>{l.pageCount} ページ</td>
                  <td className="memo-cell">{l.comment || '-'}</td>
                  <td>
                    <Button size="sm" variant="danger" onClick={() => handleDelete(l.id)}>削除</Button>
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
