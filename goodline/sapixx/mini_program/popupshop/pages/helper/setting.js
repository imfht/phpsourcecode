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
    wx.showModal({
      content: '请确认要退出登录?',
      success: function (res) {
        if (res.confirm) {
          wx.clearStorage({
            success: function () {
              wx.switchTab({
                url: '../index/index',
              })
            },
          })
        }
      }
    })
  },
  /**
   * 绑定公众号帐号
   */
  pubOffice: function () {
    wx.showModal({
      content: '确定要绑定同主体公众号帐号吗？',
      success: function (res) {
        if (res.confirm) {
          wx.navigateTo({ url: 'startup' })
        }
      }
    })
  },
  /**
   *地址 
   */
  address: function () {
    let that = this;
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