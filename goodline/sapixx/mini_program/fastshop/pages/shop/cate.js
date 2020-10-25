const app = getApp()
Page({
  data: {
    loading: true,
    curIndex: 0,
    page: 0,
    cate: [],
    cate_sub: [],
    item:[]
  },
  //载入
  onLoad: function () {
    this.selectNav();
  },
  onShow: function () {
    app.setTabBarCartNumber();
  },
  //上拉加载
  onReachBottom: function () {
    var that = this;
    that.setData({
      loading: true,
    });
    that.geItem();
  },
  //读取目录
  selectNav: function (callback) {
    let that = this;
    wx.showLoading({ title: '正在加载' })
    app.api().Get('api/v3/fastshop/shop/cate', function (result) {
      that.setData({
        cate: result.data.root_data,
        cate_sub: result.data.subs_data,
        curIndex: result.data.root_data[0]['id'],
      });
      wx.hideLoading();
    })
  },
  //读取子目录
  selectCate: function (e) {
    let that = this;
    var id = parseInt(e.currentTarget.dataset.id);
    that.setData({
      curIndex: id,
    });
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