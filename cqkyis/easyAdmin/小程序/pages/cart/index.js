var app = getApp();
var CartJs = require("../../utils/cart.js");
var Orders = require("../../utils/common.js");
var allgood=null;

Page({
  data: {
    goodlist:'',
    types:'hide',
    headerhide:'show',
  
    paidprice:'',
    
   
  },
  onLoad: function () {
    var self  = this;
    wx.setNavigationBarTitle({
      title: '我的购物车',
    });
    var config = wx.getStorageSync('goodconfig'); 
    console.log(config); 
    self.setData({
      disfee: config.data.postprice,
      sendtime: config.data.sendtime,
      pm: config.data.mprice
    })
  },
    onShow: function () {
     
      var that = this;
      
    
       
      var config = wx.getStorageSync('goodconfig');  
         
      var Cart = wx.getStorageSync('cart');
      
      
      if (!Cart) {
            
             that.setData({
               goodlist: "",
               headerhide: 'show',
               types: 'hide'
             });
             wx.removeTabBarBadge({
               index: 1
             })
      }else{
      
      
         
        allgood = Cart.productlist;
      
        if (parseFloat(Cart.totalAmount) >= config.data.mprice){
     
       that.setData({
         mm:'hide',
         disfee:0
       });

     }else{
      
       
       that.setData({
         mm:'show',
         cm: (parseFloat(config.data.mprice)- parseFloat(Cart.totalAmount)).toFixed(2),
         disfee: config.data.postprice 
       })
     }

        var relaprice = (parseFloat(Cart.totalAmount) + parseFloat(that.data.disfee)).toFixed(2);
        console.log(relaprice);
        that.setData({
          goodlist: Cart.productlist,
          headerhide:'hide',
          types:'show',
          toalpric: Cart.totalAmount,

          compay: Cart.totalAmount,
          toal: relaprice
        });
       
          wx.setTabBarBadge({
            index: 1,
            text: Cart.totalNumber.toString()
          })
       
       
      }
  },
  add:function(e){
    var that = this;
    var id = e.currentTarget.dataset.id;
    console.log(id);
    var good = allgood;
    for (var i in good) {
      if (good[i]['good_id'] == id) {

        good[i]['num'] = good[i]['num'] + 1;
       }

    }
   that.setData({
     goodlist: good,
   })
   CartJs.AddCart(id);
   that.onShow();
  },
  del:function(e){
    var that = this;
    var id = e.currentTarget.dataset.id;
    console.log(id);
    var good = allgood;
    for (var i in good) {
      if (good[i]['good_id'] == id) {

        good[i]['num'] = good[i]['num']- 1;
       
      }

    }
    that.setData({
      goodlist: good,
    })
    CartJs.UpdataNum(id);
    that.onShow();
    

  },
  creatOrder:function(){
    // wx.navigateTo({
    //   url: 'order/pay',
    // })
    var that = this;
    //console.log(Logins.userInfo());
    var userInfo = wx.getStorageSync('user');
    
    var cart = wx.getStorageSync('cart');
    var feeprice = that.data.disfee;
   
    if(!userInfo){
      //没有值的情况下
      wx.showToast({
        title: '你还没有登录',
        icon: 'none',
        duration: 2000
      })
      setTimeout(function(){
        wx.navigateTo({
          url: '../user/login/login',
        })
      },2000)
      //console.log(1);
    }else{
      console.log(2);
     //点击结算就创建订单
      wx.request({
        url: Orders.Order,
        data:{
         uid:userInfo.uid,
         cart:cart,
         postprice: feeprice
        },
        success:function(e){
          console.log(e);
          //创建成功就到另一个页面
          if (e.data.code == 1) {
            wx.removeStorageSync('cart');
            that.onShow();
            wx.setStorageSync('code', e.data.ordercode);
            wx.navigateTo({
              url: 'order/pay'
            })
          }else{
            wx.showToast({
                  title: '创建订单失败',
                  icon:"none",
                  duration:1500
                })
          }
         
        }
      })

    }
  }

})