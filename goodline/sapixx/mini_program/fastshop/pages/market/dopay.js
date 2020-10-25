var api = require('../../utils/request');
const app = getApp();
Page({
  data: {
    item: [],
    amount: [],
    address: {},
    address_isnull: 0,
    actions: [],
    disabled:false,
    orderParams: {},    // 支付参数
    chickPayBtn: false, //点击了支付按钮（订单信息交由古德云组件）
    chickOnPay: false, // 用户是否已经点击了「支付」并成功跳转到 古德云收银台 小程序
  },
  /*
   生命周期函数--监听页面初次载入
  */
  onLoad: function () {
    this.getAddress();
    this.getCart();
  },
  /**
   * 唤起微信支付
   */
  wchatPayment: function (buytype) {
    let that = this;
    var param = {
      address: that.data.address.id,
      ids: wx.getStorageSync('cart'),
      ucode: app.globalData.loginuser.ucode,
      buytype: buytype,
    }
    app.api().Post('api/v3/fastshop/order/doPay',param,function (rel) {
      if (200 == rel.code) {
        if (rel.data.type == 1) {
          that.setData({
            chickPayBtn: true,
            orderParams: rel.data.order
          })
        } else {
          wx.removeStorageSync('cart');
          app.doWechatPay(rel.data.order, function () {
            wx.navigateTo({ url: '../user/gift' });
          }, function () {
            wx.showModal({
              content: '支付失败或取消', showCancel: false, complete: function () {
                wx.switchTab({ url: '../market/index' })
              }
            })
          })
        }
      }
      wx.hideLoading();
    })
  },
  /**
  * 读取微信地址
  */
  getAddress: function () {
    let that = this;
    app.api().Get("openapi/v1/user/getaddress", {'signkey':'dopay' }, function (rel) {
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
    //获取用户权限
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
              app.api().Post("openapi/v1/user/createAddress", {
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
  },
  /**
   * 获取购物车中的订单
   */
  getCart: function () {
    let that = this;
    wx.showLoading({
      title: '正在加载',
      mask: true
    })
    var cart = wx.getStorageSync('cart');
    app.api().Post('api/v3/fastshop/order/cartItem', {
      cart: cart
    }, function (result) {
      if (result.code == 200) {
        that.setData({
          item: result.data.item,
          amount: result.data.amount
        })
      } else {
        wx.setStorageSync('cart', 0)
        wx.switchTab({
          url: result.url
        })
      }
      wx.hideLoading();
    })
  },
  /**
  * 支付方式
  */
  payTypes: function () {
    let that = this;
    var actions = that.data.actions;
    var address_isnull = that.data.address_isnull
    if (address_isnull == 0) {
      wx.showModal({
        content: '必须选择收货地址',
      })
    } else {
      this.setData({
        show: true
      });
    }
    if (that.data.show) {
      app.api().Get('api/v3/fastshop/index/saleBuyTypes',{'signkey': 'saleBuyTypes' }, function (result) {
        that.setData({
          actions: result.data
        });
      })
    }
  },
  /**
   * 是否弹出支付菜单
  */
  onClose() {
    this.setData({
      show: false,
      disabled: false
    });
  },
  /**
   * 支付方式
   */
  payTypes: function () {
    let that = this;
    var actions = that.data.actions;
    var address_isnull = that.data.address_isnull
    if (address_isnull == 0) {
      wx.showModal({
        content: '必须选择收货地址',
      })
    } else {
      that.setData({
        show: true,
        disabled: true
      });
    }
    if (that.data.show) {
      app.api().Get('api/v3/fastshop/index/saleBuyTypes', { 'signkey': 'saleBuyTypes' }, function (result) {
        that.setData({
          actions: result.data
        });
      })
    }
  },
  /**
   * 选择支付方式
   */
  onSelect(event) {
    var address_isnull = this.data.address_isnull
    var addressId = this.data.address.id;
    var payTypes = event.detail.types;
    switch (payTypes) {
      case 1:
        this.wchatPayment("wepay");
        break;
      case 2:
        this.wchatPayment("point");
        break;
      default:
        this.onClose();
    }
  },
  /**
   * 支付成功的事件处理函数
   * res.detail 为 payjs 小程序返回的订单信息
   * 可通过 res.detail.payjsOrderId 拿到 payjs 订单号
   * 可通过 res.detail.responseData 拿到详细支付信息
   */
  goodPaySuccess: function (res) {
    if (res.detail.return_code = "SUCCESS") {
      wx.removeStorageSync('cart');
      wx.navigateTo({
        url: '/pages/user/gift'
      })
    }
  },
  /**
  * 支付失败的事件处理函数
  * res.detail.error 为 true 代表传入小组件的参数存在问题
  * res.detail.navigateSuccess 代表了是否成功跳转到 PAYJS 小程序
  * res.detail.event 可能存有失败的原因
  * 如果下单成功但是用户取消支付则 res.detail.event.return_code == FAIL
  */
  goodPayFail: function (res) {
    this.setData({
      chickPayBtn: false,
    })
  },
  /**
   * 支付完毕的事件处理函数
   * 无论支付成功或失败均会执行
   */
  goodPayComplete: function (res) {
    this.setData({
      chickPayBtn: false,
    })
  },
  /**
   * 组件内部数据被修改时的事件
   * 当用户跳转到 云收银台 小程序并等待返回的过程中 chickOnPay 值为 true
   */
  goodPayChange(res) {
    if (res.detail.chickOnPay) {
      this.setData({
        chickOnPay: res.detail.chickOnPay
      })
    }
  }
})