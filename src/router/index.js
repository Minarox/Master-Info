import {createRouter, createWebHistory} from 'vue-router'
import Home from '@/views/Home.vue'
import Survey from '@/views/Survey.vue'
import Cours from "@/views/Cours";
import NotFound from "@/views/NotFound";

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/cours/:name', name: 'Cours', component: Cours },
  { path: '/survey/:category/:question', name: 'Survey', component: Survey },
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  scrollBehavior(to, from, SavedPosition) {
    if (to.hash) {
      let id = window.location.href.split("#")[1];
      if (id.length) {
        let yOffset = -64;
        let element = document.getElementById(id);
        let y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({ top: y, behavior: 'smooth' })
      }
    } else if (SavedPosition) {
      return SavedPosition;
    } else {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  },
  routes
})

export default router
