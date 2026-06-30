<template>
  <div class="flex flex-col h-screen bg-white relative overflow-hidden">
    
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
               <button @click="router.push(`/projects/${route.params.id}`)" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-900 rounded-lg hover:bg-white/60 transition-colors flex items-center">
                 <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                 Weekly Report
               </button>
               <button class="px-4 py-2 text-sm font-bold bg-white text-blue-700 rounded-lg shadow-sm border border-gray-200/50 flex items-center">
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

        <!-- Right Side Filters -->
        <div class="flex flex-col items-end gap-3 mt-1">
           <div class="flex items-center space-x-5">
              <!-- Timeline Mode Toggle -->
              <div class="flex bg-gray-100 rounded-lg p-0.5 border border-gray-200 mr-2">
                <button @click="timelineMode = 'Planned'" :class="['px-3 py-1.5 text-xs font-bold rounded-md transition-colors', timelineMode === 'Planned' ? 'bg-white text-gray-900 shadow-sm border border-gray-200/50' : 'text-gray-500 hover:text-gray-700']">
                  Planned
                </button>
                <button @click="timelineMode = 'Real'" :class="['px-3 py-1.5 text-xs font-bold rounded-md transition-colors', timelineMode === 'Real' ? 'bg-blue-600 text-white shadow-sm border border-blue-700' : 'text-gray-500 hover:text-gray-700']">
                  Real
                </button>
              </div>

              <!-- Dev Theme Toggle -->
              <button @click="toggleTheme" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 px-3 py-1.5 rounded-lg uppercase tracking-wider hover:bg-indigo-100 mr-2">Theme V{{colorVariant}}</button>
              
              <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mr-3">Team Filter</span>
              <!-- Avatars -->
              <div class="flex -space-x-2">
                 <div v-for="member in teamMembers" :key="member.id" 
                      @click="toggleFilter(member.id)"
                      :class="[
                        'w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold text-white border-2 border-white shadow-sm cursor-pointer transition-all hover:-translate-y-1',
                        activeFilterPic && activeFilterPic !== member.id ? 'opacity-30' : 'opacity-100 ring-2 ring-offset-1 ring-indigo-500'
                      ]"
                      :style="{ backgroundColor: getColor(member.name) }"
                      :title="member.name">
                    {{ getInitials(member.name) }}
                 </div>
                 
                 <!-- Clear Filter -->
                 <button v-if="activeFilterPic" @click="activeFilterPic = null" class="ml-4 text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-lg uppercase tracking-wider hover:bg-gray-200">Clear</button>
              </div>
           </div>
        </div>
      </div>
    </div>

    <!-- Matrix Grid (Scrollable) -->
    <div class="flex-1 overflow-auto bg-gray-50/20">
       <div v-if="matrixData.groupedWeeks.length === 0" class="text-center py-20">
         <p class="text-gray-500 font-medium">No plans or tasks available to build the matrix.</p>
       </div>
       
       <table v-else class="w-full border-separate min-w-max" style="border-spacing: 0;">
          <thead>
             <!-- Months Header -->
             <tr>
                <th class="sticky left-0 z-30 bg-white border-b border-r border-gray-200 w-48 shadow-[1px_0_0_0_#e5e7eb]"></th>
                <!-- Group weeks by planId -->
                <th v-for="(planGroup, index) in matrixData.groupedWeeks" :key="planGroup.planId" :colspan="planGroup.weeks.length" :class="['border-b border-gray-300 bg-white p-3 text-center', index < matrixData.groupedWeeks.length - 1 ? 'border-r-4 border-slate-300' : 'border-r border-gray-300']">
                   <span class="text-sm font-black text-gray-800 uppercase tracking-widest">{{ planGroup.monthName }} {{ planGroup.year }}</span>
                </th>
             </tr>
             <!-- Weeks Header -->
             <tr>
                <th class="w-48 text-left p-4 text-[10px] font-black tracking-widest text-gray-400 uppercase border-b-2 border-r border-gray-300 bg-white sticky left-0 z-30">
                   <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Phases</span>
                </th>
                <th v-for="week in matrixData.mWeeks" :key="week.id" :class="['w-40 p-4 text-[10px] font-black tracking-widest text-gray-700 uppercase border-b-2 text-center bg-gray-50/80', week.isMonthEnd && week !== matrixData.mWeeks[matrixData.mWeeks.length - 1] ? 'border-r-4 border-slate-300' : 'border-r border-gray-300']">
                   <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ week.label }}</span>
                </th>
             </tr>
          </thead>
          <tbody>
             <tr v-for="(phase, index) in matrixData.phases" :key="phase" class="group border-b border-gray-300">
                <!-- Sticky Left Phase Lane -->
                <td class="w-48 px-5 py-4 align-middle border-b border-r border-gray-300 bg-white sticky left-0 z-30 transition-colors group-hover:bg-gray-50 shadow-[1px_0_0_0_#e5e7eb]">
                   <div class="flex items-center justify-start space-x-3">
                      <div v-html="getPhaseIcon(phase)"></div>
                      <span class="text-sm font-black text-gray-800 uppercase tracking-wider">{{ phase }}</span>
                   </div>
                </td>
                
                <!-- Spanning Week Cells Container -->
                <td :colspan="matrixData.mWeeks.length" class="p-0 relative align-top border-b border-gray-300">
                   <!-- Background grid lines to match headers -->
                   <div class="absolute inset-0 grid z-0" :style="{ gridTemplateColumns: `repeat(${matrixData.mWeeks.length}, minmax(10rem, 1fr))` }">
                      <div v-for="(week, wIndex) in matrixData.mWeeks" :key="'bg-'+week.id"
                           :class="['h-full border-r border-gray-200/60 transition-colors hover:bg-gray-50/30', week.isMonthEnd && wIndex !== matrixData.mWeeks.length - 1 ? 'border-r-4 border-slate-300' : '']">
                      </div>
                   </div>
                   
                   <!-- Foreground Tasks -->
                   <div class="relative grid gap-y-2 py-2 z-10" :style="{ gridTemplateColumns: `repeat(${matrixData.mWeeks.length}, minmax(10rem, 1fr))` }">
                      <div v-for="task in getTasks(phase)" :key="task.id"
                           @click="selectTask(task)"
                           :style="{ gridColumn: `${task.startIndex + 1} / span ${task.span}`, ...getPhaseStyle(phase) }"
                           :class="[
                              'px-2.5 py-1.5 mx-1 rounded-md flex items-center space-x-2.5 cursor-pointer transition-colors duration-200 hover:brightness-95 relative z-20',
                              task.uiStatus === 'problem' ? 'ring-2 ring-amber-400' : ''
                           ]">
                         
                         <!-- Done Icon -->
                         <div v-if="task.uiStatus === 'completed'" class="flex-shrink-0 text-current opacity-90 flex items-center">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                         </div>
                         <div v-else class="flex-shrink-0 flex items-center">
                            <div class="w-4 h-4 rounded-full border-2 border-current opacity-30"></div>
                         </div>
                         
                         <!-- Content -->
                         <div class="flex-1 min-w-0 flex flex-col justify-center">
                            <p class="text-xs font-bold truncate leading-tight" style="color: var(--title-color, inherit)">
                               {{ task.title }}
                            </p>
                            <p class="text-[10px] truncate mt-0.5 font-medium leading-tight" style="color: var(--sub-color, inherit)">
                               {{ task.pic.name }}
                            </p>
                         </div>
                         
                         <!-- Problem Icon -->
                         <div v-if="task.uiStatus === 'problem'" class="flex-shrink-0 text-amber-500" v-html="icons.alert"></div>
                      </div>
                      
                      <!-- Empty placeholder for spacing -->
                      <div v-if="getTasks(phase).length === 0" class="h-10 opacity-0" style="grid-column: 1 / -1;"></div>
                   </div>
                </td>
             </tr>
          </tbody>
       </table>
    </div>
    
    <!-- Detail Panel (Side Drawer) -->
    <Transition name="slide-right">
      <div v-if="selectedTask" class="absolute inset-y-0 right-0 w-96 bg-white shadow-[0_0_40px_rgba(0,0,0,0.1)] border-l border-gray-100 z-50 flex flex-col">
         <!-- Header -->
         <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-black text-gray-900">Task Details</h3>
            <button @click="selectedTask = null" class="text-gray-400 hover:text-gray-600 bg-white p-1.5 rounded-lg border border-gray-200 transition-colors">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
         </div>
         
         <div class="p-6 overflow-y-auto flex-1 space-y-6">
            <div>
               <div class="flex items-center space-x-2 mb-3">
                 <span :class="[
                   'text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md border',
                   selectedTask.uiStatus === 'completed' ? 'bg-gray-100 text-gray-500 border-gray-200' :
                   selectedTask.uiStatus === 'problem' ? 'bg-amber-100 text-amber-800 border-amber-200' :
                   'bg-indigo-50 text-indigo-700 border-indigo-200'
                 ]">{{ selectedTask.uiStatus === 'completed' ? 'Completed' : selectedTask.uiStatus === 'problem' ? 'Blocked / Problem' : 'Normal / Progress' }}</span>
               </div>
               <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ selectedTask.title }}</h2>
               <p v-if="selectedTask.description" class="text-sm text-gray-600 mt-3 leading-relaxed">{{ selectedTask.description }}</p>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-4">
               <div class="flex items-center justify-between">
                  <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">PIC</span>
                  <div class="flex items-center space-x-2">
                     <div class="w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-bold text-white shadow-inner"
                          :style="{ backgroundColor: getColor(selectedTask.pic.name) }">
                        {{ getInitials(selectedTask.pic.name) }}
                     </div>
                     <span class="text-sm font-bold text-gray-800">{{ selectedTask.pic.name }}</span>
                  </div>
               </div>
               
               <div class="pt-2 pb-2 border-t border-b border-gray-100 flex flex-col space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Planned Timeline</span>
                    <span class="text-xs font-bold text-gray-900 bg-white border border-gray-200 px-2 py-1 rounded shadow-sm">
                      <span v-if="selectedTask.start_date || selectedTask.end_date">
                        {{ selectedTask.start_date ? selectedTask.start_date.split('T')[0] : '...' }} - {{ selectedTask.end_date ? selectedTask.end_date.split('T')[0] : '...' }}
                      </span>
                      <span v-else>Unscheduled</span>
                    </span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">Real Timeline</span>
                    <span class="text-xs font-bold text-blue-900 bg-blue-50 border border-blue-200 px-2 py-1 rounded shadow-sm">
                      <span v-if="selectedTask.real_start_date || selectedTask.real_end_date">
                        {{ selectedTask.real_start_date ? selectedTask.real_start_date.split('T')[0] : '...' }} - {{ selectedTask.real_end_date ? selectedTask.real_end_date.split('T')[0] : '...' }}
                      </span>
                      <span v-else>Pending</span>
                    </span>
                  </div>
               </div>
               
               <div class="flex items-center justify-between">
                  <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Phase / Lane</span>
                  <span class="text-sm font-bold text-gray-800">{{ selectedTask.category || 'Development' }}</span>
               </div>
            </div>
            
            <div v-if="selectedTask.uiStatus === 'problem'" class="bg-amber-50 rounded-xl p-5 border border-amber-200 shadow-sm">
               <div class="flex items-center space-x-2 text-amber-800 mb-3">
                  <div v-html="icons.alert"></div>
                  <span class="text-xs font-black uppercase tracking-wider">Problem Reported</span>
               </div>
               <p class="text-sm text-amber-900 leading-relaxed font-medium">{{ selectedTask.problemDesc || 'A blocker was reported during the weekly meeting for this task.' }}</p>
            </div>
            
            <div v-if="selectedTask.resolution_notes" class="bg-blue-50 rounded-xl p-5 border border-blue-200 shadow-sm">
               <div class="flex items-center space-x-2 text-blue-800 mb-3">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  <span class="text-xs font-black uppercase tracking-wider">Resolution Notes</span>
               </div>
               <p class="text-sm text-blue-900 leading-relaxed font-medium whitespace-pre-wrap">{{ selectedTask.resolution_notes }}</p>
            </div>
         </div>
      </div>
    </Transition>

    <!-- Backdrop -->
    <Transition name="fade">
      <div v-if="selectedTask" @click="selectedTask = null" class="absolute inset-0 bg-gray-900/10 backdrop-blur-[1px] z-40"></div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTaskStore } from '../stores/task'
import { useProjectStore } from '../stores/project'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const taskStore = useTaskStore()
const projectStore = useProjectStore()
const authStore = useAuthStore()

const selectedTask = ref(null)
const activeFilterPic = ref(null)
const timelineMode = ref('Planned') // 'Planned' or 'Real'

onMounted(async () => {
  const projectId = route.params.id;
  if (!projectStore.currentProject || projectStore.currentProject.id != projectId) {
    await projectStore.fetchProject(projectId)
  }
  // Always fetch tasks for the new route, because the store might hold the previous project's tasks
  await taskStore.fetchProjectTasks(route.params.id)
})



const teamMembers = computed(() => {
  const all = authStore.allUsers || [];
  const memberSet = new Map();
  
  const projectMembers = projectStore.currentProject?.members || [];
  projectMembers.forEach(m => memberSet.set(m.id, m));
  
  const creator = projectStore.currentProject?.creator;
  if (creator) memberSet.set(creator.id, creator);
  
  const allTasks = taskStore.projectTasks || [];
  allTasks.forEach(task => {
    if (task.assigned_to) {
      const u = all.find(x => x.id === task.assigned_to);
      if (u) memberSet.set(u.id, u);
    }
    if (task.subtasks) {
      task.subtasks.forEach(sub => {
         if (sub.assigned_to) {
            const u = all.find(x => x.id === sub.assigned_to);
            if (u) memberSet.set(u.id, u);
         }
      })
    }
  });
  
  return Array.from(memberSet.values());
})

const getProblems = (meeting) => meeting.progress?.filter(p => p.type === 'problem') || []

const matrixData = computed(() => {
  const allTasks = taskStore.projectTasks || [];
  const project = projectStore.currentProject;
  
  // Use project dates for timeline boundaries, fallback to current month if missing
  let minDate = new Date();
  let maxDate = new Date();
  
  if (project && project.start_date && project.end_date) {
    minDate = new Date(project.start_date);
    maxDate = new Date(project.end_date);
  } else {
    let hasDates = false;
    allTasks.forEach(t => {
      const getTargetStart = (task) => timelineMode.value === 'Real' ? (task.real_start_date || task.start_date) : task.start_date;
      const getTargetEnd = (task) => timelineMode.value === 'Real' ? (task.real_end_date || task.end_date) : task.end_date;

      const tStart = getTargetStart(t);
      const tEnd = getTargetEnd(t);

      if (tStart) {
        const d = new Date(tStart);
        if (!hasDates || d < minDate) minDate = d;
        hasDates = true;
      }
      if (tEnd) {
        const d = new Date(tEnd);
        if (!hasDates || d > maxDate) maxDate = d;
        hasDates = true;
      }
    });

    if (!hasDates) {
      maxDate.setMonth(maxDate.getMonth() + 1);
    }
  }

  // Align boundaries to the full month so weeks are generated cleanly
  minDate = new Date(minDate.getFullYear(), minDate.getMonth(), 1);
  maxDate = new Date(maxDate.getFullYear(), maxDate.getMonth() + 1, 0); // Last day of max month

  const mWeeks = [];
  const groupedWeeks = [];
  
  let curr = new Date(minDate);
  curr.setDate(1);
  
  while (curr <= maxDate) {
    const year = curr.getFullYear();
    const month = curr.getMonth() + 1;
    const monthName = curr.toLocaleString('default', { month: 'long' });
    
    const planGroup = {
      planId: `${year}-${month}`,
      monthName,
      year,
      weeks: []
    }
    
    for (let i = 1; i <= 4; i++) {
      const wObj = {
        id: `${year}-${month}-W${i}`,
        planId: planGroup.planId,
        weekNum: i,
        label: `Week ${i}`,
        isMonthEnd: i === 4
      }
      mWeeks.push(wObj)
      planGroup.weeks.push(wObj)
    }
    groupedWeeks.push(planGroup)
    
    curr.setMonth(curr.getMonth() + 1);
  }

  // Extract phases
  const pSet = new Set(allTasks.map(t => t.category || 'Development'))
  const phaseOrder = ['Research', 'Design', 'Development', 'Testing', 'Review', 'Release']
  const phases = Array.from(pSet).sort((a, b) => {
    const idxA = phaseOrder.indexOf(a)
    const idxB = phaseOrder.indexOf(b)
    if (idxA === -1 && idxB === -1) return a.localeCompare(b)
    if (idxA === -1) return 1
    if (idxB === -1) return -1
    return idxA - idxB
  })
  if (phases.length === 0) phases.push('Development')

  // Map tasks
  const grid = {}
  phases.forEach(ph => {
    grid[ph] = []
  })

  // Helper to get week id from date
  const getWeekId = (dateStr) => {
    if (!dateStr) return null;
    const d = new Date(dateStr);
    const y = d.getFullYear();
    const m = d.getMonth() + 1;
    const date = d.getDate();
    // Rough week of month 1-4
    const w = Math.min(4, Math.ceil(date / 7));
    return `${y}-${m}-W${w}`;
  }

  const getWeekIndex = (weekId) => mWeeks.findIndex(w => w.id === weekId);

  allTasks.forEach(task => {
    if (task.parent_id) return; // Only show top level tasks on timeline
    
    let currentStatus = task.status || 'Backlog';
    let isBlocked = currentStatus === 'Blocked';
    
    if (currentStatus !== 'Cancelled' && task.subtasks && task.subtasks.length > 0) {
      const hasBlockedSubtask = task.subtasks.some(s => s.status === 'Blocked');
      const allDone = task.subtasks.every(s => s.status === 'Done');
      const allBacklog = task.subtasks.every(s => !s.status || s.status === 'Backlog');
      
      isBlocked = isBlocked || hasBlockedSubtask;
      
      if (allDone) {
        currentStatus = 'Done';
      } else if (!allBacklog) {
        currentStatus = 'In Progress';
      } else {
        currentStatus = 'Backlog';
      }
    } else {
      if (currentStatus === 'Blocked') {
        currentStatus = 'In Progress';
      }
    }
    
    if (currentStatus === 'Backlog') return; // Hide backlog tasks from timeline
    
    const phase = task.category || 'Development'
    
    const getTargetStart = (task) => timelineMode.value === 'Real' ? (task.real_start_date || task.start_date) : task.start_date;
    const getTargetEnd = (task) => timelineMode.value === 'Real' ? (task.real_end_date || task.end_date) : task.end_date;

    const startWeekId = getWeekId(getTargetStart(task));
    const endWeekId = getWeekId(getTargetEnd(task));
    
    let startIndex = getWeekIndex(startWeekId);
    let endIndex = getWeekIndex(endWeekId);
    
    // If task has no dates, skip or place in current week
    if (startIndex === -1 && endIndex === -1) return;
    if (startIndex === -1) startIndex = endIndex;
    if (endIndex === -1) endIndex = startIndex;
    
    if (startIndex > endIndex) {
      const temp = startIndex;
      startIndex = endIndex;
      endIndex = temp;
    }

    let uiStatus = 'normal'; // visually represents 'In Progress' for intermediate weeks
    if (currentStatus === 'Done') uiStatus = 'completed'; // Make 'Done' tasks green across ALL weeks they span
    if (isBlocked) uiStatus = 'problem';

    const pic = authStore.allUsers.find(member => member.id === task.assigned_to) || { name: 'Unassigned', id: 0 };
    
    const enhancedTask = {
      ...task,
      uiStatus,
      problemDesc: isBlocked ? 'Task or one of its subtasks is blocked.' : '',
      pic,
      startIndex,
      span: endIndex - startIndex + 1,
      weekLabel: startIndex === endIndex ? mWeeks[startIndex].label : `${mWeeks[startIndex].label} - ${mWeeks[endIndex].label}`
    };
    
    grid[phase].push(enhancedTask);
  });

  return { mWeeks, groupedWeeks, phases, grid }
})

const getTasks = (phase) => {
  let tasks = matrixData.value.grid[phase] || []
  if (activeFilterPic.value) {
    tasks = tasks.filter(t => t.pic.id === activeFilterPic.value)
  }
  return tasks
}

const toggleFilter = (id) => {
  if (activeFilterPic.value === id) activeFilterPic.value = null
  else activeFilterPic.value = id
}

// Helpers
const colors = ['#6366f1', '#ec4899', '#14b8a6', '#f59e0b', '#8b5cf6', '#ef4444', '#3b82f6', '#10b981']
const getColor = (name) => {
  if (!name || name === 'Unassigned') return '#9ca3af'
  let sum = 0
  for (let i = 0; i < name.length; i++) sum += name.charCodeAt(i)
  return colors[sum % colors.length]
}

const getInitials = (name) => {
  if (!name || name === 'Unassigned') return '?'
  return name.split(' ').map(n=>n[0]).join('').substring(0,2).toUpperCase()
}

const selectTask = (task) => {
  selectedTask.value = task
}

const icons = {
  alert: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`,
  code: `<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>`,
  palette: `<svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c1.5 0 2.5-1 2.5-2.5 0-.5-.2-1-.6-1.4-.4-.4-.6-.9-.6-1.5 0-1.1.9-2 2-2h1.6c2.5 0 4.5-2 4.5-4.5C22 7.5 17.5 2 12 2z"/></svg>`,
  check: `<svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>`,
  magnifier: `<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>`,
  ab: `<svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><text x="12" y="16" font-size="12" font-weight="bold" text-anchor="middle" fill="currentColor" font-family="sans-serif">A/B</text></svg>`,
  rocket: `<svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 13a8 8 0 0 1 7 7a6 6 0 0 0 3 -5a9 9 0 0 0 6 -8a3 3 0 0 0 -3 -3a9 9 0 0 0 -8 6a6 6 0 0 0 -5 3"/><path d="M7 14a6 6 0 0 0 -3 6a6 6 0 0 0 6 -3"/><circle cx="15" cy="9" r="1"/></svg>`
}

const getPhaseIcon = (phase) => {
  const p = phase.toLowerCase()
  if (p.includes('research')) return icons.magnifier
  if (p.includes('design')) return icons.palette
  if (p.includes('develop')) return icons.code
  if (p.includes('test') || p.includes('qa')) return icons.ab
  if (p.includes('review')) return icons.check
  if (p.includes('release') || p.includes('deploy')) return icons.rocket
  return icons.code
}

const colorVariant = ref(authStore.user?.theme_preference || 1)

watch(() => authStore.user?.theme_preference, (newVal) => {
  if (newVal) colorVariant.value = newVal
}, { immediate: true })

const toggleTheme = () => {
  const newVariant = colorVariant.value === 1 ? 2 : 1
  colorVariant.value = newVariant
  authStore.updateTheme(newVariant)
}

const colorsV1 = {
  research: { bg: '#FFECA2', title: '#796215', sub: '#695200' },
  design: { bg: '#FFBFCC', title: '#8D2C3F', sub: '#461821' },
  development: { bg: '#B3E3F7', title: '#376180', sub: '#275872' },
  testing: { bg: '#F2D2FD', title: '#6A507F', sub: '#6C4D7B' },
  review: { bg: '#C3EDBC', title: '#1F542A', sub: '#5F8D62' },
  release: { bg: '#FFC6BF', title: '#60221E', sub: '#7A3E38' }
}

const colorsV2 = {
  research: '#FFCB0A',
  design: '#FF42A1',
  development: '#00C7FB',
  testing: '#8225FF',
  review: '#2FC759',
  release: '#FD3C30'
}

const getPhaseStyle = (phase) => {
  const p = (phase || '').toLowerCase()
  let phaseKey = 'development'
  if (p.includes('research')) phaseKey = 'research'
  else if (p.includes('design')) phaseKey = 'design'
  else if (p.includes('develop')) phaseKey = 'development'
  else if (p.includes('test') || p.includes('qa')) phaseKey = 'testing'
  else if (p.includes('review')) phaseKey = 'review'
  else if (p.includes('release') || p.includes('deploy')) phaseKey = 'release'

  if (colorVariant.value === 1) {
    const pal = colorsV1[phaseKey]
    return {
      backgroundColor: pal.bg,
      color: pal.title,
      '--title-color': pal.title,
      '--sub-color': pal.sub
    }
  } else {
    const bg = colorsV2[phaseKey]
    // Use dark text for lighter vibrant backgrounds to ensure readability
    const isLightBg = bg === colorsV2.research || bg === colorsV2.development || bg === colorsV2.review
    return {
      backgroundColor: bg,
      color: isLightBg ? '#000000' : '#ffffff',
      '--title-color': isLightBg ? '#000000' : '#ffffff',
      '--sub-color': isLightBg ? '#4b5563' : '#e5e7eb'
    }
  }
}
</script>

<style scoped>
.slide-right-enter-active, .slide-right-leave-active {
  transition: transform 0.3s ease;
}
.slide-right-enter-from, .slide-right-leave-to {
  transform: translateX(100%);
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
