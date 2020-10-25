const app = getApp()
Page({
  data: {
    loading: true,
    page: 0,
    keyword: '',
    item: [],
  },
  //监听页面初次载入完成
  onLoad: function (event) {
    var that = this;
    if (!app.util().isNull(event.keyword)) {
      that.setData({
        keyword: event.keyword
      })
    }
    that.getItem();
  },
  //底部加载更多
  onReachBottom: function () {
    var that = this;
    that.setData({
      loading: true,
    });
    this.getItem();
  },
  /**
   * 获取店铺
   */
  getItem: function () {
    let that = this;
    if (that.data.loading) {
      var parms = {
        keyword: that.data.keyword,
        page: that.data.page + 1
      }
      app.api().Get('api/v3/fastshop/shop/search', parms, function (result) {
        if (result.code == 200) {
          var item = that.data.item;
          for (let i in result.data) {
            item.push(result.data[i]);
          }
          that.setData({
            item: item,
            page: parms.page,
          });
        }
        that.setData({
          loading: false
        })
      })
    }
  },
  //搜索
  onSearch: function (event) {
    let that = this;
    var keyword = '';
    that.setData({
      loading: true,
      page: 0,
      item: [],
      keyword: keyword,
    })
    if (app.util().isNull(event.detail)) {
      wx.showModal({
        content: '请输入商品关键词', showCancel: false
      })
    } else {
      keyword = event.detail;
      that.setData({
        keyword: keyword
      })
      that.getItem();
    }
  },
});