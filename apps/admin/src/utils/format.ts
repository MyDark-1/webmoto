export const formatPrice = (price: number | string): string =>
  new Intl.NumberFormat('ru-RU').format(Number(price) || 0)

export const formatDate = (value: string | Date): string =>
  new Date(value).toLocaleDateString('ru-RU')

export const formatDateTime = (value: string | Date): string =>
  new Date(value).toLocaleString('ru-RU')
