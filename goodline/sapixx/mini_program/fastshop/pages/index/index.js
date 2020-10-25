const app = getApp()
var common = require('../../utils/common');
Page({
  data: {
    loading:true,
    page: 0,
    index: 0,
    swiper_height:0,
    imgheights: [],
    adwords:[],
    notice: {id: 0,title:''},
    tabs:[],
    item: [],
    sale:[],
  },
  //页面创建时执行
  onLoad: function (options) {
    app.setUcode(options);
    this.getApi();
    this.getItem(0);
  },
  //页面出现在前台时执行
  onShow: function () {
    app.setTabBarCartNumber();
  },
  //获取首页接口
  getApi: function () {
    let that = this;
    //获取广告
    var param = {
      apis: JSON.stringify([2,3,4])
    }
    app.api().Get('api/v3/fastshop/adwords/all', param, function (result) {
      if (200 == result.code) {
        that.setData({
          adwords: result.data,
        });
      }
    })
    //获取公告
    app.api().Get('api/v3/fastshop/index/notice',{'signkey':'notice'},function (result) {
      if (200 == result.code) {
        that.setData({
          notice: result.data,
        });
      }
    })
    //获取导航
    app.api().Get('api/v3/fastshop/shop/tabs', function (result) {
      if (result.code == 200) {
        that.setData({
          catetab: result.data
        });
      }
    })
    //获取套装
    app.api().Get('api/v3/fastshop/sale/index', {'signkey':'index'}, function (result) {
      if (200 == result.code) {
        that.setData({
          sale: result.data,
        });
        setTimeout(() => {
          that.autoHeight();
        },1000)
      }
    })
  },
  /**
   * 点击Tab栏目切换
   */
  onClickTab: function (event) {
    let id = parseInt(event.detail.name);
    this.setData({
      index: id,
      loading: true,
      page: 0,
      item: [],
    });
    this.getItem(id);
  },
  /**
   * 活动指定分类商品(所有子类)
   */
  getItem: function (id) {
    let that = this;
    if (that.data.loading) {
      var parms = {
        cate_id: id,
        types: 1,
        page: that.data.page + 1
      }
      app.api().Get('api/v3/fastshop/shop/cateitem', parms, function (result) {
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
      url: '/pages/helper/webview?src=' + app.api_root + '/api-' + config.app_id + '/v3/fastshop/webview/index/' + id
    })
  },
  //点击访问某个商品
  onItem: function (event) {
    wx.navigateTo({
      url: '../shop/item?id=' + event.currentTarget.dataset.id,
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
  qrcode: function () {
    wx.navigateTo({
      url: '../user/qrcode',
    })
  },
  //搜索
  onSearch: function (event) {
    wx.navigateTo({
      url: '../shop/search?keyword=',
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