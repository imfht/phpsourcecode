const app = getApp()
Page({
  /**
    * 页面的初始数据
    */
  data: {
    show: false,
  },
  //页面创建时执行
  onLoad: function (options) {
    app.setUcode(options);
  },
  //每次打开都获取要确人
  onShow: function () {
    this.getUcode();
  },
  /**
 * 获取邀请用户
 */
  getUcode: function () {
    if (!app.util().isNull(app.globalData.ucode)) {
      app.api().Get('openapi/v1/getCodeUser',{'ucode':app.globalData.ucode},(rel) => {
        if (rel.code == 200) {
          this.setData({
            ucode: app.globalData.ucode,
            ucode_user: rel.data,
            show:true,
          })
        }else{
          
        }
      })
    }
  },
  //输入邀请码
  inputCode: function (event) {
    var that = this;
    var cursor = event.detail.cursor, code = event.detail.value;
    if (cursor >= 4) {
      app.globalData.ucode = code;
      that.getUcode();
    } else {
      var user = {
        face: '/img/me.png'
      }
      that.setData({
        ucode_user: user
      })
    }
  },
  /**
   * 关闭弹出窗口
   */
  onClose: function () {
    this.setData({
      show:!this.data.show
    })
  },
  /**
   * 用户登录
   */
  authorLogin: function (e) {
    let that = this;
    if (e.detail.errMsg !== 'getUserInfo:ok') {
      return false;
    }
    wx.showLoading({
      title: '正在授权',
    })
    //登录返回上一页
    app.doLogin(e,function (isLogin){
      if (isLogin){
        app.globalData.ucode = '';
        wx.navigateBack({
          delta:1
        })
      }
    });
  }
})