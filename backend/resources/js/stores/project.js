import { defineStore } from 'pinia'
import axios from '../axios'

export const useProjectStore = defineStore('project', {
  state: () => ({
    projects: [],
    currentProject: null,
    loading: false,
  }),
  actions: {
    async fetchProjects() {
      this.loading = true
      try {
        const response = await axios.get('/api/v1/projects')
        this.projects = response.data
      } catch (error) {
        console.error('Failed to fetch projects', error)
      } finally {
        this.loading = false
      }
    },
    async fetchProject(id) {
      this.loading = true
      if (!this.currentProject || this.currentProject.id != id) {
        this.currentProject = null // Clear immediately to prevent old project details from flashing, but ONLY if the project ID changed
      }
      try {
        const response = await axios.get(`/api/v1/projects/${id}`)
        this.currentProject = response.data
      } catch (error) {
        console.error('Failed to fetch project', error)
      } finally {
        this.loading = false
      }
    },
    async createProject(projectData) {
      try {
        const response = await axios.post('/api/v1/projects', projectData)
        this.projects.push(response.data)
        return response.data
      } catch (error) {
        console.error('Failed to create project', error)
        throw error
      }
    },
    async updateProject(id, projectData) {
      try {
        const response = await axios.put(`/api/v1/projects/${id}`, projectData)
        this.currentProject = response.data
        const index = this.projects.findIndex(p => p.id === id)
        if (index !== -1) {
          this.projects[index] = response.data
        }
        return response.data
      } catch (error) {
        console.error('Failed to update project', error)
        throw error
      }
    },
    async deleteProject(id) {
      try {
        await axios.delete(`/api/v1/projects/${id}`)
        this.projects = this.projects.filter(p => p.id !== id)
      } catch (error) {
        console.error('Failed to delete project', error)
        throw error
      }
    },
    clearCurrentProject() {
      this.currentProject = null
      this.loading = false
    }
  }
})
