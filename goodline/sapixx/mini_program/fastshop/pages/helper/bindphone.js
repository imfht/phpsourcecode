const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    loading: false,
    phone_uid: '',
  },
  onShow:function(){
    let that = this;
    app.isLogin(function(rel){
      that.isUsrInfo();      
    });
  },
  /**
   * 获取用户信息检测是否绑定手机号
   */
  isUsrInfo: function () {
    let that = this;
    app.api().Get('openapi/v1/user/getUserInfo', function (res) {
      if (res.code == 200) {
        that.setData({
          phone_uid: res.data.phone_uid
        })
      }
    })
  },
  /**
  * 设置输入手机号
  */
  getPhoneNumber: function (e) {
    let that = this;
    var detail = e.detail;
    if (detail.errMsg == 'getPhoneNumber:ok'){
      var parms = {
        encryptedData: detail.encryptedData,
        errMsg: detail.errMsg,
        iv: detail.iv
      }
      app.api().Post('openapi/v1/bindWechatPhone', parms, function (rel) {
        if (rel.code == 200) {
          wx.showModal({
            content: rel.msg, showCancel: false,
          })
          that.setData({
            phone_uid: rel.data.phone_uid
          })
        }
      })
    }
  }
})
