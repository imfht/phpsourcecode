const app = getApp();
var sliderWidth = 96; // 需要设置slider的宽度，用于计算中间位置
var api = require('../../utils/request');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    loading: true,
    tabs: ["未付款", "已付款", "已发货", "已完成"],
    activeIndex: 0,
    page: 0,
    item: [],
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
    var types = options.type ? options.type : 0;
    that.setData({
      activeIndex: types
    })
    that.getOrder(types);
  },
  onView: function (e) {
    var id = e.currentTarget.id;
    wx.navigateTo({
      url: 'review?order_no=' + id
    })
  },
  /**
   * 读取我的订单
   */
  getOrder: function (types) {
    let that = this;
    if (that.data.loading) {
      var page = that.data.page + 1;
      api.Get("api/v1/popupshop-cart-order", { types: types, page: page }, function (result) {
        if (result.code == 200) {
          var item = that.data.item;
          for (let i in result.data) {
            item.push(result.data[i]);
          }
          that.setData({
            item: item,
            page: page,
          });
        }
      })
      that.setData({
        loading: false
      });
    }
  },
  /**
  * 点击Tab切换
  */
  selecttab: function (e) {
    var id = e.currentTarget.id;
    this.setData({
      page: 0,
      loading: true,
      activeIndex: id,
      item: []
    });
    this.getOrder(id);
  }
})