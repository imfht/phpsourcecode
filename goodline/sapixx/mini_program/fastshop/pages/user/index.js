const app = getApp()
var api = require('../../utils/request');

Page({
  data: {
    islogin: false,
    config: [],
  },
  /**
  * 生命周期函数--监听页面显示
  */
  onShow: function () {
    app.setTabBarCartNumber();
    this.getInfo();
  },
  /**
   * 读取我的订单
   */
  getInfo: function() {
    let that = this;
    api.Get("api/v3/fastshop/index/config", function(result) {
      if (result.code == 200) {
        app.globalData.config = result.data;
        that.setData({
          config: result.data,
          islogin: !app.util().isNull(app.globalData.loginuser),
        });
      }
    })
  },
  /**
   * 判断是微信IOS还是Android
   */
  openVip: function(res) {
    let that = this;
    wx.getSystemInfo({
      success: function(res) {
        that.setData({
          systemInfo: res,
        })
        if (res.platform == "ios") {
          wx.showModal({
            content: '十分抱歉,由于相关规定,暂时不支苹果用户开通会员.',
          })
        } else {
          wx.navigateTo({
            url: '/pages/user/vip'
          })
        }
      }
    })
  },
  /**
   * 分享按钮
   */
  onShareAppMessage: function (res) {
    let that = this;
    var ucode = app.globalData.loginuser.ucode;
    return {
      desc: app.appname,
      path: '/pages/index/index?ucode=' + ucode
    }
  },
})