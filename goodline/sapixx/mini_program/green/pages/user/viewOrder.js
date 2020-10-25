const app = getApp()

Page({

  //页面的初始数据
  data: {
    varcode:'',
    item:[]
  },
  //监听页面显示
  onLoad: function (options) {
    this.setData({
      id:options.id,
    })
    this.data.isvar = app.util().isNull(options.var) ? false : true
    this.getApi();
  },
  //读取我的订单
  getApi: function() {
    var param = {
      signkey: app.util().getRandom(12),
      id: this.data.id
    }
    app.api().Get('api/v1/green/shop/getOrder',param,(result) => {
      this.setData({
        item: result.data
      });
      if(this.data.isvar){
        this.onVer();
      }
    })
  },
  //订单重新支付
  doPayment(){
    var param = {
      signkey:app.util().getRandom(12),
      order_no:this.data.item.order_no
    }
    wx.showLoading({
      title:'加载中'
    })
    app.api().Post('api/v1/green/shop/reDopay',param,(res) => {
      if (res.code == 200) {
        wx.hideLoading()
        app.doWechatPay(res.data,()=>{
          var item = this.data.item;
              item['paid_at'] = 1; 
          this.setData({
            item: item
          });
        },()=>{
          app.wxAlert('你取消了支付')
        })
      }
    })
  },
  //状态更改
  finished(event) {
    var item = this.data.item;
        item['is_del'] = 1; 
    this.setData({
      item:item
    })
  }
})