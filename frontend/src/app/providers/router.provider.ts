import { createRouter, createWebHistory } from 'vue-router'
import ProductsPage from '@pages/ProductsPage/ui/ProductsPage.vue'
import ProductDetailPage from '@pages/ProductDetailPage/ui/ProductDetailPage.vue'
import ProductCreatePage from '@pages/ProductCreatePage/ui/ProductCreatePage.vue'
import ProductEditPage from '@pages/ProductEditPage/ui/ProductEditPage.vue'
import BrandSearchPage from '@pages/BrandSearchPage/ui/BrandSearchPage.vue'

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
      component: ProductsPage,
    },
    {
      path: '/product/:id',
      name: 'product-detail',
      component: ProductDetailPage,
    },
    {
      path: '/product/create',
      name: 'product-create',
      component: ProductCreatePage,
    },
    {
      path: '/product/edit/:id',
      name: 'product-edit',
      component: ProductEditPage,
    },
    {
      path: '/brand/search',
      name: 'brand-search',
      component: BrandSearchPage,
    },
  ],
})

export default router
