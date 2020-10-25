// pages/user/login/login.js

var Logins = require("../../../utils/common.js");
var interval = null //倒计时函数
Page({

  /**
   * 页面的初始数据
   */
  data: {
    canIUse: wx.canIUse('button.open-type.getUserInfo'),
    $loading: {
      isShow: false
    },
    time: '获取验证码', //倒计时 
    currentTime: 610
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    wx.setNavigationBarTitle({
      title: '会员登录',
    });
   
  },
  bindGetUserInfo: function (e) {
    var that = this;
    that.setData({
      $loading: {
        isShow: true
      },
    })
    console.log(e);
    var openid;
   //console.log(e.detail.userInfo)
   //获取用户的code
   wx.login({
     success:function(res){
 if(res.code){
       wx.request({
         url: Logins.OpenId,
         data:{
           code:res.code
         },
         success:function(r){
           console.log(r);
           var v=JSON.parse(r.data);
           console.log(JSON.parse(r.data));
           openid=v.openid;
           console.log(openid);
          //成功后将数据保存到本地数据库
          wx.request({
            url: Logins.Wxlogin,
            data:{
              id:openid,
              nickname:e.detail.userInfo.nickName,
              face: e.detail.userInfo.avatarUrl
            },
            success:function(data){
              that.setData({
                $loading: {
                  isShow: false
                },
              })
              wx.setStorageSync('user', data.data);
              wx.showToast({
                title: '登录成功',
               
                icon: 'success',
                duration: 2000
              });
              setTimeout(function () {
                //要延时执行的代码  
                wx.switchTab({
                  url: '../index',
                  success: function (e) {
                    var page = getCurrentPages().pop();
                    if (page == undefined || page == null) return;
                    page.onLoad();

                  }
                })
              }, 2000)
              
              
            }
          })

         }
       })
 }else{
   console.log('登录失败！' + res.errMsg)
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
  formSubmit: function (e) {
    //console.log('form发生了submit事件，携带数据为：', e.detail.value);
    var phone = e.detail.value.phone;
    var code = e.detail.value.code;
    //console.log(code);
    wx.request({
      url: Logins.UserLogin,
      data:{
        phone:phone,
        code:code
      },
      success:function(res){
        console.log(res);
            if(res.data.code==2){
              //如果错误显示
              wx.showToast({
                title: res.data.msg,
                icon:'none',
                
              })
            }else{
              console.log(res);
              console.log(res.data);
              wx.setStorageSync('user', res.data);
              wx.showToast({
                title: '登录成功',

                icon: 'success',
                duration: 2000
              });

              setTimeout(function () {
                //要延时执行的代码  
                wx.switchTab({
                  url: '../index',
                  success: function (e) {
                    var page = getCurrentPages().pop();
                    if (page == undefined || page == null) return;
                    page.onLoad();

                  }
                })
              }, 2000)

            }
      }
    })
    

  },
  userphone: function (e) {
    this.setData({
      userphone: e.detail.value
    })
  },
  getCode: function (options) {
    var that = this;
    var currentTime = that.data.currentTime
    interval = setInterval(function () {
      currentTime--;
      that.setData({
        time: parseInt(currentTime/10) + '秒'
      })
      if (currentTime <= 0) {
        clearInterval(interval)
        that.setData({
          time: '重新发送',
          currentTime: 610,
          disabled: false
        })
      }
    }, 100)
  },
  sendcode:function(){
    // console.log("用户名：" + this.data.userphone);
    // //var phone = this.data.userphone;
    var that =this;
    wx.request({
      url: Logins.UserCode,
      data:{
        phone: that.data.userphone
      },
      success:function(res){
        console.log(res.data);
        if(res.data.code==1){
          that.getCode();
          that.setData({
      disabled: true
    })
        }else{
          wx.showToast({
            title: res.data.msg,
            icon:'none'
          })
        }
      }
    })
    
    
  }
})