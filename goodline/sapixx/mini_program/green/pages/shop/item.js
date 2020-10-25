const app = getApp()
Page({
  data: {
    item_id: 0,
    items: [],
  },
  //生命周期函数--监听页面显示
  onLoad: function (options) {
    app.getParam(options,(param)=>{
      this.setData({
        item_id: param.id
      });
    })
  },
  //生命周期函数--监听页面显示
  onShow: function () {
    this.getItem();
  },
  /**
   * 获取商品信息
   */
  getItem: function () {
    var item_id = this.data.item_id;
    app.api().Get('api/v1/green/shop/item',{'id':item_id},(result) => {
      this.setData({
        items: result.data,
      });
    })
  },
  //立即购买
  buy_now: function () {
    wx.navigateTo({
      url:'cart?id='+this.data.item_id
    })
  },
  //分享按钮
  onShareAppMessage: function (res) {
    var item_id = this.data.item_id, items = this.data.items, ucode = app.globalData.loginuser.ucode;
    return {
      title: '我使用【' + items.points + '】积分,兑换了' + items.name,
      desc: items.note,
      path: '/pages/shop/item?id=' + item_id + '&ucode=' + ucode,
      imageUrl: items.img
    }
  }
})