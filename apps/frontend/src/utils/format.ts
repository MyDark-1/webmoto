export const formatPrice = (price: number | string): string =>
  new Intl.NumberFormat('ru-RU').format(Number(price) || 0)

export const formatDate = (value: string | Date): string =>
  new Date(value).toLocaleDateString('ru-RU', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })

export const formatDateTime = (value: string | Date): string =>
  new Date(value).toLocaleString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
