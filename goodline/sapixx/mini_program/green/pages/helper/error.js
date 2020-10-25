const app = getApp()
Page({
  data: {
    appname: app.appname,
    message: '请求失败,请稍后再试',
    url: '/pages/index'
  },
  //错误提示文本
  onLoad: function (options) {
    //提示内容
    var msg = options.msg;
    if (app.util().isNull(msg)) {
      this.setData({
        message: msg
      })
    }
    //网址
    var url = options.url;
    if (!app.util().isNull(url)) {
      this.setData({
        url: url
      })
    }
  },
  //重试
  postRests: function () {
    var url = this.data.url;
    wx.reLaunch({
      url: url,
    })
  }
})