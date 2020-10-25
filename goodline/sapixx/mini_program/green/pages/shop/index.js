const app = getApp()
Page({
  data: {
    loading: true,
    page:0,
    cate_id:'0',
    catetab: [],
    item: []
  },
  //监听页面加载
  onLoad: function(options) {
    this.getItem();
  },
  //下拉刷新
  onPullDownRefresh: function () {
    wx.showNavigationBarLoading();
    setTimeout(()=>{
      wx.hideNavigationBarLoading();
      wx.stopPullDownRefresh();
    },1500);
    this.setData({
      loading: true,
      page:0,
      item:[],
    });
    this.getItem();
  },
  //上拉加载
  onReachBottom: function() {
    this.setData({
      loading: true,
    });
    this.getItem();
  },
  //点击请求数据
  getItem: function() {
    if (this.data.loading) {
      var parms = {
        page: this.data.page + 1
      }
      app.api().Get('api/v1/green/shop/list',parms,(result)=>{
        if (result.code == 200) {
          var item = this.data.item;
          for (let i in result.data) {
            item.push(result.data[i]);
          }
          this.setData({
            item: item,
            page: parms.page,
          });
        }
        this.setData({
          loading: false,
        });
      })
    }
  },
  //点击Tab切换
  onClickTab: function(e) {
    this.setData({
      cate_id: parseInt(e.detail.name),
      loading: true,
      item: [],
      page: 0,
    });
    this.getItem();
  },
})