const app = getApp()
var api = require('../../utils/request');

Page({

  /**
   * 页面的初始数据
   */
  data: {
    loading: true,
    tabs: ["待确认","已确认","已发货"],
    activeIndex: 0,
    sliderLeft: 0,
    page: 0,
    gift:[],
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onShow: function (options) {
    let that = this;
    that.getApi();
  },
  // 下拉刷新
  onPullDownRefresh: function () {
    var that = this;
    that.setData({
      page: 0,
      loading: true,
      gift: []
    });
    wx.showNavigationBarLoading();
    this.getApi();
    //停止下拉刷新
    setTimeout(function () {
      wx.hideNavigationBarLoading();
      wx.stopPullDownRefresh();
    }, 1500);
  },
  //下拉
  onReachBottom: function () {
    var that = this;
    that.setData({
      loading: true,
    });
    this.getApi();
  },
  /**
   * 我的出售API
   */
  getApi: function () {
    let that = this;
    if (that.data.loading) {
      var page = that.data.page + 1;
      var index = that.data.activeIndex;
      api.Get('api/v3/fastshop/order/gift', { page: page, types: index}, function (result) {
        if (result.code == 200) {
          var gift = that.data.gift;
          for (let i in result.data) {
            gift.push(result.data[i]);
          }
          that.setData({
            gift: gift,
            page: page,
          });
        }
        that.setData({
          loading: false,
        });
      })
    }
  },
  onView: function (e) {
    var id = e.currentTarget.id;
    wx.navigateTo({
      url: 'giftview?order_no=' + id
    })
  },
  /**
  * 点击Tab切换
  */
  selecttab: function (e) {
    let that = this;
    var id = e.currentTarget.id;
    var sliderLeft = e.currentTarget.offsetLeft;
    that.setData({
      page: 0,
      loading: true,
      sliderLeft: sliderLeft,
      activeIndex: id,
      gift: [],
    });
    that.getApi();
  }
})