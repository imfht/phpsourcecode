var siteinfo = require('../config');  //引入配置文件
const BASE_URL = siteinfo.siteroot + '/';
/**
 * 图片上传
 * @param url 请求地址
 * @param data 请求参数
 * @param success 成功回调
 * @param fail  失败回调
 * @constructor
 *
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
    filePath:data.tempPath,
    name:'file',
    success: function (res) {
      var data = JSON.parse(res.data); 
      success(data)
    }
  })
  uploadTask.onProgressUpdate((res) => {
    progress({ progress: res.progress })
  })
  return uploadTask;
}
exports.Upload = upload;
