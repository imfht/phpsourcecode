const app = getApp()
Page({
  data: {
    loading: true,
    page: 0,
    cate_id: 0,
    catetab: [],
    item: []
  },
  //监听页面加载
  onLoad: function(options) {
    this.getcate(options.id);
  },
  //上拉加载
  onReachBottom: function() {
    var that = this;
    that.setData({
      loading: true,
    });
    that.getItem();
  },
  //商品分类
  getcate: function (cate_id) {
    let that = this;
    var parms = {
      cate_id: cate_id,
      types: 0
    }
    app.api().Get('api/v3/fastshop/shop/cateTop',parms,function (result) {
      if (result.code == 200) {
        that.setData({
          catetab: result.data,
          cate_id: result.data[0].id,
        });
        that.getItem();
      }
    })
  },
  //点击请求数据
  getItem: function() {
    let that = this;
    if (that.data.loading) {
      var parms = {
        cate_id: that.data.cate_id,
        types:0,
        page: that.data.page + 1
      }
      app.api().Get('api/v3/fastshop/shop/cateItem',parms,function(result) {
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
  //点击Tab切换
  onClickTab: function(e) {
    let that = this;
    that.setData({
      cate_id: parseInt(e.detail.name),
      loading: true,
      item: [],
      page: 0,
    });
    that.getItem();
  },
})