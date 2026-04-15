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
    const index = toasts.findIndex((t) => t.id === id)
    if (index !== -1) toasts.splice(index, 1)
  }, timeout)
}

export const notifySuccess = (m: string) => notify(m, 'success')
export const notifyError = (m: string) => notify(m, 'error')
