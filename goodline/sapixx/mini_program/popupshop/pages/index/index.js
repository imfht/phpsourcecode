const app = getApp()
var common = require('../../utils/common');
Page({
  data: {
    loading: true,
    swiper_height:0,
    page: 0,
    adwords: [],
    item: [],
    sale: [],
    imgheights: [],
    notice: { id: 0, title: '' },
    index:0
  },
 //页面创建时执行
  onLoad: function (options) {
    app.setUcode(options);
    this.getApi();
    this.getCate();
    this.getApiTabItem(0);
  },
  //页面出现在前台时执行
  onShow:function(){
    app.setTabBarCartNumber();
    this.getSale();
  },
  //上拉加载
  onReachBottom: function () {
    this.setData({
      loading: true,
    });
    this.getApiTabItem(this.data.index);
  },
  //获取首页接口
  getApi: function () {
    let that = this;
    //获取广告
    app.api().Get('api/v1/popupshop/adwords/all', {apis:'1/2/3'}, function (result) {
      if (200 == result.code){
        that.setData({
          adwords: result.data
        });
      }
    })
    //获取公告
    app.api().Get('api/v1/popupshop/index/notice', { 'signkey': 'notice' }, function (result) {
      if (200 == result.code) {
        that.setData({
          notice: result.data,
        });
      }
    })
  },
  //获取套装
  getSale:function(){
    //获取套装
    app.api().Get('api/v1/popupshop/sale/index', { 'signkey': 'index' }, (result) => {
      if (200 == result.code) {
        this.setData({
          sale: result.data,
        });
        setTimeout(() => {
          this.autoHeight();
        },1500)
      }
    })
  },
  //获取推荐栏目Tab
  getCate: function () {
    let that = this;
    app.api().Get('api/v1/popupshop/shop/cateTop', {types: 1, cate_id:0},function (result) {
      if (result.code == 200) {
        that.setData({
          catetab: result.data,
        });
        that.getApiTabItem(0);  //API请求店铺
      }
    })
  },
  /**
   * 活动指定分类商品(所有子类)
   */
  getApiTabItem: function (id) {
    let that = this;
    if (that.data.loading) {
      var parms = {
        cate_id: id,
        types: 1,
        page: that.data.page + 1
      }
      app.api().Get('api/v1/popupshop/shop/cateitem', parms, function (result) {
        if (result.code == 200) {
          var item = that.data.item;
          for (let i in result.data) {
            item.push(result.data[i]);
          }
          that.setData({
            item: item,
            page: parms.page,
          });
        }
        that.setData({
          loading: false,
        });
      })
    }
  },
  /**
   * 点击Tab栏目切换
   */
  onClickTab: function (event) {
    let id = parseInt(event.detail.name);
    this.setData({
      index:id,
      loading: true,
      page:0,
      item: [],
    });
    this.getApiTabItem(id);
  },
  //图片高度
  imageLoad: function (event) {
    this.setData({
      imgheights: common.autoimg(event)
    })
  },
  //点击公告栏
  onNotice: function (event) {
    let id = event.currentTarget.dataset.id, config = app.globalData.wechat_config;
    wx.navigateTo({
      url: '/pages/helper/webview?src=' + app.apiroot + '/app-' + config.app_id + '/popupshop/article/review/' + id
    })
  },
  //点击访问某个商品
  onItem: function (event) {
    wx.navigateTo({
      url: '../shop/item?id='+event.currentTarget.dataset.id,
    })
  },
  /**
   * 分享按钮
   */
  onShareAppMessage: function () {
    return {
      path: '/pages/index/index?ucode=' + app.globalData.loginuser.ucode
    }
  },
  //推荐二维码
  qrcode:function(){
    wx.navigateTo({
      url: '../user/qrcode',
    })
  },
  //判断活动的高度
  autoHeight() {
    wx.createSelectorQuery().select('.card').boundingClientRect().exec(rect => {
      if (!app.util().isNull(rect[0])) {
        this.setData({
          swiper_height: rect[0].height
        });
      }
    })
  },
  //滚动
  onPageScroll(res) {
    let scrollTop = res.scrollTop;
    if (scrollTop <= 10 && scrollTop <= 15) {
      this.autoHeight();
    }
  }
})