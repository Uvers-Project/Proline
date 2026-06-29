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
               <button class="px-4 py-2 text-sm font-bold bg-white text-blue-700 rounded-lg shadow-sm border border-gray-200/50 flex items-center">
                 <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9V5a2 2 0 002-2h2a2 2 0 012 2"></path></svg>
                 Kanban Board
               </button>
               <button @click="router.push(`/projects/${route.params.id}`)" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-900 rounded-lg hover:bg-white/60 transition-colors flex items-center">
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
        <div class="flex flex-col items-end gap-3">
          <div class="flex items-center gap-3">
            <button @click="openEditModal" class="px-4 py-2.5 bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl font-bold shadow-sm transition-colors flex items-center text-sm">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
              Edit Project
            </button>
            <button @click="showNewTaskModal = true" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-sm transition-colors flex items-center text-sm">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
              Add Task
            </button>
          </div>
          <div v-if="projectStore.currentProject" class="flex items-center space-x-2 text-sm bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
             <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Period:</span>
             <svg class="w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
             <span class="font-bold text-gray-700 text-xs">{{ formatDateShort(projectStore.currentProject.start_date) }}</span>
             <span class="text-gray-300 font-black text-xs">→</span>
             <span class="font-bold text-gray-700 text-xs">{{ formatDateShort(projectStore.currentProject.end_date) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex-1 overflow-hidden p-6 flex flex-col">
      <div class="flex-1 overflow-x-auto overflow-y-hidden">
        <div class="flex items-start gap-6 h-full min-w-max">
        
        <!-- Column Iterator -->
        <div v-for="col in columns" :key="col.id" :class="`w-80 flex flex-col rounded-3xl border shadow-sm overflow-hidden max-h-full ${col.bgClass} ${col.borderClass}`">
          <div :class="`px-5 py-4 border-b bg-white/50 backdrop-blur-sm flex justify-between items-center shrink-0 ${col.headerBorderClass}`">
            <div class="flex items-center space-x-2">
              <div :class="`w-2.5 h-2.5 rounded-full ${col.dotClass}`"></div>
              <h3 :class="`font-black tracking-tight ${col.textClass}`">{{ col.label }}</h3>
            </div>
            <span :class="`text-xs font-bold bg-white border px-2 py-0.5 rounded-full ${col.countClass}`">{{ kanbanColumns[col.id]?.length || 0 }}</span>
          </div>
          <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <div v-for="t in kanbanColumns[col.id]" :key="t.id" @click="openTaskDetail(t)"
              class="rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all cursor-pointer h-max overflow-hidden bg-white"
              :class="t._isBlocked ? '!border-red-200' : ''"
              :style="{ borderLeftWidth: '3px', borderLeftColor: t._isBlocked ? '#F87171' : (categoryColors[t.category?.toLowerCase()] || categoryColors['development']).title + '99' }"
            >
              <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                  <span
                    class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md border"
                    :style="{
                      backgroundColor: (categoryColors[t.category?.toLowerCase()] || categoryColors['development']).bg + '99',
                      color: (categoryColors[t.category?.toLowerCase()] || categoryColors['development']).title,
                      borderColor: (categoryColors[t.category?.toLowerCase()] || categoryColors['development']).title + '33'
                    }"
                  >
                    {{ t.category || 'Development' }}
                  </span>
                  <div class="flex items-center space-x-1">
                     <div v-if="t._isBlocked" class="bg-red-100 text-red-600 p-1 rounded flex items-center justify-center mr-1 shadow-sm" title="Task or Subtask is Blocked">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                     </div>
                     <button v-if="t.status === 'Cancelled'" @click.stop="restoreTask(t)" class="text-gray-400 hover:text-green-600 transition-colors p-1 rounded-full hover:bg-white/60" title="Restore Task">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                     </button>
                     <button @click.stop="confirmDeleteTask(t)" class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-white/60" :title="t.status === 'Cancelled' ? 'Delete Permanently' : 'Cancel Task'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                     </button>
                  </div>
                </div>
                <!-- Title: always dark, always readable -->
                <h4 class="font-bold text-sm text-gray-800 leading-snug">{{ t.title }}</h4>
                
                <!-- Date & subtasks -->  
                <div class="mt-2 text-[10px] text-gray-400 font-medium flex items-center justify-between">
                  <span v-if="t.start_date || t.end_date" class="flex items-center gap-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    {{ formatDateShort(t.start_date) }} – {{ formatDateShort(t.end_date) }}
                  </span>
                  <span v-else>Unscheduled</span>
                  <span v-if="t.subtasks && t.subtasks.length > 0" class="flex items-center gap-1 bg-gray-50 border border-gray-100 px-1.5 py-0.5 rounded-md">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    {{ t.subtasks.filter(s => s.status === 'Done').length }}/{{ t.subtasks.length }}
                  </span>
                </div>

                <!-- Bottom: priority + status pill + avatar -->
                <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between">
                  <!-- Priority -->
                  <span class="text-[11px] font-bold" :class="priorityTextClass(t.priority)">{{ t.priority || 'Medium' }}</span>

                  <!-- Avatar -->
                  <div v-if="t.assigned_to"
                    class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-[9px] font-bold text-gray-500 shadow-sm cursor-help"
                    :title="getUserName(t.assigned_to)">{{ getUserInitials(t.assigned_to) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
    
    <!-- Footer -->
    <div class="flex-none py-3 px-8 text-center border-t border-gray-200 text-xs font-bold text-gray-400 bg-white shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.02)] relative z-20">
       Project created by <span class="text-gray-600">{{ projectStore.currentProject?.creator?.name || 'Unknown' }}</span>
    </div>
    
  </div>

    <!-- New Task Modal -->
    <div v-if="showNewTaskModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 animate-in zoom-in-95 duration-200 flex flex-col h-[600px] max-h-[85vh]">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-4 shrink-0">
          <h3 class="text-xl font-bold text-gray-900">Add New Task</h3>
          <button @click="showNewTaskModal = false" class="text-gray-400 hover:text-gray-600 bg-white p-1.5 rounded-lg border border-gray-200 transition-colors">
             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="space-y-4 overflow-y-auto flex-1 -m-1 p-1 pr-2">
          <div>
             <label class="block text-sm font-medium text-gray-700 mb-1">Task Title</label>
             <input v-model="newTask.title" placeholder="What needs to be done?" :class="{'border-red-400 ring-2 ring-red-400/20': formErrors.title}" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-colors" />
             <p v-if="formErrors.title" class="text-xs text-red-500 mt-1 font-medium">{{ formErrors.title }}</p>
          </div>
          <div>
             <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
             <textarea v-model="newTask.description" placeholder="Add some details..." rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-colors"></textarea>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
               <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
               <input type="date" v-model="newTask.start_date" :class="{'border-red-400 ring-2 ring-red-400/20': formErrors.start_date}" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-colors" />
               <p v-if="formErrors.start_date" class="text-xs text-red-500 mt-1 font-medium">{{ formErrors.start_date }}</p>
            </div>
            <div>
               <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
               <input type="date" v-model="newTask.end_date" :class="{'border-red-400 ring-2 ring-red-400/20': formErrors.end_date}" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-colors" />
               <p v-if="formErrors.end_date" class="text-xs text-red-500 mt-1 font-medium">{{ formErrors.end_date }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
               <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
               <div class="relative">
                 <select v-model="newTask.priority" class="appearance-none w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                 </select>
                 <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                 </div>
               </div>
            </div>
            <div>
               <label class="block text-sm font-medium text-gray-700 mb-1">Category (Phase)</label>
               <div class="relative">
                 <select v-model="newTask.category" class="appearance-none w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="Research">Research</option>
                    <option value="Design">Design</option>
                    <option value="Development">Development</option>
                    <option value="Testing">Testing</option>
                    <option value="Review">Review</option>
                    <option value="Release">Release</option>
                 </select>
                 <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                 </div>
               </div>
            </div>
          </div>
          <div>
             <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
             <div class="relative">
               <select v-model="newTask.assigned_to" class="appearance-none w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                  <option :value="null">Unassigned</option>
                  <option v-for="user in authStore.allUsers" :key="user.id" :value="user.id">{{ user.name }}</option>
               </select>
               <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
               </div>
             </div>
          </div>
          
          <!-- Draft Subtasks Section -->
          <div class="border-t border-gray-100 pt-4 mt-2">
             <div class="flex justify-between items-center mb-3">
                <label class="block text-sm font-medium text-gray-700">Subtasks</label>
                <button @click="openSubtaskModal('new_task')" class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                  + Add Subtask
                </button>
             </div>
             
             <div v-if="newTask.subtasks && newTask.subtasks.length > 0" class="space-y-2 mb-3">
                <div v-for="(sub, idx) in newTask.subtasks" :key="idx" class="flex items-center justify-between bg-gray-50 border border-gray-200 px-3 py-2 rounded-xl">
                   <div class="flex flex-col flex-1">
                     <span class="text-sm font-medium text-gray-700">{{ sub.title }}</span>
                     <span v-if="sub.description" class="text-xs text-gray-500 mt-0.5">{{ sub.description }}</span>
                   </div>
                   <button @click="newTask.subtasks.splice(idx, 1)" class="text-gray-400 hover:text-red-500 ml-2 shrink-0">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                   </button>
                </div>
             </div>
             <div v-else class="text-sm text-gray-400 italic bg-gray-50 p-4 rounded-xl border border-dashed border-gray-200 text-center">
                Click "+ Add Subtask" to add one.
             </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-100 shrink-0">
          <button @click="showNewTaskModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-medium transition-colors">Cancel</button>
          <button @click="createTask" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-sm transition-colors shadow-blue-500/30">Save Task</button>
        </div>
      </div>
    </div>

    <!-- Blocked Task Confirmation Modal -->
    <div v-if="showBlockedConfirmModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="cancelBlockedAction"></div>
      <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="p-6">
          <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4 mx-auto">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Task is Blocked</h3>
          <p class="text-sm text-gray-500 text-center">This subtask is currently marked as blocked. Are you sure you want to mark it as done?</p>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end border-t border-gray-100">
          <button @click="cancelBlockedAction" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex-1">Cancel</button>
          <button @click="confirmBlockedAction" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex-1">Yes, mark as done</button>
        </div>
      </div>
    </div>

    <!-- Block Task Reason Modal -->
    <div v-if="showBlockTaskModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="cancelBlockTask"></div>
      <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="p-6">
          <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4 mx-auto">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Block Task</h3>
          <p class="text-sm text-gray-500 text-center mb-4">Please provide a reason why this task is blocked.</p>
          <textarea v-model="blockTaskReason" rows="3" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-red-500 transition-colors" placeholder="Blocker reason..."></textarea>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end border-t border-gray-100">
          <button @click="cancelBlockTask" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex-1">Cancel</button>
          <button @click="confirmBlockTask" :disabled="!blockTaskReason.trim()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 transition-colors shadow-sm flex-1 disabled:opacity-50 disabled:cursor-not-allowed">Block Task</button>
        </div>
      </div>
    </div>

    <!-- Add Subtasks Modal -->
    <div v-if="showSubtaskModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-[70] flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 animate-in zoom-in-95 duration-200 flex flex-col h-[600px] max-h-[85vh]">
        <div class="flex items-center justify-between mb-4 shrink-0">
          <h3 class="text-xl font-bold text-gray-900">Add Subtasks</h3>
          <button @click="closeSubtaskModal" class="text-gray-400 hover:text-gray-600 bg-white p-1.5 rounded-lg border border-gray-200 transition-colors">
             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="space-y-3 overflow-y-auto flex-1 -m-1 p-1 pr-2">
           <div v-for="(sub, idx) in draftSubtasks" :key="idx" class="relative bg-gray-50 border border-gray-200 p-3 rounded-xl flex flex-col gap-2">
             <button @click="draftSubtasks.splice(idx, 1)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 bg-gray-50 rounded-full p-1 z-10">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
             </button>
             <input v-model="sub.title" type="text" placeholder="Subtask Title" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white shadow-sm" />
             <textarea v-model="sub.description" rows="2" placeholder="Description (optional)" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white shadow-sm"></textarea>
           </div>
           
           <button @click="draftSubtasks.push({title: '', description: ''})" class="w-full py-2 border border-dashed border-gray-300 rounded-xl text-gray-500 font-medium hover:bg-gray-50 hover:text-gray-700 transition-colors flex items-center justify-center gap-2">
             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
             Add Another Subtask
           </button>
        </div>
        
        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-100 shrink-0">
          <button @click="closeSubtaskModal" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-medium transition-colors">Cancel</button>
          <button @click="saveDraftSubtasks" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-sm transition-colors shadow-blue-500/30">Save Subtasks</button>
        </div>
      </div>
    </div>

    <!-- Task Detail Sidebar -->
    <div v-if="showTaskDetailModal && selectedTask" class="fixed inset-0 z-50 flex justify-end">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-gray-900/20 backdrop-blur-sm transition-opacity" @click="showTaskDetailModal = false"></div>
      
      <!-- Sidebar Panel -->
      <div class="relative w-full max-w-md bg-white h-full shadow-2xl flex flex-col animate-in slide-in-from-right-full duration-300 border-l border-gray-100">
        
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
          <div class="flex items-center gap-3">
            <button v-if="isEditingTask" @click="cancelEditing" class="text-gray-400 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-full p-1.5 transition-colors border border-gray-200">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h2 class="text-lg font-black text-gray-900 tracking-tight">{{ selectedTask.parent_id ? 'Edit Subtask' : (isEditingTask ? 'Edit Task' : 'Task Details') }}</h2>
          </div>
          <button @click="showTaskDetailModal = false" class="text-gray-400 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-full p-1.5 transition-colors border border-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <div class="flex-1 overflow-y-auto">
          <div class="p-6 space-y-6">
            
            <div class="flex justify-between items-center">
              <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md bg-gray-100 text-gray-600">
                {{ selectedTask.status || 'BACKLOG' }}
              </span>
              <div class="flex items-center gap-2">
                <button v-if="!isEditingTask && selectedTask.status !== 'Blocked' && selectedTask.status !== 'Done'" @click="openBlockTaskModal(selectedTask)" class="text-[10px] font-bold text-amber-600 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 px-2.5 py-1 rounded-md transition-colors border border-amber-200">
                  Block Task
                </button>
                <button v-if="!isEditingTask" @click="startEditing" class="text-xs font-bold text-blue-600 hover:text-blue-800 ml-1">
                  Edit Task
                </button>
              </div>
            </div>

            <!-- View Mode -->
            <template v-if="!isEditingTask">
              <div>
                <div class="flex items-start gap-3 mb-2">
                  <input type="checkbox" :checked="selectedTask.status === 'Done'" @change="toggleMainTask(selectedTask, $event)" class="mt-1.5 w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                  <h3 class="text-2xl font-bold text-gray-900 leading-tight" :class="{'line-through text-gray-400': selectedTask.status === 'Done'}">{{ selectedTask.title }}</h3>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">
                  {{ selectedTask.description || 'No description provided.' }}
                </p>
                <div v-if="selectedTask.blocked_description" class="mt-4 bg-red-50 border border-red-100 rounded-xl p-4">
                  <h4 class="text-[10px] font-black text-red-800 uppercase tracking-widest mb-1">Blocker Note</h4>
                  <p class="text-sm text-red-700 leading-relaxed whitespace-pre-wrap">{{ selectedTask.blocked_description }}</p>
                </div>
              </div>

              <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 space-y-5">
                
                <div class="flex justify-between items-center">
                  <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">PIC</span>
                  <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-[9px] font-bold text-white shadow-sm" :class="selectedTask.assigned_to ? 'bg-indigo-400' : 'bg-gray-400'">
                      {{ selectedTask.assigned_to ? getUserInitials(selectedTask.assigned_to) : '?' }}
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ selectedTask.assigned_to ? getUserName(selectedTask.assigned_to) : 'Unassigned' }}</span>
                  </div>
                </div>
                
                <div class="flex justify-between items-center">
                  <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Timeline</span>
                  <span class="text-xs font-bold text-gray-900 bg-white border border-gray-200 px-2 py-1 rounded shadow-sm">
                    <span v-if="selectedTask.start_date || selectedTask.end_date">
                      {{ formatDateShort(selectedTask.start_date) }} - {{ formatDateShort(selectedTask.end_date) }}
                    </span>
                    <span v-else>Unscheduled</span>
                  </span>
                </div>
                
                <div class="flex justify-between items-center">
                  <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Phase / Lane</span>
                  <span class="text-sm font-bold text-gray-900">{{ selectedTask.category || 'Development' }}</span>
                </div>

                <div class="flex justify-between items-center">
                  <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Priority</span>
                  <span class="text-sm font-bold" :class="priorityTextClass(selectedTask.priority)">{{ selectedTask.priority || 'Medium' }}</span>
                </div>

              </div>

              <!-- Subtasks Section -->
              <div v-if="!selectedTask.parent_id" class="mt-6 border-t border-gray-100 pt-6">
                <div class="flex justify-between items-center mb-4">
                  <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest">Subtasks</h4>
                  <button @click="openSubtaskModal('existing_task')" class="text-xs font-bold text-blue-600 hover:text-blue-800">+ Add</button>
                </div>
                
                <div class="space-y-2">
                  <div v-for="sub in selectedTask.subtasks || []" :key="sub.id" class="flex items-center justify-between border p-3 rounded-xl shadow-sm transition-colors" :class="sub.status === 'Blocked' ? 'bg-red-50/30 border-red-200' : 'bg-white border-gray-200'">
                    <div class="flex items-center gap-3 w-full">
                      <input type="checkbox" :checked="sub.status === 'Done'" @change="toggleSubtask(sub, $event)" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                      <div class="flex flex-col flex-1">
                        <div class="flex items-center gap-2">
                          <span class="text-sm font-medium cursor-pointer" :class="sub.status === 'Done' ? 'text-gray-400 line-through' : (sub.status === 'Blocked' ? 'text-red-800' : 'text-gray-700')" @click="openTaskDetail(sub)">{{ sub.title }}</span>
                          <span v-if="sub.status === 'Blocked'" class="text-[9px] font-bold bg-red-100 text-red-700 px-1.5 py-0.5 rounded border border-red-200 flex items-center shadow-sm">Blocked</span>
                        </div>
                        <span v-if="sub.description" class="text-xs mt-0.5" :class="sub.status === 'Blocked' ? 'text-red-500' : 'text-gray-500'">{{ sub.description }}</span>
                      </div>
                    </div>
                    <div class="flex items-center">
                      <button v-if="sub.status !== 'Blocked' && sub.status !== 'Done'" @click.stop="openBlockTaskModal(sub)" class="text-gray-300 hover:text-red-500 transition-colors mr-2" title="Block Task">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                      </button>
                      <button v-else-if="sub.status === 'Blocked'" @click.stop="unblockTask(sub)" class="text-red-500 hover:text-gray-500 transition-colors mr-2" title="Unblock Task">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                      </button>
                      <button @click.stop="deleteSubtask(sub.id)" class="text-gray-300 hover:text-red-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Attachments Section (View Mode) -->
              <div v-if="selectedTask.attachments && selectedTask.attachments.length > 0" class="mt-6 border-t border-gray-100 pt-6">
                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4">Attachments</h4>
                <div class="space-y-2">
                  <a :href="file.url" target="_blank" v-for="file in selectedTask.attachments" :key="file.id" class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-xl shadow-sm hover:border-blue-300 hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-center gap-3 overflow-hidden">
                      <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                      </div>
                      <div class="flex flex-col truncate">
                        <span class="text-sm font-bold text-gray-800 group-hover:text-blue-600 truncate transition-colors">{{ file.file_name }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ formatFileSize(file.file_size) }}</span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>

            </template>

            <!-- Edit Mode -->
            <template v-else>
              <div class="space-y-4">
                <div>
                   <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Task Title</label>
                   <input v-model="editTaskForm.title" :class="{'border-red-300 ring-1 ring-red-300': formErrors.title, 'border-gray-200': !formErrors.title}" class="w-full text-lg font-bold bg-white border rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm transition-colors" />
                   <p v-if="formErrors.title" class="text-xs text-red-500 mt-1 font-medium">{{ formErrors.title }}</p>
                </div>
                <div>
                   <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Description</label>
                   <textarea v-model="editTaskForm.description" rows="5" class="w-full p-4 bg-white border border-gray-200 rounded-xl text-gray-700 text-sm leading-relaxed focus:ring-2 focus:ring-blue-500 outline-none shadow-sm"></textarea>
                </div>
                <div v-if="editTaskForm.blocked_description || editTaskForm.status === 'Blocked'">
                   <label class="block text-xs font-bold text-red-500 uppercase tracking-wider mb-1.5">Blocker Note</label>
                   <textarea v-model="editTaskForm.blocked_description" rows="3" class="w-full p-4 bg-red-50/30 border border-red-200 rounded-xl text-red-700 text-sm leading-relaxed focus:ring-2 focus:ring-red-500 outline-none shadow-sm"></textarea>
                </div>
                <template v-if="!selectedTask.parent_id">
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                       <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Start Date</label>
                       <input type="date" v-model="editTaskForm.start_date" :class="{'border-red-300 ring-1 ring-red-300': formErrors.start_date, 'border-gray-200': !formErrors.start_date}" class="w-full text-sm font-bold bg-white border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm transition-colors" />
                       <p v-if="formErrors.start_date" class="text-xs text-red-500 mt-1 font-medium">{{ formErrors.start_date }}</p>
                    </div>
                    <div>
                       <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">End Date</label>
                       <input type="date" v-model="editTaskForm.end_date" :class="{'border-red-300 ring-1 ring-red-300': formErrors.end_date, 'border-gray-200': !formErrors.end_date}" class="w-full text-sm font-bold bg-white border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm transition-colors" />
                       <p v-if="formErrors.end_date" class="text-xs text-red-500 mt-1 font-medium">{{ formErrors.end_date }}</p>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                       <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Priority</label>
                       <div class="relative">
                         <select v-model="editTaskForm.priority" class="appearance-none w-full text-sm font-bold bg-white border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                         </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                         </div>
                       </div>
                    </div>
                    <div>
                       <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Phase</label>
                       <div class="relative">
                         <select v-model="editTaskForm.category" class="appearance-none w-full text-sm font-bold bg-white border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                            <option value="Research">Research</option>
                            <option value="Design">Design</option>
                            <option value="Development">Development</option>
                            <option value="Testing">Testing</option>
                            <option value="Review">Review</option>
                            <option value="Release">Release</option>
                         </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                         </div>
                       </div>
                    </div>
                  </div>
                  <div>
                     <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Assignee</label>
                     <div class="relative">
                       <select v-model="editTaskForm.assigned_to" class="appearance-none w-full text-sm font-bold bg-white border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm">
                          <option :value="null">Unassigned</option>
                          <option v-for="user in authStore.allUsers" :key="user.id" :value="user.id">{{ user.name }}</option>
                       </select>
                       <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                       </div>
                     </div>
                  </div>
                </template>
                <!-- Attachments Section (Edit Mode) -->
                <div v-if="!selectedTask.parent_id" class="mt-6 pt-6 border-t border-gray-100">
                  <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4">Attachments</h4>
                  
                  <div 
                    @dragover.prevent="isDragging = true" 
                    @dragleave.prevent="isDragging = false" 
                    @drop.prevent="onFileDrop"
                    @click="$refs.fileInput.click()"
                    class="border-2 border-dashed rounded-2xl p-6 text-center cursor-pointer transition-colors"
                    :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'"
                  >
                    <input type="file" ref="fileInput" class="hidden" multiple @change="onFileSelect" />
                    <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <p class="text-sm font-bold text-gray-700">Click or drag files here to upload</p>
                    <p class="text-xs text-gray-500 mt-1">Maximum size is 2MB per file</p>
                  </div>

                  <!-- Uploaded Files List (Staging) -->
                  <div v-if="(editTaskForm.attachments && editTaskForm.attachments.length > 0) || (editTaskForm.newAttachments && editTaskForm.newAttachments.length > 0)" class="mt-4 space-y-2">
                    <template v-for="file in editTaskForm.attachments" :key="file.id">
                      <a :href="file.url" target="_blank" class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-xl shadow-sm hover:border-blue-300 hover:shadow-md transition-all cursor-pointer group">
                        <div class="flex items-center gap-3 overflow-hidden">
                          <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                          </div>
                          <div class="flex flex-col truncate">
                            <span class="text-sm font-bold text-gray-800 group-hover:text-blue-600 truncate transition-colors">{{ file.file_name }}</span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ formatFileSize(file.file_size) }}</span>
                          </div>
                        </div>
                        <button @click.prevent.stop="removeExistingAttachment(file)" class="text-gray-300 hover:text-red-500 transition-colors ml-2 shrink-0">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                      </a>
                    </template>
                    <template v-if="editTaskForm.newAttachments">
                      <template v-for="(file, index) in editTaskForm.newAttachments" :key="'new-'+index">
                        <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-xl shadow-sm border-l-4 border-l-blue-500">
                          <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                            <div class="flex flex-col truncate">
                              <span class="text-sm font-bold text-gray-800 truncate">{{ file.name }} <span class="text-xs text-blue-600 font-bold ml-1">(New)</span></span>
                              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ formatFileSize(file.size) }}</span>
                            </div>
                          </div>
                          <button @click.prevent.stop="removeNewAttachment(index)" class="text-gray-300 hover:text-red-500 transition-colors ml-2 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                          </button>
                        </div>
                      </template>
                    </template>
                  </div>
                </div>
                
                <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                   <button @click="cancelEditing" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-medium transition-colors">Cancel</button>
                   <button @click="saveEditedTask" :disabled="isSavingTask" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-sm transition-colors shadow-blue-500/30 flex items-center disabled:opacity-70">
                     <span v-if="isSavingTask" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span>
                     Save Changes
                   </button>
                </div>
              </div>
            </template>
            

            
          </div>
        </div>
      </div>
    </div>
    <!-- Edit Project Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-bold text-gray-900">Edit Project</h3>
          <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <form @submit.prevent="submitEditProject" class="p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
              <input v-model="editProjectForm.name" required type="text" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea v-model="editProjectForm.description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input v-model="editProjectForm.start_date" required type="date" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input v-model="editProjectForm.end_date" required type="date" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" />
              </div>
            </div>
          </div>
          
          <div class="mt-8 flex gap-3">
            <button type="button" @click="showEditModal = false" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-medium transition-colors">
              Cancel
            </button>
            <button type="submit" :disabled="isSubmittingEdit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition-colors disabled:opacity-70 flex justify-center items-center">
              <span v-if="isSubmittingEdit" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span>
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTaskStore } from '../stores/task'
import { useAuthStore } from '../stores/auth'
import { useProjectStore } from '../stores/project'
import { useModal } from '../composables/useModal'
import { useToast } from '../composables/useToast'

const route = useRoute()
const router = useRouter()
const taskStore = useTaskStore()
const authStore = useAuthStore()
const projectStore = useProjectStore()
const modal = useModal()
const toast = useToast()

const showEditModal = ref(false)
const isSubmittingEdit = ref(false)
const editProjectForm = ref({
  name: '',
  description: '',
  start_date: '',
  end_date: ''
})

const openEditModal = () => {
  if (projectStore.currentProject) {
    editProjectForm.value = {
      name: projectStore.currentProject.name,
      description: projectStore.currentProject.description || '',
      start_date: projectStore.currentProject.start_date.split('T')[0],
      end_date: projectStore.currentProject.end_date.split('T')[0]
    }
    showEditModal.value = true
  }
}

const submitEditProject = async () => {
  isSubmittingEdit.value = true
  try {
    await projectStore.updateProject(projectStore.currentProject.id, editProjectForm.value)
    showEditModal.value = false
  } catch (error) {
    if (error.response?.status === 403) {
      toast.showToast('Only the creator of this project can edit it.', 'Error')
    } else {
      toast.showToast('Failed to update project.', 'Error')
    }
  } finally {
    isSubmittingEdit.value = false
  }
}

let pollingInterval = null;

onMounted(async () => {
  if (!projectStore.currentProject || projectStore.currentProject.id != route.params.id) {
    await projectStore.fetchProject(route.params.id)
  }
  if (!taskStore.projectTasks || taskStore.projectTasks.length === 0) {
    await taskStore.fetchProjectTasks(route.params.id)
  }
  if (authStore.allUsers.length === 0) {
    await authStore.fetchAllUsers()
  }
  
  // Real-time polling
  pollingInterval = setInterval(() => {
    // Only poll if there's no modal open that could be disrupted by a silent refresh, 
    // actually silent refresh is perfectly fine since Vue handles reactivity smoothly!
    // But don't poll if we are actively editing a task to prevent overwriting inputs.
    if (!isEditingTask.value) {
      taskStore.fetchProjectTasks(route.params.id, true)
    }
  }, 5000)
})

onUnmounted(() => {
  if (pollingInterval) clearInterval(pollingInterval)
})

const columns = [
  { id: 'Backlog', label: 'Backlog', bgClass: 'bg-slate-200/70', borderClass: 'border-slate-200', headerBorderClass: 'border-slate-200', textClass: 'text-gray-800', dotClass: 'bg-gray-400', countClass: 'border-gray-300 text-gray-500', cardBorderHoverClass: 'hover:border-gray-300' },
  { id: 'In Progress', label: 'In Progress', bgClass: 'bg-blue-50/50', borderClass: 'border-blue-100', headerBorderClass: 'border-blue-100', textClass: 'text-blue-900', dotClass: 'bg-blue-500 animate-pulse', countClass: 'border-blue-100 text-blue-600', cardBorderHoverClass: 'hover:border-blue-300' },
  { id: 'Done', label: 'Done', bgClass: 'bg-green-50/50', borderClass: 'border-green-100', headerBorderClass: 'border-green-100', textClass: 'text-green-900', dotClass: 'bg-green-500', countClass: 'border-green-100 text-green-600', cardBorderHoverClass: 'hover:border-green-300' },
  { id: 'Cancelled', label: 'Cancelled', bgClass: 'bg-gray-100/50', borderClass: 'border-gray-200', headerBorderClass: 'border-gray-200', textClass: 'text-gray-500', dotClass: 'bg-gray-300', countClass: 'border-gray-200 text-gray-400', cardBorderHoverClass: 'hover:border-gray-300 opacity-70 hover:opacity-100' }
]

const kanbanColumns = computed(() => {
  const cols = {
    'Backlog': [],
    'In Progress': [],
    'Done': [],
    'Cancelled': []
  }
  
  if (taskStore.projectTasks) {
    // Only show top level tasks in the board (parent_id is null)
    const topLevelTasks = taskStore.projectTasks.filter(t => !t.parent_id);
    
    topLevelTasks.forEach(task => {
      let status = task.status || 'Backlog';
      let isBlocked = status === 'Blocked';
      
      // Auto-calculate parent status based on subtasks
      if (status !== 'Cancelled' && task.subtasks && task.subtasks.length > 0) {
        const hasBlockedSubtask = task.subtasks.some(s => s.status === 'Blocked');
        const allDone = task.subtasks.every(s => s.status === 'Done');
        const allBacklog = task.subtasks.every(s => !s.status || s.status === 'Backlog');
        
        isBlocked = isBlocked || hasBlockedSubtask;
        
        if (allDone) {
          status = 'Done';
        } else if (!allBacklog) {
          status = 'In Progress';
        } else {
          status = 'Backlog';
        }
      } else {
        if (status === 'Blocked') {
          status = 'In Progress';
        }
      }
      
      task._isBlocked = isBlocked;
      
      if (cols[status]) {
        cols[status].push(task)
      } else {
        cols['Backlog'].push(task)
      }
    })

    // Sort each column by most recently updated first
    for (const col of Object.values(cols)) {
      col.sort((a, b) => {
        const dateA = new Date(a.updated_at || a.created_at || 0)
        const dateB = new Date(b.updated_at || b.created_at || 0)
        return dateB - dateA
      })
    }
  }
  
  return cols
})

const showNewTaskModal = ref(false)
const newTask = ref({ title: '', description: '', priority: 'Medium', category: 'Development', start_date: '', end_date: '', assigned_to: null, subtasks: [] })

const showTaskDetailModal = ref(false)
const selectedTask = ref(null)
const isEditingTask = ref(false)
const editTaskForm = ref({})
const isSavingTask = ref(false)

const showSubtaskModal = ref(false)
const subtaskModalContext = ref('new_task')
const draftSubtasks = ref([])

const openSubtaskModal = (context) => {
  subtaskModalContext.value = context
  draftSubtasks.value = [{title: '', description: ''}]
  showSubtaskModal.value = true
}

const closeSubtaskModal = () => {
  showSubtaskModal.value = false
  draftSubtasks.value = []
}

const showBlockedConfirmModal = ref(false)
const showBlockTaskModal = ref(false)
const taskToBlock = ref(null)
const blockTaskReason = ref('')
const blockedConfirmSubtask = ref(null)
const blockedConfirmEventTarget = ref(null)

const formErrors = ref({})

const validateTaskForm = (form) => {
  const errors = {};
  if (!form.title || !form.title.trim()) errors.title = "Task title is required.";
  
  if (!form.parent_id) {
    if (!form.start_date) errors.start_date = "Start date is required.";
    if (!form.end_date) errors.end_date = "End date is required.";
    else if (form.start_date && form.end_date && new Date(form.start_date) > new Date(form.end_date)) {
      errors.end_date = "End date cannot be before start date.";
    }
  }
  
  return errors;
}

const openTaskDetail = (task) => {
  selectedTask.value = task
  formErrors.value = {}
  
  if (task.parent_id) {
    startEditing()
  } else {
    isEditingTask.value = false
  }
  
  showTaskDetailModal.value = true
}

const startEditing = () => {
  editTaskForm.value = { ...selectedTask.value }
  
  // Initialize attachment staging arrays
  if (selectedTask.value.attachments) {
    editTaskForm.value.attachments = [...selectedTask.value.attachments]
  } else {
    editTaskForm.value.attachments = []
  }
  editTaskForm.value.newAttachments = []
  editTaskForm.value.pendingDeletedAttachments = []
  
  // Ensure dates are correctly formatted for input
  if (editTaskForm.value.start_date) editTaskForm.value.start_date = editTaskForm.value.start_date.split('T')[0]
  if (editTaskForm.value.end_date) editTaskForm.value.end_date = editTaskForm.value.end_date.split('T')[0]
  formErrors.value = {}
  isEditingTask.value = true
}

const cancelEditing = () => {
  if (selectedTask.value.parent_id) {
    const parent = taskStore.projectTasks.find(t => t.id === selectedTask.value.parent_id)
    if (parent) {
      selectedTask.value = parent
      isEditingTask.value = false
      formErrors.value = {}
      return
    }
  }
  isEditingTask.value = false
  formErrors.value = {}
}

const saveEditedTask = async () => {
  formErrors.value = validateTaskForm(editTaskForm.value)
  if (Object.keys(formErrors.value).length > 0) return

  isSavingTask.value = true
  try {
    // 1. Process deletions
    if (editTaskForm.value.pendingDeletedAttachments && editTaskForm.value.pendingDeletedAttachments.length > 0) {
      for (const attachment of editTaskForm.value.pendingDeletedAttachments) {
        await taskStore.deleteTaskFile(route.params.id, selectedTask.value.id, attachment.id)
      }
    }
    
    // 2. Process new uploads
    if (editTaskForm.value.newAttachments && editTaskForm.value.newAttachments.length > 0) {
      for (const file of editTaskForm.value.newAttachments) {
        await taskStore.uploadTaskFile(route.params.id, selectedTask.value.id, file)
      }
    }

    // 3. Update task details
    const updatedTask = await taskStore.updateProjectTask(route.params.id, selectedTask.value.id, editTaskForm.value)
    
    if (selectedTask.value.parent_id) {
      const parentId = selectedTask.value.parent_id;
      await taskStore.fetchProjectTasks(route.params.id)
      const parent = taskStore.projectTasks.find(t => t.id === parentId)
      if (parent) {
        selectedTask.value = parent
      } else {
        showTaskDetailModal.value = false
      }
    } else {
      Object.assign(selectedTask.value, updatedTask)
    }
    
    isEditingTask.value = false
    toast.showToast('Task updated successfully', 'Success')
  } catch (error) {
    console.error('Task save error:', error)
    toast.showToast('Failed to save task changes.', 'Error')
  } finally {
    isSavingTask.value = false
  }
}

const confirmDeleteTask = async (task) => {
  if (task.status === 'Cancelled') {
    if (await modal.showConfirm('Are you sure you want to delete this task permanently? This action cannot be undone.', 'Delete Task Permanently', 'danger')) {
      try {
        await taskStore.deleteProjectTask(route.params.id, task.id)
      } catch (error) {
        toast.showToast('Failed to delete task', 'Error')
      }
    }
  } else {
    if (await modal.showConfirm('Are you sure you want to cancel this task? It will be moved to the Cancelled column.', 'Cancel Task', 'warning')) {
      try {
        await taskStore.updateProjectTask(route.params.id, task.id, { status: 'Cancelled' })
        // Update local object to reflect status in modal if open
        if (selectedTask.value && selectedTask.value.id === task.id) {
          selectedTask.value.status = 'Cancelled'
        }
      } catch (error) {
        toast.showToast('Failed to cancel task', 'Error')
      }
    }
  }
}

const restoreTask = async (task) => {
  try {
    await taskStore.updateProjectTask(route.params.id, task.id, { status: 'Backlog' })
  } catch (error) {
    toast.showToast('Failed to restore task', 'Error')
  }
}

const addDraftSubtask = () => {
  if (newSubtaskTitle.value && newSubtaskTitle.value.trim()) {
    if (!newTask.value.subtasks) newTask.value.subtasks = []
    newTask.value.subtasks.push({ 
      title: newSubtaskTitle.value.trim(),
      description: newSubtaskDescription.value.trim() 
    })
    newSubtaskTitle.value = ''
    newSubtaskDescription.value = ''
  }
}

const createTask = async () => {
  formErrors.value = validateTaskForm(newTask.value)
  if (Object.keys(formErrors.value).length > 0) return
  
  try {
    const parentTask = await taskStore.createProjectTask(route.params.id, {
      ...newTask.value,
      status: 'Backlog'
    })

    if (newTask.value.subtasks && newTask.value.subtasks.length > 0) {
      for (const sub of newTask.value.subtasks) {
        if (!sub.title || !sub.title.trim()) continue;
        await taskStore.createProjectTask(route.params.id, {
          title: sub.title,
          description: sub.description,
          parent_id: parentTask.id,
          status: 'Backlog',
          priority: 'Medium'
        })
      }
      await taskStore.fetchProjectTasks(route.params.id)
    }
    
    showNewTaskModal.value = false
    newTask.value = { title: '', description: '', priority: 'Medium', category: 'Development', start_date: '', end_date: '', assigned_to: null, subtasks: [] }
    formErrors.value = {}
  } catch(error) {
    toast.showToast('Failed to create task', 'Error')
  }
}

const saveDraftSubtasks = async () => {
  const validSubtasks = draftSubtasks.value.filter(s => s.title && s.title.trim())
  
  if (subtaskModalContext.value === 'new_task') {
    if (!newTask.value.subtasks) newTask.value.subtasks = []
    newTask.value.subtasks.push(...validSubtasks)
    closeSubtaskModal()
  } else if (subtaskModalContext.value === 'existing_task') {
    if (validSubtasks.length > 0) {
      try {
        for (const sub of validSubtasks) {
          const created = await taskStore.createProjectTask(route.params.id, {
            title: sub.title,
            description: sub.description,
            parent_id: selectedTask.value.id,
            status: 'Backlog',
            priority: 'Medium'
          })
          if (!selectedTask.value.subtasks) selectedTask.value.subtasks = []
          selectedTask.value.subtasks.push(created)
        }
        closeSubtaskModal()
      } catch (e) {
        toast.showToast('Failed to save subtasks', 'Error')
      }
    } else {
       closeSubtaskModal()
    }
  }
}

const toggleMainTask = async (task, event) => {
  try {
    const newStatus = task.status === 'Done' ? 'Backlog' : 'Done'
    
    // Check if task or any subtask is blocked when trying to mark as Done
    if (newStatus === 'Done') {
      const hasBlockedSubtask = task.subtasks?.some(s => s.status === 'Blocked');
      if (task.status === 'Blocked' || hasBlockedSubtask) {
        blockedConfirmSubtask.value = task; // Reuse for main task
        if (event && event.target) {
          blockedConfirmEventTarget.value = event.target;
        }
        showBlockedConfirmModal.value = true;
        return;
      }
    }
    
    await executeToggleMainTask(task, newStatus);
  } catch (error) {
    toast.showToast('Failed to update task status', 'Error')
    if (event && event.target) {
      event.target.checked = task.status === 'Done'
    }
  }
}

const executeToggleMainTask = async (task, newStatus) => {
  try {
    const updateData = { status: newStatus }
    if (newStatus === 'Done') updateData.blocked_description = ''
    
    await taskStore.updateProjectTask(route.params.id, task.id, updateData)
    if (selectedTask.value && selectedTask.value.id === task.id) {
      selectedTask.value.status = newStatus
      if (newStatus === 'Done') selectedTask.value.blocked_description = ''
    }
    
    // Also update all subtasks to Done if main task is checked
    if (newStatus === 'Done' && task.subtasks && task.subtasks.length > 0) {
      const promises = []
      for (const sub of task.subtasks) {
        if (sub.status !== 'Done') {
          sub.status = 'Done'
          sub.blocked_description = ''
          promises.push(taskStore.updateProjectTask(route.params.id, sub.id, { status: 'Done', blocked_description: '' }))
        }
      }
      await Promise.all(promises)
    }
  } catch (error) {
    toast.showToast('Failed to update tasks', 'Error')
  }
}

const toggleSubtask = async (sub, event) => {
  const newStatus = sub.status === 'Done' ? 'Backlog' : 'Done'
  
  if (newStatus === 'Done' && sub.status === 'Blocked') {
    blockedConfirmSubtask.value = sub
    if (event && event.target) {
      blockedConfirmEventTarget.value = event.target
    }
    showBlockedConfirmModal.value = true
    return;
  }
  
  await executeToggleSubtask(sub, newStatus)
}

const executeToggleSubtask = async (sub, newStatus) => {
  try {
    const updateData = { status: newStatus }
    if (newStatus === 'Done') updateData.blocked_description = ''
    
    const updated = await taskStore.updateProjectTask(route.params.id, sub.id, updateData)
    sub.status = newStatus
    if (newStatus === 'Done') sub.blocked_description = ''
    
    // Auto-update parent task status based on all subtasks
    if (selectedTask.value && selectedTask.value.subtasks) {
      const allDone = selectedTask.value.subtasks.every(s => s.status === 'Done');
      const allBacklog = selectedTask.value.subtasks.every(s => !s.status || s.status === 'Backlog');
      
      let parentStatus = selectedTask.value.status;
      if (allDone) {
        parentStatus = 'Done';
      } else if (!allBacklog) {
        parentStatus = 'In Progress';
      } else {
        parentStatus = 'Backlog';
      }
      
      if (parentStatus !== selectedTask.value.status) {
        await taskStore.updateProjectTask(route.params.id, selectedTask.value.id, { status: parentStatus });
        selectedTask.value.status = parentStatus;
      }
    }
  } catch (e) {
    toast.showToast('Failed to update subtask', 'Error')
  }
}

const confirmBlockedAction = async () => {
  if (blockedConfirmSubtask.value) {
    if (!blockedConfirmSubtask.value.parent_id) {
      await executeToggleMainTask(blockedConfirmSubtask.value, 'Done')
    } else {
      await executeToggleSubtask(blockedConfirmSubtask.value, 'Done')
    }
  }
  closeBlockedConfirmModal()
}

const cancelBlockedAction = () => {
  if (blockedConfirmEventTarget.value) {
    blockedConfirmEventTarget.value.checked = false
  }
  closeBlockedConfirmModal()
}

const openBlockTaskModal = (task) => {
  taskToBlock.value = task
  blockTaskReason.value = ''
  showBlockTaskModal.value = true
}

const cancelBlockTask = () => {
  taskToBlock.value = null
  blockTaskReason.value = ''
  showBlockTaskModal.value = false
}

const confirmBlockTask = async () => {
  if (!taskToBlock.value || !blockTaskReason.value.trim()) return
  
  try {
    const updatedData = {
      status: 'Blocked',
      blocked_description: blockTaskReason.value.trim()
    }
    
    // Update local immediately for responsiveness
    taskToBlock.value.status = 'Blocked'
    taskToBlock.value.blocked_description = updatedData.blocked_description
    
    await taskStore.updateProjectTask(route.params.id, taskToBlock.value.id, updatedData)
    
    cancelBlockTask()
  } catch (error) {
    console.error('Failed to block task', error)
    // Revert if failed
    taskToBlock.value.status = 'In Progress'
    taskToBlock.value.blocked_description = null
  }
}

const unblockTask = async (task) => {
  try {
    const updatedData = {
      status: 'In Progress',
      blocked_description: null
    }
    
    task.status = 'In Progress'
    task.blocked_description = null
    
    await taskStore.updateProjectTask(route.params.id, task.id, updatedData)
  } catch (error) {
    console.error('Failed to unblock task', error)
    task.status = 'Blocked'
  }
}

const closeBlockedConfirmModal = () => {
  showBlockedConfirmModal.value = false
  blockedConfirmSubtask.value = null
  blockedConfirmEventTarget.value = null
}

const deleteSubtask = async (subId) => {
  try {
    await taskStore.deleteProjectTask(route.params.id, subId)
    selectedTask.value.subtasks = selectedTask.value.subtasks.filter(s => s.id !== subId)
  } catch (e) {
    toast.showToast('Failed to delete subtask', 'Error')
  }
}

const getUserInitials = (userId) => {
  const user = authStore.allUsers.find(u => u.id === userId)
  if (!user || !user.name) return 'PIC'
  return user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
}

const getUserName = (userId) => {
  const user = authStore.allUsers.find(u => u.id === userId)
  return user ? user.name : 'Unknown User'
}

const formatDateShort = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

// Theme 1 color palette — mirrors colorsV1 in ProjectTimeline.vue
const categoryColors = {
  research:    { bg: '#FFECA2', title: '#796215', sub: '#695200' },
  design:      { bg: '#FFBFCC', title: '#8D2C3F', sub: '#461821' },
  development: { bg: '#B3E3F7', title: '#376180', sub: '#275872' },
  testing:     { bg: '#F2D2FD', title: '#6A507F', sub: '#6C4D7B' },
  review:      { bg: '#C3EDBC', title: '#1F542A', sub: '#5F8D62' },
  release:     { bg: '#FFC6BF', title: '#60221E', sub: '#7A3E38' },
}

const getStatusIndicator = (colId, isBlocked) => {
  if (isBlocked) return { color: '#EF4444', label: 'Blocked', pulse: false }
  switch (colId) {
    case 'In Progress': return { color: '#3B82F6', label: 'In Progress', pulse: true }
    case 'Done':        return { color: '#22C55E', label: 'Done',        pulse: false }
    case 'Cancelled':   return { color: '#9CA3AF', label: 'Cancelled',   pulse: false }
    default:            return { color: '#94A3B8', label: 'Backlog',     pulse: false }
  }
}

const priorityTextClass = (priority) => {
  switch(priority) {
    case 'High': return 'text-red-600'
    case 'Medium': return 'text-amber-600'
    case 'Low': return 'text-emerald-600'
    default: return 'text-gray-500'
  }
}

// File Upload Logic
const isDragging = ref(false)
const uploadingFiles = ref(false)
const fileInput = ref(null)

const onFileDrop = async (e) => {
  isDragging.value = false
  if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
    await handleFiles(Array.from(e.dataTransfer.files))
  }
}

const onFileSelect = async (e) => {
  if (e.target.files && e.target.files.length > 0) {
    await handleFiles(Array.from(e.target.files))
  }
}

const handleFiles = async (files) => {
  if (!isEditingTask.value) return
  
  // Validate file sizes (PHP default is 2MB)
  const maxSize = 2 * 1024 * 1024; // 2MB
  
  for (const file of files) {
    if (file.size > maxSize) {
      toast.showToast(`File ${file.name} is too large. Maximum size is 2MB.`, 'Error')
    } else {
      if (!editTaskForm.value.newAttachments) editTaskForm.value.newAttachments = []
      // Check if file with same name is already staged
      if (!editTaskForm.value.newAttachments.some(f => f.name === file.name)) {
        editTaskForm.value.newAttachments.push(file)
      }
    }
  }
  if (fileInput.value) fileInput.value.value = ''
}

const removeExistingAttachment = (attachment) => {
  if (!editTaskForm.value.pendingDeletedAttachments) editTaskForm.value.pendingDeletedAttachments = []
  editTaskForm.value.pendingDeletedAttachments.push(attachment)
  // Remove from view
  editTaskForm.value.attachments = editTaskForm.value.attachments.filter(a => a.id !== attachment.id)
}

const removeNewAttachment = (index) => {
  editTaskForm.value.newAttachments.splice(index, 1)
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>
