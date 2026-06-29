import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  { path: '/login', name: 'Login', component: () => import('../views/Login.vue') },
  { path: '/auth/callback', name: 'AuthCallback', component: () => import('../views/AuthCallback.vue') },
  { path: '/dashboard', name: 'Dashboard', component: () => import('../views/Dashboard.vue'), meta: { requiresAuth: true } },
  { path: '/projects/new', name: 'NewProject', component: () => import('../views/NewProject.vue'), meta: { requiresAuth: true } },
  { path: '/projects/:id', name: 'ProjectDetail', component: () => import('../views/ProjectDetail.vue'), meta: { requiresAuth: true } },
  { path: '/projects/:id/planning', name: 'ProjectPlanning', component: () => import('../views/ProjectPlanning.vue'), meta: { requiresAuth: true } },
  { path: '/projects/:id/timeline', name: 'ProjectTimeline', component: () => import('../views/ProjectTimeline.vue'), meta: { requiresAuth: true } },
  { path: '/projects/:id/plan/:planId', name: 'MonthlyPlanDetail', component: () => import('../views/MonthlyPlanDetail.vue'), meta: { requiresAuth: true } },
  { path: '/global-timeline', name: 'GlobalTimeline', component: () => import('../views/GlobalTimeline.vue'), meta: { requiresAuth: true } },
  { path: '/:pathMatch(.*)*', redirect: '/dashboard' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      next('/login')
    } else {
      if (!authStore.user) {
        await authStore.fetchUser()
      }
      next()
    }
  } else {
    next()
  }
})

export default router
