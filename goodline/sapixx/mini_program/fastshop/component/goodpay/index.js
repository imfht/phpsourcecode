Component({
  /**
   * 组件的属性列表
   */
  properties: {
    params: {
      type: Object,
      value: null
    },
    envVersion: {
      type: String,
      value: "release"
    }
  },
  /**
   * 组件的初始数据
   */
  data: {
    appid:'wx162ab11aed32751e',
    showPayModal:false,
    chickOnPay: false   //标记是否支付中状态
  },
  /**
   * 组件的方法列表
   */
  methods: {
    //设置支付点击了打开云小程序
    setPaying(paystatus) {
      this.setData({
        chickOnPay: paystatus
      })
      this.triggerEvent('Change',{chickOnPay:paystatus})
    },
    //[跳转到【收银台】小程序失败-用户点击了支付组件外的地方（灰色地方）
    onTapCancel () {
      this.triggerEvent('Fail',{navigateSuccess:false})
      this.triggerEvent('Complete')
    },
    //跳转到【收银台】小程序成功
    navigateSuccess () {
      this.setPaying(true)
    },
    /**
     * 跳转失败
     * 如果下单成功但是用户取消支付则可在
     * 调用组件用 res.detail.event 查看原因
     */
    navigateFail (event) {
      this.triggerEvent('Fail',{navigateSuccess: false,event:event})
      this.triggerEvent('Complete')
    }
  },
  /** 
   * 组件生命周期
   */
  lifetimes: {
    attached() {
      this.setPaying(false)
      if (!this.data.params) {
        console.error('跳转到【收银台】小程序失败 - 没有传递跳转参数')
        this.triggerEvent('Fail',{error: true,navigateSuccess: false})
        this.triggerEvent('Complete')
      }
      //监听app.onShow事件
      wx.onAppShow(appOptions => {
        if (!this.data.chickOnPay){
          return;
        }
        this.setPaying(false)
        if (appOptions.scene === 1038 && appOptions.referrerInfo.appId === 'wx162ab11aed32751e') {
          console.log('确认来源于【收银台】回调返回')
          let extraData = appOptions.referrerInfo.extraData
          if (extraData.return_code =='SUCCESS') {
            this.triggerEvent('Success',extraData)
            this.triggerEvent('Complete')
          } else {
            this.triggerEvent('Fail',{navigateSuccess:true,event:extraData})
            this.triggerEvent('Complete')
          }
        }
      })
      var params = this.data.params;
      //尝试打开
      wx.navigateToMiniProgram({
        appId: this.data.appid,
        path: 'pages/dopay/index',
        extraData: params,
        envVersion: this.data.envVersion,
        success:yes => {
          console.log('跳转到【收银台】小程序成功')
          this.setPaying(true)
        },
        fail:no => {
          console.log('跳转到【收银台】小程序失败-弹窗提醒')
          this.setData({
            showPayModal: true,
            appid: this.data.appid
          })
        },
      })
    }
  }
})
