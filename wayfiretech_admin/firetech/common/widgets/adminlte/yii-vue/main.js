/*
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-06 14:29:18
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-30 23:34:41
 */
// import Vue from '/node_modules/vue/dist/vue.min.js'

// console.log(window.basepath)
// import Vue from './node_modules/vue/dist/vue.min.js'
import dikaerji from './public/utli/dikaerji.js'
import global from './public/utli/global.js'
import Print from './public/utli/print.js'

Vue.prototype.global = global 
Vue.config.productionTip = false
Vue.use(global)
Vue.use(dikaerji)
Vue.use(Print) // 注册

// 以form data 方式进行post请求
Vue.http.options.emulateJSON = true
Vue.http.options.headers = {
    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
    'X-CSRF-Token':window.sysinfo.csrfToken // _csrf验证
}  
Vue.prototype.VueResource=VueResource
// fnResize();
// function fnResize() {
//   var width = document.documentElement.clientWidth || document.body.clientWidth;
//   if (width > 540) {
//       width = 540;
//   }
//   fontSize = (width / 100);
//   document.documentElement.style.fontSize = 0.625+'px';
//   // document.documentElement.style.fontSize = 0.1+'px';
// }
