var api = require('./utils/request');
var util = require('./utils/util');
var siteInfo = require('config.js');
App({
  globalData: {
    loginuser: [],
    wechat_config:[],
    config:[],
    official_uid: null,
    ucode: null
  },
  /**
   * 生命周期函数--监听小程序初始化
   */
  onLaunch: function (options) {
    this.api_root = siteInfo.siteroot; //设置api地址
    this.appname = siteInfo.name; //设置应用名称
    this.updateMiniapp()  //小程序更新
    this.setNavigation();  //工具栏
  },
  /**
   * 生命周期函数--监听小程序显示
   */
  onShow: function () {
    let that = this;
    //微信配置
    that.getConfig(function (data) {
      wx.setStorageSync('wechat_config', data);
      that.globalData.wechat_config = data;
    });
    //登录用户信息
    var loginuser = wx.getStorageSync('loginuser');
    if (!util.isNull(loginuser)) {
      that.globalData.loginuser = loginuser;
    }
  },
  //读取小程序配置
  getConfig: function (callback) {
    api.Get('openapi/v1/config', function (result) {
      callback && callback(result.data);
    })
  },
  //用全局变量保存邀请码
  setUcode: function (options) {
    let that = this;
    if (options.scene) {
      var scene = util.strToArray(decodeURIComponent(options.scene));
      if (!util.isNull(scene.ucode)) {
        that.globalData.ucode = scene.ucode;
      }
    } else {
      if (!util.isNull(options.ucode)) {
        that.globalData.ucode = options.ucode;
      }
    }
  },
  //当前用户id
  getUserId: function () {
    return this.globalData.loginuser.uid;
  },
  //判断是否登录
  isLogin: function (callback) {
    let session_id = wx.getStorageSync('session_id'),
      loginuser = wx.getStorageSync('loginuser'),
      token = wx.getStorageSync('token')
    wx.checkSession({
      success(rel) {
        if (util.isNull(session_id) || util.isNull(token) || util.isNull(loginuser)) {
          wx.navigateTo({
            url: '/pages/helper/login'
          })
        } else {
          callback && callback(true);
        }
      },
      fail(rel) {
        wx.navigateTo({
          url: '/pages/helper/login'
        })
      }
    })
  },
  /**
    * 微信小程序登录
    */
  doLogin: function (options, callback) {
    let that = this;
    wx.login({
      success: function (res) {
        api.Post('openapi/v1/login', {
          code: res.code,
          user_info: options.detail.rawData,
          encrypted_data: options.detail.encryptedData,
          iv: options.detail.iv,
          signature: options.detail.signature,
          official_uid: wx.getStorageSync('official_uid'),
          invite_code: that.globalData.ucode
        }, function (result) {
          wx.setStorageSync('token', result.data.token);
          wx.setStorageSync('session_id', result.data.session_id);
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
  /**
  * 调用微信支付
  */
  doWechatPay: function (data, successCallback, failCallback) {
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
   * 更新小程序
   */
  updateMiniapp: function () {
    if (wx.canIUse('getUpdateManager')) {
      const updateManager = wx.getUpdateManager()
      //判断是否有新的小程序
      updateManager.onCheckForUpdate(function (res) {
        if (res.hasUpdate) {
          updateManager.onUpdateReady(function () {
            wx.clearStorage()   //清理缓存
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
* Navgation导航的高度
*/
  setNavigation() {
    let that = this
    wx.getSystemInfo({
      success: function (e) {
        that.globalData.startBarHeight = e.statusBarHeight;
        if (e.platform == 'android') {
          that.globalData.navgationHeight = 50;
        } else {
          that.globalData.navgationHeight = 45;
        };
      }
    })
  },
  /**
   * 调用购物车图标
   */
  setTabBarCartNumber: function () {
    var cartItemNumber = wx.getStorageSync('shopping_num')
    if (cartItemNumber > 0 && !this.util().isNull(cartItemNumber)) {
      wx.setTabBarBadge({
        index: 2,
        text: cartItemNumber.toString()
      })
    } else {
      wx.removeTabBarBadge({
        index: 2,
      })
    }
  },
  /**
   * 调用API
   */
  api: function () {
    return api;
  },
  util: function () {
    return util;
  }
});