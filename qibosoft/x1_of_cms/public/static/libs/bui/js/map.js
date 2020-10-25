if(typeof(window.map_x)=='undefined'){


jQuery.getScript("/public/static/js/map-gps.js").done(function() {			
}).fail(function() {
	layer.msg('public/static/js/map-gps.js加载失败',{time:800});
});



function get_gps_location(callback){
	if(typeof(window.map_x)!='undefined'){
		callback(window.map_x,window.map_y);
		return ;
	}
	if(typeof(have_load_wx_config)!="undefined"){
		get_map_location(callback);
	}else{
		jQuery.getScript("/public/static/js/jweixin.js").done(function() {
			get_map_location(callback,'need_confg');
		}).fail(function() {
			layer.msg('jweixin.js加载失败',{time:800});
		});
	}
}

function get_map_location(callback,type){
	var is_wxapp = false;
	if(typeof(wx)=='undefined'){
		get_bd_map_location(callback);
		return ;
	}
	wx.miniProgram.getEnv(function(res) {
		if(res.miniprogram==true){
			is_wxapp = true;
			if(type=='need_confg'){
				load_wx_config(callback);
			}else{
				get_wx_map_location(callback)
			}			
		}else{			
		}
	});
	setTimeout(function(){
		if(is_wxapp==false) get_bd_map_location(callback);
	},300);	
}

function get_bd_map_location(callback){
	window.HOST_TYPE = "2";
	window.BMap_loadScriptTime = (new Date).getTime();

	jQuery.getScript("/public/static/js/bdmap.js").done(function() {
	}).fail(function() {
		layer.msg('public/static/js/bdmap.js加载失败',{time:800});
	});

	jQuery.getScript("https://api.map.baidu.com/getscript?v=2.0&ak=MGdbmO6pP5Eg1hiPhpYB0IVd&services=&t=20190622163250").done(function() {
		var geolocation = new BMap.Geolocation();
		geolocation.getCurrentPosition(function(result){
			if(this.getStatus() == window.BMAP_STATUS_SUCCESS){
			  window.map_x = result.point.lng;
			  window.map_y = result.point.lat;
			  callback(window.map_x,window.map_y);
				//var geoc = new BMap.Geocoder();
				//geoc.getLocation(result.point, function(rs){
				//	var addComp = rs.addressComponents;
				//	alert(addComp.district + addComp.street + addComp.streetNumber);
				//});

				//gg = GPS.bd_decrypt(result.point.lat, result.point.lng);	//百度转谷歌
				//wgs = GPS.gcj_decrypt(gg.lat, gg.lon); //谷歌转GPS
				//showMapPosition(wgs.lon,wgs.lat);
			} else {
				alert('failed:'+this.getStatus());
			}        
		},{enableHighAccuracy: true})
	}).fail(function() {
		layer.msg('api.map.baidu.com/getscript加载失败',{time:800});
	});
}

function load_wx_config(callback){
	var url = window.location.href;
	if(typeof(from_main)!="undefined" && !!navigator.userAgent.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/)==false ){
		url = url.substring(0,url.indexOf('#'))+'#main';	//安卓手机,这里要特别处理
	}
	$.get("/index.php/index/wxapp.weixin/getconfig.html?url="+encodeURIComponent(url),function(res){
		if(res.code!=0){
			layer.msg('微信配置文件加载失败');
			return ;
		}
		wx.config({
			debug: false,
			appId: res.data.appId,
			timestamp: res.data.timestamp,
			nonceStr: res.data.nonceStr,
			signature: res.data.signature,
			jsApiList: [
				'openLocation',
				'getLocation'
			  ]
		});

		wx.ready(function () {
			get_wx_map_location(callback);
		});

		wx.error(function (res) {
		   alert(res.errMsg);
		});
		
	});
}

function get_wx_map_location(callback){
	wx.getLocation({
				type: 'gcj02', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'' 
				success: function (res) {
				  var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
				  var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。

				   //alert(res.latitude+','+res.longitude);
					var obj = GPS.bd_encrypt(res.latitude,res.longitude);
				   //alert(obj.lat+','+obj.lon );

				   window.map_x = obj.lon;//res.longitude;
				   window.map_y = obj.lat;//res.latitude;
				  callback(window.map_x,window.map_y);
			  },
			  cancel: function (res) {
				alert('用户拒绝授权获取地理位置');
			  }
			});
}


}