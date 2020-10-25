var siteinfo = require('../config');  //引入配置文件
const BASE_URL = siteinfo.siteroot+'/';
const SECRET = siteinfo.miniapp //应用ID
/*
* 签名认证
*/
const getSign = obj => {
  let keys = Object.keys(obj)
  keys.sort()
  let params = []
  keys.forEach(e => {
    if (obj[e] != null) {
      params.push(e + '=' + obj[e])
    }
  })
  params.push('key=' + SECRET)
  let paramStr = params.join('&')
  const md5Util = require('../utils/md5.js')
  let signResult = md5Util.md5(paramStr).toUpperCase()
  return signResult
}
/**
 * 网络请求封装
 * @param url url路径名 例：/books
 * @param method 请求方式 POST/GET/DELETE等
 * @param data 请求参数 string类型
 * @param success  成功回调
 * @param fail 失败回调
 */
function request(url, method, data, success, fail) {
  if (!fail && !success && typeof data === 'function') {
    success = data;
    data = "";
  }else if (!fail) {
    if (typeof data === 'function') {
      fail = success
      success = data
      data = ""
    } else if (typeof data === 'object') {
      let sign = getSign(data)
      data.sign = sign
    } else {
      console.log("传递参数类型不正确");
    }
  }
  var wxtask = wx.request({
    url: BASE_URL + url,
    header: {
        'content-type':'application/json',  //默认 application/json :数据序列化
        'request-miniapp': siteinfo.miniapp,
        'request-time': Date.parse(new Date()),
        'request-token': wx.getStorageSync('token'),
        'Cookie':'PHPSESSID='+ wx.getStorageSync('session_id'),  
    },
    method: method,
    data: data,
    success: function (res) {
      switch (res.data.code) {
        case 200: //请求成功code
          success(res.data)
          break
        case 204: //成功请求但空内容。
          success(res.data)
          break
        case 301: //永久跳转
          wx.showModal({
            content: res.data.msg,
            success: function (rel) {
              if (rel.confirm) {
                wx.redirectTo({
                  url: res.data.url,
                  fail: function (res) {
                    wx.showModal({ content: '访问失败', showCancel: false })
                  }
                })
              }
            }
          })
        case 302: //临时跳转
          wx.showModal({
            content: res.data.msg,
            success: function (rel) {
              if (rel.confirm) {
                wx.navigateTo({
                  url: res.data.url, fail:function (res) {
                    wx.showModal({content:'访问失败',showCancel: false })
                  },
                })
              }
            }
          })
          break
        case 401://解决请求用户认证
          wx.navigateTo({
            url: '/pages/helper/login', fail: function (res) {
              wx.showModal({ content: '访问失败', showCancel: false })
            }
          })
          break 
        case 403://请求失败code弹出提示
          wx.showModal({content:res.data.msg,showCancel:false})
          break
        case 500://请求失败code调整
          wx.redirectTo({
            url: '/pages/helper/error?msg=' + res.data.msg, fail: function (res) {
              wx.showModal({ content: '访问失败', showCancel: false })
            }
          })
          break
      }
      wx.hideLoading();
    },
    fail: function (res) {
      if (fail) {
        fail(res)
      }
    }
  })
  return wxtask;
}
/**
 * 请求封装-Get
 * @param url 请求地址
 * @param data 请求参数
 * @param success 成功回调
 * @param fail  失败回调
 * @constructor
 *
 * 返回值为微信请求实例   用于取消请求
 */
function Get(url, data, success, fail) {
  return new Promise((resolve, reject) => {
    return request(url, "GET", data, success, fail)
  })
}
/**
 * 请求封装-Post
 * @param url 请求地址
 * @param data 请求参数
 * @param success 成功回调
 * @param fail  失败回调
 * @constructor
 *
 * 返回值为微信请求实例   用于取消请求
 */
function Post(url, data, success, fail) {
  return new Promise((resolve, reject) => {
    return request(url, 'POST', data, success, fail)
  })
}
/**
 * 请求封装-PUT
 * @param url 请求地址
 * @param data 请求参数
 * @param success 成功回调
 * @param fail  失败回调
 * @constructor
 *
 * 返回值为微信请求实例   用于取消请求
 */
function Put(url, data, success, fail) {
  return new Promise((resolve, reject) => {
    return request(url, 'PUT', data, success, fail)
  })
}
/**
 * 请求封装-Delete
 * @param url 请求地址
 * @param data 请求参数
 * @param success 成功回调
 * @param fail  失败回调
 * @constructor
 *
 * 返回值为微信请求实例   用于取消请求
 */
function Delete(url, data, success, fail) {
  return new Promise((resolve, reject) => {
    return request(url, 'DELETE', data, success, fail)
  })
}
exports.Get = Get;
exports.Post = Post;
exports.Put = Put;
exports.Delete = Delete;