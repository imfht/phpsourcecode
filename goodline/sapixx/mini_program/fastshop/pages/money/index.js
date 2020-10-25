var api = require('../../utils/request');
const app = getApp();
Page({
  data: {
    loading: true,
    shopping_name:'购物积分',
    bank: {
      money: 0,
      due_money: 0,
      lack_money: 0,
      income_monney: 0,
      shop_money: 0
    },
    list: [],
    today: 0,
    page: 0,
    loading: true,
    types:0
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    var that = this;
    app.isLogin(function () {
      that.getBank();
    });
  },
  /**
  * 生命周期函数--监听页面初次渲染完成
  */
  onReady: function () {
    this.setData({
      shopping_name: app.globalData.config.shopping_name
    })
  },
  // 下拉刷新
  onPullDownRefresh: function () {
    var that = this;
    that.setData({
      page: 0,
      loading: true,
      list: [],
      bank: {
        money: 0,
        due_money: 0,
        lack_money: 0,
        income_monney: 0,
        shop_money: 0
      },
    });
    wx.showNavigationBarLoading();
    this.getBank();
    this.getList();
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
    this.getList();
  },
  /**
   * 点击切换
   */
  onChange(event) {
    this.setData({
      page: 0,
      loading: true,
      list: [],
      types: event.detail.name
    });
    this.getList();
  },
  /**
    * 读取我的账单
    */
  getBank: function () {
    let that = this;
    api.Get("api/v3/fastshop/bank/index", function (result) {
      if (result.code == 200){
        that.setData({ bank: result.data });
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
      api.Get("api/v3/fastshop/bank/bill", { today: types, page: page }, function (result) {
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
  //点击底部菜单
  ontabbar(event) {
    var url = event.detail;
    wx.navigateTo({
      url: url,
    })
  }
})