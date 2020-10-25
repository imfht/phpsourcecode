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
    app.isLogin((rel) => {
      this.isPassword();
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
    api.Get('openapi/v1/user/isSafePassword',{types:1},(res) =>{
      if (res.code == 204) {
        this.setData({
          isPassword: true
        })
      }
    })
  },
  /**
   * 读取手机验证码
   */
  getSms: function (e) {
    var detail = e.detail;
    if (detail.errMsg == 'getPhoneNumber:ok') {
      var parms = {
        encryptedData: detail.encryptedData,
        errMsg: detail.errMsg,
        iv: detail.iv
      }
      app.api().Post('openapi/v1/getBindWechatPhoneSms',parms,(rel) =>{
        if (rel.code == 200) {
          wx.setStorageSync('session_id', rel.data.session_id);
          common.settime(this);
          this.setData({
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
    if (safepassword.length == 6){
      wx.showLoading({
        title: '密码验证中',
      })
      var parms = {
        safepassword:safepassword,
      }
      api.Post('openapi/v1/user/checksafepassword',parms,(res) => {
        wx.hideLoading();
        if (res.code == 200) {
          this.setData({
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
      api.Post('openapi/v1/user/setSafePassword', parms, (res) => {
        app.wxLayer(res.msg,()=>{
          wx.navigateBack({
            delta: 1
          })
        })
      })
      wx.hideLoading();
    }
  }
})