import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  headers: { 'Content-Type': 'application/json' },
})

// ── Reports ──────────────────────────────────────────────
export const fetchReports = (params) =>
  api.get('/reports', { params }).then(r => r.data.data)

export const fetchSchema = () =>
  api.get('/reports/schema').then(r => r.data.data)

export const fetchFacets = (field, q = '') =>
  api.get(`/facets/${field}`, { params: { q } }).then(r => r.data.data)

// ── Charts ───────────────────────────────────────────────
export const fetchChartData = (params) =>
  api.get('/charts', { params }).then(r => r.data.data)

// ── Saved Views ───────────────────────────────────────────
export const fetchSavedViews = (userId = 1) =>
  api.get('/saved-views', { params: { user_id: userId } }).then(r => r.data.data)

export const createSavedView = (payload) =>
  api.post('/saved-views', payload).then(r => r.data.data)

export const updateSavedView = (id, payload) =>
  api.put(`/saved-views/${id}`, payload).then(r => r.data.data)

export const deleteSavedView = (id) =>
  api.delete(`/saved-views/${id}`).then(r => r.data)

// ── Column Config ─────────────────────────────────────────
export const fetchColumnConfig = (userId = 1, reportId = 'default') =>
  api.get('/column-config', { params: { user_id: userId, report_id: reportId } }).then(r => r.data.data)

export const saveColumnConfig = (payload) =>
  api.put('/column-config', payload).then(r => r.data)

// ── Date Compare ──────────────────────────────────────────
export const fetchDateCompare = (payload) =>
  api.post('/date-compare', payload).then(r => r.data.data)
