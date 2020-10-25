const app = getApp()
var api = require('../../utils/request');
var util = require('../../utils/util');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    loading: false,
    bank: {
      money: 0,
      due_money: 0,
      lack_money: 0,
      income_monney: 0,
      shop_money: 0,
      orderParams: {},    // 支付参数
      chickPayBtn: false, //点击了支付按钮（订单信息交由古德云组件）
      chickOnPay: false, // 用户是否已经点击了「支付」并成功跳转到 古德云收银台 小程序
    },
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    this.getBank();
  },
  /**
   * 读取我的账单
   */
  getBank: function () {
    let that = this;
    app.api().Get("api/v3/fastshop/bank/index", function (result) {
      if (result.code == 200) {
        that.setData({ bank: result.data });
      }
    })
  },
  /**
   * 申请提现
   */
  onSubmit: function (e) {
    let that = this;
    var data = e.detail.value;
    var isPost = false;
    //安全密码
    if (util.isNull(data.safepassword)) {
      wx.showModal({
        content: '安全密码必须填写', showCancel: false
      })
    } else{
      if (util.isNull(data.money)) {
        wx.showModal({
          content: '转入金额必须填写', showCancel: false
        })
      } else {
        if (!(/^\d+$/.test(data.money))) {
          wx.showModal({
            content: '转入金额只能输入整数', showCancel: false
          })
        } else {
          if (data < 100) {
            wx.showModal({
              content: '最小转入金额100元', showCancel: false
            })
          }else{
            isPost = true;
          }
        }
      }
    }
    //提交数据
    if (isPost == true) {
      wx.showLoading({
        title: '提交申请中',
        mask: true
      })
      var parms = {
        money: data.money,
        safepassword: data.safepassword,
      }
      app.api().Post('api/v3/fastshop/bank/recharge', parms, function (rel) {
        if (rel.code == 200){
          if (rel.data.type == 1) {
            that.setData({
              chickPayBtn: true,
              orderParams: rel.data.order
            })
          } else {
            app.doWechatPay(rel.data.order, function (res) {
              wx.navigateBack({
                delta: 1
              })
            }, function (res) {
              wx.showModal({
                content: '充值失败',
                showCancel: false
              })
            })
          }
        }
      })
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
      wx.navigateBack({
        delta: 1
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
  goodPayComplete: function () {
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