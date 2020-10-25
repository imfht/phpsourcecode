// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store'
import iView from 'iview'
import i18n from '@/locale'
import config from '@/config'
import importDirective from '@/directive'
import 'iview/dist/styles/iview.css'
import './index.less'
import '@/assets/icons/iconfont.css'
import Highlight from '@/libs/highlight.js'

// import apiRequest from './libs/api.request'
import appUrl from '../config/url'
// import Cookies from 'js-cookie'
import {TOKEN_KEY} from '@/libs/util'

window.$ = window.jQuery = require('jquery')
require('./assets/vendor/fancybox/jquery.fancybox');

// window.access_token = Cookies.get(TOKEN_KEY)

window.uploadUrl = {
  uploadAvatar: appUrl + 'api/upload/avatar',
  uploadAdvertisement: appUrl + 'api/upload/advertisement',
  uploadWang: appUrl + 'api/upload/wang',
  uploadTmp: appUrl + 'api/upload/tmp',
  uploadNewVersion: appUrl + 'api/upload/new_version',
  uploadCarousel: appUrl + 'api/upload/carousel',
  uploadNewVersion: appUrl + 'api/upload/new_version',
  importExcelAdvertisementPosition: appUrl + 'api/excels/import/advertisement_positions'
}
window.exportExcelUrl = {
  exportAdvertisementPosition: appUrl + 'api/excels/export/advertisement_positions'
}

Vue.use(iView, {
  i18n: (key, value) => i18n.t(key, value)
})

Vue.use(Highlight)
Vue.config.productionTip = false
/* @description 全局注册应用配置 */
Vue.prototype.$config = config
/* 注册指令 */
importDirective(Vue)

Vue.prototype.globalPlatformType = function() {
  function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
      if (userAgentInfo.indexOf(Agents[v]) > 0) {
        flag = false;
        break;
      }
    }
    return flag;
  }

  if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {
    return 'mobile' // iphone
  } else if (/(Android)/i.test(navigator.userAgent)) {
    return 'mobile' // Android
  } else {
    return 'pc'
  };
}

Vue.prototype.globalFancybox = function() {

  this.$nextTick(() => {
    $(function() {
      $('.fancybox').attr('rel', 'media-gallery').fancybox({
        openEffect: 'none',
        closeEffect: 'none',
        prevEffect: 'none',
        nextEffect: 'none',

        arrows: false,
        helpers: {
          media: {},
          buttons: {}
        }
      });
    })
  })
}

// 引入vue-amap
import VueAMap from 'vue-amap';
Vue.use(VueAMap);

// 初始化vue-amap
VueAMap.initAMapApiLoader({
  // 高德的key
  key: config.gaode_map_token,
  // 插件集合
  plugin: [
    'AMap.Autocomplete',
    'AMap.PlaceSearch',
    'AMap.Scale',
    'AMap.OverView',
    'AMap.ToolBar',
    'AMap.MapType',
    'AMap.PolyEditor',
    'AMap.CircleEditor',
    'AMap.Geocoder',
    'AMap.Geolocation'
  ],
  // 高德 sdk 版本，默认为 1.4.4
  v: '1.4.4'
});

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  i18n,
  store,
  render: h => h(App)
})
