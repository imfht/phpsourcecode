const app = getApp()
Page({
  /**
   * 页面的初始数据
   */
  data: {
    isinfo: true,
    bankinfo: [],
  },
  onShow: function () {
    app.isLogin(() => {
      this.getBank();
    })
  },
  //获取绑定信息
  getBank: function () {
    app.api().Get('openapi/v1/bank/info', (rel) => {
      if (rel.code == 200) {
        this.setData({
          bankinfo: rel.data,
        })
      } else {
        this.setData({
          isinfo: false,
        })
      }
    })
  },
  //更改绑定信息
  bindBank: function () {
    this.setData({
      isinfo: false,
    })
  },
  /**
   * 提交绑定
   */
  formSubmit: function (e) {
    var data = e.detail.value;
    var isPost = false;
    var msg = '';
    console.log(data);
    if (app.util().isNull(data.bankname)) {
      msg = '开会银行必须填写'
    } else if (app.util().isNull(data.bankid)) {
      msg = '银行卡必须填写'
    } else if (app.util().isNull(data.bankid_confirm)) {
      msg = '确认银行卡必须填写'
    } else if (data.bankid != data.bankid_confirm) {
      msg = '两次输入卡号不一样'
    } else if (app.util().isNull(data.name)) {
      msg = '姓名必须填写'
    } else if (app.util().isNull(data.idcard)) {
      msg = '身份证号必须填写'
    } else if (app.util().isNull(data.safepassword)) {
      msg = '安全密码必须填写'
    } else {
      isPost = true;
      var parms = {
        bankname: data.bankname,
        bankid: data.bankid,
        bankid_confirm: data.bankid_confirm,
        name: data.name,
        idcard: data.idcard,
        safepassword: data.safepassword,
        formId: e.detail.formId,
      }
      wx.showLoading({ title: '提交中', mask: true })
      app.api().Post('openapi/v1/bank/bind', parms,(res) => {
        wx.showModal({
          showCancel: false,
          title: '友情提示',
          content: res.msg,
        })
        this.setData({
          isinfo: true,
        })
        wx.hideLoading();
      })
    }
    if (!isPost) {
      wx.showModal({
        content: msg,
        showCancel: false,
      })
    }
  }
})
