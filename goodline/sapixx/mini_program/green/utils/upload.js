var siteinfo = require('../config');  //引入配置文件
var util = require('util');           //引入封装工具
const BASE_URL = siteinfo.siteroot + '/';
/**
 * 图片上传
 * @param url 请求地址
 * @param data 请求参数
 * @param success 成功回调
 * @param fail  失败回调
 * @constructor
 * 返回值为微信请求实例
 */
function upload(url, data, success, progress) {
  var uploadTask = wx.uploadFile({
    header: {
      'content-type': 'application/x-www-form-urlencoded',
      'request-miniapp': siteinfo.miniapp,
      'request-time': Date.parse(new Date()),
      'request-token': wx.getStorageSync('token'),
      'Cookie': 'PHPSESSID=' + wx.getStorageSync('session_id')
    },
    url: BASE_URL + url,
    filePath: data.tempPath,
    name: 'file',
    success: function (res) {
      typeof success == "function" && success(JSON.parse(res.data))
    }
  })
  uploadTask.onProgressUpdate((res) => {
    typeof progress == "function" && progress({
      progress: res.progress
    })
  })
  return uploadTask;
}
/**
* 上传封装
*/
function upfiles(url, param, callback) {
  var files = Array();
  if (!util.isArray(param)) {
    typeof callback == "function" && callback({
      isend: true,
      filePaths: files
    })
  }
  var count = util.count(param);
  if (count == 0) {
    typeof callback == "function" && callback({
      isend: true,
      filePaths: files
    })
  } else {
    for (var i = 0; i < count; i++) {
      (function (i) {
        upload(url, { tempPath: param[i] }, (res) => {
          if (res.code == 200) {
            files.push(res.data);
            if (i == count - 1) {
              typeof callback == "function" && callback({
                isend: true,
                filePaths: files
              })
            }
          }
        });
      })(i)
    }
  }
}
exports.Upload = upload;
exports.Upfiles = upfiles;