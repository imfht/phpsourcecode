// pages/user/address/list.js
var UserJs = require("../../../utils/common.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
   addresslist:'',
   userId:''
   
  },
  addaddress:function(){
    var that=this;
    wx.navigateTo({
      url: 'add'
    })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
    var user = wx.getStorageSync('user');
    var addressId =wx.getStorageSync('addressId');
    console.log(addressId);
    wx.setNavigationBarTitle({
      title: '我的收货地址',
    }) 
    //console.log(options);
    
    wx.request({
      url: UserJs.Uaddress,
      data:{
        uid: user.uid
      },
      success: function (r) {
        console.log(r);
       if(!r.data){

       }else{
         var list=r.data;
         for (var i in list) {
           list[i]['addressId'] = addressId
         }
       that.setData({
         addresslist: list
       })
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
  onShow: function (options) {
   //var userInfo = wx.getStorageSync('user');
   

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
  selectAddress:function(e){
    var id =  e.currentTarget.dataset.id;


    wx.setStorageSync('selectaddress', id);


    // wx.setStorageSync({
    //   key: "selectaddress",
    //   data: id
    // })
    wx.navigateBack({
      delta: 1,
      success: function (e) {
        var page = getCurrentPages().pop();
        if (page == undefined || page == null) return;
        page.onLoad();

      }
    })
  }
})