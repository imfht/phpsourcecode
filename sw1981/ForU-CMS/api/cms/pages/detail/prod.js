var app = getApp()
var common = require('../../utils/common.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    id:0,
    title:'',
    date:'',
    imgs: []
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var id = this.setData({
      id: options.id
    })
    this.getInfo()
  },

  getInfo: function () {
    common.getInfo(this)
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {}
})
