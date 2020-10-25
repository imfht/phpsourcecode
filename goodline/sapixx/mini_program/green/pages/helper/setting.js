const app = getApp()
Page({

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    app.isLogin()
  },

  //清空缓存
  clearStorage: function () {
    app.wxLayer('请确认要退出登录?',() => {
      try {
        wx.clearStorageSync()
        wx.switchTab({
          url: '../index',
        })
      } catch(e) {
        app.wxAlert('请退出登录');
      }
    })
  },
  /**
   *地址 
   */
  address: function () {
    //获取用户权限
    wx.getSetting({
      success(res) {
        if (res.authSetting['scope.address'] == false) {
          wx.openSetting({
            success: (res) => {
              res.authSetting = {
                "scope.address": true,
              }
            }
          })
        } else {
          wx.chooseAddress({})
        }
      }
    })
  },
})