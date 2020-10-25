var app = getApp()
var common = require('../../utils/common.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    list:[],
    page:2,
    page_total:2
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.getList()
  },

  getList: function () {
    common.getList(2, 1, this)
  },

  navToPage:function(e){
    common.navToPage(e)
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
    if (this.data.page <= this.data.page_total) {
      common.getList(2, this.data.page, this)
    } else {
      wx.showToast({
        title: '加载完毕',
        icon: 'success',
        duration: 2000
      })
    }
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {}
})
