import Vue from 'vue';
import Scrollspy from 'vue2-scrollspy';
import App from './App.vue';
import router from './router';
import store from './store';

import injection from './helpers/injection';
import RouterLink from './directives/router-link';
import './assets/less/main.less';

Vue.use(Scrollspy);
Vue.use(injection);

Vue.directive('router-link', RouterLink);

/* eslint-disable no-new */
new Vue({
    el: '#app',
    router,
    store,
    template: '<App/>',
    components: { App },
});
