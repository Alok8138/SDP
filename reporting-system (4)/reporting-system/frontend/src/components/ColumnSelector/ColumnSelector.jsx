import React from 'react'
import {
  DndContext, closestCenter, KeyboardSensor, PointerSensor, useSensor, useSensors,
} from '@dnd-kit/core'
import {
  SortableContext, sortableKeyboardCoordinates, useSortable,
  verticalListSortingStrategy, arrayMove,
} from '@dnd-kit/sortable'
import { CSS } from '@dnd-kit/utilities'
import useReportStore from '../../store/useReportStore'

function SortableColumn({ col, enabled, onToggle }) {
  const { attributes, listeners, setNodeRef, transform, transition } = useSortable({ id: col.key })
  const style = { transform: CSS.Transform.toString(transform), transition }

  return (
    <div ref={setNodeRef} style={style} className="flex items-center gap-2 p-2 bg-white border border-gray-200 rounded-md mb-1">
      <span {...attributes} {...listeners} className="cursor-grab text-gray-400 select-none">⠿</span>
      <input
        type="checkbox"
        checked={enabled}
        onChange={() => onToggle(col.key)}
        className="accent-indigo-600"
        id={`col-${col.key}`}
      />
      <label htmlFor={`col-${col.key}`} className="text-sm text-gray-700 flex-1 cursor-pointer">
        {col.label}
        {col.group && <span className="ml-2 text-xs text-gray-400">({col.group})</span>}
      </label>
      <span className="text-xs text-gray-400">{col.type}</span>
    </div>
  )
}

export default function ColumnSelector({ schema = [] }) {
  const { columns, toggleColumn, reorderColumns } = useReportStore()

  const sensors = useSensors(
    useSensor(PointerSensor),
    useSensor(KeyboardSensor, { coordinateGetter: sortableKeyboardCoordinates })
  )

  const sortedSchema = [...schema].sort((a, b) => {
    const ai = columns.indexOf(a.key)
    const bi = columns.indexOf(b.key)
    return (ai === -1 ? 999 : ai) - (bi === -1 ? 999 : bi)
  })

  const handleDragEnd = ({ active, over }) => {
    if (!over || active.id === over.id) return
    const oldIdx = columns.indexOf(active.id)
    const newIdx = columns.indexOf(over.id)
    if (oldIdx !== -1 && newIdx !== -1) {
      reorderColumns(arrayMove(columns, oldIdx, newIdx))
    }
  }

  return (
    <div className="bg-gray-50 border border-gray-200 rounded-lg p-3 w-64">
      <h3 className="text-sm font-semibold text-gray-700 mb-2">Columns</h3>
      <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
        <SortableContext items={sortedSchema.map(c => c.key)} strategy={verticalListSortingStrategy}>
          {sortedSchema.map(col => (
            <SortableColumn
              key={col.key}
              col={col}
              enabled={columns.includes(col.key)}
              onToggle={toggleColumn}
            />
          ))}
        </SortableContext>
      </DndContext>
    </div>
  )
}
