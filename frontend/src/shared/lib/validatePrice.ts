/**
 * Валидация цены
 * @param price - цена для проверки
 * @returns сообщение об ошибке или null
 */
export const validatePrice = (price: number): string | null => {
  if (!price && price !== 0) {
    return 'Цена обязательна'
  }
  if (price <= 0) {
    return 'Цена должна быть больше 0'
  }
  if (price > 999999999) {
    return 'Цена слишком большая'
  }
  return null
}

/**
 * Валидация названия товара
 * @param name - название для проверки
 * @returns сообщение об ошибке или null
 */
export const validateName = (name: string): string | null => {
  if (!name || name.trim() === '') {
    return 'Название обязательно'
  }
  if (name.length < 2) {
    return 'Название должно содержать минимум 2 символа'
  }
  if (name.length > 255) {
    return 'Название не должно превышать 255 символов'
  }
  if (/test/i.test(name)) {
    return 'Название не должно содержать слово "test"'
  }
  return null
}
