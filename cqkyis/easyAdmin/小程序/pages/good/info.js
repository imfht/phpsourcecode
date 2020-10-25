// pages/good/info.js
var infos = require("../../utils/common.js");
var WxParse = require('../../utils/wxParse/wxParse.js');
var CartJs = require("../../utils/cart.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    imgUrls:'',
    article:'',
    nums:0
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
   
   wx.request({
     url: infos.GoodInfo,
     data:{
       id: options.goodid
     },
     success:function(r){
      console.log(r);
      wx.setNavigationBarTitle({
        title: r.data.good_name,
      });
       if(r.data.imgs){
       that.setData({
         imgUrls:r.data.imgs,
         good_name: r.data.good_name,
         good_s_name: r.data.good_s_name,
         price:r.data.price,
         mall_price: r.data.mall_price,
         context: r.data.context,
         good_id:r.data.good_id,
         good_img:r.data.good_img
       })
       var temp = WxParse.wxParse('article', 'html', r.data.context, that, 10);
     
       }
     }
   })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  },
  buy:function(e){
    var id = e.currentTarget.dataset.id;
    var that = this;
    var product = {
      id: id,
      name: that.data.good_name,
      price: that.data.price,
      saleprice: that.data.mall_price,
      imgs: that.data.good_img,
      sname: that.data.good_s_name,
      num: 1

    }
  //console.log(that.data.good_img);
    CartJs.GoodCart(product);
    console.log(wx.getStorageSync('cart'));
    var nums = that.data.nums+1;
    that.setData({
      nums:nums
    })
    

  },
  cart:function(){
    // wx.navigateTo({
    //   url: '../../cart/index',
    // })
   // alert("fdsafd");
    wx.switchTab({
      url: '../cart/index',
      success: function (e) {
        var page = getCurrentPages().pop();
        if (page == undefined || page == null) return;
         page.onShow();

      }
    })
  }
})