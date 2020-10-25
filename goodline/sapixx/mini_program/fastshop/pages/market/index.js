const app = getApp()
Page({
  data: {
    loading: true,
    startBarHeight: app.globalData.startBarHeight,
    navgationHeight: app.globalData.navgationHeight,
    windowHeight: '',
    windowWidth: '',
    page: 0,
    activeIndex: 0,
    sliderLeft: 0,
    menuIndex: 0,
    times:[],
    time_id: 0,
    market:[],
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onLoad: function (options) {
    app.setUcode(options);
  },
  /**
   * 每次载入
   */
  onShow:function () {
    this.setData({
      page: 0,
      loading: true,
      market: []
    });
    this.getExtend();
  },
  //下拉
  onReachBottom: function () {
    var that = this;
    that.setData({
      loading: true,
    });
    this.getApi();
  },
  //获取首页接口
  getExtend: function () {
    let that = this;
    let time_id = that.data.time_id, index = that.data.activeIndex;
    let param = {
      time_id: time_id,
      index: index
    }
    app.api().Get('api/v3/fastshop/times/index',param,function (result) {
      if (result.code == 200){
        //计算我的tab索引
        for (let i in result.data) {
          if (result.data[i].state == 1) {
            index = i;
            time_id = result.data[i].id;
          }
        }
        that.setData({
          times: result.data,
          time_id: time_id,
          activeIndex: index,
          timeHeight:45
        });
        //开始计算滚动条位移
        wx.getSystemInfo({
          success: function (res) {
            var tabWidth = res.windowWidth / 5;
            that.setData({
              windowHeight: res.windowHeight,
              windowWidth: res.windowWidth,
              sliderLeft: (index - 2) * tabWidth
            });
          }
        });
      }else{
        that.setData({
          time_id:0,
          timeHeight:0
        });
      }
    })
    this.getApi();
  },
  //点击Tab切换
  selecttab: function (e) {
    var id         = parseInt(e.currentTarget.dataset.id);
    var sliderLeft = e.currentTarget.offsetLeft;
    var index      = e.currentTarget.dataset.index;
    var tabWidth = this.data.windowWidth/5;
    this.setData({
      page: 0,
      loading: true,
      time_id: id,
      sliderLeft: (index - 2) * tabWidth,
      market: []
    });
    this.getApi();
  },
  /**
 * 添加商品
 */
  getApi: function () {
    let that = this;
    if (that.data.loading) {
      var param = {
        page: that.data.page + 1,
        time_id: that.data.time_id
      }
      app.api().Get('api/v3/fastshop/sale/lists',param,function (result) {
        if (result.code == 200) {
          var market = that.data.market;
          for (let i in result.data) {
            market.push(result.data[i]);
          }
          that.setData({
            market: market,
            page: param.page,
          });
        }
        that.setData({
          loading: false,
        });
      })
    }
  },
  //分享按钮
  onShareAppMessage: function () {
    return {
      desc: app.appname,
      path: '/pages/index/index?ucode=' + app.globalData.loginuser.ucode
    }
  },
  //滚动
  onPageScroll(res) {
    let scrollTop = res.scrollTop;
    this.setData({
      'barbolor': 10 <= scrollTop ? '#FF3B35' : 'none'
    })
  }
})