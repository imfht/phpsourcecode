const app = getApp();
Page({
  data: {
    loading: false,
    item: [],
    amount: [],
    address: [],
    address_isnull: 0,
    actions: [],
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
   * 获取购物车中的订单
   */
  getCart: function () {
    let that = this;
    var cart = wx.getStorageSync('group');
    app.api().Post('api/v3/fastshop/group/cartItem', {
      cart: cart
    }, function (result) {
      if (result.code == 200) {
        that.setData({
          item: result.data.item,
          amount: result.data.amount
        })
      } else {
        wx.removeStorageSync('store')
        wx.navigateBack()
      }
    })
  },
  /**
    * 唤起微信支付
    */
  wchatPayment: function (buytype) {
    let that = this;
    var ids = app.util().clearArray(wx.getStorageSync('group'));
    var param = {
      ids: ids,
      address: that.data.address.id,
      buytype: buytype,
      ucode: app.globalData.loginuser.ucode
    }
    app.api().Post("api/v3/fastshop/group/doPay", param, function (rel) {
      if (200 == rel.code) {
        wx.removeStorageSync('group')
        if (rel.data.type == 1) {
          that.setData({
            chickPayBtn: true,
            orderParams: rel.data.order
          })
        } else {
          app.doWechatPay(rel.data.order, function (res) {
            wx.navigateTo({
              url: '../order/index'
            })
          }, function (res) {
            wx.showModal({
              content: '支付失败或取消', showCancel: false, complete: function () {
                wx.navigateTo({
                  url: '../order/index'
                });
              }
            })
          })
        }
      }
    })
  },
  /**
   * 读取微信地址
   */
  getAddress: function () {
    let that = this;
    app.api().Get("openapi/v1/user/getaddress", { 'signkey': 'dopay' }, function (rel) {
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
      app.api().Get('api/v3/fastshop/index/shopBuyTypes', { 'signkey': 'shopBuyTypes' }, function (result) {
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
      show: false
    });
  },
  onSelect(event) {
    let that = this;
    var address_isnull = that.data.address_isnull
    var addressId = that.data.address.id;
    var payTypes = event.detail.types;
    wx.showLoading({ title: '支付中' })
    switch (payTypes) {
      case 1:
        that.wchatPayment("wepay");
        break;
      case 2:
        that.wchatPayment("point");
        break;
      default:
        that.onClose();
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
      wx.navigateTo({
        url: '/pages/order/index'
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
      cart_number: 0
    })
  },
  /**
   * 支付完毕的事件处理函数
   * 无论支付成功或失败均会执行
   */
  goodPayComplete: function () {
    this.setData({
      chickPayBtn: false,
      cart_number: 0
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