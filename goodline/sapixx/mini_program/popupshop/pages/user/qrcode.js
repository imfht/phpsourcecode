const app = getApp()
var api = require('../../utils/request');

Page({

  /**
   * 页面的初始数据
   */
  data: {
    skeleton: true,
    qrcode:"/img/qrcode.jpg",
    ucode:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onShow: function () {
    this.getCode()
  },
  /**
   * 生成二维码
   **/
  getCode: function () {
    let that = this;
    var ucode = app.globalData.loginuser.ucode;
    api.Post('openapi/v1/user/miniprogramcode', {
      scene: {ucode: ucode },
      page: 'pages/index/index',
      name: 'popupshop_' + app.globalData.loginuser.uid
    }, function (result) {
      that.setData({
        qrcode: result.data,
        ucode: app.globalData.loginuser.ucode
      })
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