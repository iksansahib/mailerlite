import { createRouter, createWebHistory } from 'vue-router'
import SubscribersViewVue from '@/views/SubscribersView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'subscribers',
      component: SubscribersViewVue
    },
  ]
})

export default router
