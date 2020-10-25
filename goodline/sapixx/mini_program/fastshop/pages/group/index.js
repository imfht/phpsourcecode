const app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    loading: true,
    page: 0,
    item: [],
  },
  //生命周期函数--监听页面加载
  onLoad: function () {
    this.getApi();
  },
  //上拉加载
  onReachBottom: function () {
    var that = this;
    that.setData({
      loading: true,
    });
    that.getApi();
  },  
  //读取团购数据
  getApi: function () {
    let that = this;
    if (that.data.loading) {
      var page = that.data.page + 1;
      app.api().Get('api/v3/fastshop/group/index', {page: page}, function (result) {
        var item = that.data.item;
        for (let i in result.data) {
          item.push(result.data[i]);
        }
        that.setData({
          item: item,
          page: page,
          loading: false,
        });
      })
    }
  },
})