// pages/user/address/map.js
var app = getApp()
var amapFile = require('../../../utils/amap-wx.js');

Page({

  /**
   * 页面的初始数据
   */
  data: {
    markers: [],
    latitude: '',
    longitude: '',
    textData: {},
    poidata:[],
    tips: {},
    scrollHeight:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
    var myAmapFun = new amapFile.AMapWX({ key: "4215ecb5bc0ec372a5637590891246d4" });
    myAmapFun.getRegeo({
      iconPath: "../../../common/images/bz.png",
      iconWidth: 22,
      iconHeight: 22,
      success: function (data) {
          console.log(data);
        myAmapFun.getPoiAround({
          querykeywords: '小区|学校|公司|商城|KTV|网吧|酒吧|酒店',
          //querykeywords:'酒店,',
          location: '' + data[0].longitude + ',' + data[0].latitude + '',
          success: function (ress) {
            console.log(ress);
            that.setData({
              poidata:ress.poisData
            })
          },

        })

       that.setData({
          latitude: data[0].latitude
        });
        that.setData({
          longitude: data[0].longitude
        });



        
      }

    });

    wx.getSystemInfo({
      success: function (res) {
        // console.log('getSystemInfo');
        // console.log(res.windowWidth);

        let height = res.windowHeight;
        wx.createSelectorQuery().select('.header').boundingClientRect(function (rects) {

          that.setData({
            scrollHeight: res.windowHeight - rects.bottom

          });
        }).exec();  

        that.setData({
        
          controls: [{
            id: 1,
            iconPath: '../../../common/images/bz.png',
            position: {
              left: res.windowWidth / 2-11 ,
              top: 80,
              width: 22,
              height:22
            },
            clickable: true
          }]
        })
      }
    })

  



 




  }, 
  regionchange(e) {
    
   
    if(e.type=='end'){
      //console.log("执行获取当前位置");
      this.getNowLong();
    }
  },
   getNowLong:function(){
     var that = this;
     this.mapCtx = wx.createMapContext("map");
     this.mapCtx.getCenterLocation({
       success: function (res) {
         //成功后，用高德地图解析
         var lg = res.longitude;
         var lt = res.latitude;
         var myAmapFun = new amapFile.AMapWX({ key: "4215ecb5bc0ec372a5637590891246d4" });
         myAmapFun.getRegeo({
           iconPath: "../../../common/images/bz.png",
           iconWidth: 22,
           iconHeight: 22,
           location: '' + lg + ',' + lt + '',
           success: function (data) {
             console.log(data);
               console.log(data[0].latitude);

               myAmapFun.getPoiAround({
                 
                 //querykeywords:'小区|学校|公司|商城|KTV|网吧|酒吧|酒店',
                 location: '' + data[0].longitude + ',' + data[0].latitude + '',
                 success:function(ress){
                   console.log(ress);
                   that.setData({
                     poidata: ress.poisData
                   })
                 },
                 fail(info){
                   console.log(info);
                   that.setData({
                     poidata:''
                   })
                 }
                 
               })



           }
         })

       }

     })
   },

   findaddress:function(e){
     var name = e.currentTarget.dataset.name;
     console.log(name);  
     wx.setStorage({
       key: "findaddress",
       data: name
     })
    //  wx.navigateTo({
    //    url: 'add'
    //  })

     wx.navigateBack({
       delta: 1,
       success: function (e) {
         var page = getCurrentPages().pop();
         if (page == undefined || page == null) return;
         page.onLoad();

       }
     })
    //  wx.redirectTo({
    //    url: 'add'
    //  })
     
    //  wx.navigateTo({
    //    url: 'add'
    //  })
   },
  

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  
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
  bindInput: function (e) {
    var that = this;
    var keywords = e.detail.value;
    //var key = config.Config.key;
    var myAmapFun = new amapFile.AMapWX({ key: '4215ecb5bc0ec372a5637590891246d4' });
    myAmapFun.getInputtips({
      keywords: keywords,
      location: '',
      success: function (data) {
        if (data && data.tips) {
          that.setData({
            //tips: data.tips,
            poidata:data.tips
          });
        }

      }
    })
  },
})