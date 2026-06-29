<template>
  <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl hover:shadow-blue-900/5 border border-gray-100 p-6 cursor-pointer transition-all duration-300 hover:-translate-y-1 group">
    <div class="flex justify-end items-start mb-5">
      <div class="flex items-center space-x-2">
        <span class="px-3 py-1 text-xs font-semibold bg-green-50 text-green-600 rounded-full border border-green-100">Active</span>
        <button @click.stop="$emit('delete', project.id)" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete Project">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        </button>
      </div>
    </div>
    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">{{ project.name }}</h3>
    <p class="text-gray-500 text-sm line-clamp-2 mb-6">{{ project.description || 'No description provided.' }}</p>
    
    <div class="flex items-center justify-between text-sm text-gray-500 border-t border-gray-50 pt-4 mt-auto">
      <div class="flex items-center bg-gray-50 px-2.5 py-1 rounded-md">
        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <span class="font-medium">{{ formatDate(project.end_date) }}</span>
      </div>
      <div class="flex items-center space-x-2" :title="`Created by ${project.creator?.name || 'Unknown'}`">
        <span class="text-xs font-medium text-gray-400">by {{ project.creator?.name?.split(' ')[0] || 'Unknown' }}</span>
        <img v-if="project.creator?.avatar" :src="project.creator.avatar" class="w-6 h-6 rounded-full border border-gray-200" />
        <div v-else class="w-6 h-6 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-[10px] font-bold text-indigo-700">
          {{ project.creator?.name ? project.creator.name.charAt(0).toUpperCase() : '?' }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  project: {
    type: Object,
    required: true
  }
})

defineEmits(['delete'])

const formatDate = (dateString) => {
  if (!dateString) return 'No date';
  return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
</script>
