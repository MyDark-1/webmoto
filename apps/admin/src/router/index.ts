import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

import Dashboard from '../views/Dashboard.vue'
import Products from '../views/Products.vue'
import ProductEdit from '../views/ProductEdit.vue'
import Orders from '../views/Orders.vue'
import News from '../views/News.vue'
import Promotions from '../views/Promotions.vue'
import Feedback from '../views/Feedback.vue'
import Login from '../views/Login.vue'
import NotFound from '../views/NotFound.vue'

const routes: RouteRecordRaw[] = [
  { path: '/', component: Dashboard, meta: { title: 'Дашборд' } },
  { path: '/products', component: Products, meta: { title: 'Товары' } },
  { path: '/products/:id', component: ProductEdit, meta: { title: 'Товар' } },
  { path: '/orders', component: Orders, meta: { title: 'Заказы' } },
  { path: '/news', component: News, meta: { title: 'Новости' } },
  { path: '/promotions', component: Promotions, meta: { title: 'Акции' } },
  { path: '/feedback', component: Feedback, meta: { title: 'Обратная связь' } },
  {
    path: '/login',
    component: Login,
    meta: { title: 'Вход', public: true }
  },
  { path: '/:pathMatch(.*)*', component: NotFound, meta: { title: '404' } }
]

export const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

router.beforeEach((to) => {
  const token = localStorage.getItem('admin_token')
  if (!to.meta.public && !token) {
    return { path: '/login' }
  }
  if (to.path === '/login' && token) {
    return { path: '/' }
  }
})

router.afterEach((to) => {
  document.title = `${(to.meta.title as string) || 'Админ'} — Radar Extreme`
})

export default router
