import { reactive } from 'vue'

export interface Toast {
  id: number
  type: 'success' | 'error' | 'info'
  message: string
}

export const toasts = reactive<Toast[]>([])
let nextId = 1

export function notify(message: string, type: Toast['type'] = 'info', timeout = 3000) {
  const id = nextId++
  toasts.push({ id, type, message })
  setTimeout(() => {
    const idx = toasts.findIndex((t) => t.id === id)
    if (idx !== -1) toasts.splice(idx, 1)
  }, timeout)
}

export const notifySuccess = (m: string) => notify(m, 'success')
export const notifyError = (m: string) => notify(m, 'error')
