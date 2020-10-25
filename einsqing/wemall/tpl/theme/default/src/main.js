// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import VueLocalStorage from 'vue-localstorage';
import VueBus from 'vue-bus';
import Mint from 'mint-ui';
import 'mint-ui/lib/style.css';
import { Lazyload } from 'mint-ui';



Vue.use(VueLocalStorage);
Vue.use(VueBus);
Vue.use(Mint);
Vue.use(Lazyload);

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
});

