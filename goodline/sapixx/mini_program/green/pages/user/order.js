const app = getApp()

Page({

  //页面的初始数据
  data: {
    active:'0',
    item:[]
  },
  //监听页面加载
  onLoad: function (options) {
    this.setData({
      active:app.util().isNull(options.active)?0:options.active
    })
  },
  //监听页面显示
  onShow: function (options) {
    this.getApi();
  },
  //读取我的订单
  getApi: function() {
    var param = {
      signkey: app.util().getRandom(12),
      active: this.data.active
    }
    app.api().Get('api/v1/green/shop/order',param,(result) => {
      this.setData({
        item: result.data
      });
    })
  },
  //订单重新支付
  doPayment(event){
    var param = {
      signkey: app.util().getRandom(12),
      order_no: event.currentTarget.dataset.order_no,
    }
    wx.showLoading({
      title:'加载中'
    })
    app.api().Post('api/v1/green/shop/reDopay',param,(res) => {
      if (res.code == 200) {
        wx.hideLoading()
        app.doWechatPay(res.data,()=>{
          wx.navigateTo({
            url: 'viewOrder?id='+ event.currentTarget.id
          })
        },()=>{
          app.wxAlert('你取消了兑换')
        })
      }
    })
  },
  //订单预览
  onViews(event){
    wx.navigateTo({
      url:'viewOrder?id='+event.currentTarget.id
    })
  },
   //状态更改
  finished(event) {
    var order = this.data.item;
    order[event.currentTarget.dataset.id].is_del = 1;
    this.setData({
      item:order
    })
  },
  //Tab状态
  onChange(event) {
    this.setData({
      active:event.detail.name,
      item:[],
    })
    this.getApi();
  }
})