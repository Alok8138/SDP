import React, { useRef, useCallback, useEffect } from 'react'
import {
  useReactTable, getCoreRowModel, flexRender,
} from '@tanstack/react-table'
import { useVirtualizer } from '@tanstack/react-virtual'
import useReportStore from '../../store/useReportStore'
import { useReportData, useSaveColumnConfig } from '../../hooks'

function SkeletonRow({ cols }) {
  return (
    <tr>
      {Array.from({ length: cols }).map((_, i) => (
        <td key={i} className="px-3 py-2">
          <div className="h-4 bg-gray-200 rounded animate-pulse w-3/4" />
        </td>
      ))}
    </tr>
  )
}

export default function DataTable({ schema = [] }) {
  const { columns: activeKeys, sort, setSort, nextPage, prevPage, cursorHistory } = useReportStore()
  const { data, isLoading, isFetching } = useReportData()
  const saveConfig = useSaveColumnConfig()
  const parentRef  = useRef(null)
  const resizeRef  = useRef({})

  const rows        = data?.rows        ?? []
  const total       = data?.total       ?? 0
  const nextCursor  = data?.nextCursor  ?? null

  const colDefs = activeKeys
    .map(key => schema.find(s => s.key === key))
    .filter(Boolean)
    .map(col => ({
      id:           col.key,
      accessorKey:  col.key,
      header:       col.label,
      size:         resizeRef.current[col.key] ?? 150,
      enableSorting: col.sortable,
      cell: ({ getValue }) => {
        const v = getValue()
        if (typeof v === 'boolean') return v ? '✓' : '—'
        if (col.type === 'date') return v ? new Date(v).toLocaleDateString() : '—'
        return v ?? '—'
      },
    }))

  const table = useReactTable({
    data: rows,
    columns: colDefs,
    getCoreRowModel: getCoreRowModel(),
    columnResizeMode: 'onChange',
    manualSorting: true,
  })

  // Persist column widths on resize end
  const handleResizeEnd = useCallback(() => {
    const config = {}
    table.getAllColumns().forEach(col => {
      config[col.id] = { width: col.getSize() }
    })
    resizeRef.current = Object.fromEntries(
      Object.entries(config).map(([k, v]) => [k, v.width])
    )
    saveConfig.mutate({ user_id: 1, report_id: 'default', config })
  }, [table, saveConfig])

  useEffect(() => {
    window.addEventListener('mouseup', handleResizeEnd)
    return () => window.removeEventListener('mouseup', handleResizeEnd)
  }, [handleResizeEnd])

  const rowVirtualizer = useVirtualizer({
    count:         rows.length,
    getScrollElement: () => parentRef.current,
    estimateSize:  () => 36,
    overscan:      10,
  })

  const virtualRows  = rowVirtualizer.getVirtualItems()
  const totalHeight  = rowVirtualizer.getTotalSize()
  const paddingTop   = virtualRows.length > 0 ? (virtualRows[0]?.start ?? 0) : 0
  const paddingBottom = virtualRows.length > 0
    ? totalHeight - (virtualRows[virtualRows.length - 1]?.end ?? 0)
    : 0

  const handleSort = (colId, sortable) => {
    if (!sortable) return
    const newDir = sort.field === colId && sort.dir === 'asc' ? 'desc' : 'asc'
    setSort(colId, newDir)
  }

  return (
    <div className="bg-white border border-gray-200 rounded-lg overflow-hidden flex flex-col">
      {/* Table header info */}
      <div className="flex items-center justify-between px-4 py-2 border-b border-gray-100 text-xs text-gray-500">
        <span>{total.toLocaleString()} total records</span>
        {isFetching && <span className="text-indigo-500 animate-pulse">Refreshing…</span>}
      </div>

      {/* Scrollable virtual table */}
      <div ref={parentRef} className="overflow-auto" style={{ maxHeight: '60vh' }}>
        <table className="w-full text-sm border-collapse" style={{ tableLayout: 'fixed' }}>
          <thead className="sticky top-0 bg-gray-50 z-10">
            {table.getHeaderGroups().map(hg => (
              <tr key={hg.id}>
                {hg.headers.map(header => (
                  <th
                    key={header.id}
                    style={{ width: header.getSize(), position: 'relative' }}
                    className="text-left px-3 py-2 text-xs font-semibold text-gray-600 border-b border-gray-200 select-none"
                  >
                    <div
                      className={`flex items-center gap-1 ${header.column.columnDef.enableSorting ? 'cursor-pointer hover:text-indigo-600' : ''}`}
                      onClick={() => handleSort(header.column.id, header.column.columnDef.enableSorting)}
                    >
                      {flexRender(header.column.columnDef.header, header.getContext())}
                      {header.column.columnDef.enableSorting && sort.field === header.column.id && (
                        <span>{sort.dir === 'asc' ? '↑' : '↓'}</span>
                      )}
                    </div>
                    {/* Resize handle */}
                    <div
                      onMouseDown={header.getResizeHandler()}
                      className="absolute right-0 top-0 h-full w-1 cursor-col-resize bg-transparent hover:bg-indigo-400"
                    />
                  </th>
                ))}
              </tr>
            ))}
          </thead>

          <tbody>
            {isLoading
              ? Array.from({ length: 8 }).map((_, i) => (
                  <SkeletonRow key={i} cols={activeKeys.length} />
                ))
              : (
                <>
                  {paddingTop > 0 && <tr><td colSpan={activeKeys.length} style={{ height: paddingTop }} /></tr>}
                  {virtualRows.map(vRow => {
                    const row = table.getRowModel().rows[vRow.index]
                    if (!row) return null
                    return (
                      <tr key={row.id} className="hover:bg-indigo-50 transition-colors border-b border-gray-100">
                        {row.getVisibleCells().map(cell => (
                          <td key={cell.id} className="px-3 py-2 text-gray-700 truncate" style={{ width: cell.column.getSize() }}>
                            {flexRender(cell.column.columnDef.cell, cell.getContext())}
                          </td>
                        ))}
                      </tr>
                    )
                  })}
                  {paddingBottom > 0 && <tr><td colSpan={activeKeys.length} style={{ height: paddingBottom }} /></tr>}
                </>
              )
            }
          </tbody>
        </table>
      </div>

      {/* Pagination */}
      <div className="flex items-center justify-between px-4 py-2 border-t border-gray-100 text-xs text-gray-500">
        <button
          onClick={prevPage}
          disabled={cursorHistory.length <= 1}
          className="px-3 py-1 rounded border border-gray-200 disabled:opacity-40 hover:bg-gray-50"
        >
          ← Prev
        </button>
        <span>Page {cursorHistory.length} · {rows.length} rows shown</span>
        <button
          onClick={() => nextPage(nextCursor)}
          disabled={!nextCursor || nextCursor === '*'}
          className="px-3 py-1 rounded border border-gray-200 disabled:opacity-40 hover:bg-gray-50"
        >
          Next →
        </button>
      </div>
    </div>
  )
}
