// pages/user/check.js
Page({

  data: {
    page: ''
  },

  onLoad: function (options) {
    // 接收跳转页面参数赋值变量
    this.setData({
      page: options.page
    })
  },

  checkUser:function(){
    var that = this
    var oid = wx.getStorageSync('openid')
    try {
      var value = wx.getStorageSync('openid')
      wx.request({
        url: apiUrl + 'user.php?act=check&oid=' + oid,
        success:function(e){
          if (e.data.err==0) {
            wx.navigateTo({
              url: '../user/' + that.data.page,
            })
          } else {
            that.checkFail()
          }
        }
      })
    } catch (e) {
      that.checkFail()
    }
  },

  checkFail:function(){
    wx.showToast({
      title: '获取用户登录态失败，请重新登录！',
      duration: 2000
    })
    wx.navigateTo({
      url: '../user/login',
    })
  }

})
