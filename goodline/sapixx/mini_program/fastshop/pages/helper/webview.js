var api = require('../../utils/request');
var util = require('../../utils/util');

const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    src: ''
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var src = options.src;
    var ids = options.ids;
    var token = wx.getStorageSync('token');
    if (util.isNull(src)) {
      wx.redirectTo({url: 'error?msg=访问页面路径错误' })
    } else {
      src = decodeURIComponent(options.src);
    }
    if (!util.isNull(ids)) {
      ids = '?ids=' + decodeURIComponent(options.ids);
    } else {
      ids = '';
    }
    var pages = getCurrentPages();
    var currentPage = pages[pages.length - 1];
    var src = decodeURIComponent(currentPage.options.src || this.data.src)
    this.setData({
      src: src + ids
    });
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    wx.showLoading({title:'正在加载'})
    setTimeout(function () {
      wx.hideLoading();
    }, 2000);
  },
  /**
   * 接受参数
  */
  getMessage: function (e) {
    var that = this;
    var options = e.detail.data[0];
  },
  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function (options) {
    return {
      path: options.webViewUrl,
      success(e) {
        wx.showShareMenu({withShareTicket: true});
      }
    }
  },
})