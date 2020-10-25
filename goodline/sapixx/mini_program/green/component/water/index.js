Component({
  data: {
    visible: true,
  },
  attached: function () {
    const systemInfo = wx.getSystemInfoSync();
    this.setData({
      systemInfo: {
        width: systemInfo.windowWidth,
      },
    })
  }
})