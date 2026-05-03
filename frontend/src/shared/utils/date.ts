export function formatDate(value: string | Date, includeTime = false): string {
  const date = typeof value === 'string' ? new Date(value) : value

  const options: Intl.DateTimeFormatOptions = {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }

  if (includeTime) {
    options.hour = '2-digit'
    options.minute = '2-digit'
  }

  return date.toLocaleString('pt-BR', options)
}

export function formatDateTime(value: string | Date): string {
  return formatDate(value, true)
}
