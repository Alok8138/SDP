import React, { useState } from 'react'
import { useDateCompare } from '../../hooks'
import useReportStore from '../../store/useReportStore'

function StatCard({ label, current, previous, diff }) {
  const pct = diff?.percent
  const abs = diff?.absolute
  const up  = abs >= 0

  return (
    <div className="bg-gray-50 border border-gray-200 rounded-lg p-3 flex-1 min-w-36">
      <p className="text-xs text-gray-500 mb-1">{label}</p>
      <p className="text-lg font-semibold text-gray-800">{Number(current ?? 0).toLocaleString(undefined, { maximumFractionDigits: 2 })}</p>
      <p className="text-xs text-gray-400">vs {Number(previous ?? 0).toLocaleString(undefined, { maximumFractionDigits: 2 })}</p>
      {pct !== null && pct !== undefined && (
        <p className={`text-xs font-medium mt-1 ${up ? 'text-emerald-600' : 'text-red-500'}`}>
          {up ? '↑' : '↓'} {Math.abs(pct)}% ({abs >= 0 ? '+' : ''}{Number(abs).toLocaleString(undefined, { maximumFractionDigits: 2 })})
        </p>
      )}
    </div>
  )
}

export default function DateCompare() {
  const { filters } = useReportStore()
  const today  = new Date().toISOString().slice(0, 10)
  const first  = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10)

  const [periodStart, setPeriodStart] = useState(first)
  const [periodEnd,   setPeriodEnd]   = useState(today)
  const [mode, setMode]               = useState('previous_period')
  const [enabled, setEnabled]         = useState(false)

  const payload = enabled ? {
    field:        'created_at',
    period_start: periodStart,
    period_end:   periodEnd,
    compare_mode: mode,
    filters:      filters.rules,
    metrics:      ['price', 'quantity'],
  } : null

  const { data, isLoading } = useDateCompare(payload)

  return (
    <div className="bg-white border border-gray-200 rounded-lg p-4">
      <div className="flex items-center justify-between mb-3">
        <h3 className="text-sm font-semibold text-gray-700">Date Compare</h3>
        <label className="flex items-center gap-2 cursor-pointer text-xs text-gray-500">
          <input type="checkbox" checked={enabled} onChange={e => setEnabled(e.target.checked)}
            className="accent-indigo-600" />
          Enable
        </label>
      </div>

      <div className="flex flex-wrap gap-3 mb-3">
        <div className="flex flex-col gap-1">
          <label className="text-xs text-gray-400">Period</label>
          <div className="flex items-center gap-1">
            <input type="date" value={periodStart} onChange={e => setPeriodStart(e.target.value)}
              className="border border-gray-200 rounded px-2 py-1 text-sm" />
            <span className="text-gray-400 text-xs">–</span>
            <input type="date" value={periodEnd} onChange={e => setPeriodEnd(e.target.value)}
              className="border border-gray-200 rounded px-2 py-1 text-sm" />
          </div>
        </div>
        <div className="flex flex-col gap-1">
          <label className="text-xs text-gray-400">Compare with</label>
          <select value={mode} onChange={e => setMode(e.target.value)}
            className="border border-gray-200 rounded px-2 py-1 text-sm">
            <option value="previous_period">Previous period</option>
            <option value="same_period_last_year">Same period last year</option>
          </select>
        </div>
      </div>

      {enabled && (
        isLoading ? (
          <div className="text-xs text-gray-400 animate-pulse">Comparing periods…</div>
        ) : data ? (
          <div className="flex flex-wrap gap-2 mt-2">
            <StatCard
              label="Records"
              current={data.current?.count}
              previous={data.previous?.count}
              diff={data.diff?.count}
            />
            <StatCard
              label="Price (sum)"
              current={data.current?.price_sum}
              previous={data.previous?.price_sum}
              diff={data.diff?.price}
            />
            <StatCard
              label="Qty (sum)"
              current={data.current?.quantity_sum}
              previous={data.previous?.quantity_sum}
              diff={data.diff?.quantity}
            />
          </div>
        ) : null
      )}
    </div>
  )
}
