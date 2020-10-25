var orders= require("../../utils/common.js");


Page({
  data: {
   nickname:'登录/注册',
   face:'../../common/images/icon.png',
   nopay:0,
   send:0,
   sendtime:0,
   outpay:0,
   orderbj:0
  },
  onLoad:function(){
    //var userInfo = wx.getStorageSync('user');
    wx.setNavigationBarTitle({
      title: '会员中心',
    })
  },
  onShow:function(){
        console.log("执行了一次");
    //获取用户信息
    var userInfo = wx.getStorageSync('user');
    var that = this;
    //that.onLoad();
    if(userInfo){

      that.setData({
        show:false,
        nickname: userInfo.nickname,
        face: userInfo.face
      });
      //查询用户订单
      that.showOrder(userInfo.uid);
      
    }else{
      that.setData({
        show: true,
        nickname: '登录/注册',
        face: '../../common/images/icon.png',
        nopay: 0,
        send: 0,
        sendtime: 0,
        outpay: 0,
        orderbj: 0
      });

     
    }


  },
  showOrder:function(res){
    //此处查询订单的多种状态
    var that = this;
    wx.request({
      url: orders.ShowOrders,
      data:{
          uid:res
      },
      success:function(r){
        console.log(r);
        that.setData({
          nopay:r.data.nopay,
          send:r.data.send,
          sendtime:r.data.sendtime,
          outpay:r.data.outpay,
          orderbj:r.data.orderbj
        })
      }
    })
  },
  set:function(){
    wx.navigateTo({
      url: 'set/index'
    })
  },
  address:function(){
    var userInfo = wx.getStorageSync('user');
   
    if (!userInfo) {
     wx.showToast({
       title: '你还没有登录,请先登录',
       icon: 'none',
       duration: 2000
     })
     setTimeout(function () {
       wx.navigateTo({
         url: 'login/login'
       })  
     }, 2000)

      

    }else{
      
      wx.navigateTo({
        url: 'address/list?userId='+userInfo.uid
      })
    }


    
  },
  login:function(){
    wx.navigateTo({
      url: 'login/login'
    })
  },
  allorder:function(){
    
    var userInfo = wx.getStorageSync('user');

    if (!userInfo) {
      wx.showToast({
        title: '你还没有登录,请先登录',
        icon: 'none',
        duration: 2000
      })
      setTimeout(function () {
        wx.navigateTo({
          url: 'login/login'
        })
      }, 2000)



    } else {

      wx.navigateTo({
        url: 'order/all' 
      })
    }
    
    
    
   
  },
  orderstatus:function(r){
    var id = r.currentTarget.dataset.id;
    var userInfo = wx.getStorageSync('user');
    if (!userInfo) {
      wx.showToast({
        title: '你还没有登录,请先登录',
        icon: 'none',
        duration: 2000
      })
      setTimeout(function () {
        wx.navigateTo({
          url: 'login/login'
        })
      }, 2000)



    } else {

      wx.navigateTo({
        url: 'order/list?id='+id
      })
    }
  }

})