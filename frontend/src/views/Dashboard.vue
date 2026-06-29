<template>
  <AppLayout>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
      <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Your Projects</h1>
        <p class="text-gray-500 mt-1.5 text-lg">Manage and track all your active timelines.</p>
      </div>
      <div class="flex items-center space-x-3">
        <button @click="router.push('/global-timeline')" class="bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 px-6 py-2.5 rounded-xl font-semibold shadow-sm transition-all transform hover:scale-105 active:scale-95 flex items-center">
          <svg class="w-5 h-5 mr-2 text-[#0672B9]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path></svg>
          Timeline
        </button>
        <button @click="showNewProjectModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold shadow-md shadow-blue-600/20 transition-all transform hover:scale-105 active:scale-95 flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          New Project
        </button>
      </div>
    </div>

    <div v-if="projectStore.loading" class="flex justify-center py-32">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <div v-else-if="projectStore.projects.length === 0" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-16 text-center max-w-2xl mx-auto mt-12">
      <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-6 transform rotate-3">
        <svg class="w-10 h-10 text-blue-500 transform -rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
      </div>
      <h3 class="text-2xl font-bold text-gray-900">No projects yet</h3>
      <p class="text-gray-500 mt-3 max-w-md mx-auto text-lg">Get started by creating your first project to manage timelines, tasks, and team meetings.</p>
      <button @click="showNewProjectModal = true" class="mt-8 bg-gray-900 hover:bg-gray-800 text-white px-8 py-3 rounded-xl shadow-lg transition-colors font-medium text-lg">
        Create Your First Project
      </button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <ProjectCard 
        v-for="project in projectStore.projects" 
        :key="project.id" 
        :project="project" 
        @click="goToProject(project.id)"
        @delete="confirmDelete"
      />
    </div>

    <!-- New Project Modal -->
    <div v-if="showNewProjectModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-bold text-gray-900">Create New Project</h3>
          <button @click="showNewProjectModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <form @submit.prevent="submitProject" class="p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
              <input v-model="newProject.name" required type="text" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="E.g., Q3 Marketing Campaign" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
              <textarea v-model="newProject.description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="Brief details about the project..."></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input v-model="newProject.start_date" required type="date" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input v-model="newProject.end_date" required type="date" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
              </div>
            </div>
          </div>
          
          <div class="mt-8 flex gap-3">
            <button type="button" @click="showNewProjectModal = false" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-medium transition-colors">
              Cancel
            </button>
            <button type="submit" :disabled="isSubmitting" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition-colors disabled:opacity-70 flex justify-center items-center">
              <span v-if="isSubmitting" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span>
              Create Project
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useProjectStore } from '../stores/project'
import { useModal } from '../composables/useModal'
import { useToast } from '../composables/useToast'
import AppLayout from '../components/AppLayout.vue'
import ProjectCard from '../components/ProjectCard.vue'

const router = useRouter()
const projectStore = useProjectStore()
const modal = useModal()
const toast = useToast()

const showNewProjectModal = ref(false)
const isSubmitting = ref(false)

const newProject = ref({
  name: '',
  description: '',
  start_date: new Date().toISOString().split('T')[0],
  end_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0] // +30 days
})

onMounted(() => {
  projectStore.fetchProjects()
})

const goToProject = (id) => {
  router.push(`/projects/${id}/planning`)
}

const submitProject = async () => {
  isSubmitting.value = true
  try {
    const project = await projectStore.createProject(newProject.value)
    showNewProjectModal.value = false
    // Reset form
    newProject.value.name = ''
    newProject.value.description = ''
    goToProject(project.id)
  } catch (error) {
    toast.showToast('Failed to create project', 'Error')
  } finally {
    isSubmitting.value = false
  }
}

const confirmDelete = async (projectId) => {
  const confirmed = await modal.showConfirm('Are you sure you want to delete this project? This action cannot be undone.', 'Delete Project')
  if (confirmed) {
    try {
      await projectStore.deleteProject(projectId)
    } catch (error) {
      if (error.response?.status === 403) {
        toast.showToast('Only the creator of this project can delete it.', 'Permission Denied')
      } else {
        toast.showToast('Failed to delete project.', 'Error')
      }
    }
  }
}
</script>
