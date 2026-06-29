<template>
  <AppLayout>
    <div v-if="projectStore.loading || !projectStore.currentProject" class="flex justify-center py-32">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>
    
    <div v-else class="space-y-6">
      <button @click="router.push(`/projects/${route.params.id}/planning`)" class="text-gray-500 hover:text-gray-900 font-medium flex items-center transition-colors group w-max">
        <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Kanban Board
      </button>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 min-h-[400px]">
        <WeeklyTab :projectId="route.params.id" />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProjectStore } from '../stores/project'
import AppLayout from '../components/AppLayout.vue'
import WeeklyTab from '../components/WeeklyTab.vue'

const route = useRoute()
const router = useRouter()
const projectStore = useProjectStore()

onMounted(() => {
  if (!projectStore.currentProject || projectStore.currentProject.id != route.params.id) {
    projectStore.fetchProject(route.params.id)
  }
})
</script>
