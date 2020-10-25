// import Vue from 'vue'
window.Vue = require('vue');
import VueResource from 'vue-resource'
import dikaerji from './utli/dikaerji.js'
import global from './utli/global.js'
import Print from './utli/print.js'
import ElmentUi from 'element-ui'
Vue.prototype.ElmentUi = ElmentUi 
Vue.prototype.global = global 
Vue.prototype.VueResource=VueResource
Vue.config.productionTip = false

Vue.use(global)
Vue.use(dikaerji)
Vue.use(Print) // 注册
Vue.use(VueResource);
Vue.use(ElmentUi)
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    console.log(token)
    // 以form data 方式进行post请求
    Vue.http.options.emulateJSON = true
    Vue.http.options.headers = {
        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
        'X-CSRF-Token':token.content // _csrf验证
    }  

} else {
    console.error('CSRF token not found');
}
window.app = new Vue({
    el: '#APP',
    data: {
        ceshi:'nini'
    },
    methods: {
    }
});