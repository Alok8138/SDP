import { create } from 'zustand'

const useReportStore = create((set, get) => ({
  // Active columns (keys from schema)
  columns: [
    'product_id_i',
    'Product_Name_s',
    'Brand_Name_s',
    'Price_f',
    'AF_PRICE_f',
    'Quantity_i',
    'Stock_s',
    'Type_s',
    'Default_SKU_s',
    'source_file_s',
    'AF_URL_s',
  ],

  // Filter tree: { operator: 'AND', rules: [...] }
  filters: { operator: 'AND', rules: [] },

  // Sorting
  sort: { field: 'product_id_i', dir: 'asc' },

  // Pagination
  rows: 25,
  cursor: '*',
  cursorHistory: ['*'],

  // Active saved view
  savedViewId: null,

  // Actions
  setColumns: (columns) => set({ columns }),

  toggleColumn: (key) => {
    const cols = get().columns
    set({
      columns: cols.includes(key) ? cols.filter(c => c !== key) : [...cols, key],
    })
  },

  reorderColumns: (newOrder) => set({ columns: newOrder }),

  addFilter: (rule) => {
    const filters = get().filters
    set({ filters: { ...filters, rules: [...filters.rules, rule] }, cursor: '*' })
  },

  removeFilter: (index) => {
    const rules = [...get().filters.rules]
    rules.splice(index, 1)
    set({ filters: { ...get().filters, rules }, cursor: '*' })
  },

  updateFilter: (index, rule) => {
    const rules = [...get().filters.rules]
    rules[index] = rule
    set({ filters: { ...get().filters, rules }, cursor: '*' })
  },

  setFilterOperator: (operator) => set({ filters: { ...get().filters, operator }, cursor: '*' }),

  resetFilters: () => set({ filters: { operator: 'AND', rules: [] }, cursor: '*', cursorHistory: ['*'] }),

  setSort: (field, dir = 'asc') => set({ sort: { field, dir }, cursor: '*', cursorHistory: ['*'] }),

  nextPage: (nextCursor) => {
    if (!nextCursor || nextCursor === get().cursor) return
    const history = [...get().cursorHistory, nextCursor]
    set({ cursor: nextCursor, cursorHistory: history })
  },

  prevPage: () => {
    const history = [...get().cursorHistory]
    if (history.length <= 1) return
    history.pop()
    const prev = history[history.length - 1]
    set({ cursor: prev, cursorHistory: history })
  },

  loadSavedView: (view) => {
    set({
      columns:     view.columns_config || [],
      filters:     view.filters        || { operator: 'AND', rules: [] },
      sort:        view.sort_config    || { field: 'id', dir: 'asc' },
      savedViewId: view.id,
      cursor:      '*',
      cursorHistory: ['*'],
    })
  },
}))

export default useReportStore
