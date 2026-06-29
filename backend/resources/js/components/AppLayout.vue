<template>
  <div class="min-h-screen bg-gray-50 text-gray-900 font-sans">
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center cursor-pointer" @click="router.push('/dashboard')">
            <span class="text-2xl font-black tracking-tighter" style="color: #0672B9;">
              Proline
            </span>
          </div>
          <div class="flex items-center space-x-6">
            <div v-if="authStore.user" class="flex items-center space-x-3">
              <img v-if="authStore.user.avatar" :src="authStore.user.avatar" @error="authStore.user.avatar = null" class="w-8 h-8 rounded-full border border-gray-200 shadow-sm" :title="authStore.user.name" />
              <div v-else class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[11px] font-black shadow-sm" :title="authStore.user.name">
                {{ userInitials }}
              </div>
              <span class="text-sm font-bold text-gray-700 hidden sm:block">
                {{ authStore.user.name }}
              </span>
            </div>
            <button @click="logout" class="text-sm font-medium text-gray-500 hover:text-red-600 transition-colors">
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot></slot>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const userInitials = computed(() => {
  if (!authStore.user || !authStore.user.name) return 'U'
  return authStore.user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
})

const logout = () => {
  authStore.logout()
  window.location.href = '/login'
}
</script>
