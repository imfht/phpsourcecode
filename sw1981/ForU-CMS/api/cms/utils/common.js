function navToPage(e) {
  console.log(e);
  var dataset = e.currentTarget.dataset;
  var id = dataset.id || 0;
  var path = dataset.path;
  wx.navigateTo({
    url: path + (id>0 ? '?id=' + id : '')
  });
}

function tabToPage(e) {
  console.log(e);
  var dataset = e.currentTarget.dataset;
  var path = dataset.path;
  wx.switchTab({
    url: path,
  })
}

function mergeArray(a1, a2){
  for(var i=0;i<a2.length;i++){
    a1.push(a2[i]);
  }
  return a1;
}

function getList(id, page, that){
  var app = getApp();
  var p = typeof page != 'undefined' ? page : 1;
  var list = that.data.list;
  wx.showLoading({
    title: '加载中',
  })
  wx.request({
    url: app.gData.apiUrl+'common.php',
    method: 'GET',
    data: {
      act:'getListDetail',
      id:id,
      page:p
    },
    success(res){
      console.log(res.data);
      if (p==1) {
        that.setData({
          page_total:res.data.totalPage || 1
        });
      }
      if (res.data.totalPage>=p) {
        that.setData({
          page:p+1,
          list:mergeArray(list, res.data.ex)
        })
      }
      wx.hideLoading();
    }
  });
}

function getInfo(that) {
  var app = getApp()
  var wp = require('wxParse/wxParse.js')
  var id = that.data.id
  wx.request({
    url: app.gData.apiUrl + 'common.php',
    method: 'GET',
    data: {
      act:'getRow',
      id:id,
      tbl:'detail'
    },
    success(e){
      console.log(e.data.ex)
      that.setData({
        title: e.data.ex.d_name,
        date: e.data.ex.date || '',
        imgs: typeof e.data.ex.slideshow != 'undefined' ? e.data.ex.slideshow.split('|') : ''
      })
      wp.wxParse('content', 'html', e.data.ex.d_content, that, 5)
    }
  })
}

module.exports = {
  navToPage:navToPage,
  tabToPage:tabToPage,
  mergeArray:mergeArray,
  getList:getList,
  getInfo:getInfo
}
