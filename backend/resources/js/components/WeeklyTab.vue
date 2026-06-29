<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
      <h3 class="text-2xl font-black text-gray-800 tracking-tight">Weekly Reports</h3>
      <button @click="openNextWeekModal" :disabled="isSubmitting" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-colors text-sm shadow-sm flex justify-center items-center disabled:opacity-50">
        <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span>
        <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        Generate Weekly Report
      </button>
    </div>


    <div class="flex justify-end mb-4">
      <button @click="deleteAllMeetings" class="text-xs font-bold bg-red-100 text-red-700 px-3 py-1.5 rounded-lg border border-red-200 hover:bg-red-200 transition-colors shadow-sm">
        DEV: Delete All Reports
      </button>
    </div>

    <!-- List of previous weekly meetings/reports -->
    <div v-if="taskStore.weeklyMeetings.length === 0" class="text-center py-12 bg-gray-50 rounded-2xl border border-gray-100">
      <p class="text-gray-500 font-medium">No weekly reports found for this project.</p>
    </div>
    <div v-else class="space-y-6">
      <div v-for="(meeting, index) in taskStore.weeklyMeetings" :key="meeting.id" class="p-6 bg-white border border-gray-200 rounded-3xl shadow-sm hover:shadow-md transition-all group cursor-pointer" @click="openDetailModal(meeting)">
        <div class="flex justify-between items-start mb-2">
          <div class="flex items-center space-x-3">
            <span class="px-3 py-1 bg-gradient-to-r from-violet-100 to-fuchsia-100 text-violet-700 rounded-full text-xs font-black uppercase tracking-widest shadow-sm">Week {{ meeting.week_number }}</span>
            <h4 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Sprint Report</h4>
          </div>
          <div class="flex space-x-2">

            <button v-if="index === 0" @click.stop="deleteMeeting(meeting.id)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
          </div>
        </div>
        <p class="text-sm font-medium text-gray-500 flex items-center mb-4">
          <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          {{ new Date(meeting.date_range_start).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) }} - {{ new Date(meeting.date_range_end).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }) }}
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Finished Tasks Summary -->
          <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100/50">
            <h5 class="text-xs font-black text-emerald-800 uppercase tracking-widest mb-2 flex items-center">
              <svg class="w-4 h-4 mr-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Completed Tasks
            </h5>
            <div class="flex flex-col gap-2">
              <template v-for="group in getGroupedTasksByType(meeting, 'completed')" :key="group.parent.id">
                <!-- Parent Task -->
                <div class="bg-white border border-emerald-100/60 rounded-lg p-2.5 shadow-sm">
                  <div class="flex items-start">
                    <svg class="w-3.5 h-3.5 mt-0.5 mr-2 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="flex flex-col flex-1 min-w-0">
                      <span class="text-xs font-bold text-gray-800 truncate">{{ group.parent.title }}</span>
                      <span v-if="group.parent.description" class="text-[10px] text-gray-500 mt-0.5 line-clamp-1">{{ group.parent.description }}</span>
                    </div>
                  </div>
                  <!-- Subtasks nested under parent -->
                  <div v-if="group.children.length > 0" class="mt-2 ml-5 pl-3 flex flex-col gap-1.5">
                    <div v-for="child in group.children" :key="child.id" class="flex items-start">
                      <svg class="w-3 h-3 mt-0.5 mr-1.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                      <div class="flex flex-col flex-1 min-w-0">
                        <span class="text-[11px] font-semibold text-gray-700 truncate">{{ child.title }}</span>
                        <span v-if="child.description" class="text-[10px] text-gray-400 mt-0.5 line-clamp-1">{{ child.description }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
          <!-- Blocked Tasks Summary -->
          <div class="bg-rose-50/50 p-4 rounded-xl border border-rose-100/50">
             <h5 class="text-xs font-black text-rose-800 uppercase tracking-widest mb-2 flex items-center">
               <svg class="w-4 h-4 mr-1.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
               Blocked Tasks
             </h5>
             <div class="flex flex-col gap-2">
               <template v-for="group in getGroupedTasksByType(meeting, 'blocked')" :key="'blk-'+group.parent.id">
                 <div class="bg-white border border-rose-100/60 rounded-lg p-2.5 shadow-sm">
                   <div class="flex items-start">
                     <svg class="w-3.5 h-3.5 mt-0.5 mr-2 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                     <div class="flex flex-col flex-1 min-w-0">
                       <span class="text-xs font-bold text-gray-800 truncate">{{ group.parent.title }}</span>
                       <span v-if="group.parent.description" class="text-[10px] text-gray-500 mt-0.5 line-clamp-1">{{ group.parent.description }}</span>
                     </div>
                   </div>
                   <div v-if="group.children.length > 0" class="mt-2 ml-5 pl-3 flex flex-col gap-1.5">
                     <div v-for="child in group.children" :key="'blk-ch-'+child.id" class="flex items-start">
                       <svg class="w-3 h-3 mt-0.5 mr-1.5 shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                       <div class="flex flex-col flex-1 min-w-0">
                         <span class="text-[11px] font-semibold text-gray-700 truncate">{{ child.title }}</span>
                         <span v-if="child.description" class="text-[10px] text-gray-400 mt-0.5 line-clamp-1">{{ child.description }}</span>
                       </div>
                     </div>
                   </div>
                 </div>
               </template>
               <div v-if="getGroupedTasksByType(meeting, 'blocked').length === 0" class="text-[11px] text-gray-500 italic">None reported</div>
             </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Next Week Targets Modal -->
    <div v-if="showNextWeekModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto py-10">
      <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl flex flex-col h-[80vh] animate-in zoom-in-95 duration-200">
        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center shrink-0">
          <div>
            <h3 class="text-2xl font-black text-gray-900">Next Week Targets</h3>
            <p class="text-sm text-gray-500 mt-1">Select the tasks you plan to work on next week.</p>
          </div>
          <button @click="showNextWeekModal = false" class="text-gray-400 hover:text-gray-600 bg-gray-100 p-2 rounded-full transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="flex-1 p-8 overflow-y-auto bg-gray-50/50">
          <div class="space-y-4">
            <div v-if="availableForNextWeek.length === 0" class="text-sm text-gray-500 italic text-center p-12 bg-gray-50 rounded-2xl border border-dashed border-gray-200">No available tasks found.</div>
            <div v-else class="space-y-3">
              <div v-for="task in availableForNextWeek" :key="'next-'+task.id" class="border rounded-xl overflow-hidden shadow-sm" :class="selectedNextWeekTasks.includes(task.id) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white'">
                <label class="flex items-start p-3 cursor-pointer hover:bg-gray-50/50 transition-colors">
                  <input type="checkbox" :value="task.id" v-model="selectedNextWeekTasks" class="w-5 h-5 mt-0.5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-4 shrink-0">
                  <div class="flex-1">
                    <div class="flex items-center gap-2">
                      <span class="text-sm font-bold block" :class="selectedNextWeekTasks.includes(task.id) ? 'text-blue-900' : 'text-gray-900'">{{ task.title }}</span>
                      <span v-if="task.status === 'Blocked'" class="text-[10px] font-bold bg-red-50 text-red-600 px-1.5 py-0.5 rounded border border-red-100 flex items-center">Blocked</span>
                    </div>
                    <span class="block text-xs mt-0.5 text-gray-500">{{ task.category || 'General' }} &bull; {{ task.priority || 'Medium' }}</span>
                  </div>
                </label>
                
                <div v-if="getAvailableSubtasksForNextWeek(task).length > 0" class="pl-12 pb-3 pr-3 space-y-1 border-t border-gray-100 pt-2" :class="selectedNextWeekTasks.includes(task.id) ? 'bg-blue-50/30' : 'bg-gray-50/50'">
                  <label v-for="sub in getAvailableSubtasksForNextWeek(task)" :key="'next-sub-'+sub.id" class="flex items-center hover:bg-white p-2 rounded-lg transition-colors cursor-pointer" :class="selectedNextWeekTasks.includes(sub.id) ? 'bg-blue-100/50' : ''">
                    <input type="checkbox" :value="sub.id" v-model="selectedNextWeekTasks" class="w-4 h-4 text-blue-500 rounded border-gray-300 focus:ring-blue-500 mr-3 shrink-0">
                    <div class="flex items-center gap-2">
                       <span class="text-sm" :class="selectedNextWeekTasks.includes(sub.id) ? 'text-blue-900 font-medium' : 'text-gray-700'">{{ sub.title }}</span>
                       <span v-if="sub.status === 'Blocked'" class="text-[9px] font-bold bg-red-50 text-red-600 px-1 py-0.5 rounded border border-red-100">Blocked</span>
                    </div>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="px-8 py-5 border-t border-gray-100 bg-white flex justify-end space-x-4 shrink-0 rounded-b-3xl">
          <button @click="showNextWeekModal = false" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-bold transition-colors">Cancel</button>
          <button @click="generateWeeklyReport" :disabled="isSubmitting" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-sm transition-colors flex items-center disabled:opacity-70">
            <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span>
            Generate Report
          </button>
        </div>
      </div>
    </div>


    <!-- Detail Modal -->
    <div v-if="selectedMeetingDetails" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="selectedMeetingDetails = null">
      <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl flex flex-col h-[85vh] animate-in zoom-in-95 duration-200">
        <div class="px-8 py-6 border-b border-slate-300 flex justify-between items-center shrink-0 bg-slate-50 rounded-t-3xl">
          <div>
            <h3 class="text-2xl font-black text-slate-900">Weekly Report Details</h3>
            <p class="text-sm text-slate-500 mt-1">Week {{ selectedMeetingDetails.week_number }}</p>
          </div>
          <div class="flex items-center gap-2">

            <button @click="selectedMeetingDetails = null" class="text-gray-400 hover:text-gray-600 bg-gray-100 p-2 rounded-full transition-colors border border-gray-200">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
        </div>
        <div class="flex-1 p-8 overflow-y-auto space-y-8">
          
          <div>
            <h4 class="text-lg font-bold text-gray-900 mb-3 border-b border-gray-200 pb-2">Completed Tasks</h4>
            <div class="space-y-3">
              <template v-for="group in getGroupedTasksByType(selectedMeetingDetails, 'completed')" :key="group.parent.id">
                <!-- Parent Task Block -->
                <div class="rounded-xl border border-gray-200 overflow-hidden">
                  <div class="flex items-start p-3 bg-gray-50">
                    <svg class="w-5 h-5 mt-0.5 text-emerald-500 shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <div class="flex flex-col">
                      <span class="text-sm font-bold text-gray-800">{{ group.parent.title }}</span>
                      <span v-if="group.parent.description" class="text-xs text-gray-500 mt-0.5">{{ group.parent.description }}</span>
                    </div>
                  </div>
                  <!-- Subtasks -->
                  <div v-if="group.children.length > 0" class="divide-y divide-gray-200">
                    <div v-for="child in group.children" :key="child.id" class="flex items-start px-4 py-2.5 bg-white pl-10">
                      <div class="flex items-center mr-2 shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8m0 0l-4-4m4 4l-4 4"></path></svg>
                      </div>
                      <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-700">{{ child.title }}</span>
                        <span v-if="child.description" class="text-xs text-gray-400 mt-0.5">{{ child.description }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
              <div v-if="getTasksByType(selectedMeetingDetails, 'completed').length === 0" class="text-sm text-gray-500 italic">None reported</div>
            </div>
          </div>
          
          <div>
            <h4 class="text-lg font-bold text-gray-900 mb-3 border-b border-gray-200 pb-2">
              Blocked Tasks
            </h4>
            <div class="space-y-3">
              <template v-for="group in getGroupedTasksByType(selectedMeetingDetails, 'blocked')" :key="'blocked-task-'+group.parent.id">
                <div class="rounded-xl border border-red-200 overflow-hidden">
                  <div class="flex items-start p-3 bg-red-50">
                    <svg class="w-5 h-5 mt-0.5 text-red-500 shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div class="flex flex-col">
                      <span class="text-sm font-bold text-red-900">{{ group.parent.title }}</span>
                      <span v-if="group.parent.description" class="text-xs text-red-600 mt-0.5">{{ group.parent.description }}</span>
                      <div v-if="group.parent.blocked_description" class="mt-2 bg-white/60 p-2.5 rounded-lg border border-red-100/50">
                        <span class="text-[11px] font-bold text-red-800 uppercase tracking-wider block mb-0.5">Blocker Note</span>
                        <span class="text-xs text-red-700">{{ group.parent.blocked_description }}</span>
                      </div>
                    </div>
                  </div>
                  <div v-if="group.children.length > 0" class="divide-y divide-red-100 bg-white">
                    <div v-for="child in group.children" :key="child.id" class="flex items-start px-4 py-2.5 pl-10 border-t border-red-100">
                      <div class="flex items-center mr-2 shrink-0">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8m0 0l-4-4m4 4l-4 4"></path></svg>
                      </div>
                      <div class="flex flex-col">
                        <span class="text-sm font-medium text-red-700">{{ child.title }}</span>
                        <span v-if="child.description" class="text-xs text-red-400 mt-0.5">{{ child.description }}</span>
                        <div v-if="child.blocked_description" class="mt-2 bg-red-50/50 p-2.5 rounded-lg border border-red-100/50">
                          <span class="text-[10px] font-bold text-red-800 uppercase tracking-wider block mb-0.5">Blocker Note</span>
                          <span class="text-xs text-red-600">{{ child.blocked_description }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
              <div v-if="getGroupedTasksByType(selectedMeetingDetails, 'blocked').length === 0" class="text-sm text-gray-500 italic">None reported</div>
            </div>
          </div>



          <!-- Next To Do -->
          <div>
            <h4 class="text-lg font-bold text-gray-900 mb-3 border-b border-gray-200 pb-2">
              Next To Do
            </h4>
            <div class="space-y-3">
              <template v-for="group in getGroupedTasksByType(selectedMeetingDetails, 'next_todo')" :key="group.parent.id">
                <div class="rounded-xl border border-gray-200 overflow-hidden">
                  <div class="flex items-start p-3 bg-blue-50/50">
                    <svg class="w-5 h-5 mt-0.5 text-blue-500 shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                    <div class="flex flex-col">
                      <span class="text-sm font-bold text-gray-800">{{ group.parent.title }}</span>
                      <span v-if="group.parent.description" class="text-xs text-gray-500 mt-0.5">{{ group.parent.description }}</span>
                    </div>
                  </div>
                  <div v-if="group.children.length > 0" class="divide-y divide-gray-200">
                    <div v-for="child in group.children" :key="child.id" class="flex items-start px-4 py-2.5 bg-white pl-10">
                      <div class="flex items-center mr-2 shrink-0">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                      </div>
                      <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-700">{{ child.title }}</span>
                        <span v-if="child.description" class="text-xs text-gray-400 mt-0.5">{{ child.description }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
              <div v-if="getTasksByType(selectedMeetingDetails, 'next_todo').length === 0" class="text-sm text-gray-500 italic">None planned</div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="meetingToDelete" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6 animate-in zoom-in-95 duration-200 text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
          <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="text-xl font-black text-gray-900 mb-2">Delete Weekly Report?</h3>
        <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this historical report record? <br><br><strong>Note:</strong> Deleting this report will <strong>NOT</strong> change or revert any task statuses on your Kanban board.</p>
        <div class="flex space-x-3">
          <button @click="meetingToDelete = null" class="flex-1 px-4 py-2 bg-gray-100 text-gray-800 rounded-xl font-bold hover:bg-gray-200 transition-colors">Cancel</button>
          <button @click="confirmDeleteMeeting" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition-colors">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useTaskStore } from '../stores/task'
import { useProjectStore } from '../stores/project'
import { useAuthStore } from '../stores/auth'
import { useModal } from '../composables/useModal'
import { useToast } from '../composables/useToast'

const props = defineProps({
  projectId: [Number, String]
})

const taskStore = useTaskStore()
const projectStore = useProjectStore()
const authStore = useAuthStore()
const modal = useModal()
const toast = useToast()

const isSubmitting = ref(false)
const selectedMeetingDetails = ref(null)
const meetingToDelete = ref(null)
const showNextWeekModal = ref(false)
const selectedNextWeekTasks = ref([])

const availableForNextWeek = computed(() => {
  if (!taskStore.projectTasks) return []
  return taskStore.projectTasks.filter(t => 
    t.parent_id == null && 
    t.status !== 'Done' && 
    t.status !== 'Cancelled'
  )
})

const getAvailableSubtasksForNextWeek = (task) => {
  if (!task.subtasks) return []
  return task.subtasks.filter(sub => 
    sub.status !== 'Done' && 
    sub.status !== 'Cancelled'
  )
}

const reportedCompletedTasks = computed(() => {
  const reported = new Set();
  taskStore.weeklyMeetings.forEach(meeting => {
    if (meeting.tasks) {
      meeting.tasks.filter(t => t.pivot?.relation_type === 'completed').forEach(t => reported.add(t.id));
    }
  });
  return reported;
})

const generateWeeklyReport = async () => {
  isSubmitting.value = true;
  
  let weekNumber = 1;
  if (taskStore.weeklyMeetings && taskStore.weeklyMeetings.length > 0) {
    const maxWeek = Math.max(...taskStore.weeklyMeetings.map(m => m.week_number || 0));
    weekNumber = maxWeek + 1;
  }

  const completedTasks = [];
  const blockedTasks = [];
  const nextWeekTasks = [];
  const problems = [];
  
  if (taskStore.projectTasks) {
    taskStore.projectTasks.forEach(t => {
      // Main tasks
      if (t.status === 'Done' && !reportedCompletedTasks.value.has(t.id)) {
        completedTasks.push(t.id);
      } else if (t.status === 'Blocked') {
        blockedTasks.push(t.id);
        if (t.blocked_description && t.blocked_description.trim() !== '') {
          problems.push({ title: `Blocked: ${t.title}`, description: t.blocked_description.trim() });
        }
      } else if (t.status === 'In Progress') {
        nextWeekTasks.push(t.id);
      }
      
      // Subtasks
      if (t.subtasks) {
        t.subtasks.forEach(sub => {
          if (sub.status === 'Done' && !reportedCompletedTasks.value.has(sub.id)) {
            completedTasks.push(sub.id);
          } else if (sub.status === 'Blocked') {
            blockedTasks.push(sub.id);
            if (sub.blocked_description && sub.blocked_description.trim() !== '') {
              problems.push({ title: `Blocked: ${sub.title}`, description: sub.blocked_description.trim() });
            }
          }
        });
      }
    });
  }

  const form = {
    week_number: weekNumber,
    completed_tasks: completedTasks,
    problems: problems,
    unexpected_tasks: [],
    next_week_tasks: selectedNextWeekTasks.value,
    blocked_tasks: blockedTasks,
  }

  try {
    await taskStore.submitWeeklyReport(props.projectId, form)
    toast.showToast('Weekly report generated successfully!', 'Success')
    await taskStore.fetchWeeklyMeetings(props.projectId)
    showNextWeekModal.value = false
  } catch(e) {
    toast.showToast('Failed to generate report', 'Error')
  } finally {
    isSubmitting.value = false
  }
}

const openNextWeekModal = () => {
  selectedNextWeekTasks.value = []
  showNextWeekModal.value = true
}

const deleteMeeting = (meetingId) => {
  meetingToDelete.value = meetingId
}

const confirmDeleteMeeting = async () => {
  if (!meetingToDelete.value) return;
  try {
    await taskStore.deleteWeeklyReport(props.projectId, meetingToDelete.value)
    await taskStore.fetchWeeklyMeetings(props.projectId)
    meetingToDelete.value = null
    toast.showToast('Weekly report deleted successfully!', 'Success')
  } catch(e) {
    toast.showToast('Failed to delete report', 'Error')
  }
}

const deleteAllMeetings = async () => {
  if (confirm("DEV: Are you sure you want to delete ALL weekly reports? This is for development testing only.")) {
    try {
      await taskStore.deleteAllWeeklyReports(props.projectId)
      await taskStore.fetchWeeklyMeetings(props.projectId)
      toast.showToast('All weekly reports deleted successfully!', 'Success')
    } catch(e) {
      const msg = e.response?.data?.message || e.message;
      toast.showToast('Failed to delete: ' + msg, 'Error');
    }
  }
}

const openDetailModal = (meeting) => {
  selectedMeetingDetails.value = meeting
}

// Helpers for displaying meetings
const getTasksByType = (meeting, type) => {
  if (!meeting.tasks) return [];
  return meeting.tasks.filter(t => t.pivot?.relation_type === type);
}

// Groups tasks into { parent, children[] } structure for hierarchical display
const getGroupedTasksByType = (meeting, type) => {
  const tasks = getTasksByType(meeting, type);
  const parentMap = new Map();
  const orphans = [];

  tasks.forEach(t => {
    if (!t.parent_id) {
      parentMap.set(t.id, { parent: t, children: [] });
    }
  });

  tasks.forEach(t => {
    if (t.parent_id) {
      if (parentMap.has(t.parent_id)) {
        parentMap.get(t.parent_id).children.push(t);
      } else {
        orphans.push({ parent: t, children: [] });
      }
    }
  });

  return [...parentMap.values(), ...orphans];
}

onMounted(async () => {
  await taskStore.fetchWeeklyMeetings(props.projectId)
  if (!taskStore.projectTasks || taskStore.projectTasks.length === 0) {
    await taskStore.fetchProjectTasks(props.projectId)
  }
})
</script>
