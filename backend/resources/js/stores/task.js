import { defineStore } from 'pinia'
import axios from '../axios'

export const useTaskStore = defineStore('task', {
  state: () => ({
    monthlyPlans: [],
    projectTasks: [],
    dailyEntries: [],
    weeklyMeetings: [],
    loading: false,
  }),
  actions: {
    async fetchMonthlyPlans(projectId) {
      this.loading = true
      try {
        const response = await axios.get(`/api/v1/projects/${projectId}/monthly-plans`)
        this.monthlyPlans = response.data
      } catch (error) {
        console.error('Failed to fetch monthly plans', error)
      } finally {
        this.loading = false
      }
    },
    async createMonthlyPlan(projectId, data) {
      const response = await axios.post(`/api/v1/projects/${projectId}/monthly-plans`, data)
      this.monthlyPlans.push(response.data)
      return response.data
    },
    async deleteMonthlyPlan(projectId, planId) {
      await axios.delete(`/api/v1/projects/${projectId}/monthly-plans/${planId}`)
      this.monthlyPlans = this.monthlyPlans.filter(p => p.id !== planId)
    },
    async fetchProjectTasks(projectId, silent = false) {
      if (!silent) this.loading = true
      try {
        const response = await axios.get(`/api/v1/projects/${projectId}/tasks`)
        this.projectTasks = response.data
      } catch (error) {
        console.error('Failed to fetch project tasks', error)
      } finally {
        if (!silent) this.loading = false
      }
    },
    async createProjectTask(projectId, data) {
      const response = await axios.post(`/api/v1/projects/${projectId}/tasks`, data)
      if (!this.projectTasks) this.projectTasks = []
      this.projectTasks.push(response.data)
      return response.data
    },
    async updateProjectTask(projectId, taskId, data) {
      const response = await axios.put(`/api/v1/projects/${projectId}/tasks/${taskId}`, data)
      if (this.projectTasks) {
        const index = this.projectTasks.findIndex(t => t.id === taskId)
        if (index !== -1) {
          this.projectTasks[index] = response.data
        }
      }
      return response.data
    },
    async deleteProjectTask(projectId, taskId) {
      await axios.delete(`/api/v1/projects/${projectId}/tasks/${taskId}`)
      if (this.projectTasks) {
        this.projectTasks = this.projectTasks.filter(t => t.id !== taskId)
      }
    },
    async uploadTaskFile(projectId, taskId, file) {
      const formData = new FormData()
      formData.append('file', file)
      const response = await axios.post(`/api/v1/projects/${projectId}/tasks/${taskId}/attachments`, formData)
      if (this.projectTasks) {
        const taskIndex = this.projectTasks.findIndex(t => t.id === taskId)
        if (taskIndex !== -1) {
          if (!this.projectTasks[taskIndex].attachments) this.projectTasks[taskIndex].attachments = []
          this.projectTasks[taskIndex].attachments.push(response.data)
        }
      }
      return response.data
    },
    async deleteTaskFile(projectId, taskId, attachmentId) {
      await axios.delete(`/api/v1/projects/${projectId}/tasks/${taskId}/attachments/${attachmentId}`)
      if (this.projectTasks) {
        const taskIndex = this.projectTasks.findIndex(t => t.id === taskId)
        if (taskIndex !== -1 && this.projectTasks[taskIndex].attachments) {
          this.projectTasks[taskIndex].attachments = this.projectTasks[taskIndex].attachments.filter(a => a.id !== attachmentId)
        }
      }
    },
    async submitWeeklyReport(projectId, data) {
      const response = await axios.post(`/api/v1/projects/${projectId}/weekly-report`, data)
      await this.fetchProjectTasks(projectId)
      return response.data
    },
    async updateWeeklyReport(projectId, meetingId, data) {
      const response = await axios.put(`/api/v1/projects/${projectId}/weekly-report/${meetingId}`, data)
      await this.fetchProjectTasks(projectId)
      return response.data
    },
    async deleteWeeklyReport(projectId, meetingId) {
      await axios.delete(`/api/v1/projects/${projectId}/weekly-report/${meetingId}`)
      await this.fetchProjectTasks(projectId)
    },
    async deleteAllWeeklyReports(projectId) {
      await axios.delete(`/api/v1/projects/${projectId}/weekly-report`)
      await this.fetchProjectTasks(projectId)
    },
    // Keep old monthly methods for backwards compatibility if needed, but we mostly use ProjectTasks now
    async createMonthlyTask(projectId, data) {
      const response = await axios.post(`/api/v1/projects/${projectId}/monthly-tasks`, data)
      const plan = this.monthlyPlans.find(p => p.id === data.monthly_plan_id)
      if (plan) {
        if (!plan.tasks) plan.tasks = []
        plan.tasks.push(response.data)
      }
      return response.data
    },
    async updateMonthlyTask(projectId, taskId, data) {
      const response = await axios.put(`/api/v1/projects/${projectId}/monthly-tasks/${taskId}`, data)
      return response.data
    },
    async deleteMonthlyTask(projectId, planId, taskId) {
      await axios.delete(`/api/v1/projects/${projectId}/monthly-tasks/${taskId}`)
      const plan = this.monthlyPlans.find(p => p.id === planId)
      if (plan && plan.tasks) {
        plan.tasks = plan.tasks.filter(t => t.id !== taskId)
      }
    },
    async fetchDailyEntries(projectId) {
      this.loading = true
      try {
        const response = await axios.get(`/api/v1/projects/${projectId}/daily-entries`)
        this.dailyEntries = response.data
      } catch (error) {
        console.error('Failed to fetch daily entries', error)
      } finally {
        this.loading = false
      }
    },
    async createDailyEntry(projectId, data) {
      const response = await axios.post(`/api/v1/projects/${projectId}/daily-entries`, data)
      this.dailyEntries.unshift(response.data)
      return response.data
    },
    async fetchWeeklyMeetings(projectId) {
      this.loading = true
      try {
        const response = await axios.get(`/api/v1/projects/${projectId}/weekly-meetings`)
        this.weeklyMeetings = response.data
      } catch (error) {
        console.error('Failed to fetch weekly meetings', error)
      } finally {
        this.loading = false
      }
    },
    async updateWeeklyMeeting(projectId, meetingId, data) {
      const response = await axios.put(`/api/v1/projects/${projectId}/weekly-meetings/${meetingId}`, data)
      const index = this.weeklyMeetings.findIndex(m => m.id === meetingId)
      if (index !== -1) {
        this.weeklyMeetings[index] = response.data
      }
      
      // If new tasks were created, refresh the monthly plans to sync the Kanban board
      if (data.new_tasks && data.new_tasks.length > 0) {
        await this.fetchMonthlyPlans(projectId)
      }
      
      return response.data
    },
    async updateWeeklyProgress(projectId, progressId, data) {
      const response = await axios.put(`/api/v1/projects/${projectId}/weekly-progress/${progressId}`, data)
      // We will re-fetch the weekly meetings to update the kanban board's unattached problems
      await this.fetchWeeklyMeetings(projectId)
      return response.data
    }
  }
})
