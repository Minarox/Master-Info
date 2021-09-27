import { createRouter, createWebHashHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Survey from '../views/Survey.vue'

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/survey/:category/:question', name: 'Survey', component: Survey }
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

export default router
