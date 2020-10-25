var api = require('../../utils/request');
var util = require('../../utils/util');
const app = getApp()

Page({
  data: {
    loading: true,
    skeleton: true,
    page:0,
    lists: [],
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.getApi();
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    this.setData({
      skeleton: false,
      loading: false
    })
  },
  // 下拉刷新
  onPullDownRefresh: function () {
    var that = this;
    that.setData({
      page: 0,
      loading: true,
      lists: []
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
    * 获取活动
  */
  getApi: function () {
    let that = this;
    if (that.data.loading) {
      var page = that.data.page + 1;
      api.Get('openapi/v1/user/levelUser',{page: page },function (result) {
        if (result.code == 200) {
          var lists = that.data.lists;
          for (let i in result.data) {
            lists.push(result.data[i]);
          }
          that.setData({
            lists: lists,
            page: page,
          });
        }
        that.setData({
          loading: false,
        });
      })
    }
  },
})