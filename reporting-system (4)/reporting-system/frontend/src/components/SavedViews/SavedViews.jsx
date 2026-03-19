import React, { useState } from 'react'
import { useSavedViews, useCreateSavedView, useUpdateSavedView, useDeleteSavedView } from '../../hooks'
import useReportStore from '../../store/useReportStore'

export default function SavedViews() {
  const { columns, filters, sort, loadSavedView, savedViewId } = useReportStore()
  const { data: views = [], isLoading } = useSavedViews()
  const createView = useCreateSavedView()
  const updateView = useUpdateSavedView()
  const deleteView = useDeleteSavedView()

  const [name, setName]       = useState('')
  const [saving, setSaving]   = useState(false)
  const [showForm, setShowForm] = useState(false)

  const handleSave = async () => {
    if (!name.trim()) return
    setSaving(true)
    await createView.mutateAsync({
      user_id:    1,
      name:       name.trim(),
      columns,
      filters,
      sort,
      is_default: 0,
      shared_with: [],
    })
    setName('')
    setShowForm(false)
    setSaving(false)
  }

  const handleSetDefault = (view) => {
    updateView.mutate({ id: view.id, ...view, is_default: 1 })
  }

  return (
    <div className="bg-white border border-gray-200 rounded-lg p-4">
      <div className="flex items-center justify-between mb-3">
        <h3 className="text-sm font-semibold text-gray-700">Saved Views</h3>
        <button
          onClick={() => setShowForm(v => !v)}
          className="text-xs bg-indigo-50 text-indigo-600 border border-indigo-200 rounded px-2 py-1 hover:bg-indigo-100"
        >
          {showForm ? 'Cancel' : '+ Save current'}
        </button>
      </div>

      {showForm && (
        <div className="flex items-center gap-2 mb-3">
          <input
            type="text" placeholder="View name…"
            value={name} onChange={e => setName(e.target.value)}
            className="border border-gray-200 rounded px-2 py-1 text-sm flex-1"
            onKeyDown={e => e.key === 'Enter' && handleSave()}
          />
          <button onClick={handleSave} disabled={saving || !name.trim()}
            className="text-xs bg-indigo-600 text-white rounded px-3 py-1 hover:bg-indigo-700 disabled:opacity-50">
            Save
          </button>
        </div>
      )}

      {isLoading && <p className="text-xs text-gray-400 animate-pulse">Loading…</p>}

      <div className="space-y-1 max-h-48 overflow-y-auto">
        {views.map(view => (
          <div key={view.id}
            className={`flex items-center justify-between px-3 py-2 rounded-md border text-sm cursor-pointer transition-colors
              ${savedViewId === view.id ? 'border-indigo-300 bg-indigo-50' : 'border-gray-100 hover:bg-gray-50'}`}
            onClick={() => loadSavedView(view)}
          >
            <div className="flex items-center gap-2 flex-1 min-w-0">
              <span className="truncate text-gray-700">{view.name}</span>
              {view.is_default ? (
                <span className="text-xs bg-emerald-100 text-emerald-700 rounded px-1">default</span>
              ) : null}
              <span className="text-xs text-gray-400 ml-auto">v{view.version}</span>
            </div>
            <div className="flex items-center gap-1 ml-2 shrink-0">
              <button
                onClick={e => { e.stopPropagation(); handleSetDefault(view) }}
                className="text-xs text-gray-400 hover:text-indigo-600 px-1"
                title="Set as default"
              >★</button>
              <button
                onClick={e => { e.stopPropagation(); deleteView.mutate(view.id) }}
                className="text-xs text-red-300 hover:text-red-500 px-1"
                title="Delete"
              >✕</button>
            </div>
          </div>
        ))}
        {views.length === 0 && !isLoading && (
          <p className="text-xs text-gray-400 italic">No saved views yet.</p>
        )}
      </div>
    </div>
  )
}
