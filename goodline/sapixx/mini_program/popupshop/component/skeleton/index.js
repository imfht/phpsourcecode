Component({
  data: {
    visible: true,
  },
  attached: function () {
    const systemInfo = wx.getSystemInfoSync();
    this.setData({
      systemInfo: {
        width: systemInfo.windowWidth,
        height: systemInfo.windowHeight
      },
    })
  },
  ready: function () {
    const that = this;
    setTimeout(() => {
      that.setData({
        visible: false
      })
    },1000)
  },
  methods: {
    //锁屏
    touchmove: function () {

    }
  },
})