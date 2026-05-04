<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { RouterView } from 'vue-router'
import { Toaster } from 'vue-sonner'
import AppShell from '@/shared/components/AppShell.vue'

const route = useRoute()
const useShellLayout = computed(() => route.meta.layout === 'shell')
</script>

<template>
  <div class="min-h-screen bg-zinc-50 text-zinc-900">
    <AppShell v-if="useShellLayout">
      <RouterView />
    </AppShell>
    <main v-else>
      <RouterView />
    </main>
    <Toaster rich-colors position="top-right" />
  </div>
</template>
