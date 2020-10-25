import Vue from 'vue'
import App from './App.vue'
import routes from './routes'
import VueRouter from 'vue-router'
import ViewUI from 'view-design';
import Language from '../_modules/language'
import Mixins from '../_modules/mixins'

import '../common'
import './main'

Vue.use(VueRouter);
Vue.use(ViewUI);
Vue.use(Language);
Vue.use(Mixins);

import Title from '../_components/Title.vue'
import sreachTitle from '../_components/sreachTitle.vue'
import UserInput from './components/UserInput'
import UserView from './components/UserView'
import UserImg from './components/UserImg'
import WLoading from './components/WLoading'

Vue.component('VTitle', Title);
Vue.component('sreachTitle', sreachTitle);
Vue.component('UserInput', UserInput);
Vue.component('UserView', UserView);
Vue.component('UserImg', UserImg);
Vue.component('WLoading', WLoading);

import TaskDetail from './components/project/task/detail'
Vue.prototype.taskDetail = TaskDetail;

import ReportDetail from './components/report/detail'
Vue.prototype.reportDetail = ReportDetail;

const originalPush = VueRouter.prototype.push
VueRouter.prototype.push = function push(location) {
    return originalPush.call(this, location).catch(err => err)
}
const router = new VueRouter({
    mode: 'history',
    routes
});

//进度条配置
ViewUI.LoadingBar.config({
    color: '#3fcc25',
    failedColor: '#ff0000'
});
router.beforeEach((to, from, next) => {
    ViewUI.LoadingBar.start();
    next();
});
router.afterEach((to, from, next) => {
    ViewUI.LoadingBar.finish();
});

//加载函数
Vue.prototype.goForward = function(location, isReplace) {
    if (typeof location === 'string') location = {name: location};
    if (isReplace === true) {
        this.$router.replace(location);
    }else{
        this.$router.push(location);
    }
};

//返回函数
Vue.prototype.goBack = function (number) {
    let history = $A.jsonParse(window.sessionStorage['__history__'] || '{}');
    if ($A.runNum(history['::count']) > 2) {
        this.$router.go(typeof number === 'number' ? number : -1);
    } else {
        this.$router.replace(typeof number === "object" ? number : {path: '/'});
    }
};

Vue.prototype.$A = $A;

Vue.config.productionTip = false;

const app = new Vue({
    el: '#app',
    router,
    template: '<App/>',
    components: { App }
});

$A.app = app;

window.localStorage.setItem("__::WookTeam:check", "success")

