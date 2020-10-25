var WxParse = require('../../wxParse/wxParse.js');
const app = getApp()
Page({
  data: {
    loading: false,
    disabled: true,    
    item_id: 0,
    items: [],
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onLoad: function (options) {
    app.setUcode(options);  //读取邀请码
    if (options.scene) {
      var scene = app.util().strToArray(decodeURIComponent(options.scene));
      var item_id = parseInt(scene.id);
    } else {
      var item_id = parseInt(options.id);
    }
    //获取当前页面点击ID
    this.setData({ item_id: item_id });
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    this.getItem();
  },
  /**
   * 获取商品信息
   */
  getItem: function (e) {
    let that = this;
    var item_id = that.data.item_id;
    app.api().Get('api/v3/fastshop/sale/item', {
      'id': item_id
    }, function (result) {
      if (result.data.types == 1 && result.data.state == 1){
        that.setData({
          disabled:false,
        });
      }
      that.setData({
        items: result.data,
      });
      WxParse.wxParse('content','html',result.data.item.content,that,0);
    })
  },
  //立即购买
  buy_now: function () {
    let that = this;
    var item_id = that.data.item_id;
    //下单前查询是否有库存和购买
    wx.showLoading({title: '抢购中'})
    app.api().Post('api/v3/fastshop/order/isOrder',{'id': item_id}, function (result) {
        wx.setStorageSync('cart',item_id)
        setTimeout(function () {
          wx.navigateTo({ url: 'dopay' })
        }, 600)
    })
  },
  /**
   * 分享按钮
   */
  onShareAppMessage: function (res) {
    let that = this;
    var item_id = that.data.item_id,ucode = app.globalData.loginuser.ucode;
    return {
      path: '/pages/market/item?id=' + item_id + '&ucode=' + ucode,
    }
  },
  //返回首页
  toRedirect: function () {
    wx.switchTab({
      url: '/pages/index/index',
    });
  },
  //滚动
  onPageScroll(res) {
    wx.createSelectorQuery().select('.weui-panel').boundingClientRect().exec(rect => {
      if (rect[0].top - 100 <= 0){
        this.setData({
          'barbolor':'#FFFFFF',
          'appname': '产品详情'
        })
      }else{
        this.setData({
          'barbolor': 'none',
          'appname': ''
        })
      }
    })
  }
})