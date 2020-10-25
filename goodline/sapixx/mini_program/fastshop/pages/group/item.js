var api = require('../../utils/request');
var util = require('../../utils/util');
var WxParse = require('../../wxParse/wxParse.js');
const app = getApp()
Page({
  data: {
    tip: false,     // 轮播图指针
    group_id: 0,
    items: [],
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onLoad: function (options) {
    this.setData({
      group_id: parseInt(options.id)
    });
    this.getItem();
  },
  /**
   * 获取商品信息
   */
  getItem: function (e) {
    let that = this;
    var group_id = that.data.group_id;
    api.Get('api/v3/fastshop/group/item', { 'id': group_id}, function (result) {
      that.setData({
        items: result.data,
        tip: result.data.face.length >= 1 ? true : false
      });
      WxParse.wxParse('content', 'html', result.data.content, that, 0);
    })
  },
  //立即购买
  buy_now: function () {
    let that = this;
    var group_id = that.data.group_id;
    wx.showLoading({ title: '下单中' })
    wx.setStorageSync('group',group_id)
    setTimeout(function () {
      wx.navigateTo({
        url: 'dopay'
      })
    },600)
  }
})