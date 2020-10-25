import {Message} from 'element-ui'

window._ = require('lodash')

window.$ = window.jQuery = require('jquery')

window.axios = require('axios')
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

let token = document.head.querySelector('meta[name="csrf-token"]')

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
  Message({showClose: true, message: 'CSRF token not found.', type: 'error'})
}

// 拦截响应response，并做一些错误处理
window.axios.interceptors.response.use((response) => {
  return response
}, (error) => {
  if (error && error.response) {
    let resp = error.response
    switch (resp.status) {
      case 429:
        error.message = '请求次数过多，请秒后再试'
        break;
      case 419:
        error.message = '你没有操作权限'
        break;
      case 401:
        error.message = '请登录后再操作'
        break;
      default:
        error.message = '出错了'
    }

  }
  Message({showClose: true, message: error.message, type: 'error'})
  return ''
});
