<template>
  <div class="min-h-screen flex items-center justify-center">
    <div class="text-xl text-gray-700">Authenticating...</div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

onMounted(async () => {
  const token = route.query.token
  if (token) {
    authStore.setToken(token)
    await authStore.fetchUser()
    router.push('/dashboard')
  } else {
    router.push('/login')
  }
})
</script>
