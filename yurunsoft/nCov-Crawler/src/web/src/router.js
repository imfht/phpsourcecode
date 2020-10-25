import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
  scrollBehavior(to, from, savedPosition) {
      if (savedPosition) {
          return savedPosition
      }
      return {x: 0, y: 0}
  },
  routes: [
    {
      path: '/',
      name: 'Home',
      meta: {
        title: '新型冠状病毒肺炎疫情实时动态',
      },
      component: () => import('@/views/Home.vue'),
    },
    {
      path: '/areaDetail',
      name: 'AreaDetail',
      meta: {
        title: '地区详情',
      },
      component: () => import('@/views/AreaDetail.vue'),
    },
    {
      path: '/cityDetail',
      name: 'CityDetail',
      meta: {
        title: '城市详情',
      },
      component: () => import('@/views/CityDetail.vue'),
    },
  ]
})
