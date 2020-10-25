const app = getApp();

Page({
  /**
   * 页面的初始数据
   */
  data: {
    steps: [
      { text: '待付款' },
      { text: '待确认' },
      { text: '待发货' },
      { text: '已发货' },
    ],
    active: 0,
    item: [],
    order_no: null
  },
  /**
   * 生命周期函数--监听页面加载   */
  onLoad: function (options) {
    let order_no = options.order_no;
    this.setData({
      order_no: order_no,
    })
  },
  onShow: function () {
    this.getOrder(this.data.order_no);
  },
  /**
    * 读取订单信息
    */
  getOrder: function (order_no) {
    let that = this,active = 0;
    var param = {
      order_no: order_no
    }
    app.api().Get("api/v1/popupshop/user/saleOrderReview",param,function (rel) {
      if (rel.data.status == 1) {
        active = 3;
      } else {
        if (rel.data.paid_at == 1) {
          if (rel.data.is_entrust == 1) {
            active = rel.data.express_status == 1 ? 3 :2;
          } else {
            active = 1;
          }
        } else {
          active = 0;
        }
      }
      that.setData({
        item: rel.data,
        active: active,
      })
    })
  },
  /*确认签收 */
  okOrder: function (event) {
    let that = this;
    var onderNo = that.data.order_no;
    wx.showModal({
      content: '您确认收到了吗?',
      success: function (res) {
        if (res.confirm) {
          api.Post("api/v1/popupshop-order-signorder", { order_no: onderNo }, function (rel) {
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
  /**查询快递 */
  getMaps: function (event) {
    var onderNo = this.data.order_no;
    let config = app.globalData.wechat_config;
    wx.navigateTo({
      url: '/pages/helper/webview?src=' + this.data.url + '/api-' + config.app_id + '/v1/popupshop-webview-express&ids=' + onderNo,
    })
  },
  /**
   * 买了去还钱
   */
  onUnder: function (event) {
    let that = this;
    wx.showModal({
      content: "你确认要把当前宝贝卖了换钱",
      success(res) {
        if (res.confirm) {
          var param = {
            id: event.currentTarget.dataset.id,
            order_no: event.currentTarget.dataset.order_no
          }
          app.api().Post('api/v1/popupshop/store/isOnSale', param,(res) =>{
            wx.navigateTo({
              url: res.url,
            })
          })
        }
      }
    });
  },
  /**
   * 确认退货
   */
  onOut: function (e) {
    let that = this;
    wx.showModal({
      content: "你确认要申请退货",
      success(res) {
        if (res.confirm) {
          var param = {
            order_no: e.currentTarget.dataset.order_no
          }
          app.api().Post('api/v1/popupshop/user/orderOut', param, (result) => {
            if (result.code == 200) {
              wx.showModal({
                content: result.msg,
                showCancel: false,
                success: function (rel) {
                  that.getOrder(e.currentTarget.dataset.order_no);
                }
              })
            }
          })
        };
      }
    });
  },
  /**
   * 去管理小店
   */
  onStore: function () {
    wx.navigateTo({
      url: '/pages/store/index'
    })
  },
})