var api = require('./utils/request');
var util = require('./utils/util');
var siteInfo = require('config.js');
App({
  globalData: {
    ucode:"",        //邀请码
    loginuser:{},    //登录用户
    gps:{},          //经纬度
    udp: {
      ip: '313de380deb0d533.natapp.cc',
      port: 1100
    }
  },
  //生命周期函数--监听小程序初始化
  onLaunch: function (options) {
    this.apiroot = siteInfo.siteroot; //设置api地址
    this.appname = siteInfo.name;     //设置应用名称
    this.updateMiniapp();  //小程序更新
    this.getConfig()
  },
  //应用配置
  getConfig: function () {
    if (util.isNull(wx.getStorageSync('config'))) {
      api.Get('openapi/v1/config',(result) => {
        wx.setStorageSync('config',result.data);
      })
    }
    var loginuser = wx.getStorageSync('loginuser');
    if (!util.isNull(loginuser)) {
      this.globalData.loginuser = loginuser;
    }
  },
  //判断是否登录
  isLogin: function (callback) {
    wx.checkSession({success:(rel)=>{
      if(rel.errMsg == 'checkSession:ok'){
        if (util.isNull(this.globalData.loginuser)){
          wx.navigateTo({url: '/pages/helper/login'})
        }else{
          typeof callback == "function" && callback(true)
        }
      }else{
        wx.navigateTo({url:'/pages/helper/login'})
      }
    },fail:(rel)=>{
      wx.navigateTo({url: '/pages/helper/login'})
    }})
  },
  //微信小程序登录
  doLogin: function (options,callback) {
    let that = this;
    wx.login({
      success: function (res) {
        api.Post('openapi/v1/login',{
          code: res.code,
          user_info: options.detail.rawData,
          encrypted_data: options.detail.encryptedData,
          iv: options.detail.iv,
          signature: options.detail.signature,
          official_uid: wx.getStorageSync('official_uid'),
          invite_code: that.globalData.ucode
        }, function (result) {
          wx.setStorageSync('token', result.data.token);
          wx.setStorageSync('session_id',result.data.session_id);
          wx.setStorageSync('loginuser', result.data);
          that.globalData.loginuser = result.data;
          callback && callback(true);
        })
      },
      fail: function () {
        callback && callback(false);
      }
    });
  },
  //调用微信支付
  doWechatPay: function (data,successCallback, failCallback) {
    var dataMap = {
      timeStamp: data.timestamp,
      nonceStr: data.nonceStr,
      package: data.package,
      signType: data.signType,
      paySign: data.paySign,
      success: successCallback,
      fail: failCallback
    }
    wx.requestPayment(dataMap);
  },
  /**
    * 确认窗口并返回
    */
  wxLayer: function (message, callback) {
    wx.showModal({
      content: message,
      success: (rel) => {
        if (rel.confirm) {
          typeof callback == "function" && callback()
        }
      }
    })
  },
  /**
   * 弹窗提示
   */
  wxAlert: function (message, callback) {
    wx.showModal({
      content: message, showCancel: false,
      success: (rel) => {
        if (rel.confirm) {
          typeof callback == "function" && callback()
        }
      }
    })
  },
  //用全局变量保存邀请码
  getParam: function (options, callback) {
    if (options.scene) {
      var options = util.strToArray(decodeURIComponent(options.scene));
      if (!util.isNull(options.ucode)) {
        this.globalData.ucode = options.ucode;
      }
    } else {
      if (!util.isNull(options.ucode)) {
        this.globalData.ucode = options.ucode;
      }
    }
    typeof callback == "function" && callback(options)
  },
  /**
   * 更新小程序
   */
  updateMiniapp: function () {
    var that = this;
    if (wx.canIUse('getUpdateManager')) {
      const updateManager = wx.getUpdateManager()
      //判断是否有新的小程序
      updateManager.onCheckForUpdate(function (res) {
        if (res.hasUpdate) {
          updateManager.onUpdateReady(function () {
            wx.clearStorage()    //清理缓存
            that.getLocation();
            wx.showModal({
              showCancel: false,
              title: '更新提示',
              content: '新版本已经升级完成，请重启应用!',
              success: function (res) {
                updateManager.applyUpdate()
              }
            })
          })
          //新的版本下载失败
          updateManager.onUpdateFailed(function () {
            wx.showModal({
              showCancel: false,
              title: '已经有新版本了哟~',
              content: '新版本自动升级失败了~请您删除当前小程序，重新搜索打开哟~',
              success: function (res) {
                updateManager.applyUpdate()
              }
            })
          })
        }
      })
    } else {
      wx.showModal({
        title: '提示',
        content: '当前微信版本过低，无法使用该功能，请升级到最新微信版本后重试。'
      })
    }
  },
  /**
   * 获取经纬度
   * 并判断所在城市,第一次如果城市已定位,第二次将不再进行城市定位判断
   */
  getLocation: function (callback) {
    var that = this
    wx.authorize({
      scope: 'scope.userLocation',
      success(res) {
        wx.getLocation({
          type: 'wgs84',
          success: function (res) {
            that.globalData.gps.latitude = res.latitude;
            that.globalData.gps.longitude = res.longitude;
            typeof callback == "function" && callback(true)
          },fail() {
            typeof callback == "function" && callback(false)
          }
        })
      }, fail() {
         typeof callback == "function" && callback(false)
      }
    })
  },
  //调用API
  api: function () {
    return api;
  },
  //工具集
  util: function () {
    return util;
  }
});