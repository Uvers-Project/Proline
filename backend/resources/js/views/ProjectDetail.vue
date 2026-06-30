<template>
  <div class="flex flex-col h-screen bg-gray-50 relative overflow-hidden">
    <!-- Header -->
    <div class="flex-none px-8 py-6 border-b border-gray-200 bg-white z-30 shadow-sm relative">
      <div class="flex justify-between items-start">
        <div class="flex items-start space-x-4">
          <button @click="router.push('/dashboard')" class="mt-1 text-gray-500 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 p-2.5 rounded-xl transition-all" title="Back to Dashboard">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <div v-if="projectStore.currentProject">
             <div class="flex items-center gap-3">
               <h2 class="text-3xl font-black text-gray-900">{{ projectStore.currentProject.name }}</h2>
             </div>
             <p class="text-sm font-medium text-gray-500 mt-2 max-w-3xl">{{ projectStore.currentProject.description || 'No description provided.' }}</p>
             
             <!-- View Switcher -->
             <div class="flex bg-gray-100/80 p-1 rounded-xl mt-4 w-max border border-gray-200/60">
               <button @click="router.push(`/projects/${route.params.id}/planning`)" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-900 rounded-lg hover:bg-white/60 transition-colors flex items-center">
                 <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9V5a2 2 0 002-2h2a2 2 0 012 2"></path></svg>
                 Kanban Board
               </button>
               <button class="px-4 py-2 text-sm font-bold bg-white text-blue-700 rounded-lg shadow-sm border border-gray-200/50 flex items-center">
                 <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                 Weekly Report
               </button>
               <button @click="router.push(`/projects/${route.params.id}/timeline`)" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-900 rounded-lg hover:bg-white/60 transition-colors flex items-center">
                 <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                 Timeline
               </button>
             </div>
          </div>
          <div v-else class="py-2">
            <div class="animate-pulse flex space-x-4">
              <div class="h-8 bg-gray-200 rounded w-48"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Content Area -->
    <div class="flex-1 overflow-auto p-6">
      <div v-if="projectStore.loading || !projectStore.currentProject" class="flex justify-center py-32">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
      
      <div v-else class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 min-h-[400px]">
          <WeeklyTab :projectId="route.params.id" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProjectStore } from '../stores/project'
import { useTaskStore } from '../stores/task'
import WeeklyTab from '../components/WeeklyTab.vue'

const route = useRoute()
const router = useRouter()
const projectStore = useProjectStore()
const taskStore = useTaskStore()

onMounted(() => {
  if (!projectStore.currentProject || projectStore.currentProject.id != route.params.id) {
    projectStore.fetchProject(route.params.id)
  }
})


</script>
