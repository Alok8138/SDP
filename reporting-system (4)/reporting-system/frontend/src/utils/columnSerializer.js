/**
 * Serializes active column keys to a comma-separated string
 * for the API ?columns= param.
 */
export function serializeColumns(columns = []) {
  return columns.join(',')
}

/**
 * Converts a column config object { price: { width: 120 }, name: { width: 250 } }
 * into a flat array for easy iteration in the DataTable.
 */
export function flattenColumnConfig(config = {}) {
  return Object.entries(config).map(([key, val]) => ({
    key,
    width: val.width ?? 150,
  }))
}

/**
 * Merges persisted column widths from the API into the
 * active column list from Zustand.
 */
export function mergeColumnWidths(activeColumns = [], savedConfig = {}) {
  return activeColumns.map(key => ({
    key,
    width: savedConfig[key]?.width ?? 150,
  }))
}
