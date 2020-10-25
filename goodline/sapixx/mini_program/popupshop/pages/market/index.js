const app = getApp()
Page({
  
  data: {
    loading: true,
    startBarHeight: app.globalData.startBarHeight,
    navgationHeight: app.globalData.navgationHeight,
    page: 0,
    item: [],
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onLoad: function (options) {
    app.setUcode(options);
  },
  /**
   * 显示页面
   */
  onShow:function(){
    this.setData({
      loading: true,
      item: [],
      page: 0,
    }); 
    this.getApi();
    app.setTabBarCartNumber();
  },

  // 下拉刷新
  onPullDownRefresh: function () {
    var that = this;
    that.setData({
      page: 0,
      loading: true,
      market: []
    });
    wx.showNavigationBarLoading();
    //停止下拉刷新
    setTimeout(function () {
      wx.hideNavigationBarLoading();
      wx.stopPullDownRefresh();
    }, 1500);
    that.getApi();
  },
  //下拉
  onReachBottom: function () {
    var that = this;
    that.setData({
      loading: true,
    });
    that.getApi();
  },
  /**
   * 添加商品
   */
  getApi: function () {
    let that = this;
    if (that.data.loading) {
      var page = that.data.page + 1;
      app.api().Get('api/v1/popupshop-sale-lists',{page:page},function (result) {
        if (result.code == 200) {
          var item = that.data.item;
          for (let i in result.data) {
            item.push(result.data[i]);
          }
          that.setData({
            item: item,
            page: page
          });
        }
        that.setData({
          loading: false,
        });
      })
    }
  },
  //分享按钮
  onShareAppMessage: function (res) {
    return {
      desc: app.appname,
      path: '/pages/index/index?ucode=' + app.globalData.loginuser.ucode
    }
  },
  //滚动
  onPageScroll(res){
    let scrollTop = res.scrollTop;
    this.setData({
      'barbolor': 10 <= scrollTop ? '#FF3B35' :'none'
    })
  }
})