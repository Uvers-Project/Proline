import { defineStore } from 'pinia'
import axios from '../axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token') || null,
    allUsers: [],
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    setToken(token) {
      this.token = token
      localStorage.setItem('auth_token', token)
    },
    async fetchUser() {
      if (!this.token) return
      try {
        const response = await axios.get('/api/v1/user')
        this.user = response.data
        this.fetchAllUsers() // Also fetch all users when getting the current user
      } catch (error) {
        this.logout()
      }
    },
    async fetchAllUsers() {
      if (!this.token) return
      try {
        const response = await axios.get('/api/v1/users')
        this.allUsers = response.data
      } catch (error) {
        console.error('Failed to fetch all users', error)
      }
    },
    async updateTheme(variant) {
      if (!this.token || !this.user) return
      try {
        await axios.put('/api/v1/user/theme', { theme_preference: variant })
        this.user.theme_preference = variant
      } catch (error) {
        console.error('Failed to update theme preference', error)
      }
    },
    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('auth_token')
    }
  }
})
