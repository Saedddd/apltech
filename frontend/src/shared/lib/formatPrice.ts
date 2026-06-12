/**
 * Форматирует цену для отображения
 * @param price - число или строка
 * @returns отформатированная цена
 */
export const formatPrice = (price: number | string | undefined): string => {
  if (!price && price !== 0) return '—'

  // Если цена уже строка (например "цена по запросу")
  if (typeof price === 'string') {
    return price
  }

  // Форматируем число
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price)
}
