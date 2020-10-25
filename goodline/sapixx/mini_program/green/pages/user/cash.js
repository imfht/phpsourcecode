const app = getApp()

Page({

  data: {
    points:0,
  },

  //监听页面显示
  onShow: function () {
    this.getBank();
  },

  //读取我的账单
  getBank: function () {
    app.api().Get("api/v1/green/index/user",{signkey:app.util().getRandom(12)},(rel)=>{
      if (rel.code == 200) {
        this.setData({
          points: parseInt(rel.data.points/1000)
        });
      }
    })
  },
  /**
   * 申请提现
   */
  onSubmit: function (e) {
    var data = e.detail.value;
    var isPost = true;
    //安全密码
    if (app.util().isNull(data.safepassword)) {
      wx.showModal({
        content: '安全密码必须填写', showCancel: false
      })
      isPost = false;
    }else if (app.util().isNull(data.money)) {
      wx.showModal({
        content: '提现金额必须填写', showCancel: false
      })
      isPost = false;
    } else {
      if (!(/^\d+$/.test(data.money))) {
        wx.showModal({
          content: '提现金额只能输入整数', showCancel: false
        })
        isPost = false;
      }
    }
    //提交数据
    if (isPost == true) {
      wx.showLoading({
        title: '提交申请中',
        mask: true
      })
      var parms = {
        money: data.money,
        safepassword: data.safepassword,
      }
      app.api().Post('api/v1/green/user/cash', parms, function (res) {
        wx.hideLoading();
        if (res.code == 200) {
          wx.showModal({
            showCancel: false,title: '友情提示',content: res.msg,
            complete: function () {
              wx.navigateBack({delta:1})
            }
          })
        }
      })
    }
  }
})