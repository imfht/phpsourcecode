const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    loading: true,
    tabs: ["我的订单","待确认", "已确认", "已发货"],
    activeIndex: 0,
    page: 0,
    order:[],
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onShow: function (options) {
    this.setData({
      loading: true,
      page:0,
      order: [],
    });
    this.getApi();
  },
  //下拉
  onReachBottom: function () {
    this.setData({
      loading: true,
    });
    this.getApi();
  },
  /**
   * 我的出售API
   */
  getApi: function () {
    let that = this;
    if (that.data.loading) {
      var param = {
        page: that.data.page + 1,
        types: that.data.activeIndex
      }
      app.api().Get('api/v1/popupshop/user/saleOrder', param,function (result) {
        if (result.code == 200) {
          var order = that.data.order;
          for (let i in result.data) {
            order.push(result.data[i]);
          }
          that.setData({
            order: order,
            page: param.page,
          });
        }
        that.setData({
          loading: false,
        });
      })
    }
  },
  /**
   * 买了去还钱
   */
  onUnder: function(event) {
    let that = this;
     wx.showModal({
      content: "你确认要把当前宝贝卖了换钱",
      success(res) {
        if (res.confirm) {
          var param = {
            id:event.currentTarget.dataset.id,
            order_no:event.currentTarget.dataset.order_no
          }
          app.api().Post('api/v1/popupshop/store/isOnSale',param,function (res) {
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
    wx.showModal({
      content: "你确认要申请退货",
      success(res) {
        if (res.confirm) {
          var param = {
            order_no: e.currentTarget.dataset.order_no
          }
          app.api().Post('api/v1/popupshop/user/orderOut', param, function (result) {
            if (result.code == 200) {
              wx.showModal({
                content: result.msg,
                showCancel: false,
                success: function (rel) {
                  wx.navigateTo({
                    url: 'giftview?order_no=' + param.order_no
                  })
                }
              })
            }
          })
        };
      }
    });
  },
  /**
   * 查看订单详情
   */
  onView: function (e) {
    var order_no = e.currentTarget.dataset.order_no;
    wx.navigateTo({
      url: 'giftview?order_no=' + order_no
    })
  },
  /**
   * 去管理小店
   */
  onStore: function () {
    wx.navigateTo({
      url: '/pages/store/index'
    })
  },
  /**
  * 点击Tab切换
  */
  selecttab: function (e) {
    let that = this;
    var id = e.currentTarget.id;
    that.setData({
      page: 0,
      loading: true,
      activeIndex: id,
      order: [],
    });
    that.getApi();
  }
})