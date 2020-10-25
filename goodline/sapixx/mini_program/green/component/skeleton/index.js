Component({
  data: {
    visible: true,
  },
  properties: {
    show: {
      type: Boolean,
      value: false
    },
    loading: {
      type: Boolean,
      value: false
    },
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
    if(this.properties.show){
      this.setData({
        visible: this.properties.loading
      })
    }else{
      setTimeout(() => {
        this.setData({
          visible: false
        })
      },1000)
    }
  },
  methods: {
    //锁屏
    touchmove: function () {

    }
  },
})