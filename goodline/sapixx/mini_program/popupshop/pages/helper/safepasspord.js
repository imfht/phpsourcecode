const app = getApp()
var api = require('../../utils/request');
var util = require('../../utils/util');
var common = require('../../utils/common');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    disabled: false,
    countdown: 60,
    isPassword:false,
    sms: '',
    showType: 'number',//如果是密码换成'password'
    isFocus: true,
    dataSource: [{initValue:''},{initValue:''}, {initValue:''},{initValue:''},{initValue:''},{initValue:''}]
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    let that = this;
    app.isLogin(function (rel) {
      that.isPassword();
    });
  },
  /**
   * 用户tap假的Input,focus到隐藏的input
   */
  onTapFocus: function () {
    this.setData({
      isFocus:true
    });
  },
  /**
   * 移动端键入
   * 注意:在pc端无法显示键盘，移动端下编译、预览正常
   */
  mobileInput: function (e) {
    let dataSource = this.data.dataSource;
    let curInpArr = e.detail.value.split('');
    let curInpArrLength = curInpArr.length;
    if (curInpArr.length != this.data.dataSource.length)
      for (let i = 0; i < dataSource.length - curInpArrLength; i++)
        curInpArr.push('');
    for (let i = 0; i < this.data.dataSource.length; i++) {
      let initValue = 'dataSource[' + i + '].initValue';
      this.setData({
        [initValue]: curInpArr[i]
      });
    }
    if (6 == curInpArrLength){
      var checkPassword = curInpArr.join("");
      this.checkPassword(checkPassword);
    }
  },
  /**
   * 第一次近期检测是否设置了密码
   */
  isPassword: function () {
    let that = this;
    api.Get('openapi/v1/user/isSafePassword',{types:1},function (res) {
      if (res.code == 204) {
        that.setData({
          isPassword: true
        })
      }
    })
  },
  /**
   * 读取手机验证码
   */
  getSms: function (e) {
    let that = this;
    var detail = e.detail;
    if (detail.errMsg == 'getPhoneNumber:ok') {
      var parms = {
        encryptedData: detail.encryptedData,
        errMsg: detail.errMsg,
        iv: detail.iv
      }
      app.api().Post('openapi/v1/getBindWechatPhoneSms',parms,function (rel) {
        if (rel.code == 200) {
          wx.setStorageSync('session_id', rel.data.session_id);
          common.settime(that);
          that.setData({
            disabled: true,
            sms: rel.data.sms
          })
        }
      })
    }
  },
  /**
   * 验证密码是否正确
   */
  checkPassword: function (safepassword) {
    let that = this;
    if (safepassword.length == 6){
      wx.showLoading({
        title: '密码验证中',
      })
      var parms = {
        safepassword:safepassword,
      }
      api.Post('openapi/v1/user/checksafepassword',parms,function (res) {
        wx.hideLoading();
        if (res.code == 200) {
          that.setData({
            isPassword:true
          })
        }
        wx.hideLoading();
      })
    }
  },
  /**
    * 修改密码
    */
  formSubmit: function (e) {
    let that = this;
    var data = e.detail.value;
    var isPost = true;
    if (util.isNull(data.code)) {
      wx.showModal({
        content: '验证码必须输入'
      })
      isPost = false;
    } else {
      if (!(/^\d{6}$/.test(data.code))) {
        wx.showModal({
          content: '验证码输入错误'
        })
        isPost = false;
      }
    }
    if (data.safepassword != data.resafepassword) {
      wx.showModal({
        content: '两次密码必须一致'
      })
      isPost = false;
    }
    if (util.isNull(data.resafepassword)) {
      wx.showModal({
        content: '确认密码必须输入'
      })
      isPost = false;
    }
    if (util.isNull(data.safepassword)) {
      wx.showModal({
        content: '密码必须输入'
      })
      isPost = false;
    } else {
      if (!(/^\d{6}$/.test(data.safepassword))) {
        wx.showModal({
          content: '密码只能是6位数字'
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
        safepassword: data.safepassword,
        resafepassword: data.resafepassword,
        code: data.code,
      }
      api.Post('openapi/v1/user/setSafePassword', parms, function (res) {
        wx.showModal({
          showCancel: false,
          title: '友情提示',
          content: res.msg,
          complete: function () {
            wx.navigateBack({
              delta: 1
            })
          }
        })
      })
      wx.hideLoading();
    }
  }
})