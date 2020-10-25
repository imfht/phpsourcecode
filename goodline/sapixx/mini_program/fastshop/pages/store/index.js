const app = getApp()
Page({
  data: {
    loading: true,
    openStore: false,
    is_shop: false,
    service: false,
    store_name:'匿名小店',
    lists: [],
    page: 0,
    types:0,
    actions: [],
    orderParams: {},    // 支付参数
    chickPayBtn: false, //点击了支付按钮（订单信息交由古德云组件）
    chickOnPay: false, // 用户是否已经点击了「支付」并成功跳转到 古德云收银台 小程序
  },
  //每次重新载入
  onShow:function(options) {
    this.getStore();
  },
  //上拉加载
  onReachBottom: function () {
    this.setData({
      loading: true,
    });
    that.getItem(this.data.types);
  },
  //我的小店
  getStore: function () {
    var that = this;
    app.api().Get('api/v3/fastshop/store/isopen',function (result) {
      if (result.code == 200){
        that.setData({
          is_shop: true,
          store_name: result.data.name
        });
        that.getItem(0);
      }
    })
  },
  //点击请求数据
  getItem: function (types) {
    let that = this;
    if (that.data.loading) {
      var param = {
        types: types,
        page:that.data.page + 1
      };
      app.api().Get('api/v3/fastshop/store/index',param,function (result) {
        if (result.code == 200) {
          var lists = that.data.lists;
          for (let i in result.data) {
            lists.push(result.data[i]);
          }
          that.setData({
            lists: lists,
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
  * 确认委托出售
  */
  formSubmit:function (e) {
    let that = this;
    var data = e.detail.value;
    var isPost = true;
    var service = that.data.service;
    if (service == false) {
      wx.showModal({
        content: '您必须遵守平台用户服务协议', showCancel: false
      })
      isPost = false;
    } else if (app.util().isNull(data.store_name)) {
      wx.showModal({
        content: '小店名称必须输入', showCancel: false
      })
      isPost = false;
    }
    //提交数据
    if (isPost == true) {
      wx.showLoading({
        title: '提交申请中',
        mask: true
      })
      var parms = {
        store_name: data.store_name
      }
      app.api().Post('api/v3/fastshop/store/regStore', parms, function (res) {
        wx.showModal({
          showCancel: false,
          title: '友情提示',
          content: res.msg,
          complete: function () {
            wx.navigateBack({
              delta: 1
            })
          }
        })
      })
    }
  },
  //操作上下架
  onUnder:function(event){
    let that = this;
    var id = event.currentTarget.dataset.id, key = event.currentTarget.dataset.key;
    var lists = that.data.lists;
    wx.showModal({
      content: "你确认要操作当前宝贝",
      success(res) {
        if (res.confirm) {
          app.api().Post('api/v3/fastshop/store/onUnder', {id: id}, function (res) {
            wx.showModal({
              showCancel: false,
              content: res.msg
            })
            lists[key].is_under = lists[key].is_under == 0 ? 1 : 0,
            that.setData({
              lists: lists,
              });
          })
        }
      }
    });
  },
  //操作提货
  wchatPayment: function (buytype,id) {
    let that = this;
    wx.showModal({
      content: "提货,将无法再次委托,默认以上一次收货地址为准,需单独支付快递费",
      success(res) {
        if (res.confirm) {
          var param = {
            id: id,
            buytype: buytype,
          }
          app.api().Post('api/v3/fastshop/store/onOrder', param,function (rel) {
            if (200 == rel.code) {
              if (rel.data.type == 1) {
                that.setData({
                  chickPayBtn: true,
                  orderParams: rel.data.order
                })
              } else {
                app.doWechatPay(rel.data.orde, function (res) {
                  wx.navigateTo({url: '../order/index' });
                }, function (res) {
                  wx.showModal({
                    content: '支付失败或取消',
                    showCancel: false
                  })
                })
              }
            }
          })
        }
      }
    });
  },
  //点击切换
  onChange(event) {
    this.setData({
      types: event.detail.name,
      lists: [],
      page: 0,
      loading: true,
    })
    this.getItem(event.detail.name);
  },
  //咨询客服层
  toggleServicePopup: function () {
    let that = this;
    that.setData({
      openStore: !this.data.openStore
    });
  },
  /**
   * 是否选择用户服务协议
   */
  onService(event) {
    this.setData({
      service: event.detail
    });
  },
  /**
   * 服务协议
   */
  service() {
    let that = this;
    let config = app.globalData.wechat_config;
    wx.navigateTo({
      url: '/pages/helper/webview?src='+ app.api_root + '/api-' + config.app_id + '/v3/fastshop/webview/service'
    })
  },
  //移除触摸限制
  moveTouch: function () {

  },
  //是否弹出支付菜单
  onClose() {
    this.setData({
      show: false,
      disabled: false
    });
  },
  //支付方式
  payTypes: function (event) {
    let that = this;
    that.setData({
      show: true,
    });
    if (that.data.show) {
      app.api().Get('api/v3/fastshop/index/shopBuyTypes', { 'signkey': 'shopBuyTypes' }, function (result) {
        that.setData({
          actions: result.data,
          item_id: event.currentTarget.dataset.id,
        });
      })
    }
  },
  //选择支付方式
  onSelect(event) {
    var payTypes = event.detail.types;
    this.setData({
      disabled: true
    });
    switch (payTypes) {
      case 1:
        this.wchatPayment("wepay",this.data.item_id);
        break;
      case 2:
        this.wchatPayment("point",this.data.item_id);
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