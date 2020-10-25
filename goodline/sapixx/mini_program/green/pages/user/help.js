const app = getApp()
Page({

  data: {
    info:{},
  },

  //监听页面加载
  onLoad: function (options) {
    this.getApi();
  },

  //请求设备经纬度
  getApi(){
    var param = {
      signkey:app.util().getRandom(12)
    }
    app.api().Get('api/v1/green/config/index',param,(rel) =>{
      if (rel.code == 200) {
        this.setData({
          info:rel.data,
        })
      }
    })
  }
})