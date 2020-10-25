const app = getApp()
var WxParse = require('../../wxParse/wxParse.js');
Page({
  data: {
    disabled: true,
    bottom: false,
    item_id: 0,
    items: [],
    shopping_num: 0,  //购物车中的商品数量
    buy_num: 1,     //购买单个物品数量
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
    this.setData({
      item_id: item_id
    });
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    this.getItem();
    var shopping_num = wx.getStorageSync('shopping_num') || 0;
    this.setData({
      shopping_num: shopping_num
    });
  },
  /**
   * 获取商品信息
   */
  getItem: function (e) {
    let that = this;
    var item_id = that.data.item_id;
    app.api().Get('api/v1/popupshop/shop/item', {'id': item_id},function (result) {
      if (result.data.types == 1 && result.data.state == 1) {
        that.setData({
          disabled: false,
        });
      }
      that.setData({
        items: result.data,
      });
      WxParse.wxParse('content', 'html', result.data.content, that, 0);
    })
  },
  //加入购物车
  add_cart: function () {
    let that = this;
    var param = {
      buy_num:that.data.buy_num,
      item_id:that.data.item_id
    }
    app.api().Post('api/v1/popupshop-cart-edit',param,function (result) {
      let shopping_cart = wx.getStorageSync('shopping_cart') || [];
      var cart = new Array();
      if (app.util().isNull(shopping_cart)) {
        cart = result.data;
      } else {
        for (let sku in result.data) {
          shopping_cart[sku] = result.data[sku];
        }
        cart = shopping_cart;
      }
      //循环计算数组的和
      var shopping_num = 0;
      for (let i in cart) {
        shopping_num += cart[i];
      }
      wx.setStorageSync('shopping_cart',cart);
      wx.setStorageSync('shopping_num',shopping_num);
      that.setData({
        shopping_num:shopping_num
      })
      app.globalData.cartItemNumber = shopping_num;
    })
    that.setData({
      bottom:false
    });
  },
  //立即购买
  buy_now: function () {
    this.add_cart();
    setTimeout(function () {
      wx.switchTab({url: 'cart' })
    }, 1000);
  },
  //购物车层
  toggleBottomPopup: function () {
    this.setData({
      bottom: !this.data.bottom
    });
  },
  //编辑购物数量
  buyNumber: function (e) {
    this.setData({
      buy_num: e.detail
    })
  },
  /**
   * 分享按钮
   */
  onShareAppMessage: function (res) {
    let that = this;
    var item_id = that.data.item_id, items = that.data.items, ucode = app.globalData.loginuser.ucode;
    return {
      title: '[￥' + items.item.sell_price + ']' + items.item.name,
      desc: items.item.title,
      path: '/pages/shop/item?id=' + item_id + '&ucode=' + ucode,
      imageUrl: that.data.url + items.item.img,
      success(e) {
        wx.showShareMenu({ withShareTicket: true });
      }
    }
  },
  //移除触摸限制
  moveTouch: function () {

  }
})