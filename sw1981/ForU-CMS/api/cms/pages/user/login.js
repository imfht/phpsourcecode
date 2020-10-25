var app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {},

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    this.getOpenId()
    this.login()
  },

  login:function(){
    var name = 'admin'
    var pass = 'admin'
    var oid = wx.getStorageSync('openid')
    wx.request({
      url: app.gData.apiUrl + 'user.php?act=login&name='+name+'&pass='+pass+'&oid='+oid,
      success:function(e){
        if (e.data.err==0) {
          wx.navigateTo({
            url: '../user/index',
          })
        }
      },
      fail:function(){
        wx.showToast({
          title: '小程序端登录失败，请稍后重试！',
          duration: 3000
        })
      }
    })
  },

  getOpenId:function(){
    wx.login({
      success:function(res) {
        if (res.code) {
          console.log(res)
          //发起网络请求
          wx.request({
            url: 'https://api.weixin.qq.com/sns/jscode2session?appid='+app.gData.appId+'&secret='+app.gData.appSe+'&js_code='+res.code+'&grant_type=authorization_code',
            success:function(rs){
              console.log(rs.data)
              wx.setStorageSync('openid', rs.data.openid);
            },
            fail:function(){
              wx.showToast({
                title: '无法获取用户登录信息，请稍后重试！',
                duration: 2000
              })
            }
          })
        } else {
          wx.showToast({
            title: '获取用户登录态失败！',
            duration: 2000
          })
        }
      },
      fail:function(){
        wx.showToast({
            title: '用户尝试登录失败！',
            duration: 2000
          })
      }
    });
  }
})