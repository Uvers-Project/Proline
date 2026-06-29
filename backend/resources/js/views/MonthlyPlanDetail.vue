<template>
  <div class="min-h-screen bg-[#F8FAFC] font-sans text-gray-900 selection:bg-blue-100 pb-12">
    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <div class="flex items-center space-x-4">
             <button @click="router.push(`/projects/${projectId}`)" class="p-2 text-gray-400 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-full transition-all">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
             </button>
             <h1 class="text-xl font-black text-gray-900 tracking-tight">
                {{ monthName }} <span class="text-gray-400 font-medium hidden sm:inline">| Monthly Plan Matrix</span>
             </h1>
          </div>
          <button @click="showNewTaskModal = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-sm transition-colors">
            + Add Task
          </button>
        </div>
      </div>
    </nav>

    <main class="w-full h-[calc(100vh-64px)] overflow-hidden flex flex-col" v-if="plan">
      
      <!-- Kanban Board -->
      <div class="flex-1 overflow-x-auto overflow-y-hidden p-6">
        <div class="flex items-start gap-6 h-full min-w-max pb-4">
          
          <!-- Column: Backlog -->
          <div class="w-80 flex flex-col bg-gray-50/80 rounded-3xl border border-gray-100 shadow-sm overflow-hidden max-h-full">
            <div class="px-5 py-4 border-b border-gray-200 bg-white/50 backdrop-blur-sm flex justify-between items-center shrink-0">
              <div class="flex items-center space-x-2">
                <div class="w-2.5 h-2.5 rounded-full bg-gray-400"></div>
                <h3 class="font-black text-gray-800 tracking-tight">Backlog</h3>
              </div>
              <span class="text-xs font-bold bg-white border border-gray-200 px-2 py-0.5 rounded-full text-gray-500">{{ kanbanColumns.backlog.length }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
              <div v-for="t in kanbanColumns.backlog" :key="'b'+t.id" class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300 transition-all cursor-pointer h-max" @click="openTaskDetail(t)">
                <div class="flex items-start justify-between mb-2">
                  <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border shadow-sm" :class="categoryBadgeClass(t.category)">
                    {{ t.category || 'Development' }}
                  </span>
                  <div class="flex items-center space-x-2">
                     <button @click.stop="confirmDeleteTask(t)" class="text-gray-300 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-red-50" title="Delete Task">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                     </button>
                  </div>
                </div>
                <h4 class="font-bold text-sm text-gray-900 leading-snug">{{ t.title }}</h4>
                <div class="mt-3 flex items-center justify-between">
                  <span class="text-[11px] font-bold" :class="priorityTextClass(t.priority)">{{ t.priority || 'Medium' }}</span>
                  <div v-if="t.assigned_to" class="w-6 h-6 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-[9px] font-bold text-indigo-700 shadow-sm cursor-help" :title="getUserName(t.assigned_to)">{{ getUserInitials(t.assigned_to) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Column: In Progress -->
          <div class="w-80 flex flex-col bg-blue-50/50 rounded-3xl border border-blue-100 shadow-sm overflow-hidden max-h-full">
            <div class="px-5 py-4 border-b border-blue-100 bg-white/50 backdrop-blur-sm flex justify-between items-center shrink-0">
              <div class="flex items-center space-x-2">
                <div class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></div>
                <h3 class="font-black text-blue-900 tracking-tight">In Progress</h3>
              </div>
              <span class="text-xs font-bold bg-white border border-blue-100 px-2 py-0.5 rounded-full text-blue-600">{{ kanbanColumns.in_progress.length }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
              <div v-for="t in kanbanColumns.in_progress" :key="'ip'+t.id" class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm hover:shadow-md hover:border-blue-300 transition-all cursor-pointer h-max" @click="openTaskDetail(t)">
                <div class="flex items-start justify-between mb-2">
                  <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border shadow-sm" :class="categoryBadgeClass(t.category)">
                    {{ t.category || 'Development' }}
                  </span>
                  <div class="flex items-center space-x-2">
                    <div class="flex gap-1">
                      <span v-for="wk in t.weeks" :key="wk" class="bg-blue-50 text-blue-700 border border-blue-200 text-[9px] font-bold px-1.5 py-0.5 rounded">Wk {{wk}}</span>
                    </div>
                     <button @click.stop="confirmDeleteTask(t)" class="text-gray-300 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-red-50" title="Delete Task">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                     </button>
                  </div>
                </div>
                <h4 class="font-bold text-sm text-gray-900 leading-snug">{{ t.title }}</h4>
                <div class="mt-3 flex items-center justify-between">
                  <span class="text-[11px] font-bold" :class="priorityTextClass(t.priority)">{{ t.priority || 'Medium' }}</span>
                  <div v-if="t.assigned_to" class="w-6 h-6 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-[9px] font-bold text-indigo-700 shadow-sm cursor-help" :title="getUserName(t.assigned_to)">{{ getUserInitials(t.assigned_to) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Column: Blocked -->
          <div class="w-80 flex flex-col bg-red-50/50 rounded-3xl border border-red-100 shadow-sm overflow-hidden max-h-full">
            <div class="px-5 py-4 border-b border-red-100 bg-white/50 backdrop-blur-sm flex justify-between items-center shrink-0">
              <div class="flex items-center space-x-2">
                <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                <h3 class="font-black text-red-900 tracking-tight">Blocked / Issues</h3>
              </div>
              <span class="text-xs font-bold bg-white border border-red-100 px-2 py-0.5 rounded-full text-red-600">{{ kanbanColumns.blocked.length }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
              <div v-for="t in kanbanColumns.blocked" :key="'bl'+t.id" class="bg-white p-4 rounded-2xl border border-red-200 shadow-sm hover:shadow-md hover:border-red-400 transition-all cursor-pointer h-max" @click="openTaskDetail(t)">
                <div class="flex items-start justify-between mb-2">
                  <span v-if="!t.is_problem_only" class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border shadow-sm" :class="categoryBadgeClass(t.category)">
                    {{ t.category || 'Development' }}
                  </span>
                  <span v-else class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 border border-transparent opacity-0">Issue</span>
                  <div class="flex items-center space-x-2">
                    <div class="flex gap-1">
                      <span v-for="wk in t.weeks" :key="wk" class="bg-red-50 text-red-700 border border-red-200 text-[9px] font-bold px-1.5 py-0.5 rounded">Wk {{wk}}</span>
                    </div>
                     <button @click.stop="confirmDeleteTask(t)" class="text-gray-300 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-red-50" title="Delete Task">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                     </button>
                  </div>
                </div>
                
                <h4 class="font-bold text-sm text-gray-900 leading-snug">{{ t.title }}</h4>
                
                <div class="mt-3 flex items-center justify-between">
                  <span class="text-[11px] font-bold" :class="priorityTextClass(t.priority)">{{ t.priority || 'Medium' }}</span>
                  <div v-if="t.assigned_to" class="w-6 h-6 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-[9px] font-bold text-indigo-700 shadow-sm cursor-help" :title="getUserName(t.assigned_to)">{{ getUserInitials(t.assigned_to) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Column: Done -->
          <div class="w-80 flex flex-col bg-green-50/50 rounded-3xl border border-green-100 shadow-sm overflow-hidden max-h-full">
            <div class="px-5 py-4 border-b border-green-100 bg-white/50 backdrop-blur-sm flex justify-between items-center shrink-0">
              <div class="flex items-center space-x-2">
                <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                <h3 class="font-black text-green-900 tracking-tight">Done</h3>
              </div>
              <span class="text-xs font-bold bg-white border border-green-100 px-2 py-0.5 rounded-full text-green-600">{{ kanbanColumns.done.length }}</span>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
              <div v-for="t in kanbanColumns.done" :key="'d'+t.id" class="bg-white p-4 rounded-2xl border border-green-100 shadow-sm hover:shadow-md hover:border-green-300 transition-all cursor-pointer opacity-70 hover:opacity-100 h-max" @click="openTaskDetail(t)">
                <div class="flex items-start justify-between mb-2">
                  <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border shadow-sm" :class="categoryBadgeClass(t.category)">
                    {{ t.category || 'Development' }}
                  </span>
                  <div class="flex items-center space-x-2">
                    <div class="bg-green-100 text-green-700 rounded-full px-2 py-0.5 text-[9px] font-bold flex items-center shadow-sm">
                      <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                      Done
                    </div>
                     <button @click.stop="confirmDeleteTask(t)" class="text-gray-300 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-red-50" title="Delete Task">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                     </button>
                  </div>
                </div>
                <h4 class="font-bold text-sm text-gray-900 leading-snug">{{ t.title }}</h4>
                <div class="mt-3 flex items-center justify-between">
                  <span class="text-[11px] font-bold" :class="priorityTextClass(t.priority)">{{ t.priority || 'Medium' }}</span>
                  <div v-if="t.assigned_to" class="w-6 h-6 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-[9px] font-bold text-indigo-700 shadow-sm cursor-help" :title="getUserName(t.assigned_to)">{{ getUserInitials(t.assigned_to) }}</div>
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>
      
    </main>

    <!-- Loading State -->
    <div v-else-if="isLoading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>
    <div v-else class="text-center py-12 text-gray-500">
      Plan not found.
    </div>

    <!-- New Task Modal -->
    <div v-if="showNewTaskModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 animate-in zoom-in-95 duration-200">
        <h3 class="text-xl font-bold mb-4 text-gray-900">Add Task for {{ monthName }}</h3>
        
        <div class="space-y-4">
          <div>
             <label class="block text-sm font-medium text-gray-700 mb-1">Task Title</label>
             <input v-model="newTask.title" placeholder="What needs to be done?" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" />
          </div>
          <div>
             <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
             <textarea v-model="newTask.description" placeholder="Add some details..." rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
          </div>
          <div class="flex gap-4">
            <div class="flex-1">
               <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
               <div class="relative">
                 <select v-model="newTask.priority" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white appearance-none pr-10">
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                 </select>
                 <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                 </div>
               </div>
            </div>
            <div class="flex-1">
               <label class="block text-sm font-medium text-gray-700 mb-1">Category (Phase)</label>
               <div class="relative">
                 <select v-model="newTask.category" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white appearance-none pr-10">
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
        </div>
        
        <div class="flex justify-end space-x-3 mt-8">
          <button @click="showNewTaskModal = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-medium transition-colors">Cancel</button>
          <button @click="createTask" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium shadow-sm transition-colors shadow-blue-500/30">Save Task</button>
        </div>
      </div>
    </div>

    <!-- Task Detail Sidebar -->
    <div v-if="showTaskDetailModal && selectedTask" class="fixed inset-0 z-50 flex justify-end">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-gray-900/20 backdrop-blur-sm transition-opacity" @click="showTaskDetailModal = false"></div>
      
      <!-- Sidebar Panel -->
      <div class="relative w-full max-w-md bg-white h-full shadow-2xl flex flex-col animate-in slide-in-from-right-full duration-300 border-l border-gray-100">
        
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
          <h2 class="text-lg font-black text-gray-900 tracking-tight">Task Details</h2>
          <button @click="showTaskDetailModal = false" class="text-gray-400 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-full p-1.5 transition-colors border border-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto">
          <div class="p-6 space-y-6">
            
            <!-- Status Badge & Edit Toggle -->
            <div class="flex justify-between items-center">
              <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md" :class="selectedTask.is_problem_only ? 'bg-red-100 text-red-700' : (selectedTask.status === 'Done' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600')">
                {{ selectedTask.is_problem_only ? 'BLOCKED / ISSUES' : (selectedTask.status || 'BACKLOG') }}
              </span>
              <button v-if="!isEditingTask" @click="startEditing" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                {{ selectedTask.is_problem_only ? 'Edit Issue' : 'Edit Task' }}
              </button>
            </div>

            <!-- View Mode -->
            <template v-if="!isEditingTask">
              <div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2 leading-tight">{{ selectedTask.title }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">
                  <template v-if="selectedTask.is_problem_only">
                    {{ selectedTask.problems[0] }}
                  </template>
                  <template v-else>
                    {{ selectedTask.description || 'No description provided.' }}
                  </template>
                </p>
              </div>

              <!-- Properties Box -->
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
                  <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Target Week</span>
                  <span class="text-xs font-bold text-gray-900 bg-white border border-gray-200 px-2 py-1 rounded shadow-sm">
                    {{ selectedTask.weeks && selectedTask.weeks.length ? selectedTask.weeks.map(w => 'Week ' + w).join(', ') : 'Unscheduled' }}
                  </span>
                </div>
                
                <div v-if="!selectedTask.is_problem_only" class="flex justify-between items-center">
                  <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Phase / Lane</span>
                  <span class="text-sm font-bold text-gray-900">{{ selectedTask.category || 'Development' }}</span>
                </div>

                <div class="flex justify-between items-center">
                  <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Priority</span>
                  <span class="text-sm font-bold" :class="priorityTextClass(selectedTask.priority)">{{ selectedTask.priority || 'Medium' }}</span>
                </div>

              </div>
              
              <!-- Problems Section (only for normal tasks that have problems) -->
              <div v-if="!selectedTask.is_problem_only && selectedTask.problems && selectedTask.problems.length > 0" class="mt-6">
                <h4 class="text-[10px] font-bold text-red-600 uppercase tracking-wider mb-3 flex items-center">
                  <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                  Reported Issues
                </h4>
                <div class="space-y-2">
                  <div v-for="(prob, idx) in selectedTask.problems" :key="idx" class="bg-red-50 border border-red-100 rounded-xl p-3 text-sm text-red-800 leading-relaxed font-medium">
                    {{ prob }}
                  </div>
                </div>
              </div>
            </template>

            <!-- Edit Mode -->
            <template v-else>
              <div class="space-y-4">
                <template v-if="selectedTask.is_problem_only">
                  <div>
                     <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Issue Title</label>
                     <input v-model="editTaskForm.title" class="w-full text-lg font-bold bg-white border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm" placeholder="Issue Title" />
                  </div>
                  <div>
                     <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Description</label>
                     <textarea v-model="editTaskForm.problems[0]" rows="5" class="w-full p-4 bg-white border border-gray-200 rounded-xl text-gray-700 text-sm leading-relaxed focus:ring-2 focus:ring-blue-500 outline-none shadow-sm" placeholder="Add description here..."></textarea>
                  </div>
                </template>
                <template v-else>
                  <div>
                     <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Task Title</label>
                     <input v-model="editTaskForm.title" class="w-full text-lg font-bold bg-white border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm" placeholder="Task Title" />
                  </div>
                  <div>
                     <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Description</label>
                     <textarea v-model="editTaskForm.description" rows="5" class="w-full p-4 bg-white border border-gray-200 rounded-xl text-gray-700 text-sm leading-relaxed focus:ring-2 focus:ring-blue-500 outline-none shadow-sm" placeholder="Add description here..."></textarea>
                  </div>
                  <div class="flex gap-4">
                    <div class="flex-1">
                       <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Priority</label>
                       <div class="relative">
                         <select v-model="editTaskForm.priority" class="w-full text-sm font-bold bg-white border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm appearance-none pr-10">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                         </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                         </div>
                       </div>
                    </div>
                    <div class="flex-1">
                       <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Phase</label>
                       <div class="relative">
                         <select v-model="editTaskForm.category" class="w-full text-sm font-bold bg-white border border-gray-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm appearance-none pr-10">
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
                </template>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTaskStore } from '../stores/task'
import { useProjectStore } from '../stores/project'
import { useAuthStore } from '../stores/auth'
import { useModal } from '../composables/useModal'
import { useToast } from '../composables/useToast'

const route = useRoute()
const router = useRouter()
const taskStore = useTaskStore()
const projectStore = useProjectStore()
const authStore = useAuthStore()
const modal = useModal()
const toast = useToast()

const projectId = route.params.id
const planId = parseInt(route.params.planId)

const isLoading = ref(true)

onMounted(async () => {
  isLoading.value = true
  if (taskStore.monthlyPlans.length === 0) {
    await taskStore.fetchMonthlyPlans(projectId)
  }
  if (taskStore.weeklyMeetings.length === 0) {
    await taskStore.fetchWeeklyMeetings(projectId)
  }
  if (authStore.allUsers.length === 0) {
    await authStore.fetchAllUsers()
  }
  isLoading.value = false
})

const getUserInitials = (userId) => {
  const user = authStore.allUsers.find(u => u.id === userId)
  if (!user || !user.name) return 'PIC'
  return user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
}

const getUserName = (userId) => {
  const user = authStore.allUsers.find(u => u.id === userId)
  return user ? user.name : 'Unknown User'
}

const plan = computed(() => {
  return taskStore.monthlyPlans.find(p => p.id === planId)
})

const monthName = computed(() => {
  if (!plan.value) return ''
  return new Date(plan.value.year, plan.value.month - 1).toLocaleString('default', { month: 'long', year: 'numeric' })
})

const sortedTasks = computed(() => {
  return [...(plan.value?.tasks || [])].sort((a,b) => {
    if (a.status === 'Done' && b.status !== 'Done') return 1;
    if (a.status !== 'Done' && b.status === 'Done') return -1;
    return a.order - b.order
  })
})

const monthMeetings = computed(() => {
  if (!plan.value) return []
  return taskStore.weeklyMeetings.filter(m => m.monthly_plan_id === plan.value.id)
})

const matchTitle = (p, task) => {
  const pt = p.title?.toLowerCase() || ''
  const tt = task.title?.toLowerCase() || ''
  if (!pt || !tt) return false
  return pt.includes(tt) || tt.includes(pt)
}

const getCellData = (task, weekNumber) => {
  const meeting = monthMeetings.value.find(m => m.week_number === weekNumber)
  if (!meeting) return null
  
  const taskInMeeting = meeting.tasks?.find(t => t.id === task.id)
  const rel = taskInMeeting ? taskInMeeting.pivot.relation_type : null
  const userIdFromTask = taskInMeeting ? taskInMeeting.pivot.user_id : null
  
  const problems = meeting.progress?.filter(p => p.type === 'problem' && matchTitle(p, task)) || []
  const progresses = meeting.progress?.filter(p => (p.type === 'progress' || p.type === 'next_todo') && matchTitle(p, task)) || []
  
  if (!rel && problems.length === 0 && progresses.length === 0) return null
  
  let finalUserId = userIdFromTask;
  if (!finalUserId && problems.length > 0) finalUserId = problems[0].user_id;
  if (!finalUserId && progresses.length > 0) finalUserId = progresses[0].user_id;

  return {
    relation: rel,
    userId: finalUserId,
    problems: problems.map(p => p.content || p.title),
    progresses: progresses.map(p => p.content || p.title)
  }
}

const kanbanColumns = computed(() => {
  const cols = {
    backlog: [],
    in_progress: [],
    blocked: [],
    done: []
  };

  sortedTasks.value.forEach(task => {
    let isDone = task.status === 'Done';
    let isBlocked = false;
    let isInProgress = false;
    let weeksActive = [];
    let allProblems = [];
    let assignedUserId = null;
    let completedWeek = null;

    for (let w = 1; w <= 5; w++) {
      const data = getCellData(task, w);
      if (data) {
        if (data.userId) assignedUserId = data.userId;
        weeksActive.push(w);
        if (data.relation === 'completed') {
          isDone = true;
          completedWeek = w;
        }
        if (data.problems && data.problems.length > 0) {
          isBlocked = true;
          allProblems.push(...data.problems);
        }
        if (data.relation && data.relation !== 'completed') {
          isInProgress = true;
        }
        if (data.progresses && data.progresses.length > 0) {
          isInProgress = true;
        }
      }
    }

    if (isDone && completedWeek !== null) {
      weeksActive = [completedWeek];
    }

    const taskData = {
      ...task,
      weeks: weeksActive,
      problems: allProblems,
      assigned_to: assignedUserId
    };

    if (isDone) {
      cols.done.push(taskData);
    } else if (isBlocked) {
      cols.blocked.push(taskData);
    } else if (isInProgress) {
      cols.in_progress.push(taskData);
    } else {
      cols.backlog.push(taskData);
    }
  });

  // Also gather "General Problems" (problems that didn't match any task)
  const unattachedProblems = [];
  monthMeetings.value.forEach(m => {
    if (m.progress) {
      m.progress.filter(p => p.type === 'problem').forEach(p => {
        let matched = false;
        sortedTasks.value.forEach(task => {
          if (matchTitle(p, task)) matched = true;
        });
        
        if (!matched) {
            unattachedProblems.push({
              id: 'prob_' + p.id,
              original_id: p.id,
            is_problem_only: true,
            title: p.title,
            problems: [p.content || p.title],
            priority: 'High',
            category: 'General',
            weeks: [m.week_number],
            assigned_to: p.user_id
          });
        }
      });
    }
  });

  cols.blocked.push(...unattachedProblems);

  return cols;
});

const categoryBadgeClass = (cat) => {
  switch(cat) {
    case 'Research': return 'bg-purple-50 text-purple-700 border-purple-200'
    case 'Design': return 'bg-pink-50 text-pink-700 border-pink-200'
    case 'Development': return 'bg-blue-50 text-blue-700 border-blue-200'
    case 'Testing': return 'bg-orange-50 text-orange-700 border-orange-200'
    case 'Review': return 'bg-teal-50 text-teal-700 border-teal-200'
    case 'Release': return 'bg-green-50 text-green-700 border-green-200'
    default: return 'bg-gray-50 text-gray-700 border-gray-200'
  }
}

const showNewTaskModal = ref(false)
const newTask = ref({ title: '', description: '', priority: 'Medium', category: 'Development' })

const showTaskDetailModal = ref(false)
const selectedTask = ref(null)
const isEditingTask = ref(false)
const editTaskForm = ref({ title: '', description: '', priority: '', category: '' })
const isSavingTask = ref(false)

const openTaskDetail = (task) => {
  selectedTask.value = task
  isEditingTask.value = false
  showTaskDetailModal.value = true
}

const startEditing = () => {
  editTaskForm.value = { ...selectedTask.value }
  if (!editTaskForm.value.category) editTaskForm.value.category = 'Development'
  if (!editTaskForm.value.priority) editTaskForm.value.priority = 'Medium'
  isEditingTask.value = true
}

const cancelEditing = () => {
  isEditingTask.value = false
}

const saveEditedTask = async () => {
  if (!editTaskForm.value.title) return
  isSavingTask.value = true
  try {
    if (selectedTask.value.is_problem_only) {
      await taskStore.updateWeeklyProgress(projectId, selectedTask.value.original_id, {
        title: editTaskForm.value.title,
        content: editTaskForm.value.problems[0]
      })
      Object.assign(selectedTask.value, editTaskForm.value)
    } else {
      const updatedTask = await taskStore.updateMonthlyTask(projectId, selectedTask.value.id, editTaskForm.value)
      Object.assign(selectedTask.value, updatedTask)
    }
    isEditingTask.value = false
  } catch (error) {
    toast.showToast('Failed to update.', 'Error')
  } finally {
    isSavingTask.value = false
  }
}

const confirmDeleteTask = async (task) => {
  if (task.is_problem_only) {
    toast.showToast('You cannot delete an unattached issue from here.', 'Error')
    return
  }
  
  if (await modal.showConfirm('Are you sure you want to delete this task? This action cannot be undone.', 'Delete Task', 'danger')) {
    try {
      await taskStore.deleteMonthlyTask(projectId, plan.value.id, task.id)
    } catch (error) {
      toast.showToast('Failed to delete task', 'Error')
    }
  }
}

const createTask = async () => {
  if (!newTask.value.title) return
  
  await taskStore.createMonthlyTask(projectId, {
    ...newTask.value,
    monthly_plan_id: plan.value.id,
    status: 'Planned',
    target_week: null
  })
  
  showNewTaskModal.value = false
  newTask.value = { title: '', description: '', priority: 'Medium', category: 'Development' }
}

const priorityTextClass = (priority) => {
  switch(priority) {
    case 'High': return 'text-red-600'
    case 'Medium': return 'text-amber-600'
    case 'Low': return 'text-emerald-600'
    default: return 'text-gray-500'
  }
}

const detailHeaderClass = (priority) => {
  switch(priority) {
    case 'High': return 'bg-red-100/50'
    case 'Medium': return 'bg-amber-100/50'
    case 'Low': return 'bg-emerald-100/50'
    default: return 'bg-gray-50'
  }
}
</script>
