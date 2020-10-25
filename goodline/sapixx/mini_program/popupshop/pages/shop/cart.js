const app = getApp()
Page({
  /**
   * 页面的初始数据
   */
  data: {
    page: 0,
    cart_total: 0,
    cart_number: 0,
    item: [],
    likeitem:[],
    amount: [],
    address:[],
    address_isnull: 0,
    loading: false,
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    this.getCart();
    app.setTabBarCartNumber();
  },
  /**
 * 获取购物车中的订单
 */
  getCart: function () {
    let that = this;
    var cart = wx.getStorageSync('shopping_cart') || [];
    app.api().Post('api/v1/popupshop-cart-cartItem', { cart: cart }, function (result) {
      if (result.code == 200) {
        that.setData({
          item: result.data.item,
          amount: result.data.amount,
          cart_total: parseFloat(result.data.amount.real_amount) + parseFloat(result.data.amount.real_freight),
          cart_number: app.util().count(result.data.item),
        })
        that.getAddress();
      } else {
        wx.removeStorageSync('shopping_cart')
        wx.removeStorageSync('shopping_num')
        that.setData({
          item: [],
          cart_total: 0,
          cart_number: 0
        })
        that.getItem();
      }
    })
  },
  /**
  * 获取商品
  * 如果没有了商品将不再重复请求加载
  */
  getItem: function () {
    let that = this;
    var parms = {
      cate_id: 0,
      types: 3,
      page: 1
    }
    app.api().Get('api/v1/popupshop/shop/cateitem', parms, function (result) {
      that.setData({
        likeitem: result.data
      });
    })
  },
  /**
   * 删除购物车数据
   */
  onClose: function (e) {
    let that = this,
      item = that.data.item,
      id = parseInt(e.currentTarget.dataset.id);
    delete item[id];
    //计算总价格
    var cart_total = 0;
    var shopping_num = 0;
    for (let i in item) {
      cart_total += parseFloat(item[i].amount);
      shopping_num += parseInt(item[i].num);
    }
    //重新设置变量
    that.setData({
      item: item,
      cart_total: cart_total,
      cart_number: app.util().count(item),
    })
    //重新设置缓存
    var cart = wx.getStorageSync('shopping_cart');
    delete cart[id];
    wx.setStorageSync('shopping_cart',cart)
    wx.setStorageSync('shopping_num', shopping_num)  //购物车数量
    app.setTabBarCartNumber();
  },
  /**
   * 修改购物车数量
  */
  onChange: function (e) {
    let that = this;
    var item = that.data.item;
    var num = parseInt(e.detail);
    var id = parseInt(e.currentTarget.dataset.id);
      //重写购物车数据
      item[id].num = num;
      item[id].amount = app.util().accMul(num, item[id].sell_price).toFixed(2);
    var cart = wx.getStorageSync('shopping_cart') || [],
      cart_total = 0,
      shopping_num = 0;
    //计算总价格
    for (let i in item) {
      cart_total += parseFloat(item[i].amount);
      shopping_num += parseInt(item[i].num);
    }
    //重新设置缓存
    cart[id] = num;
    wx.setStorageSync('shopping_cart', cart)
    wx.setStorageSync('shopping_num', shopping_num)
    app.setTabBarCartNumber();
    //请求
    app.api().Post('api/v1/popupshop-cart-cartItem',{cart:cart}, function (result) {
      if (result.code == 200) {
        that.setData({
          amount: result.data.amount,
          item: item,
          cart_total: cart_total + parseFloat(result.data.amount.real_freight)
        })
      }
    })
  },
  /**
   * 唤起微信支付
   */
  wchatPayment: function () {
    let that = this;
    var ids = app.util().clearArray(wx.getStorageSync('shopping_cart'));
    var param = {
      ids:JSON.stringify(ids),
      address: that.data.address.id,
      ucode:app.globalData.loginuser.ucode
    }
    wx.showLoading({ title: '正在加载', mask: true })
    app.api().Post('api/v1/popupshop-cart-doPay',param,function (rel) {
      if (200 == rel.code) {
        app.doWechatPay(rel.data,function (res) {
          wx.navigateTo({
            url: '../order/index'
          })
        }, function (res) {
          wx.showModal({
            content: '支付失败或取消',showCancel: false,complete: function () {
              wx.navigateTo({
                url: '../order/index'
              });
            }
          })
        })
        wx.removeStorageSync('shopping_cart')
        wx.removeStorageSync('shopping_num')
        app.setTabBarCartNumber();
        wx.hideLoading();
      }
    })
  },
  /**
  * 读取微信地址
  */
  getAddress: function () {
    let that = this;
    app.api().Get("openapi/v1/user/getaddress", {'signkey': 'dopay'},function (rel) {
      if (rel.code == 200) {
        that.setData({
          address: rel.data,
          address_isnull: Object.keys(rel.data).length,
        })
      }
    })
  },
  /**
   * 读取微信地址
   */
  address: function () {
    let that = this;
    wx.getSetting({
      success(res) {
        if (res.authSetting['scope.address'] == false) {
          wx.openSetting({
            success: (res) => {
              res.authSetting = {
                "scope.address": true,
              }
            }
          })
        } else {
          wx.chooseAddress({
            success: function (res) {
              var name = res.userName;
              var telNumber = res.telNumber;
              var city = res.provinceName + res.cityName + res.countyName;
              var address = res.detailInfo;
              app.api().Post("openapi/v1/user/createaddress", {
                name: name,
                telphone: telNumber,
                city: city,
                address: address
              }, function (rel) {
                that.setData({
                  address: rel.data,
                  address_isnull: Object.keys(rel.data).length,
                })
              });
            }
          })
        }
      }
    })
  }
})