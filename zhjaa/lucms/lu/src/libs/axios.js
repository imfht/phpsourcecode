import Axios from 'axios'
import baseURL from '_conf/url'
import {Notice} from 'iview'
import Cookies from 'js-cookie'
import {TOKEN_KEY} from '@/libs/util'
class httpRequest {
  constructor() {
    this.options = {
      method: '',
      url: ''
    }
    // 存储请求队列
    this.queue = {}
  }
  // 销毁请求实例
  destroy(url) {
    delete this.queue[url]
    const queue = Object.keys(this.queue)
    return queue.length
  }
  // 请求拦截
  interceptors(instance, url) {
    // 添加请求拦截器
    instance.interceptors.request.use(config => {
      config.headers['Authorization'] = Cookies.get(TOKEN_KEY)
       window.access_token = Cookies.get(TOKEN_KEY)
      // Spin.show()
      // 在发送请求之前做些什么
      return config
    }, error => {
      // 对请求错误做些什么
      return Promise.reject(error)
    })

    // 添加响应拦截器
    instance.interceptors.response.use((res) => {
      return res.data
    }, (error) => {
      let {response} = error
      if (response.status === 401) {
        Cookies.remove(TOKEN_KEY)
        window.location.href = window.location.pathname + '#/login'
      }
      if (response.hasOwnProperty('data')) {
        Notice.error({title: '出错了', desc: response.data.message})
        return Promise.reject(response.data)
      } else {
        Notice.error({title: '出错了', desc: response.data.message})
        return Promise.reject(response)
      }
    })
  }
  // 创建实例
  create() {
    let conf = {
      baseURL: baseURL,
      // timeout: 2000,
      headers: {
        'Content-Type': 'application/json; charset=utf-8',
        'X-URL-PATH': location.pathname
      }
    }
    return Axios.create(conf)
  }
  // 合并请求实例
  mergeReqest(instances = []) {
    //
  }
  // 请求实例
  request(options) {
    var instance = this.create()
    this.interceptors(instance, options.url)
    options = Object.assign({}, options)
    this.queue[options.url] = instance
    return instance(options)
  }
}
export default httpRequest
