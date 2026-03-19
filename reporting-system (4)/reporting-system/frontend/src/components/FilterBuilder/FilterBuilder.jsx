import React, { useState, useCallback } from 'react'
import useReportStore from '../../store/useReportStore'
import { useFacets } from '../../hooks'

function FilterValue({ rule, schema, onChange }) {
  const col    = schema.find(s => s.key === rule.field)
  const type   = col?.type || 'text'
  const [facetQ, setFacetQ] = useState('')
  const { data: facets = [] } = useFacets(col?.facet ? rule.field : null, facetQ)

  if (type === 'boolean') {
    return (
      <select
        value={String(rule.value ?? '')}
        onChange={e => onChange({ ...rule, value: e.target.value === 'true' })}
        className="border border-gray-300 rounded px-2 py-1 text-sm"
      >
        <option value="">Select</option>
        <option value="true">Yes</option>
        <option value="false">No</option>
      </select>
    )
  }

  if (type === 'number') {
    return (
      <div className="flex items-center gap-1">
        <input type="number" placeholder="From"
          value={rule.value?.from ?? ''}
          onChange={e => onChange({ ...rule, type: 'number_range', value: { ...rule.value, from: e.target.value } })}
          className="border border-gray-300 rounded px-2 py-1 text-sm w-20"
        />
        <span className="text-gray-400 text-xs">–</span>
        <input type="number" placeholder="To"
          value={rule.value?.to ?? ''}
          onChange={e => onChange({ ...rule, type: 'number_range', value: { ...rule.value, to: e.target.value } })}
          className="border border-gray-300 rounded px-2 py-1 text-sm w-20"
        />
      </div>
    )
  }

  if (type === 'date') {
    return (
      <div className="flex items-center gap-1">
        <input type="date" value={rule.value?.from ?? ''}
          onChange={e => onChange({ ...rule, type: 'date_range', value: { ...rule.value, from: e.target.value } })}
          className="border border-gray-300 rounded px-2 py-1 text-sm"
        />
        <span className="text-gray-400 text-xs">–</span>
        <input type="date" value={rule.value?.to ?? ''}
          onChange={e => onChange({ ...rule, type: 'date_range', value: { ...rule.value, to: e.target.value } })}
          className="border border-gray-300 rounded px-2 py-1 text-sm"
        />
      </div>
    )
  }

  if (type === 'dropdown' && col?.facet) {
    return (
      <div className="relative">
        <input
          type="text" placeholder="Search..."
          value={facetQ}
          onChange={e => setFacetQ(e.target.value)}
          className="border border-gray-300 rounded px-2 py-1 text-sm w-36"
        />
        {facets.length > 0 && (
          <div className="absolute top-8 left-0 bg-white border border-gray-200 rounded shadow-lg z-10 w-48 max-h-48 overflow-y-auto">
            {facets.map(f => (
              <label key={f.value} className="flex items-center gap-2 px-3 py-1 hover:bg-gray-50 cursor-pointer text-sm">
                <input type="checkbox"
                  checked={Array.isArray(rule.value) && rule.value.includes(f.value)}
                  onChange={e => {
                    const cur = Array.isArray(rule.value) ? rule.value : []
                    const next = e.target.checked ? [...cur, f.value] : cur.filter(v => v !== f.value)
                    onChange({ ...rule, type: 'dropdown', value: next })
                  }}
                  className="accent-indigo-600"
                />
                <span>{f.value}</span>
                <span className="text-gray-400 text-xs ml-auto">{f.count}</span>
              </label>
            ))}
          </div>
        )}
      </div>
    )
  }

  return (
    <input type="text" placeholder="Value"
      value={rule.value ?? ''}
      onChange={e => onChange({ ...rule, type: 'text', value: e.target.value })}
      className="border border-gray-300 rounded px-2 py-1 text-sm w-36"
    />
  )
}

function FilterRow({ rule, index, schema, onUpdate, onRemove }) {
  return (
    <div className="flex items-center gap-2 py-1">
      <select
        value={rule.field ?? ''}
        onChange={e => onUpdate(index, { ...rule, field: e.target.value, value: null })}
        className="border border-gray-300 rounded px-2 py-1 text-sm"
      >
        <option value="">Field</option>
        {schema.filter(s => s.filterable).map(s => (
          <option key={s.key} value={s.key}>{s.label}</option>
        ))}
      </select>
      <FilterValue rule={rule} schema={schema} onChange={updated => onUpdate(index, updated)} />
      <button onClick={() => onRemove(index)} className="text-red-400 hover:text-red-600 text-sm px-1">✕</button>
    </div>
  )
}

export default function FilterBuilder({ schema = [] }) {
  const { filters, addFilter, removeFilter, updateFilter, setFilterOperator, resetFilters } = useReportStore()

  return (
    <div className="bg-white border border-gray-200 rounded-lg p-4">
      <div className="flex items-center justify-between mb-3">
        <div className="flex items-center gap-2">
          <span className="text-sm font-semibold text-gray-700">Filters</span>
          <select
            value={filters.operator}
            onChange={e => setFilterOperator(e.target.value)}
            className="border border-gray-200 rounded px-2 py-0.5 text-xs text-gray-600"
          >
            <option value="AND">AND</option>
            <option value="OR">OR</option>
          </select>
        </div>
        <div className="flex items-center gap-2">
          <button
            onClick={() => addFilter({ field: '', type: 'text', value: '' })}
            className="text-xs bg-indigo-50 text-indigo-600 border border-indigo-200 rounded px-2 py-1 hover:bg-indigo-100"
          >
            + Add Filter
          </button>
          {filters.rules.length > 0 && (
            <button onClick={resetFilters} className="text-xs text-gray-400 hover:text-gray-600">
              Clear all
            </button>
          )}
        </div>
      </div>

      {filters.rules.length === 0 && (
        <p className="text-xs text-gray-400 italic">No filters applied. Click "+ Add Filter" to start.</p>
      )}

      {filters.rules.map((rule, i) => (
        <FilterRow
          key={i}
          index={i}
          rule={rule}
          schema={schema}
          onUpdate={updateFilter}
          onRemove={removeFilter}
        />
      ))}
    </div>
  )
}
