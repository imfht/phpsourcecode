const app = getApp()
var api = require('../../utils/request');
var util = require('../../utils/util');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    loading: false,
    bank: {
      money: 0,
      due_money: 0,
      lack_money: 0,
      income_monney: 0,
      shop_money: 0
    },
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
    api.Get("api/v3/fastshop/bank/index", function (result) {
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
    if (util.isNull(data.safepassword)) {
      wx.showModal({
        content: '安全密码必须填写'
      })
      isPost = false;
    }
    if (util.isNull(data.money)) {
      wx.showModal({
        content: '提现金额必须填写'
      })
      isPost = false;
    } else {
      if (!(/^\d+$/.test(data.money))) {
        wx.showModal({
          content: '提现金额只能输入整数'
        })
        isPost = false;
      } else {
        var money = parseInt(data.money);
        var bank = this.data.bank;
        if (money > bank.due_money || money == 0) {
          wx.showModal({
            content: '超出允许提现金额'
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
      }
      api.Post('api/v3/fastshop/bank/cash', parms, function (res) {
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