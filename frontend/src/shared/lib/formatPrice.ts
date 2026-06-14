export const formatPrice = (price: number | string): string => {
  if (typeof price === 'string') return price

  return new Intl.NumberFormat('kk-KZ', {
    style: 'currency',
    currency: 'KZT',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price)
}

export const formatCurrency = (
  price: number | string,
  currency: 'KZT' | 'RUB' | 'USD' = 'KZT'
): string => {
  if (typeof price === 'string') return price

  const locales = {
    KZT: 'kk-KZ',
    RUB: 'ru-RU',
    USD: 'en-US',
  }

  return new Intl.NumberFormat(locales[currency], {
    style: 'currency',
    currency: currency,
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price)
}
