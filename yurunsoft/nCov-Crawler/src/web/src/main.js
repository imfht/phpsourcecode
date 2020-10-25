import Vue from 'vue'
import App from './App.vue'
import router from './router'

Vue.config.productionTip = false

import  { LoadingPlugin, ToastPlugin, Divider } from 'vux'
Vue.use(LoadingPlugin)
Vue.use(ToastPlugin)
Vue.component('divider', Divider)

new Vue({
  router,
  render: h => h(App),
}).$mount('#app')
