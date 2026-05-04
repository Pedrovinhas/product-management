import { onBeforeUnmount, watch, type Ref } from 'vue'

const FOCUSABLE_SELECTORS = [
  'a[href]',
  'area[href]',
  'input:not([disabled]):not([type="hidden"])',
  'select:not([disabled])',
  'textarea:not([disabled])',
  'button:not([disabled])',
  '[tabindex]:not([tabindex="-1"])',
].join(',')

export function useFocusTrap(containerRef: Ref<HTMLElement | null>, isActive: Ref<boolean>) {
  let previouslyFocused: HTMLElement | null = null

  function getFocusable(): HTMLElement[] {
    if (!containerRef.value) return []
    return Array.from(containerRef.value.querySelectorAll<HTMLElement>(FOCUSABLE_SELECTORS)).filter(
      (el) => !el.closest('[inert]') && getComputedStyle(el).display !== 'none',
    )
  }

  function onKeyDown(e: KeyboardEvent) {
    if (e.key !== 'Tab' || !containerRef.value) return

    const focusable = getFocusable()
    if (focusable.length === 0) return

    const first = focusable[0]
    const last = focusable[focusable.length - 1]

    if (e.shiftKey) {
      if (document.activeElement === first) {
        e.preventDefault()
        last.focus()
      }
    } else {
      if (document.activeElement === last) {
        e.preventDefault()
        first.focus()
      }
    }
  }

  function activate() {
    previouslyFocused = document.activeElement as HTMLElement | null

    requestAnimationFrame(() => {
      const focusable = getFocusable()
      if (focusable.length > 0) focusable[0].focus()
    })
    document.addEventListener('keydown', onKeyDown)
  }

  function deactivate() {
    document.removeEventListener('keydown', onKeyDown)
    previouslyFocused?.focus()
    previouslyFocused = null
  }

  watch(
    isActive,
    (active) => {
      if (active) activate()
      else deactivate()
    },
    { immediate: false },
  )

  onBeforeUnmount(deactivate)
}
