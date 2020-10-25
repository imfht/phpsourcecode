const app = getApp();
Page({
  /**
   * 页面的初始数据
   */
  data: {
    item: [],
    active: 0,
    steps: [
      {text: '待付款'},
      {text: '已付款'},
      {text: '待发货'},
      {text: '已收货'}
    ],
    order_no: null
  },
  /**
   * 生命周期函数--监听页面加载   */
  onLoad: function (options) {
    let order_no = options.order_no;
    this.setData({
      order_no: order_no
    })
    this.getOrder(order_no);
  },
  /**关闭订单 */
  closeOrder: function (event) {
    let that = this;
    var onderNo = that.data.order_no;
    wx.showModal({
      content: '请确认要关闭订单?',
      success: function (res) {
        if (res.confirm) {
          app.api().Post("api/v1/popupshop/cart/closeorder", { order_no: onderNo }, function (rel) {
            wx.showModal({
              content: rel.msg,
              showCancel: false,
              success: function () {
                wx.navigateBack({
                  delta:1
                })
              }
            })
          })
        }
      }
    })
  },
  /*确认收货 */
  okOrder: function (event) {
    let that = this;
    var onderNo = that.data.order_no;
    wx.showModal({
      content: '确认要签收当前商品?',
      success: function (res) {
        if (res.confirm) {
          app.api().Post("api/v1/popupshop/cart/signOrder", { order_no: onderNo }, function (rel) {
            wx.showModal({
              content: rel.msg,
              showCancel: false,
              success: function () {
                that.getOrder(onderNo)
              }
            })
          })
        }
      }
    })
  },
  /**查询快递 */
  getMaps: function (event) {
    let that = this;
    var onderNo = that.data.order_no;
    let config = app.globalData.wechat_config;
    wx.navigateTo({
      url: '/pages/helper/webview?src=' + app.apiroot + '/api-' + config.app_id + '/v1/popupshop-webview-express&ids=' + onderNo,
    })
  },
  /**
   * 读取订单信息
   */
  getOrder: function (order_no) {
    let that = this;
    wx.showLoading({ title: '正在加载', mask: true })
    app.api().Get("api/v1/popupshop/cart/review", { order_no: order_no }, function (rel) {
      var item = rel.data;
      var active = 0;
      for (let i in item) {
        if (item[i]['status'] == 0) {
          if (item[i]['paid_at'] == 0) {
            active = 0;
          } else {
            if (item[i]['is_entrust'] == 1) {
              active = 2;
            }else{
              if (item[i]['express_status'] == 1) {
                active = 3;
              } else {
                active = 1;
              }
            }
          }
        } else {
          active = 4;
        }
      }
      that.setData({
        item: rel.data,
        active: active,
      })
      wx.hideLoading();
    })
  },
  /**
   * 唤起微信支付
   */
  dopay: function (e) {
    let that = this;
    wx.showLoading({
      title: '正在加载', mask: true
    })
    var param = {
      order_no: that.data.order_no
    }
    app.api().Post('api/v1/popupshop/cart/retrypay', param, function (rel) {
      if (200 == rel.code) {
        app.doWechatPay(rel.data, function (res) {
          that.getOrder(onderNo);
        }, function (res) {
          wx.showModal({
            content: '支付失败或取消',
            showCancel: false
          })
        })
      }
    })
  }
})