import React, { Suspense, lazy, useRef, useState } from 'react'
import {
  BarChart, Bar, LineChart, Line, PieChart, Pie, Cell,
  XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend,
} from 'recharts'
import { useChartData } from '../../hooks'
import useReportStore from '../../store/useReportStore'

const COLORS = ['#6366f1','#06b6d4','#f59e0b','#10b981','#ef4444','#8b5cf6','#f97316','#14b8a6']

function ChartTypeButton({ active, label, onClick }) {
  return (
    <button
      onClick={onClick}
      className={`px-2 py-1 text-xs rounded border ${active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'}`}
    >
      {label}
    </button>
  )
}

export default function ChartRenderer({ schema = [] }) {
  const { filters, addFilter } = useReportStore()
  const [chartType, setChartType] = useState('bar')
  const [field, setField]         = useState('category')
  const [metric, setMetric]       = useState('price')
  const chartRef = useRef(null)

  const numericFields     = schema.filter(s => s.type === 'number')
  const categoricalFields = schema.filter(s => s.type === 'dropdown' || s.type === 'string')

  const { data, isLoading } = useChartData({ field, metric, filters: JSON.stringify(filters) })

  const chartData = (data?.labels ?? []).map((label, i) => ({
    name: label, value: data.values[i] ?? 0,
  }))

  // Drill-down: clicking a bar/slice adds a filter
  const handleDrillDown = (entry) => {
    if (!entry?.name) return
    addFilter({ field, type: 'dropdown', value: [entry.name] })
  }

  // Export chart as PNG
  const handleExport = async () => {
    const node = chartRef.current
    if (!node) return
    const html2canvas = (await import('html2canvas')).default
    const canvas = await html2canvas(node)
    const link   = document.createElement('a')
    link.download = `chart-${field}-${metric}.png`
    link.href    = canvas.toDataURL()
    link.click()
  }

  return (
    <div className="bg-white border border-gray-200 rounded-lg p-4">
      {/* Controls */}
      <div className="flex flex-wrap items-center gap-3 mb-4">
        <div className="flex items-center gap-1">
          <label className="text-xs text-gray-500">Group by</label>
          <select value={field} onChange={e => setField(e.target.value)}
            className="border border-gray-200 rounded px-2 py-1 text-sm">
            {categoricalFields.map(s => <option key={s.key} value={s.key}>{s.label}</option>)}
          </select>
        </div>
        <div className="flex items-center gap-1">
          <label className="text-xs text-gray-500">Metric</label>
          <select value={metric} onChange={e => setMetric(e.target.value)}
            className="border border-gray-200 rounded px-2 py-1 text-sm">
            {numericFields.map(s => <option key={s.key} value={s.key}>{s.label}</option>)}
          </select>
        </div>
        <div className="flex items-center gap-1 ml-auto">
          <ChartTypeButton active={chartType === 'bar'}  label="Bar"  onClick={() => setChartType('bar')} />
          <ChartTypeButton active={chartType === 'line'} label="Line" onClick={() => setChartType('line')} />
          <ChartTypeButton active={chartType === 'pie'}  label="Pie"  onClick={() => setChartType('pie')} />
          <button onClick={handleExport}
            className="px-2 py-1 text-xs rounded border border-gray-200 text-gray-600 hover:bg-gray-50 ml-1">
            ↓ PNG
          </button>
        </div>
      </div>

      {/* Chart area */}
      <div ref={chartRef} style={{ height: 280 }}>
        {isLoading ? (
          <div className="h-full flex items-center justify-center text-gray-400 text-sm animate-pulse">
            Loading chart…
          </div>
        ) : chartData.length === 0 ? (
          <div className="h-full flex items-center justify-center text-gray-300 text-sm">
            No data available
          </div>
        ) : chartType === 'bar' ? (
          <ResponsiveContainer width="100%" height="100%">
            <BarChart data={chartData} onClick={e => handleDrillDown(e?.activePayload?.[0]?.payload)}>
              <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
              <XAxis dataKey="name" tick={{ fontSize: 11 }} />
              <YAxis tick={{ fontSize: 11 }} />
              <Tooltip />
              <Bar dataKey="value" fill="#6366f1" radius={[4,4,0,0]} cursor="pointer" />
            </BarChart>
          </ResponsiveContainer>
        ) : chartType === 'line' ? (
          <ResponsiveContainer width="100%" height="100%">
            <LineChart data={chartData}>
              <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
              <XAxis dataKey="name" tick={{ fontSize: 11 }} />
              <YAxis tick={{ fontSize: 11 }} />
              <Tooltip />
              <Line type="monotone" dataKey="value" stroke="#6366f1" strokeWidth={2} dot={{ r: 4 }} />
            </LineChart>
          </ResponsiveContainer>
        ) : (
          <ResponsiveContainer width="100%" height="100%">
            <PieChart>
              <Pie
                data={chartData} dataKey="value" nameKey="name"
                cx="50%" cy="50%" outerRadius={100} label
                onClick={(_, i) => handleDrillDown(chartData[i])}
                cursor="pointer"
              >
                {chartData.map((_, i) => (
                  <Cell key={i} fill={COLORS[i % COLORS.length]} />
                ))}
              </Pie>
              <Tooltip />
              <Legend iconSize={10} />
            </PieChart>
          </ResponsiveContainer>
        )}
      </div>
      <p className="text-xs text-gray-400 mt-2 text-center">Click a bar or slice to drill down into that value</p>
    </div>
  )
}
