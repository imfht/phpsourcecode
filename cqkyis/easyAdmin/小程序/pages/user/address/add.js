// pages/user/address/add.js
var UserJs = require("../../../utils/common.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
  findaddress:'点击选择',
  userId:''
  },
map:function(){
  wx.navigateTo({
    url: 'map'
  })
},
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
      console.log(options);
      var user = wx.getStorageSync('user');
      that.setData({
        userId:user.uid
      })
    wx.setNavigationBarTitle({
      title: '添加收货地址',
    })



    wx.getStorage({
      key: 'findaddress',
      success: function (res) {
       // console.log(res.data)
        that.setData({
          findaddress:res.data
        })
      }
    })

  },
  addSubmit: function (e) {
    var that = this;
  console.log(e);
  var phone = e.detail.value.phone;
  var contacts = e.detail.value.contacts;
  var readdress = e.detail.value.readdress;
  var doorno = e.detail.value.doorno;
  if (contacts.length == 0) {
    wx.showToast({
      title: '请输入联系人',
      icon: 'none',
      duration: 1500
    })
    return false;
  }
  if (phone.length == 0) {
    wx.showToast({
      title: '请输入手机号！',
      icon: 'none',
      duration: 1500
    })
    return false;
  }

  if (phone.length != 11) {
    wx.showToast({
      title: '手机号长度有误！',
      icon: 'none',
      duration: 1500
    })
    return false;
  }
  var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/;
  if (!myreg.test(phone)) {
    wx.showToast({
      title: '手机号格式不正确！',
      icon: 'none',
      duration: 1500
    })
    return false;
  }
  if (readdress.length == 0) {
    wx.showToast({
      title: '请选择收货地址',
      icon: 'none',
      duration: 1500
    })
    return false;
  }
 wx.request({
   url: UserJs.Adaddress,
   data:{
     uid: that.data.userId,
     contacts: contacts,
     phone: phone,
     readdress: readdress,
     doorno: doorno
   },
   success:function(r){
     console.log(r.data);
     if(r.data.code==1){
       wx.showToast({
         title: '添加成功！',
         icon:"none",
         duration:1500,
         
       });
       setTimeout(function(){
         wx.navigateBack({
           delta: 1,
           success: function (e) {
             var page = getCurrentPages().pop();
             if (page == undefined || page == null) return;
             page.onLoad();

           }
         })
       },1500)
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
  
  }
})