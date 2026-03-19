/**
 * Returns a debounced version of fn that delays invoking it
 * until after `delay` ms have elapsed since the last call.
 */
export function debounce(fn, delay = 300) {
  let timer
  return (...args) => {
    clearTimeout(timer)
    timer = setTimeout(() => fn(...args), delay)
  }
}
