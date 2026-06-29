<template>
  <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none">
    <TransitionGroup name="toast" tag="div" class="flex flex-col gap-2">
      <div v-for="toast in toasts" :key="toast.id" 
           class="pointer-events-auto flex items-center p-4 rounded-xl shadow-lg border backdrop-blur-sm transform transition-all"
           :class="{
             'bg-emerald-50 border-emerald-200 text-emerald-800': toast.type.toLowerCase() === 'success',
             'bg-red-50 border-red-200 text-red-800': toast.type.toLowerCase() === 'error',
             'bg-blue-50 border-blue-200 text-blue-800': toast.type.toLowerCase() !== 'success' && toast.type.toLowerCase() !== 'error'
           }">
        <svg v-if="toast.type.toLowerCase() === 'success'" class="w-5 h-5 mr-3 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg v-else-if="toast.type.toLowerCase() === 'error'" class="w-5 h-5 mr-3 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        <svg v-else class="w-5 h-5 mr-3 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="text-sm font-semibold">{{ toast.message }}</div>
        <button @click="removeToast(toast.id)" class="ml-4 text-current opacity-50 hover:opacity-100 transition-opacity">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useToast } from '../composables/useToast'

const { toasts, removeToast } = useToast()
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(100%) scale(0.9);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(100%) scale(0.9);
}
</style>
