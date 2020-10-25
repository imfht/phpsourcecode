const app = getApp();
Page({
  /**
   * 页面的初始数据
   */
  data: {
    item: [],
    active: 0,
    steps: [{text: '待付款'},{text: '已付款'},{text: '已发货'},{text: '已完成'}],
    order_no: null,
    disabled:false,
    actions: [],
    orderParams: {},    // 支付参数
    chickPayBtn: false, //点击了支付按钮（订单信息交由古德云组件）
    chickOnPay: false, // 用户是否已经点击了「支付」并成功跳转到 古德云收银台 小程序
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
  /**
   * 读取订单信息
   */
  getOrder: function (order_no) {
    let that = this;
    app.api().Get("api/v3/fastshop/cart/review", {order_no:order_no}, function (rel) {
      var item = rel.data;
      var active = 0;
      for (let i in item) {
        if (item[i]['status'] == 1) {
          active = 3;
        } else {
          if (item[i]['paid_at'] == 0) {
            active = 0;
          } else {
            if (item[i]['express_status'] == 0) {
              active = 1;
            } else {
              active = 2;
            }
          }
        }
      }
      that.setData({
        item: rel.data,
        active: active,
      })
    })
  },
  /**
   * 唤起微信支付
   */
  wchatPayment: function (buytype) {
    let that = this;
    var param = {
      order_no: that.data.order_no,
      buytype: buytype,
    }
    app.api().Post('api/v3/fastshop/cart/retryPay',param,function (rel) {
      if (200 == rel.code) {
        if (rel.data.type == 1) {
          that.setData({
            chickPayBtn: true,
            orderParams: rel.data.order
          })
        } else {
          app.doWechatPay(rel.data.orde,function (res) {
            that.getOrder(onderNo);
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
  //关闭订单
  closeOrder: function (event) {
    let that = this;
    var onderNo = that.data.order_no;
    wx.showModal({
      content: '请确认要关闭订单?',
      success: function (res) {
        if (res.confirm) {
          app.api().Post("api/v3/fastshop/cart/closeorder", { order_no: onderNo }, function (rel) {
            wx.showModal({
              content: rel.msg,
              showCancel: false,
              success: function () {
                wx.navigateBack({
                  delta: 1
                })
              }
            })
          })
        }
      }
    })
  },
  //确认收货
  okOrder: function (event) {
    let that = this;
    var onderNo = that.data.order_no;
    wx.showModal({
      content: '确认要签收当前商品?',
      success: function (res) {
        if (res.confirm) {
          app.api().Post("api/v3/fastshop/cart/signOrder", { order_no: onderNo }, function (rel) {
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
  //查询快递
  getMaps: function (event) {
    let that = this;
    var onderNo = that.data.order_no;
    let config = app.globalData.wechat_config;
    wx.navigateTo({
      url: '/pages/helper/webview?src=' + app.api_root + '/api-' + config.app_id + '/v3/fastshop-webview-express&ids=' + onderNo,
    })
  },
  //支付方式
  payTypes: function () {
    let that = this;
    that.setData({
      show: true,
      disabled: true
    });
    if (that.data.show) {
      app.api().Get('api/v3/fastshop/index/shopBuyTypes', { 'signkey': 'shopBuyTypes' }, function (result) {
        that.setData({
          actions: result.data
        });
      })
    }
  },
  //是否弹出支付菜单
  onClose() {
    this.setData({
      show: false,
      disabled: false
    });
  },
  //选择支付方式
  onSelect(event) {
    let that = this;
    var payTypes = event.detail.types;
    that.setData({
      disabled: true
    });
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
      that.getOrder(this.data.order_no);
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