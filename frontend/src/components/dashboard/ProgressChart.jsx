import React from 'react';
import {
  LineChart, Line, XAxis, YAxis, CartesianGrid,
  Tooltip, Legend, ResponsiveContainer, BarChart, Bar,
} from 'recharts';
import Card from '../ui/Card.jsx';
import { useChart } from '../../hooks/useChart.js';

/**
 * 進捗推移グラフ（FR-006）
 * 日別の学習時間とページ数を折れ線/棒グラフで表示
 */
export default function ProgressChart({ subjectId = null, mode = 'line' }) {
  const { weeklyData } = useChart(subjectId);

  return (
    <Card title="📈 今週の進捗推移">
      <ResponsiveContainer width="100%" height={280}>
        {mode === 'bar' ? (
          <BarChart data={weeklyData}>
            <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
            <XAxis dataKey="label" />
            <YAxis yAxisId="left" label={{ value: '分', angle: -90, position: 'insideLeft' }} />
            <YAxis yAxisId="right" orientation="right" label={{ value: 'ページ', angle: 90, position: 'insideRight' }} />
            <Tooltip />
            <Legend />
            <Bar yAxisId="left"  dataKey="duration" name="学習時間(分)" fill="#3b82f6" />
            <Bar yAxisId="right" dataKey="pages"    name="ページ数"     fill="#10b981" />
          </BarChart>
        ) : (
          <LineChart data={weeklyData}>
            <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
            <XAxis dataKey="label" />
            <YAxis yAxisId="left"  label={{ value: '分',     angle: -90, position: 'insideLeft' }} />
            <YAxis yAxisId="right" orientation="right" label={{ value: 'ページ', angle: 90, position: 'insideRight' }} />
            <Tooltip />
            <Legend />
            <Line yAxisId="left"  type="monotone" dataKey="duration" name="学習時間(分)" stroke="#3b82f6" strokeWidth={2} />
            <Line yAxisId="right" type="monotone" dataKey="pages"    name="ページ数"     stroke="#10b981" strokeWidth={2} />
          </LineChart>
        )}
      </ResponsiveContainer>
    </Card>
  );
}
