const app = getApp()
import Toast from '../..//vant/toast/toast';
Page({
  /**
   * 用户登录
   */
  authorLogin: function (e) {
    let that = this;
    if (e.detail.errMsg !== 'getUserInfo:ok') {
      return false;
    }
    Toast.loading({
      mask: true,
      message: '帐号正在授权'
    });
    //登录返回上一页
    app.doLogin(e,function (isLogin) {
      if (isLogin) {
        wx.switchTab({
          url: '/pages/user/index'
        })
      }
    });
  }
})