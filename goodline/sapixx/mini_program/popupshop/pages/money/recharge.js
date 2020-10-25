const app = getApp()
Page({
  /**
   * 页面的初始数据
   */
  data: {
    bank: {
      due_money: 0,
      lack_money: 0,
      shop_money: 0
    },
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    this.setData({
      skeleton: false
    })
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    this.getBank();
  },
  /**
   * 读取我的账单
   */
  getBank: function () {
    let that = this;
    app.api().Get("api/v1/popupshop-bank-index", function (result) {
      if (result.code == 200) {
        that.setData({ bank: result.data });
      }
    })
  },
  /**
   * 申请提现
   */
  onSubmit: function (e) {
    let that = this;
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
        content: '转入金额必须填写', showCancel: false
      })
      isPost = false;
    } else {
      if (!(/^\d+$/.test(data.money))) {
        wx.showModal({
          content: '转入金额只能输入整数', showCancel: false
        })
        isPost = false;
      } else {
        if (100 > data.money) {
          wx.showModal({
            content: '最小转入金额100元',showCancel: false
          })
          isPost = false;
        }
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
        formId: e.detail.formId
      }
      console.log(parms);
      app.api().Post('api/v1/popupshop-bank-recharge',parms, function (rel) {
        if (200 == rel.code) {
          app.doWechatPay(rel.data, function (res) {
            wx.navigateBack({
              delta: 1
            })
          },function (res) {
            wx.showModal({
              content: '转入金额失败', showCancel: false
            })
          })
          wx.hideLoading();
        }
      })
    }
  }
})