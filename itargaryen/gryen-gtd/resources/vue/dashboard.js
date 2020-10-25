/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import './bootstrap';
import VueRouter from 'vue-router';
import routes from './routes';
import dashboardHome from './pages/dashboard/home';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.use(VueRouter);
Vue.component('dashboardHome', dashboardHome);

const router = new VueRouter({
    routes
});

// noinspection ES6ModulesDependencies
new Vue({
    router
}).$mount('#dashboardApp');
