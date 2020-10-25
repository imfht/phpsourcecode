const app = getApp();
Page({
  /**
   * 页面的初始数据
   */
  data: {
    item: [],
    active: 0,
    is_fusion: 0,
    item_checkbox:0,
    checkbox: [] ,
    service:0,
    steps: [
      {text: '待付款'},
      {text: '待确认'},
      {text: '待发货'},
      {text: '已发货'}
    ],
    order_no: null,
  },
  /**
   * 生命周期函数--监听页面加载   */
  onLoad: function (options) {
    let order_no = options.order_no;
    this.setData({
      order_no: order_no,
      gift_1:false,
    })
    this.getOrder(order_no);
  },
  /**
  * 读取订单信息
  */
  getOrder: function (order_no) {
    let that = this, checkbox = that.data.checkbox;
    app.api().Get("api/v3/fastshop/order/review", { order_no: order_no }, function (rel) {
      var item = rel.data;
      var active = 0;
      if (item['status'] == 0) {
        if (item['paid_at'] == 0) {
          active = 0;
        } else {
          if (item['is_entrust'] == 0) {
            active = 1;
          } else {
            if (item['express_status'] == 0) {
              active = 2;
            } else {
              active = 3;
            }
          }
        }
      } else {
        active = 3;
      }
      //多选
      for (let n in item.gift) {
        checkbox[n] = 0;
      }
      that.setData({
        item: rel.data,
        active: active,
        checkbox: checkbox,
      })
    })
  },
  /**
  * 确认委托出售
  */
  onClickGift: function (e) {
    let that = this;
    var service = that.data.service;
    if (service == false){
      wx.showModal({
        content: '您必须遵守平台用户服务协议',
        showCancel: false, 
      });
    }else{
      wx.showModal({
        content: '确定把已选产品卖了换钱?',
        success: function (res) {
          if (res.confirm) {
            var param = {
              order_no: that.data.order_no,
              service: service,
              gift: JSON.stringify(that.data.checkbox),
              item_checkbox: that.data.item_checkbox
            }
            app.api().Post("api/v3/fastshop/order/giftAction",param,function (rel) {
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
    }
  },
  /*确认签收 */
  okOrder: function (event) {
    let that = this;
    var onderNo = that.data.order_no;
    wx.showModal({
      content: '您确认收到了吗?',
      success: function (res) {
        if (res.confirm) {
          app.api().Post("api/v3/fastshop/order/signorder", { order_no: onderNo }, function (rel) {
            wx.showModal({
              title: '友情提示',
              content: rel.msg,
              showCancel: false,
              success: function () {
                that.getOrder(onderNo);
                that.setData({
                  active: 3,
                })
              }
            })
          })
        }
      }
    })
  },
  /**
   * 选择主商品
   */
  onChangeItem(event) {
    this.setData({
      item_checkbox: this.data.item_checkbox?0:1
    })
  },
  /**
   * 选择赠品
   */
  onChange(event) {
    var key = event.currentTarget.dataset.key, checkbox = this.data.checkbox;
    for (let i in checkbox) {
      checkbox[key] = event.detail?1:0
    }
    this.setData({
      checkbox: checkbox
    })
  },
  /**
   * 是否选择用户服务协议
   */
  onService(event) {
    this.setData({
      service: event.detail?1:0
    });
  },
  /**查询快递 */
  getMaps: function (event) {
    var onderNo = this.data.order_no;
    let config = app.globalData.wechat_config;
    wx.navigateTo({
      url: '/pages/helper/webview?src=' + app.api_root + '/api-' + config.app_id + '/v3/fastshop/webview/express&ids=' + onderNo,
    })
  },
  /**
   * 服务协议
   */
  service() {
    let config = app.globalData.wechat_config;
    wx.navigateTo({
      url: '/pages/helper/webview?src=' + app.api_root + '/api-' + config.app_id + '/v3/fastshop/webview/service'
    })
  }
})