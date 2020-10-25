// pages/cart/order/pay.js
var pays = require("../../../utils/common.js");
Page({

  /**
   * 页面的初始数据
   */
  data: {
    orderlist:'',
    allprice:'',
    addressId:null,
   
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that  = this;
    wx.setNavigationBarTitle({
      title: '支付订单',
    });
    //console.log(options);
    var userId=wx.getStorageSync('user');
    
   var code = wx.getStorageSync('code');
    wx.request({
      url: pays.OrderList,
      data:{
        ordercode: code,
        userId:userId.uid,
      
      },
      success:function(e){
        //console.log(e);
        that.selectAddress();
     
      that.setData({
        orderlist: e.data.productlist,
        allprice: e.data.price,
        relaprice: e.data.sprice,
        payprice: e.data.sprice,
        postprice: e.data.postprice
      })
      }
    })
  },
  address:function(e){

    var id = e.currentTarget.dataset.id;
    wx.setStorageSync('addressId', id);
   
    wx.navigateTo({
      url: '../../user/address/list',
    })
  },
  selectAddress:function(){
    var that =this;
  //查找地址
  //如果当前用户有没有地址，
    var selid=wx.getStorageSync('selectaddress');
  wx.request({
    url: pays.SelectAddress,
    data:{
      uid:wx.getStorageSync('user').uid,
      adid: selid
    },
    success:function(r){
      
      if(r.data.code==1){
      that.setData({
           
        address: r.data.address.readdress + r.data.address.doorno,
        contacts: r.data.address.contacts,
        adId: r.data.address.adId,
        phone: r.data.address.phone,
           show:0
          })
    }else{
      
      that.setData({
        show:1
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
  pay:function(e){
    var that =this;
   // console.log(that.data.relaprice);
    var user =wx.getStorageSync('user');
    var code = wx.getStorageSync('code');
    console.log(that.data.adId);
    var adId = that.data.adId;
    if(!adId){
      //console.log(adId);
  
      wx.showToast({
        title: '你还没有选择收货地址',
        icon: 'none',
        duration: 1500
      })



    }else{

      wx.request({
        url: pays.PayOrder,
        data: {
          openid: user.openid,
          order_sn: code,
          total_fee: that.data.relaprice,
          adid: adId
        },
        success: function (r) {
         // console.log(r.data);
          wx.requestPayment({
            timeStamp: r.data.timeStamp,
            nonceStr: r.data.nonceStr,
            package: r.data.package,
            signType: r.data.signType,
            paySign: r.data.paySign,
            success: function (e) {
              console.log(e);
              if (e.errMsg =="request:ok"){
                  wx.navigateTo({
                    url: '../../user/index',
                  })
              }
              if (e.errMsg == "requestPayment:fail cancel"){
              wx.showToast({
                title: '取消了支付',
                icon:'none',
                duration:1500
              })
              }
            }, fail: function (s) {
              console.log(s);
            }
          })

        }
      })



    
    }
 
  }
})