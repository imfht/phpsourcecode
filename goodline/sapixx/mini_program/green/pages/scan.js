const app = getApp()
const udpSocket = wx.createUDPSocket()
Page({
 /**
  * 页面的初始数据
  */
  data: {
    disabled:false,
    islogin: false,
    gps:{
      longitude:113.81908,//113.324520,
      latitude:36.08308   //23.099994
    },
    markers: [
      {id: 0,longitude:113.82208,latitude:36.08408},
    ]
  },
  //监听页面加载
  onLoad: function (options) {
    //UDP端口
    this.initUdpSocket();
    //定位
    app.getLocation((rel)=>{
      if(rel){
        this.setData({
          gps:app.globalData.gps
        })
      }
    });
    //获取扫码参数
    app.getParam(options,(param) => {
      if (!app.util().isNull(param.q)){
        var url = decodeURIComponent(param.q);
        this.openBox(url.split('?')[1]);
      }
    })
  },
  //监听页面显示
  onShow:function (){
    app.isLogin((rel)=>{
      this.setData({
        islogin:true
      })
    }) 
  },
  //监听页面初次渲染完成
  onReady: function () {
    this.getApi()
  },
  //请求设备经纬度
  getApi(){
    var param = {
      signkey:app.util().getRandom(12)
    }
    app.api().Get('api/v1/green/index/map',param,(rel) =>{
      if (rel.code == 200) {
        this.setData({
          markers: app.util().bdtotx(rel.data),
        })
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
            this.udpbox()
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
    const port = udpSocket.bind()
    udpSocket.onListening((res)=>{
      console.log('监听#'+port)
    })
    udpSocket.onMessage((res) => {
      var code = Array.prototype.map.call(new Uint8Array(res.message),x => ('00' + x.toString(16)).slice(-2)).join('');
      switch (code) {
        case '534f':
          wx.showToast({title:'小主~请投递',icon: 'success'})
          break;
        case '5343':
          wx.wxAlert('小主~投递完毕',()=>{
            wx.navigateTo({
              url: 'user/bill'
            })
          })
          break;
        case '5331':
          wx.showToast({title:'小主~爱心箱已满,欢迎下次光临'})
          break;
        default:
          wx.showToast({title:'小主~请自行开箱'})
        break;
      }
      wx.hideLoading()
      this.setData({
        disabled:false
      })
    })
  }
})