<script setup lang="ts">
import { ToastProvider } from 'radix-vue'

const props = defineProps({
  duration: {
    type: Number,
    default: 5000
  },
  swipeDirection: {
    type: String as () => 'up' | 'down' | 'left' | 'right',
    default: 'right'
  },
  swipeThreshold: {
    type: Number,
    default: 50
  }
})
</script>

<template>
  <ToastProvider v-bind="props">
    <slot />
  </ToastProvider>
</template>
