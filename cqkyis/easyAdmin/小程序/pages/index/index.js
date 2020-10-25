//index.js
//获取应用实例
//var base = require("../../common/base.js");
var configs = require('../../utils/common.js');
var amapFile = require('../../utils/amap-wx.js');
var CartJs = require("../../utils/cart.js");
var allgood=null;
const app = getApp()

Page({


    data:{
      address: "",
      cate: '',
      navScrollLeft: 0,
      currentTab: 0,
      $loading: {
        isShow: false
      },
      imgUrls:'',
      goodlist:'',
      types:'hide',
      scrollHeight:'',
      pageSize:0,
      cateID:'',
      showref:'hide',
      num:0
    },
   /**
   * 生命周期函数--监听页面加载
   */
    onLoad:function(){

    //建立连接

      // wx.connectSocket({
      //   url: 'wss://www.icqkx.com:2346',
      //   data: {
      //     x: '',
      //     y: ''
      //   },
      //   header: {
      //     'content-type': 'application/json'
      //   },
      //   protocols: ['protocol1'],
      //   method: "GET"
      // })


      // wx.connectSocket({
      //   url: "ws://www.icqkx.cn:2346",
      // });

      var that = this;
      wx.request({
        url: configs.MallConfig,
        success: function (r) {
          console.log(r);
          wx.setStorageSync('goodconfig', r);
        }
      });
       //获取分类数据
       wx.request({
         url: configs.Ajax_Home,
         header: {
           'content-type': 'application/json'
         },
         method: 'POST',
         success: function (res) {
          // console.log(res.data);
           var cateData = res.data;
           //默认展示第一个
           that.LoadGood(cateData[0]['cate_id']);
           that.setData({
             cate: cateData,
             cateID: cateData[0]['cate_id']
           })
         }
       })
      

   
      
      //  使用高德地图定位
      var myAmapFun = new amapFile.AMapWX({ key: '4215ecb5bc0ec372a5637590891246d4' });
      myAmapFun.getRegeo({
        success: function (data) {
          //console.log(data);
          that.setData({
            address: data[0].desc
          });
          wx.hideLoading()
        }, fail: function (info) {
          //失败回调
         
          that.setData({
            address: "获取位置失败"
          });

        }
      });





      wx.getSystemInfo({
        success: (res) => {
          let height = res.windowHeight;
          wx.createSelectorQuery().select('.header').boundingClientRect(function (rects) {

            that.setData({
              scrollHeight: res.windowHeight - rects.bottom

            });
          }).exec();  

          this.setData({
            pixelRatio: res.pixelRatio,
            windowHeight: res.windowHeight,
            windowWidth: res.windowWidth
          })
        },
      })
        
        
    },
    LoadGood: function (cate_id){
      var that = this;
      that.setData({
        $loading: {
          isShow: true
        },
        cateID: cate_id
      })

      wx.request({
        url: configs.Good_Cate_list,
        header: {
          'content-type': 'application/json'
        },
        data:{
          id:cate_id,
          pageSize: that.pageSize
        },
        method: 'POST',
        success:function(res){
         //console.log(res); 
         //将取得的产品放入allgood
         allgood = res.data.data;
         that.cartUpdat(res.data.data);
         if(res.data.data!=''&&res.data.data!=null){
           that.setData({
             types:'hide',
            
           })
         }else{
           that.setData({
             types: 'show'
           })
         }
         that.setData({
           goodlist:res.data.data,
           imgUrls:res.data.advert,
           $loading: {
             isShow: false
           },
           showref: 'hide'
         })
        }
      });
      //查询购物车是不是有东西，如果有东西，显示数据等相关信息
     
    },
  goodinfo:function(e){
    var id = e.currentTarget.dataset.id;
    wx.navigateTo({
      url: '../good/info?goodid='+id,
    })
  }, 
  cartUpdat:function(e){
    var that = this;
   
    var cart = wx.getStorageSync("cart");
    if (!cart) {
     
    } else {
      
      var nums = cart.totalNumber;
      
      wx.setTabBarBadge({
        index: 1,
        text: nums.toString()
      });
     
  var tt = cart.productlist;
   for(var i in e){

     for (var k in tt){
        
        if(e[i]['good_id']==tt[k]['id']){
          console.log("有相同的ID");
          e[i]['num']=tt[k]['num'];
          e[i]['hide'] = true;
          e[i]['show'] = true;
        }else{
          console.log("没有相同的ID");
        }


      }

   }
 that.setData({
  goodlist: e,
  num: nums,
 })



    }
  },
  upper: function (e) {
    // console.log(e.currentTarget.dataset.id);
    var that =this;
    var cate_id = e.currentTarget.dataset.id;
    that.setData({
      showref:'show'
    })
    setTimeout(function(){
      that.LoadGood(cate_id);
    },1500)
    
    //console.log(e);
  },
  lower: function (e) {
   // console.log(e);
    
  },
  cartClick:function(e){
    //console.log(e);
    var id = e.currentTarget.dataset.id;
    
    var that = this;
    var good = allgood;
    console.log(good);
   //采用本地缓存，存入到本地购物车
    for (var i in good) {
      if (good[i]['good_id'] == id) {
          good[i]['hide'] = true;
          good[i]['show'] = true;
          good[i]['num'] = 1;
          var nums = that.data.num + 1;
          
          var goodid = good[i]['good_id'];
          var name = good[i]['good_name'];
          var price = good[i]['price'];
          var saleprice = good[i]['mall_price'];
          var imgs = good[i]['good_img'];
          var sname = good[i]['good_s_name'];

       

      }

    }
    var product = {
      id: goodid,
      name: name,
      price: price,
      saleprice: saleprice,
      imgs: imgs,
      sname: sname,
      num: 1

    }
    CartJs.GoodCart(product);
    wx.setTabBarBadge({
      index: 1,
      text: nums.toString()
    })
    that.setData({
      goodlist: good,
      num: nums,
      
    });

  },
  add: function (e) {
    var id = e.currentTarget.dataset.id;
    var that = this;
    var good = allgood;
    // 获取事件绑定的当前组件
    for (var i in good) {
      if (good[i]['good_id'] == id) {

        good[i]['num'] = good[i]['num'] + 1;
        var nums = that.data.num + 1;
        var goodid = good[i]['good_id'];
        var name = good[i]['good_name'];
        var price = good[i]['price'];
        var saleprice = good[i]['mall_price'];
        var imgs = good[i]['good_img'];
        var sname = good[i]['good_s_name'];

      }

    }

    var product = {
      id: goodid,
      name: name,
      price: price,
      saleprice: saleprice,
      imgs: imgs,
      sname: sname,
      num: 1

    }
    CartJs.GoodCart(product);


    wx.setTabBarBadge({
      index: 1,
      text: nums.toString()
    })
    that.setData({
      goodlist: good,
      num: nums,
      
    });
  },
  del: function (e) {
    var id = e.currentTarget.dataset.id;
    var that = this;
    var good = allgood;
    // 获取事件绑定的当前组件
    for (var i in good) {
      if (good[i]['good_id'] == id) {

        good[i]['num'] = good[i]['num'] - 1;
        if (good[i]['num'] == 0) {
          good[i]['hide'] = false;
          good[i]['show'] = false;
          good[i]['num'] = 0;

        }
        var nums = that.data.num - 1;
        var delId = good[i]['good_id'];
      }

    }
    CartJs.UpdataNum(delId);
    if (nums == 0) {
      wx.removeTabBarBadge({
        index: 1
      })
    } else {
      wx.setTabBarBadge({
        index: 1,
        text: nums.toString()
      })
    }
    

    that.setData({
      goodlist: good,
      num: nums,
      
    });
  },
    /**
   * 生命周期函数--监听页面初次渲染完成
   */
    onReady: function () {
      this.setData({
        $loading: {
          isShow: false
        }
      })
    },

    /**
     * 生命周期函数--监听页面显示
     */
    onShow: function () {
      this.onLoad();
    },

    /**
     * 生命周期函数--监听页面隐藏
     */
    onHide: function () {

    },

    /**
     * 生命周期函数--监听页面卸载
     */
    onUnload: function () {

    },

    /**
     * 页面相关事件处理函数--监听用户下拉动作
     */
    onPullDownRefresh: function () {

    },

    /**
     * 页面上拉触底事件的处理函数
     */
    onReachBottom: function () {

    },

    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function () {

    },
    cateClick:function(e){
      var idx = e.currentTarget.dataset.idx;
      var index = e.currentTarget.dataset.index;
      var singleNavWidth = this.data.windowWidth / 5;
      this.LoadGood(idx);
      this.setData({
        navScrollLeft: (index - 2) * singleNavWidth
      })      

      if (this.data.currentTab == index) {
        return false;
      } else {
        this.setData({
          currentTab: index
        })
      }
     
    }
  
 
})
