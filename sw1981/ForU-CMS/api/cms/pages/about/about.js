var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {},

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.getInfo()
  },

  getInfo: function () {
    var that = this
    var wp = require('../../utils/wxParse/wxParse.js')
    wx.request({
      url: app.gData.apiUrl + 'common.php',
      data:{
        act:'getCol',
        id:1,
        tbl:'channel',
        col:'c_content'
      },
      success: function (e) {
        console.log(e.data)
        wp.wxParse('cont', 'html', e.data.ex, that, 5)
      }
    })
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {}
})