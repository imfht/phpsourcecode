var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    markers: [{
      id: 0,
      latitude: 31.260585,
      longitude: 121.367803,
      width: 24,
      height: 24,
      callout:{
        content:'上海飞墨信息科技有限公司',
        padding:5,
        bgColor:'#FFFFFF',
        display:'ALWAYS',
      }
    }]
  },
  markertap(e) {
    console.log(e.markerId)
  },

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
        id:6,
        tbl:'channel',
        col:'c_content'
      },
      success: function (e) {
        console.log(e.data)
        wp.wxParse('cont', 'html', e.data.ex, that, 5)
      }
    })
  },

  callService:function(){
    wx.makePhoneCall({
      phoneNumber: '18964762198'
    })
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {}
})