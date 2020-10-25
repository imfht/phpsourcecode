const app = getApp()
Page({
  data: {
    islogin: false,
    config:[]
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {
    app.getConfig();
    this.setData({
      islogin:!app.util().isNull(app.globalData.loginuser),
      config:app.globalData.config,
    })
    app.setTabBarCartNumber();
  },

   /**
   * 分享按钮
   */
  onShareAppMessage: function () {
    return {
      path: '/pages/index/index?ucode=' + app.globalData.loginuser.ucode
    }
  },
})