const app = getApp()

Page({
  data: {
    service: true,
    config: {
      profit:0
    },
    user_sale_id: 0,
    user_price: 0,
    price: 0,
    amount: 0,
    item: [],
    items: [],
    checkbox: [],
  },
  /**
  * 生命周期函数--监听页面显示
  */
  onLoad: function (options) {
    var id = parseInt(options.id);
    this.setData({
      user_sale_id:id
    });
    this.getConfig(id);
  },
  getConfig: function (user_sale_id) {
    let that = this;
    //读取配置
    app.api().Get("api/v1/popupshop/index/config",{'signkey': 'config'},function (result) {
      if (result.code == 200) {
        that.setData({
          config: result.data
        });
      }
    })
    //读取商品
    app.api().Get("api/v1/popupshop/store/saleUser", { user_sale_id: user_sale_id },function (result) {
      if (result.code == 200) {
        that.setData({
          item: result.data,
          user_price: parseInt(result.data.user_price),
          price: parseInt(result.data.user_price)
        });
        that.getItem();
      }
    })
  },
  //点击请求数据
  getItem: function () {
    let that = this;
    var param = {
      page:1,
      user_sale_id: that.data.user_sale_id,
      cate_id: 0,
      num:10
    }
    app.api().Get('api/v1/popupshop/store/salelHouse', param, function (result) {
      if (result.code == 200) {
        var items = result.data;
        var checkbox = that.data.checkbox;
        for (var i = 0; i < items.length; i++) {
          checkbox.push(0);
        }
        that.setData({
          items: items,
          checkbox: checkbox,
        });
      }
    })
  },
  //提交商家
  onSubmit(event) {
    let that = this;
    let checkbox = that.data.checkbox,service = that.data.service,items = that.data.items;
    if (service == false) {
      wx.showModal({ content: '您必须遵守用户服务协议', showCancel: false });
    }else{
      let num = 0;
      if (checkbox.length > 0){
        num = checkbox.reduce(function (prev, next) {
          return prev + next;
        });
      }
      if (2 != num) {
        wx.showModal({ content: '请选择2个配套产品', showCancel: false });
      } else {
        var sela = new Array(), n = 0;
        for (let i in checkbox) {
          if (checkbox[i] == 1) {
            sela[n] = items[i].id;
            n++;
          }
        }
        var param = {
          sale_ids: JSON.stringify(sela),
          user_sale_id: that.data.user_sale_id,
          entrust_price: that.data.price
        }
        app.api().Post('api/v1/popupshop/store/onSale', param, function (result) {
          wx.showModal({
            content: result.msg,
            showCancel: false,
            complete:function() {
              wx.navigateBack({
                delta: 1
              })
            }
          });
        })
      }
    }
  },
  //价格计算求
  calculator(){
    let that = this;
    var checkbox = that.data.checkbox,items = that.data.items, price = that.data.price;
    var amount = 0, profit = 1 - that.data.config.profit / 100;
    for (let i in checkbox) {
      if (checkbox[i] == 1) {
        amount = amount + parseFloat(items[i].cost_price);
      }
    }
    return amount = price * profit - amount
  },
   /**
   * 选择
   */
  onChange(event) {
    let that = this;
    var key = event.currentTarget.dataset.key;
    var checkbox = that.data.checkbox;checkbox[key] = event.detail ? 1 : 0
    var num = checkbox.reduce(function (prev, next) {
       return prev + next;
    });
    var amount = 0;
    if (num > 2){
      checkbox[key] = 0
      wx.showModal({content: '您最多允许选择 2 个商品', showCancel: false });
    }else{
      if (2 == num){
        amount = that.calculator();
      }
      that.setData({
        checkbox: checkbox,
        amount: amount
      })
    }
  },
  /**
   * 输入金额
   */
  onChangeAmount: function (event){
    var input_price = event.detail;
    var price = app.util().isNull(input_price) ? 0 : parseInt(input_price);
    var user_price = this.data.user_price;
    if (price > user_price){
      var str = price.toString(),sub_price = str.substring(0,str.length - 1)
      this.setData({
        price: sub_price
      })
       wx.showModal({ content: '寄卖价不能大于你的购买价',showCancel: false });
    }else{
      this.setData({
        price: input_price
      })
    }
    var checkbox = this.data.checkbox;
    var num = checkbox.reduce(function (prev, next) {
      return prev + next;
    });
    this.setData({
      amount: 2 == num ? this.calculator() : 0
    })
  },
  /**
  * 点击Tab栏目切换
  */
  onClickTab: function (event) {
    let cate_id = parseInt(event.detail.name);
    this.setData({
      loading: true,
      cate_id: cate_id,
      page: 0,
      item: [],
      checkbox: [],
    });
    this.getItem(cate_id);
  },
  /**
   * 是否选择用户服务协议
   */
  onService(event) {
    this.setData({
      service: event.detail
    });
  },
  /**
   * 服务协议
   */
  service() {
    let config = app.globalData.wechat_config;
    wx.navigateTo({
      url: '/pages/helper/webview?src=' + app.apiroot + '/api-' + config.app_id + '/v1/popupshop/webview/service'
    })
  },
  //移除触摸限制
  moveTouch: function () {

  }
})