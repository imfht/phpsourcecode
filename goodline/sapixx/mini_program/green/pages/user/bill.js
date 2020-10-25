const app = getApp()
Page({
  data: {
    loading: true,
    user:{},
    list: [],
    today: 0,
    page: 0
  },
  //监听页面加载
  onLoad: function () {
    this.getApi();
  },
  //个人信息
  getApi(){
    var param = {
      signkey:app.util().getRandom(12)
    }
    app.api().Get('api/v1/green/index/user',param,(rel) =>{
      if (rel.code == 200) {
        this.setData({
          user:rel.data,
        })
        this.getList();
      }
    })
  },
  //上拉加载更多
  onReachBottom: function () {
    this.setData({
      loading: true,
    }); 
    this.getList();
  },
  /**
   * 点击切换
   */
  onChange(event) {
    this.setData({
      loading: true,
      page: 0,
      list: [],
      today: event.detail.name
    });
    this.getList();
  },
  /**
   * 账单
   */
  getList: function () {
    if (this.data.loading) {
      wx.showLoading({ title: '正在加载' });
      var param = {
        page: this.data.page + 1,
        today: this.data.today
      }
      app.api().Get("api/v1/green/index/log", param,(result) => {
        if (result.code == 200) {
          var list = this.data.list;
          for (let i in result.data.list) {
            list.push(result.data.list[i]);
          }
          this.setData({
            list: list,
            page: param.page,
          });
        }
      })
      this.setData({
        loading: false,
      });
      wx.hideLoading();
    }
  },
  //申请提现
  onCash(){
    wx.navigateTo({
      url: 'cash',
    })
  }
})