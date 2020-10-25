var api = require('../../utils/request');
const app = getApp();
Page({
  data: {
    loading: true,
    today: 0,
    page: 0,
    types: 0,
    bank: {
      due_money: 0,
      lack_money: 0,
      shop_money: 0
    },
    list: [],
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    var that = this;
    app.isLogin(() => {
      that.getBank();
    })
  },
  // 下拉刷新
  onPullDownRefresh: function () {
    wx.showNavigationBarLoading();
    setTimeout(function () {
      wx.hideNavigationBarLoading();
      wx.stopPullDownRefresh();
    }, 1500);
    this.getBank();
  },
  //下拉
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
    var that = this;
    that.setData({
      loading: true,
      types: event.detail.name,
      list: [],
      page: 0,
    });
    that.getList();
  },
  /**
    * 读取我的账单
    */
  getBank: function () {
    let that = this;
    api.Get("api/v1/popupshop-bank-index", function (result) {
      if (result.code == 200){
        that.setData({bank: result.data });
        that.getList();
      }
    })
  },
  /**
   * 获取帐号明细
   */
  getList: function () {
    let that = this;
    if (that.data.loading) {
      wx.showLoading({ title: '正在加载' });
      var page = that.data.page + 1;
      var types = that.data.types;
      api.Get("api/v1/popupshop-bank-bill", { today: types, page: page}, function (result) {
        if (result.code == 200) {
          var list = that.data.list;
          for (let i in result.data) {
            list.push(result.data[i]);
          }
          that.setData({
            list: list,
            page: page,
          });
        }
      })
      that.setData({
        loading: false,
      });
      wx.hideLoading();
    }
  },
  ontabbar(event) {
    var url = event.detail;
    wx.navigateTo({
      url: url,
    })
  }
})