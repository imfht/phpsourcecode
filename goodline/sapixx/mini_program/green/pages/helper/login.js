const app = getApp()
Page({
  /**
   * 用户登录
   */
  authorLogin: function (e) {
    if (e.detail.errMsg !== 'getUserInfo:ok') {
      return false;
    }
    wx.showLoading({
      title: '正在登录',
    })
    app.doLogin(e,function (isLogin){
      wx.navigateBack({
        delta:1
      })
    });
  }
})