const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    phone_uid: '',
  },

  onShow:function(){
    let that = this;
    app.isLogin((rel) => {
      that.isUsrInfo();      
    });
  },
  /**
   * 获取用户信息检测是否绑定手机号
   */
  isUsrInfo: function () {
    app.api().Get('openapi/v1/user/getUserInfo',(res) => {
      if (res.code == 200) {
        this.setData({
          phone_uid: res.data.phone_uid
        })
      }
    })
  },
  /**
  * 设置输入手机号
  */
  getPhoneNumber: function (e) {
    var detail = e.detail;
    if (detail.errMsg == 'getPhoneNumber:ok'){
      var parms = {
        encryptedData: detail.encryptedData,
        errMsg: detail.errMsg,
        iv: detail.iv
      }
      app.api().Post('openapi/v1/bindWechatPhone', parms, (rel) => {
        if (rel.code == 200) {
          wx.showModal({
            content: rel.msg, showCancel: false,
          })
          this.setData({
            phone_uid: rel.data.phone_uid
          })
        }
      })
    }
  }
})
