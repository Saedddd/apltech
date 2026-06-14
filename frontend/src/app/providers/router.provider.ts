import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@features/auth/model/authStore'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/products',
    },
    {
      path: '/products',
      name: 'products',
      component: () => import('@pages/ProductsPage/ui/ProductsPage.vue'),
      meta: { requiresAuth: false },
    },
    {
      path: '/product/:id',
      name: 'product-detail',
      component: () => import('@pages/ProductDetailPage/ui/ProductDetailPage.vue'),
      meta: { requiresAuth: false },
    },
    {
      path: '/product/create',
      name: 'product-create',
      component: () => import('@pages/ProductCreatePage/ui/ProductCreatePage.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/product/edit/:id',
      name: 'product-edit',
      component: () => import('@pages/ProductEditPage/ui/ProductEditPage.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/brand/search',
      name: 'brand-search',
      component: () => import('@pages/BrandSearchPage/ui/BrandSearchPage.vue'),
      meta: { requiresAuth: false },
    },
  ],
})

router.beforeEach((to, _from, next) => {
  const authStore = useAuthStore()

  authStore.updateMenu?.()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    const returnUrl = to.fullPath
    next({
      path: '/products',
      query: { showLogin: 'true', returnUrl },
    })
  } else {
    next()
  }
})

export default router
