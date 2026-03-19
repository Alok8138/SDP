import { useQuery, useMutation, useQueryClient, keepPreviousData } from '@tanstack/react-query'
import {
  fetchReports, fetchSchema, fetchFacets,
  fetchChartData, fetchSavedViews, createSavedView,
  updateSavedView, deleteSavedView,
  fetchColumnConfig, saveColumnConfig, fetchDateCompare,
} from '../api/reportApi'
import useReportStore from '../store/useReportStore'

// ── Report data ───────────────────────────────────────────
export function useReportData() {
  const { columns, filters, sort, rows, cursor } = useReportStore()
  return useQuery({
    queryKey: ['reports', columns, filters, sort, rows, cursor],
    queryFn: () => fetchReports({
      columns:    columns.join(','),
      filters:    JSON.stringify(filters),
      sort_field: sort.field,
      sort_dir:   sort.dir,
      rows,
      cursor,
    }),
    placeholderData: keepPreviousData,
  })
}

// ── Schema (server-driven columns) ───────────────────────
export function useColumnSchema() {
  // bump version to force refetch after schema field changes
  return useQuery({ queryKey: ['schema', 'v2'], queryFn: fetchSchema, staleTime: Infinity })
}

// ── Facets (autocomplete) ─────────────────────────────────
export function useFacets(field, q = '') {
  return useQuery({
    queryKey: ['facets', field, q],
    queryFn:  () => fetchFacets(field, q),
    enabled:  !!field,
    staleTime: 60_000,
  })
}

// ── Chart data ────────────────────────────────────────────
export function useChartData(params) {
  return useQuery({
    queryKey: ['chart', params],
    queryFn:  () => fetchChartData(params),
    enabled:  !!params?.field,
  })
}

// ── Saved views ───────────────────────────────────────────
export function useSavedViews() {
  return useQuery({ queryKey: ['savedViews'], queryFn: () => fetchSavedViews(1) })
}

export function useCreateSavedView() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: createSavedView,
    onSuccess:  () => qc.invalidateQueries({ queryKey: ['savedViews'] }),
  })
}

export function useUpdateSavedView() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: ({ id, ...data }) => updateSavedView(id, data),
    onSuccess:  () => qc.invalidateQueries({ queryKey: ['savedViews'] }),
  })
}

export function useDeleteSavedView() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: deleteSavedView,
    onSuccess:  () => qc.invalidateQueries({ queryKey: ['savedViews'] }),
  })
}

// ── Column config ─────────────────────────────────────────
export function useColumnConfig() {
  return useQuery({ queryKey: ['columnConfig'], queryFn: () => fetchColumnConfig(1, 'default') })
}

export function useSaveColumnConfig() {
  const qc = useQueryClient()
  return useMutation({
    mutationFn: saveColumnConfig,
    onSuccess:  () => qc.invalidateQueries({ queryKey: ['columnConfig'] }),
  })
}

// ── Date compare ──────────────────────────────────────────
export function useDateCompare(payload) {
  return useQuery({
    queryKey: ['dateCompare', payload],
    queryFn:  () => fetchDateCompare(payload),
    enabled:  !!payload?.period_start && !!payload?.period_end,
  })
}
