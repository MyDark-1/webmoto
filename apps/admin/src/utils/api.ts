interface ApiOptions extends RequestInit {
  json?: unknown
}

export interface ApiResponse<T = unknown> {
  success: boolean
  data?: T
  error?: string
}

const TOKEN_KEY = 'admin_token'

export function getToken(): string | null {
  return localStorage.getItem(TOKEN_KEY)
}

export async function apiFetch<T = unknown>(
  url: string,
  options: ApiOptions = {}
): Promise<ApiResponse<T>> {
  const headers = new Headers(options.headers)
  if (options.json !== undefined) headers.set('Content-Type', 'application/json')
  const token = getToken()
  if (token) headers.set('Authorization', `Bearer ${token}`)

  const response = await fetch(url, {
    ...options,
    headers,
    body: options.json !== undefined ? JSON.stringify(options.json) : options.body
  })
  try {
    return (await response.json()) as ApiResponse<T>
  } catch {
    return { success: response.ok, error: response.statusText }
  }
}
