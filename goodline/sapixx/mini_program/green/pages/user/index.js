const app = getApp()
const udpSocket = wx.createUDPSocket()
Page({
 /**
  * 页面的初始数据
  */
  data: {
    islogin: false,
    user:{},
    bill:{
      count:0,
      weight:0
    }
  },
  //监听页面加载
  onLoad: function (options) {
    this.getApi()
  },
  //监听页面显示
  onShow:function (){
    app.isLogin() 
  },
  //请求设备经纬度
  getApi(){
    app.api().Get('api/v1/green/index/user',{signkey:app.util().getRandom(12)},(rel) =>{
      if (rel.code == 200) {
        this.setData({
          user:rel.data
        })
      }
      this.getBill();
    })
  },
  //请求设备经纬度
  getBill(){
    //账单
    app.api().Get('api/v1/green/index/moonBill',{signkey:app.util().getRandom(12)},(rel) =>{
      if (rel.code == 200) {
        this.setData({
          bill:rel.data
        })
      }
    })
  },
  
 /**
   *地址 
   */
  address: function () {
    //获取用户权限
    wx.getSetting({
      success(res) {
        if (res.authSetting['scope.address'] == false) {
          wx.openSetting({
            success: (res) => {
              res.authSetting = {
                "scope.address": true,
              }
            }
          })
        } else {
          wx.chooseAddress({})
        }
      }
    })
  },
  //分享按钮
  onShareAppMessage: function () {
    return {
      path: '../index?ucode=' + app.globalData.loginuser.ucode
    }
  }
})