<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    id?: string
    placeholder?: string
    disabled?: boolean
    ariaInvalid?: boolean
  }>(),
  {
    placeholder: '0,00',
    disabled: false,
    ariaInvalid: false,
  },
)

const model = defineModel<number | undefined>()

const maskedValue = computed(() => {
  if (model.value === undefined || model.value === null || Number.isNaN(model.value)) {
    return ''
  }

  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(model.value)
})

function onInput(event: Event) {
  const input = event.target as HTMLInputElement
  const digits = input.value.replace(/\D/g, '')

  if (!digits) {
    model.value = undefined
    return
  }

  model.value = Number((Number(digits) / 100).toFixed(2))
}
</script>

<template>
  <input
    :id="id"
    type="text"
    inputmode="decimal"
    :value="maskedValue"
    :placeholder="placeholder"
    :disabled="disabled"
    :aria-invalid="ariaInvalid"
    @input="onInput"
  />
</template>