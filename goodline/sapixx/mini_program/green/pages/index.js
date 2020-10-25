const app = getApp()
//const udpSocket = wx.createUDPSocket()
Page({
 /**
  * 页面的初始数据
  */
  data: {
    disabled:false,
    gps:{
      longitude:113.81908,//113.324520,
      latitude:36.08308   //23.099994
    },
    markers:[]
  },
  //监听页面加载
  onLoad: function (options) {
    //this.initUdpSocket();
    this.getLocation();
    app.getParam(options,(param) => {
      if (!app.util().isNull(param.q)){
        var url = decodeURIComponent(param.q);
        this.openBox(url.split('?')[1]);
      }
    })
  },
  //判断是否定位成功
  getLocation:function() {
    var timer = setTimeout(() => {
      app.getLocation((rel) => {
        if (rel == true) {
          clearTimeout(timer);
          this.getApi();
        }else{
          this.getLocation();
        }
      })
    },1000);
  },
  //监听页面显示
  onShow:function (){
 
  },
  //监听页面就绪
  onReady:function () {
    this.mapCtx = wx.createMapContext('iotMap')
  },
  //请求设备经纬度
  getApi(){
    var param = {
      longitude:app.globalData.gps.longitude,
      latitude:app.globalData.gps.latitude,
      signkey:app.util().getRandom(12)
    }
    app.api().Get('api/v1/green/index/map',param,(rel) =>{
      if (rel.code == 200) {
        this.setData({
          markers: app.util().bdtotx(rel.data.device),
        })
        if(app.util().isNull(rel.data.near)){
          this.setData({
            gps:app.globalData.gps,
            iotTitle:rel.data.device[0].title,
          }) 
          this.data.iotTitle   = rel.data.device[0].title
          this.data.iotAddress = rel.data.device[0].address
        }else{
          var grs = app.util().baidutotencent(rel.data.near[0].longitude,rel.data.near[0].latitude)
          this.setData({
            'gps.longitude':grs.longitude,
            'gps.latitude':grs.latitude
          })
          this.data.iotTitle = rel.data.near[0].title
          this.data.iotAddress = rel.data.near[0].address
          this.includePoints()
        }
      }
    })
  },
  //扫码
  onScan(){
    wx.scanCode({
      onlyFromCamera: true,
      success:(rel) => {
        this.openBox(rel.result.split('?')[1]);
      },
      fail(){
        app.wxAlert('开门失败');
      }
    })
  },
  //打开快递柜
  openBox(aicode){
    if(app.util().isNull(aicode)){
      app.wxAlert('扫码失败,请重新扫码');
    }else{
      wx.showModal({
        content:'确认打开智能回收柜吗？',
        cancelText:'取消',confirmText	:'打开',
        success:(res) => {
          if (res.confirm) {
            this.data.aicode = parseInt(aicode);
            //this.udpbox()
          }
        }
      })
    }
  },
  //分享按钮
  onShareAppMessage: function () {
    return {
      path: '../index?ucode=' + app.globalData.loginuser.ucode
    }
  },
  //打开回收柜
  udpbox(){
    wx.showLoading({
      title: '正在开门'
    })
    var aicode = app.util().stringbty(4,this.data.aicode,['53','09']);
    var str    = app.util().stringbty(6,wx.getStorageSync('loginuser').uid,aicode);
        str.push('A1');  //开箱
        str.push(app.util().btyAdd(str)); 
    var sign = str.join('')
    var message = new Uint8Array(sign.match(/[\da-f]{2}/gi).map(function (h) {
        return parseInt(h, 16)
    })).buffer
    udpSocket.send({
      address: app.globalData.udp.ip,
      port: app.globalData.udp.port,
      message: message
    })
    this.setData({
      disabled:true
    })
    setTimeout(() => {
      if(this.data.disabled){
        this.setData({
          disabled:false
        })
        app.wxAlert('开门失败,请重新扫码')
      }
      wx.hideLoading()
    },6000);
  },
  //UDP端口监听
  initUdpSocket(){
    if(udpSocket === null){
      console.log('暂不支持')
      return ;
    }
    const port = udpSocket.bind()
    udpSocket.onListening((res)=>{
      console.log('监听#'+port)
    })
    udpSocket.onMessage((res) => {
      wx.hideLoading()
      var code = Array.prototype.map.call(new Uint8Array(res.message),x => ('00' + x.toString(16)).slice(-2)).join('');
      console.log(code);
      switch (code) {
        case '534f':
          wx.showLoading({
            title: '请投递'
          })
          setTimeout(() => {
            wx.hideLoading()
          },2000);
          break;
        case '5343':
          app.wxAlert('你已投递完毕',()=>{
            wx.navigateTo({
              url: 'user/bill'
            })
          })
          break;
        case '5331':
          app.wxAlert('投递箱已满')
          break;
        default:
          app.wxAlert("开门失败!可以手动打开投递")
        break;
      }
      this.setData({
        disabled:false
      })
    })
  },
  includePoints: function() {
    this.mapCtx.includePoints({
      padding: [0],
      points: [{
        latitude:this.data.gps.latitude,
        longitude:this.data.gps.longitude,
      }, {
        latitude:app.globalData.gps.latitude,
        longitude:app.globalData.gps.longitude,
      }]
    })
  },
  openLocation: function() {
    wx.openLocation({
      scale: 18,
      longitude: this.data.gps.longitude,
      latitude: this.data.gps.latitude,
      name:this.data.iotTitle,
      address:this.data.iotAddress 
    });
  }
})