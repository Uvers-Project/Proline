<template>
  <div v-if="isVisible" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 animate-in zoom-in-95 duration-200">
      <h3 class="text-xl font-bold mb-3 text-gray-900">{{ modalOptions.title }}</h3>
      <p class="text-gray-600 mb-8">{{ modalOptions.message }}</p>
      
      <div class="flex justify-end space-x-3">
        <button v-if="modalOptions.type === 'confirm'" @click="cancel" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-medium transition-colors">
          {{ modalOptions.cancelText }}
        </button>
        <button @click="confirm" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-sm transition-colors shadow-blue-500/30">
          {{ modalOptions.confirmText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useModal } from '../composables/useModal'
const { isVisible, modalOptions, confirm, cancel } = useModal()
</script>
