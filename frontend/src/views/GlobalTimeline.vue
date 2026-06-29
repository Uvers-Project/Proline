<template>
  <div class="flex flex-col h-screen bg-white relative overflow-hidden">
    
    <!-- Header -->
    <div class="flex-none p-6 border-b border-gray-100 flex justify-between items-center bg-white z-30 shadow-sm relative">
       <div class="flex items-center space-x-6">
          <button @click="router.push('/dashboard')" class="text-gray-500 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 p-2.5 rounded-xl transition-all" title="Back to Dashboard">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <div>
             <h2 class="text-2xl font-black tracking-tight" style="color: #0672B9;">Global Workforce Timeline</h2>
             <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">Cross-Project Resource Allocation</p>
          </div>
       </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex-1 flex justify-center items-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2" style="border-color: #0672B9;"></div>
    </div>

    <!-- Matrix Grid (Scrollable) -->
    <div v-else class="flex-1 overflow-auto bg-gray-50/20">
       <div v-if="matrixData.mMonths.length === 0" class="text-center py-20">
         <p class="text-gray-500 font-medium">No active tasks available to build the global timeline.</p>
       </div>
       
       <table v-else class="w-full border-separate min-w-max" style="border-spacing: 0;">
          <thead>
             <!-- Year / Header row (if we spanned years, we'd group here, but we are fixing to current year) -->
             <tr>
                <th class="sticky left-0 z-30 bg-white border-b border-r border-gray-200 w-72 shadow-[1px_0_0_0_#e5e7eb]"></th>
                <th :colspan="matrixData.mMonths.length" class="border-b border-gray-300 bg-white p-3 text-center border-r border-gray-300">
                   <span class="text-sm font-black text-gray-800 uppercase tracking-widest">{{ new Date().getFullYear() }}</span>
                </th>
             </tr>
             <!-- Months Header -->
             <tr>
                <th class="w-72 text-left p-4 text-[10px] font-black tracking-widest text-gray-400 uppercase border-b-2 border-r border-gray-300 bg-white sticky left-0 z-30">
                   <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Team Members</span>
                </th>
                <th v-for="month in matrixData.mMonths" :key="month.id" class="w-48 p-4 text-[10px] font-black tracking-widest text-gray-700 uppercase border-b-2 text-center bg-gray-50/80 border-r border-gray-300">
                   <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ month.label }}</span>
                </th>
             </tr>
          </thead>
          <tbody>
             <tr v-for="user in matrixData.users" :key="user.id" class="group border-b border-gray-300">
                <!-- Sticky Left User Row -->
                <td class="w-72 px-5 py-4 align-middle border-b border-r border-gray-300 bg-white sticky left-0 z-30 transition-colors group-hover:bg-gray-50 shadow-[1px_0_0_0_#e5e7eb]">
                   <div class="flex items-center space-x-3">
                      <div>
                        <span class="text-sm font-bold text-gray-800 block">{{ user.name }}</span>
                        <div class="text-[10px] text-gray-500 font-medium uppercase tracking-widest mt-0.5">
                           {{ getProjects(user.id).length }} active {{ getProjects(user.id).length === 1 ? 'project' : 'projects' }}
                        </div>
                      </div>
                   </div>
                </td>
                
                <!-- Spanning Month Cells Container -->
                <td :colspan="matrixData.mMonths.length" class="p-0 relative align-top border-b border-gray-300">
                   <!-- Background grid lines -->
                   <div class="absolute inset-0 grid z-0" :style="{ gridTemplateColumns: `repeat(${matrixData.mMonths.length}, minmax(12rem, 1fr))` }">
                      <div v-for="month in matrixData.mMonths" :key="'bg-'+month.id" class="h-full border-r border-gray-200/60 transition-colors hover:bg-gray-50/30">
                      </div>
                   </div>
                   
                   <!-- Foreground Projects -->
                   <div class="relative grid gap-y-2 py-3 z-10" :style="{ gridTemplateColumns: `repeat(${matrixData.mMonths.length}, minmax(12rem, 1fr))` }">
                      <div v-for="proj in getProjects(user.id)" :key="proj.projectId"
                           @click="router.push(`/projects/${proj.projectId}/planning`)"
                           :style="{ gridColumn: `${proj.startIndex + 1} / span ${proj.span}`, backgroundColor: getProjectColor(proj.projectId).bg, color: getProjectColor(proj.projectId).text, border: `1px solid ${getProjectColor(proj.projectId).border}` }"
                           class="px-3 py-2 mx-2 rounded-lg flex items-center space-x-2.5 cursor-pointer transition-colors duration-200 hover:brightness-95 relative z-20 shadow-sm"
                           :title="proj.projectName">
                         
                         <!-- Content -->
                         <div class="flex-1 min-w-0 flex flex-col justify-center">
                            <p class="text-xs font-bold truncate leading-tight">
                               {{ proj.projectName }}
                            </p>
                            <p class="text-[9px] font-black uppercase tracking-widest opacity-80 truncate leading-tight mt-0.5">
                               {{ proj.tasksCount }} assigned {{ proj.tasksCount === 1 ? 'task' : 'tasks' }}
                            </p>
                         </div>
                      </div>
                      
                      <!-- Empty placeholder for spacing if no projects -->
                      <div v-if="getProjects(user.id).length === 0" class="h-10 opacity-0" style="grid-column: 1 / -1;"></div>
                   </div>
                </td>
             </tr>
          </tbody>
       </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useProjectStore } from '../stores/project'
import axios from '../axios'

const router = useRouter()
const authStore = useAuthStore()
const projectStore = useProjectStore()

const loading = ref(true)
const allGlobalTasks = ref([])

onMounted(async () => {
  loading.value = true
  
  if (!authStore.allUsers || authStore.allUsers.length === 0) {
    await authStore.fetchAllUsers()
  }
  
  if (!projectStore.projects || projectStore.projects.length === 0) {
    await projectStore.fetchProjects()
  }
  
  const taskPromises = projectStore.projects.map(async (project) => {
    try {
      const response = await axios.get(`/api/v1/projects/${project.id}/tasks`)
      const tasks = response.data || []
      
      return tasks.filter(t => !t.parent_id).map(t => ({
        ...t,
        project: {
          id: project.id,
          name: project.name
        }
      }))
    } catch (e) {
      console.error(`Failed to fetch tasks for project ${project.id}`, e)
      return []
    }
  })
  
  const results = await Promise.all(taskPromises)
  allGlobalTasks.value = results.flat()
  
  loading.value = false
})

const matrixData = computed(() => {
  const tasks = allGlobalTasks.value;
  
  // 1. Generate months array from current month to end of the year
  const mMonths = [];
  const currentDate = new Date();
  const currentYear = currentDate.getFullYear();
  const currentMonth = currentDate.getMonth(); // 0-indexed
  
  for (let m = currentMonth; m <= 11; m++) {
    const d = new Date(currentYear, m, 1);
    mMonths.push({
      id: `${currentYear}-${m+1}`,
      monthNum: m,
      label: d.toLocaleString('default', { month: 'long' })
    });
  }

  // Helper to get month index relative to our mMonths array
  // If the date is before current month, we clamp it to index 0.
  // If it's after the end of the year, we clamp it to the last index.
  const getMonthIndex = (dateStr) => {
    if (!dateStr) return null;
    const d = new Date(dateStr);
    const y = d.getFullYear();
    const m = d.getMonth();
    
    if (y < currentYear) return 0;
    if (y > currentYear) return mMonths.length - 1;
    
    if (m < currentMonth) return 0;
    return m - currentMonth; // The relative index within our array
  }

  const grid = {}
  const userMap = new Map()
  
  // Temporary structure to group tasks by User -> Project
  const userProjectTasks = {};

  tasks.forEach(task => {
    if (task.status === 'Backlog' || task.status === 'Cancelled') return;
    
    const assigneeId = task.assigned_to || 0;
    
    if (!userMap.has(assigneeId)) {
       let u = authStore.allUsers.find(x => x.id === assigneeId)
       if (!u && assigneeId === 0) {
         u = { id: 0, name: 'Unassigned', avatar: null }
       }
       if (u) userMap.set(u.id, u)
    }
    
    if (!userProjectTasks[assigneeId]) {
      userProjectTasks[assigneeId] = {};
    }
    
    const projId = task.project.id;
    if (!userProjectTasks[assigneeId][projId]) {
      userProjectTasks[assigneeId][projId] = {
        projectId: projId,
        projectName: task.project.name,
        minDate: null,
        maxDate: null,
        tasksCount: 0
      };
    }
    
    const pGroup = userProjectTasks[assigneeId][projId];
    pGroup.tasksCount++;
    
    if (task.start_date) {
      const d = new Date(task.start_date);
      if (!pGroup.minDate || d < pGroup.minDate) pGroup.minDate = d;
    }
    if (task.end_date) {
      const d = new Date(task.end_date);
      if (!pGroup.maxDate || d > pGroup.maxDate) pGroup.maxDate = d;
    }
  })
  
  // Map those user/project groupings into grid bars
  Object.keys(userProjectTasks).forEach(userId => {
    grid[userId] = [];
    const projects = userProjectTasks[userId];
    
    Object.values(projects).forEach(proj => {
      // If a task lacks dates, we fallback to current month
      const startIdxRaw = getMonthIndex(proj.minDate) ?? 0;
      const endIdxRaw = getMonthIndex(proj.maxDate) ?? 0;
      
      let startIndex = startIdxRaw;
      let endIndex = endIdxRaw;
      
      if (startIndex > endIndex) {
        startIndex = endIdxRaw;
        endIndex = startIdxRaw;
      }
      
      grid[userId].push({
        ...proj,
        startIndex,
        span: endIndex - startIndex + 1,
      })
    })
    
    // Sort bars by start date for neat stacking
    grid[userId].sort((a, b) => a.startIndex - b.startIndex);
  })
  
  const users = Array.from(userMap.values()).sort((a, b) => {
     if (a.id === 0) return 1; // Unassigned at bottom
     if (b.id === 0) return -1;
     return a.name.localeCompare(b.name);
  })

  return { mMonths, users, grid }
})

const getProjects = (userId) => {
  return matrixData.value.grid[userId] || []
}

// Generate a visually pleasing unique color palette for projects
const projectColors = [
  { bg: '#E0F2FE', text: '#0369A1', border: '#BAE6FD' }, // Sky
  { bg: '#FCE7F3', text: '#BE185D', border: '#FBCFE8' }, // Pink
  { bg: '#FEF3C7', text: '#B45309', border: '#FDE68A' }, // Amber
  { bg: '#DCFCE7', text: '#15803D', border: '#BBF7D0' }, // Green
  { bg: '#F3E8FF', text: '#7E22CE', border: '#E9D5FF' }, // Purple
  { bg: '#FFEDD5', text: '#C2410C', border: '#FED7AA' }, // Orange
  { bg: '#E0E7FF', text: '#4338CA', border: '#C7D2FE' }, // Indigo
  { bg: '#FFE4E6', text: '#BE123C', border: '#FECDD3' }, // Rose
  { bg: '#CCFBF1', text: '#0F766E', border: '#99F6E4' }, // Teal
  { bg: '#F1F5F9', text: '#334155', border: '#E2E8F0' }, // Slate
]

const getProjectColor = (projectId) => {
  const index = typeof projectId === 'number' ? projectId : String(projectId).charCodeAt(0) || 0;
  return projectColors[index % projectColors.length];
}
</script>
