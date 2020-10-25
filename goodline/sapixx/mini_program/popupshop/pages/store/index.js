const app = getApp()

Page({
  data: {
    tabs: ["待确认", "已确认", "已发货"],
    loading: true,
    openStore: false,
    isopenStore: false,
    service: false,
    store_name:'匿名小店',
    item: [],
    page: 0,
  },
  //每次重新载入
  onShow:function(options) {
    this.getStore();
  },
  //上拉加载
  onReachBottom: function () {
    var that = this;
    that.setData({
      loading: true,
    });
    that.getItem(that.data.types);
  },
  //我的小店
  getStore: function () {
    var that = this;
    app.api().Get('api/v1/popupshop/store/isOpen',function (result) {
      if (result.code == 200){
        that.setData({
          isopenStore: true,
          loading: true,
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
        page:that.data.page + 1,
        types: types
      }
      app.api().Get('api/v1/popupshop/store/item', param, function (result) {
        if (result.code == 200) {
          var item = that.data.item;
          for (let i in result.data) {
            item.push(result.data[i]);
          }
          that.setData({
            item: item,
            page: param.page,
          });
        }
        that.setData({
          loading: false,
        });
      })
    }
  },
  //确认上下架
  onUnder:function(event){
    let that = this;
    var id = event.currentTarget.dataset.id, key = event.currentTarget.dataset.key;
    var item = that.data.item;
    wx.showModal({
      content: "你确认要操作当前宝贝",
      success(res) {
        if (res.confirm) {
          app.api().Post('api/v1/popupshop/store/onUnder',{id:id}, function (res) {
            wx.showModal({
              content: res.msg,
              showCancel: false,
            })
            item[key].is_sale = item[key].is_sale == 0 ? 1 : 0,
            that.setData({
              item: item,
            });
          })
        }
      }
    });
  },
  //确认提货
  onOrder: function (event) {
    let that = this;
    var id = event.currentTarget.dataset.id, key = event.currentTarget.dataset.key,item = that.data.item;
    wx.showModal({
      content:"提货,将无法再次委托,默认以上一次收货地址为准,需单独支付快递费",
      success(res) {
        if (res.confirm) {
          app.api().Post('api/v1/popupshop/store/onOrder',{id:id},function (rel) {
            item[key].is_sale = 0;
            item[key].is_out = 1;
            item[key].status_text = '已提货';
            that.setData({
              item: item,
            });
            app.doWechatPay(rel.data, function (res) {
              wx.showModal({
                content: '确认提货成功', showCancel: false})
            }, function (res) {
              wx.showModal({
                content: '请去我的订单中完成付款', showCancel: false, success(res) {
                  wx.navigateTo({
                    url: '../order/index',
                  })
                }
              })
            })
          })
        }
      }
    })
  },
  /**
   * 申请开通小店
   */
  formSubmit: function (e) {
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
        store_name: data.store_name,
        formId: e.detail.formId
      }
      app.api().Post('api/v1/popupshop/store/regStore', parms, function (res) {
        wx.showModal({
          content: res.msg, showCancel: false, complete: function () {
            that.setData({
              loading: true,
            });
            that.getStore();
            that.toggleServicePopup();
          }
        })
      })
    }
  },
  //点击切换
  onChange(event) {
    this.setData({
      types:event.detail.name,
      item:[],
      page:0,
      loading:true,
    })
    this.getItem(event.detail.name);
  },
  //咨询弹窗
  toggleServicePopup: function () {
    this.setData({
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
    let config = app.globalData.wechat_config;
    wx.navigateTo({
      url: '/pages/helper/webview?src=' + app.apiroot + '/api-' + config.app_id + '/v1/popupshop/webview/service'
    })
  },
  //移除触摸限制
  moveTouch: function () {

  }
})