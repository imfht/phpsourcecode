const app = getApp()

Page({
  /**
   * 页面的初始数据
   */
  data: {
    src: ''
  },
  //生命周期函数--监听页面加载
  onLoad: function (options) {
    app.getParam(options,(param) => {
      if (app.util().isNull(param.src)) {
        wx.redirectTo({
          url: 'error?msg=访问页面路径错误'
        })
      }
      var ids = app.util().isNull(param.ids)?'':'?ids=' + decodeURIComponent(param.ids);
      var pages = getCurrentPages();
      var currentPage = pages[pages.length - 1];
      var src = decodeURIComponent(currentPage.options.src || this.data.src)     
      this.setData({
        src:src + ids
      });
    })
  },
  //接受参数
  getMessage: function (e) {
    var options = e.detail.data[0];
  },

  //用户点击右上角分享
  onShareAppMessage: function (options) {
    var config = app.globalData.appConfig
    return {
      title: config.shop_share_text,
      imageUrl:config.store_share_img,
      path: options.webViewUrl
    }
  },
})