const app = getApp()
Page({
  data: {
    item: [],
    store:[]
  },
  //监听页面初次载入完成
  onLoad: function (options) {
    app.isLogin((rel)=>{
      this.data.id = options.id
      this.getAddress();
      this.getApi();
    })
  },
  //提交订单
  onSubmit: function(){
    var param = {
      message:this.data.message,
      shop_id:this.data.id,
      address:this.data.address.id,
      ucode: app.globalData.ucode,
      signkey: app.util().getRandom(12),
    }
    app.api().Post('api/v1/green/shop/dopay',param,(res) => {
      if (res.code == 200) {
       app.doWechatPay(res.data,()=>{
          wx.navigateTo({
            url: '../user/order?active=2',
          })
       },()=>{
        wx.showModal({
          title:'微信支付',content:'你已取消支付,是否重试',
          cancelText:'取消',confirmText	:'重试',
          success:(res) => {
            if (res.confirm) {
              this.onSubmit();
            }
          }
        })
       })
      }
    })
  },
  message: function(e) {
    this.data.message = e.detail;
  },
  //读取我的订单
  getApi: function() {
    var param = {
      id: this.data.id
    }
    app.api().Get('api/v1/green/shop/item',param,(result) => {
      this.setData({
        item: result.data,
      });
    })
  },
  /**
  * 读取微信地址
  */
  getAddress: function () {
    app.api().Get("openapi/v1/user/getaddress", {'signkey': 'dopay'},(rel)=>{
      if (rel.code == 200) {
        this.setData({
          address: rel.data,
          address_isnull: Object.keys(rel.data).length,
        })
      }
    })
  },
  //读取微信地址
  address: function () {
    let that = this;
    wx.getSetting({
      success(res) {
        if (res.authSetting['scope.address'] == false) {
          wx.openSetting({
            success: (res) => {
              res.authSetting = {
                "scope.address": true,
              }
            }
          })
        } else {
          wx.chooseAddress({
            success: function (res) {
              var name = res.userName;
              var telNumber = res.telNumber;
              var city = res.provinceName + res.cityName + res.countyName;
              var address = res.detailInfo;
              app.api().Post("openapi/v1/user/createaddress", {
                name: name,
                telphone: telNumber,
                city: city,
                address: address
              }, function (rel) {
                that.setData({
                  address: rel.data,
                  address_isnull: Object.keys(rel.data).length,
                })
              });
            }
          })
        }
      }
    })
  }
})