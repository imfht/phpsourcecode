const app = getApp()
Page({
  data: {
    loading: true,
    curIndex: 0,
    page: 0,
    item: []
  },
  //载入
  onLoad: function () {
     this.geItem();
  },
  onShow: function () {
    app.setTabBarCartNumber();
  },
  //上拉加载
  onReachBottom: function () {
    var that = this;
    if (app.globalData.config.set_tab_style) {
      that.setData({
        loading: true,
      });
      that.geItem();
    }
  },
  //读取团购数据
  geItem: function () {
    let that = this;
    if (that.data.loading) {
      var param = {
        page: that.data.page + 1,
        cate_id: 0,
        types: 2
      }
      app.api().Get('api/v3/fastshop/shop/cateItem', param, function (result) {
        if (result.code == 200) {
          var item = that.data.item;
          for (let i in result.data) {
            item.push(result.data[i]);
          }
          that.setData({
            item: item,
            page: param.page
          });
        }
        that.setData({
          loading: false,
        });
      })
    }
  },
  //搜索
  onSearch: function (event) {
    let that = this;
    if (!app.util().isNull(event.detail)) {
      var keyword = event.detail;
      wx.navigateTo({
        url: '../shop/search?keyword=' + keyword,
      })
    }
  }
})