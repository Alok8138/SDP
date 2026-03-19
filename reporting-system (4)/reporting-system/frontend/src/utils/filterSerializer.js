/**
 * Serializes the Zustand filter tree into a JSON string
 * ready to be sent as a query param to the PHP API.
 *
 * Example input:
 *   { operator: 'AND', rules: [{ field: 'category', type: 'dropdown', value: ['Chair'] }] }
 *
 * Example output (as string):
 *   '{"operator":"AND","rules":[{"field":"category","type":"dropdown","value":["Chair"]}]}'
 */
export function serializeFilters(filterTree) {
  if (!filterTree || !filterTree.rules || filterTree.rules.length === 0) {
    return ''
  }
  // Strip out any incomplete rules (no field selected yet)
  const cleaned = {
    ...filterTree,
    rules: filterTree.rules.filter(r => r.field && r.value !== null && r.value !== ''),
  }
  return JSON.stringify(cleaned)
}

/**
 * Deserializes a JSON filter string back into the filter tree object.
 */
export function deserializeFilters(str) {
  if (!str) return { operator: 'AND', rules: [] }
  try {
    return JSON.parse(str)
  } catch {
    return { operator: 'AND', rules: [] }
  }
}
