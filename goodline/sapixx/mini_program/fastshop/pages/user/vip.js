const app = getApp()

Page({

  /**
   * 页面的初始数据
   */
  data: {
    loading: false,
    isvip:false,
    price:0,
    ucode:''
  },
  /**
   * 每次显示重新载入
   */
  onShow: function () {
    this.getCard();
  },
  /**
   * 获取会员卡信息
   */
  getCard:function(){
    let that = this;
    app.api().Get('api/v3/fastshop/bank/isvip', function (rel) {
      if (rel.code == 200){
        that.setData({
          isvip:true,
          ucode:app.globalData.loginuser.ucode
        })
      }else{
        var price = rel.data;
        that.setData({
          price: price.toFixed(2)
        })
      }
    })
  },
  /**
    * 判断是微信IOS还是Android
    */
  openVip: function (res) {
    let that = this;
    wx.getSystemInfo({
      success: function (res) {
        that.setData({
          systemInfo: res,
        })
        if (res.platform == "ios") {
          wx.showModal({
            content: '十分抱歉,由于相关规定,暂不支苹果用户开通会员.',
          })
        } else {
          that.doPay();
        }
      }
    })
  },
  //唤启支付
  doPay:function(){
    let that = this;
    wx.showLoading({title:'请稍后',mask: true})
    app.api().Post('api/v3/fastshop/bank/openVip', function (rel) {
      if (rel.code == 200) {
        if (rel.data.type == 1) {
          that.setData({
            chickPayBtn: true,
            orderParams: rel.data.order
          })
        } else {
          app.doWechatPay(rel.data.order, function (res) {
            that.getCard();
          }, function (res) {
            wx.showModal({
              content: '支付失败或取消',
              showCancel: false
            })
          })
        }
      }
    })
  },
  /**
   * 服务协议
   */
  contract: function () {
    let that = this;
    let config = app.globalData.wechat_config;
    wx.navigateTo({
      url: '/pages/helper/webview?src=' + app.api_root + '/api-' + config.app_id + '/v3/fastshop/webview/contract'
    })
  },
  /**
   * 开通会员特权
   */
  service: function () {
    let that = this;
    let config = app.globalData.wechat_config;
    wx.navigateTo({
      url: '/pages/helper/webview?src=' + app.api_root + '/api-' + config.app_id + '/v3/fastshop/webview/service'
    })
  },
  /**
   * 支付成功的事件处理函数
   * res.detail 为 payjs 小程序返回的订单信息
   * 可通过 res.detail.payjsOrderId 拿到 payjs 订单号
   * 可通过 res.detail.responseData 拿到详细支付信息
   */
  goodPaySuccess: function (res) {
    if (res.detail.return_code = "SUCCESS") {
      this.getCard();
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