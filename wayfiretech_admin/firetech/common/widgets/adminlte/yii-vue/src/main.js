/*
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-06 14:29:18
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-30 23:34:38
 */
import Vue from '../node_modules/vue/dist/vue.min.js'
import App from './App.vue'
import VueResource from '../node_modules/vue-resource/dist/vue-resource.js'


global.VueResource = VueResource
Vue.config.productionTip = false

// Vue.use(VueResource)
// 以form data 方式进行post请求
Vue.http.options.emulateJSON = true
Vue.http.options.headers = {
    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
    "<?= \yii\web\Request::CSRF_HEADER; ?>" : "<?= Yii::$app->request->csrfToken; ?>" // _csrf验证
}    
console.log(VueResource)
Vue.prototype.VueResource=VueResource
new Vue({
  render: h => h(App),
}).$mount('#APP')
