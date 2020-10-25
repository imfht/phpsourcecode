const app = getApp()
var WxParse = require('../../wxParse/wxParse.js');
var common = require('../../utils/common');
Page({
  data: {
    imgheights: [],
    nav_height:750,
    barbolor:'rgba(0,0,0,0.4)',
    item_id: 0,
    item: [],
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onLoad: function (options) {
    app.setUcode(options);  //读取邀请码
    if (options.scene) {
      var scene = util.strToArray(decodeURIComponent(options.scene));
      var item_id = parseInt(scene.id);
    } else {
      var item_id = parseInt(options.id);
    }
    this.setData({
      item_id: item_id
    });
    this.getItem(item_id);
  },
  /**
   * 获取商品信息
   */
  getItem: function (item_id) {
    let that = this;
    var param ={
      id: item_id,
    }
    app.api().Get('api/v1/popupshop/sale/item',param,function (result) {
      that.setData({
        item: result.data
      });
      WxParse.wxParse('content','html',result.data.house.content,that,0);
    })
  },
  //立即购买
  buy_now: function () {
    let that = this;
    var item_id = that.data.item_id;
    wx.showLoading({title: '请稍后'})
    app.api().Post('api/v1/popupshop/sale/isOrder',{'id':item_id}, function (result) {
        wx.setStorageSync('cart',item_id)
        wx.navigateTo({
          url: 'dopay'
        })
    })
  },
  /**
   * 分享按钮
   */
  onShareAppMessage: function (res) {
    return {
      path: '/pages/store/like?ucode=' + app.globalData.loginuser.ucode,
    }
  },
  //图片高度
  imageLoad: function (event) {
    this.setData({
      imgheights: common.autoimg(event)
    })
  },
  //返回首页
  toRedirect: function () {
    wx.switchTab({
      url: '/pages/market/index',
    });
  }
})