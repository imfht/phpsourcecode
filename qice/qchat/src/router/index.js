import Vue from 'vue'
import Router from 'vue-router'
import Main from '@/components/Main'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'App',
      component: resolve => require(['@/App'], resolve)
    },

    {
      path: '/login',
      name: 'Login',
      component: require(['@/components/Login'])
    },

  ]
})
