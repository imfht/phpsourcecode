const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    url: app.apiroot + '/openapi-' + app.globalData.wechat_config.app_id + '/v1/bindOfficial'
  },
  
  onShow:function(){
    wx.showModal({
      content: this.data.url,
    })
  },
  /**
   * 接受参数
  */
  getMessage: function (e) {
    var that = this;
    var options = e.detail.data[0];
    wx.setStorageSync('official_uid',options.official_uid);
  },
})