const app = getApp()
var common = require('../../utils/common.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    list_prod:[],
    list_news:[],
    imgs:[]
  },

  onLoad: function () {
    // this.transData()
    this.getSlider()
    this.getListProd()
    this.getAbout()
    this.getListNews()
  },

  onPullDownRefresh:function(){
    this.getSlider()
    this.getListProd()
    this.getAbout()
    this.getListNews()
    wx.stopPullDownRefresh;
  },

  transData:function(){
    this.setData({
      imgs: app.gData.imgs_index,
      list_prod: app.gData.list_prod_index,
      list_news: app.gData.list_news_index
    })
  },

  getSlider:function(){
    var that = this
    wx.request({
      url: app.gData.apiUrl + 'common.php?act=getSlider',
      dataType: 'json',
      success: function (e) {
        console.log('slider:'+e.data.ex)
        that.setData({
          imgs:e.data.ex
        })
      }
    })
  },

  getListProd:function(){
    var that = this
    wx.request({
      url: app.gData.apiUrl + 'common.php?act=getListDetail&id=3&size=6',
      dataType: 'json',
      success: function (e) {
        that.setData({
          list_prod:e.data.ex
        })
      }
    })
  },

  getAbout: function () {
    var that = this
    var wp = require('../../utils/wxParse/wxParse.js')
    wx.request({
      url: app.gData.apiUrl + 'common.php?act=getCol&id=1&tbl=channel&col=c_content&size=120',
      dataType: 'json',
      success: function (e) {
        wp.wxParse('about', 'html', e.data.ex, that, 5)
      }
    })
  },

  getListNews: function () {
    var that = this
    wx.request({
      url: app.gData.apiUrl + 'common.php?act=getListDetail&id=2&size=4',
      dataType: 'json',
      success: function (e) {
        that.setData({
          list_news:e.data.ex
        })
      }
    })
  },

  navToPage:function(e){
    common.navToPage(e);
  },

  tabToPage(e){
    common.tabToPage(e);
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {}
})
