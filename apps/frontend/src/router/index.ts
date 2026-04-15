import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'

import Home from '../views/Home.vue'
import Products from '../views/Products.vue'
import ProductDetail from '../views/ProductDetail.vue'
import Cart from '../views/Cart.vue'
import Checkout from '../views/Checkout.vue'
import News from '../views/News.vue'
import Promotions from '../views/Promotions.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Profile from '../views/Profile.vue'
import NotFound from '../views/NotFound.vue'

const routes: RouteRecordRaw[] = [
  { path: '/', component: Home, meta: { title: 'Главная' } },
  { path: '/products', component: Products, meta: { title: 'Каталог' } },
  { path: '/products/:id', component: ProductDetail, meta: { title: 'Товар' } },
  { path: '/cart', component: Cart, meta: { title: 'Корзина' } },
  {
    path: '/checkout',
    component: Checkout,
    meta: { title: 'Оформление', requiresAuth: true }
  },
  { path: '/news', component: News, meta: { title: 'Новости' } },
  { path: '/promotions', component: Promotions, meta: { title: 'Акции' } },
  { path: '/login', component: Login, meta: { title: 'Вход', guestOnly: true } },
  {
    path: '/register',
    component: Register,
    meta: { title: 'Регистрация', guestOnly: true }
  },
  {
    path: '/profile',
    component: Profile,
    meta: { title: 'Профиль', requiresAuth: true }
  },
  { path: '/:pathMatch(.*)*', component: NotFound, meta: { title: 'Не найдено' } }
]

export const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

router.beforeEach((to) => {
  const token = localStorage.getItem('token')
  if (to.meta.requiresAuth && !token) {
    return { path: '/login', query: { redirect: to.fullPath } }
  }
  if (to.meta.guestOnly && token) {
    return { path: '/profile' }
  }
})

router.afterEach((to) => {
  const baseTitle = 'Radar Extreme'
  const pageTitle = (to.meta.title as string) || ''
  document.title = pageTitle ? `${pageTitle} — ${baseTitle}` : baseTitle
})

export default router
