var api = require('../../utils/request');
var util = require('../../utils/util');
import Dialog from '../../vant/dialog/dialog';
const app = getApp();
Page({
  data: {
    disabled: false,
    item: [],
    amount: [],
    address: {},
    address_isnull: 0,
  },
  /*
   生命周期函数--监听页面初次载入
  */
  onShow: function () {
    this.getAddress();
    this.getCart();
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
    var param = {
      cart: wx.getStorageSync('cart')
    }
    app.api().Post('api/v1/popupshop/sale/cartItem',param,function (result) {
      if (result.code == 200) {
        that.setData({
          item: result.data,
          amount: result.data.amount
        })
      } else {
        wx.removeStorage('cart')
        wx.switchTab({
          url: result.url
        })
      }
      wx.hideLoading();
    })
  },
  /**
   * 唤起微信支付
   */
  weWchatPayment: function (url) {
    let that = this;
    that.setData({
      disabled:true
    })
    if (app.util().isNull(wx.getStorageSync('cart'))){
     wx.navigateBack({
       delta: 1
     })
    }
    var param = {
      address: that.data.address.id,
      cart:wx.getStorageSync('cart'),
      ucode:app.globalData.loginuser.ucode
    }
    app.api().Post('api/v1/popupshop/sale/doPay',param,function (rel) {
      if (200 == rel.code) {
        wx.removeStorageSync('cart');
        app.doWechatPay(rel.data,function(){
          wx.redirectTo({
            url: '../user/gift'
          });
        },function(){
          wx.showModal({
            content: '支付失败或取消', showCancel: false,complete:function () {
              wx.switchTab({
                url: '../market/index'
              })
            }
          })
        })
      }else{
        that.setData({
          disabled: false
        })
        wx.showModal({
          content: rel.msg,
          showCancel: false
        })
      }
    })
  },
  /**
* 读取微信地址
*/
  getAddress: function () {
    let that = this;
    app.api().Get("openapi/v1/user/getaddress",{'signkey':'dopay'},function (rel) {
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
              var param = {
                name: res.userName,
                telphone: res.telNumber,
                city: res.provinceName + res.cityName + res.countyName,
                address: res.detailInfo
              }
              app.api().Post("openapi/v1/user/createaddress",param,function (rel) {
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