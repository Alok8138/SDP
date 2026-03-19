import React, { lazy, Suspense } from 'react'
import { useColumnSchema } from '../hooks'
import ColumnSelector from '../components/ColumnSelector/ColumnSelector'
import FilterBuilder  from '../components/FilterBuilder/FilterBuilder'
import DataTable      from '../components/DataTable/DataTable'
import DateCompare    from '../components/DateCompare/DateCompare'
import SavedViews     from '../components/SavedViews/SavedViews'

const ChartRenderer = lazy(() => import('../components/ChartRenderer/ChartRenderer'))

export default function ReportPage() {
  const { data: schema = [], isLoading: schemaLoading } = useColumnSchema()

  if (schemaLoading) {
    return <div className="p-8 text-gray-400 animate-pulse">Loading schema…</div>
  }

  return (
    <div className="space-y-4">
      {/* Top toolbar row */}
      <div className="flex flex-wrap gap-4 items-start">
        <ColumnSelector schema={schema} />
        <div className="flex-1 min-w-72">
          <FilterBuilder schema={schema} />
        </div>
        <SavedViews />
      </div>

      {/* Date comparison */}
      <DateCompare />

      {/* Chart */}
      <Suspense fallback={<div className="h-72 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-sm animate-pulse">Loading chart…</div>}>
        <ChartRenderer schema={schema} />
      </Suspense>

      {/* Main data table */}
      <DataTable schema={schema} />
    </div>
  )
}
