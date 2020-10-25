if(typeof(Pay)=='undefined'){

var Pay = function () {
	
	var pc_pay_callback;
	//PC中使用支付
	var pcpay = function(money,title,callback){
		pc_pay_callback = callback;
		if( typeof(title)!='string' ){
			title = '充值';
		}
		var return_url = encodeURIComponent(window.location.href);
		layer.open({
			type: 2,
			shadeClose: true,
			shade: 0.3,
			title: false,
			scrollbar: false,
			area: ['600px', '500px'],
			content: '/index.php/index/pay/index.html?money='+money+'&title='+title+'&return_url='+return_url,
		});
		return ;
	}
	
	//在手机中支付
	var mobpay = function(money,title,callback){
		var payurl = '/index.php/index/pay/index.html?money='+money+'&title='+title+'&return_url='+encodeURIComponent(window.location.href);
		if(typeof(wx)=='object'){	//在微信中
			weixin_pay(money,title,callback);
		}else if(typeof(api)=='object'){	//在仿原生APP中 Qibo.app_pay_end('ok')
			if(typeof(Qibo)=='undefined'){	//没考虑框架的情况
				Qibo = {};
			}
			Qibo.callback = callback;
			Qibo.app_pay_end = function(type){	//定义支付成功后的回调函数
				api.closeFrame({
					name: 'payiframe'
				});
				if( typeof(Qibo.callback)=='function' ){
					Qibo.callback(type);
				}
			}
			if(payurl.indexOf('://')==-1){
				if(typeof(parent.web_url)!='undefined'){
					web_url = parent.web_url;
				}
				payurl = web_url + payurl;
			}
			api.openFrame({
					name: 'payiframe',
					url: payurl,
					reload: true,
					rect: {
						x: 0,
						y: 0,
						w: api.winWidth,
						h: api.winHeight,
					},
					bounces: false
			}); 
		}else{
			var payurl = '/index.php/index/pay/index.html?money='+money+'&title='+title+'&return_url='+encodeURIComponent(window.location.href);
			if( typeof(bui)=='object' ){	//在BUI单页中支付
				bui.load({ 
					url: "/public/static/libs/bui/pages/frame/show.html",
					param:{
						url:payurl,
					}
				});
			}else{
				window.location.href = payurl;
			}
		}
	}
	
	var in_iframe = false;
	var in_wxapp = false;
	//微信公众号支付, 小程序的话,会跳转一下,不包APP支付
	var weixin_pay = function(money,title,callback){
		if(typeof(wx)=='undefined'){
			var return_url = encodeURIComponent(window.location.href);
			alert('不在微信公众号里,无法唤起微信支付! 即将偿试支付宝支付!');
			window.location.href = '/index.php/index/pay/index.html?banktype=alipay&money='+money+'&title='+title+'&return_url='+return_url;
			return ;
		}		
		var wxpay = {};		
		money = parseFloat(money).toFixed(2);
		if(isNaN(money)){
			money = 0.3;
		}
		if( typeof(title)!='string' ){
			title = '充值';
		}

		if(in_wxapp==true){	//在小程序中
			if(in_iframe==true){
				var return_url = encodeURIComponent(parent.window.location.href);
				parent.window.location.href = '/index.php/index/pay/index.html?banktype=weixin&client_type=wxapp&money='+money+'&title='+title+'&return_url='+return_url;
			}else{
				var return_url = encodeURIComponent(window.location.href);
				window.location.href = '/index.php/index/pay/index.html?banktype=weixin&client_type=wxapp&money='+money+'&title='+title+'&return_url='+return_url;
			}			
			return ;
		}

		$.get('/index.php/index/wxapp.pay/index.html?type=mp&title='+title+'&money=' + money + '&' + Math.random(),function(res){
			if(res.code==0){
				wxpay = eval("("+res.data.json+")");
				if (typeof WeixinJSBridge == "undefined"){
					if( document.addEventListener ){
						document.addEventListener('WeixinJSBridgeReady', function(){jsApiCall(callback)}, false);
					}else if (document.attachEvent){
						document.attachEvent('WeixinJSBridgeReady', function(){jsApiCall(callback)}); 
						document.attachEvent('onWeixinJSBridgeReady', function(){jsApiCall(callback)});
					}
				}else{
					jsApiCall(callback);
				}
			}else{
				layer.alert(res.msg);
			}
		});
		function jsApiCall(callback){
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				wxpay,
				function(res){
					WeixinJSBridge.log(res.err_msg);
					//alert(res.err_code+res.err_desc+res.err_msg);
					var type = 'err';
					var layer_index = ''
					if(res.err_msg=='get_brand_wcpay_request:ok'){
						layer_index = layer.msg('充值成功!');
						type = 'ok';
					}else if(res.err_msg=='get_brand_wcpay_request:cancel'){
						layer_index = layer.msg('支付失败!');
					}
					if(typeof(callback)=='function'){
						callback(type,layer_index);
					}
				}
			);
		}
	}
	return {
		init:function(){
			$(function(){
				if(typeof(parent.api)=='object'){
					in_iframe=true;
					api = parent.api;
				}
				if(typeof(parent.Qibo)=='object'){
					in_iframe=true;
					Qibo = parent.Qibo;
				}				
				if(typeof(parent.bui)=='object'){
					in_iframe=true;
					bui = parent.bui;
				}
				if(typeof(parent.wx)=='object'){
					in_iframe=true;
					wx = parent.wx;
				}
				if(typeof(parent.WeixinJSBridge)=='object'){
					WeixinJSBridge = parent.WeixinJSBridge;
				}
				if(typeof(wx)=='object'){
					wx.miniProgram.getEnv(function(res) {
						if(res.miniprogram==true){
							in_wxapp = true;
						}
					});
				}
			});
		},
		if_wxapp:function(){
			return in_wxapp;
		},
		wxpay:function(money,title,callback){
			weixin_pay(money,title,callback);
		},
		pcpay:function(money,title,callback){
			pcpay(money,title,callback);
		},
		mobpay:function(money,title,callback){
			mobpay(money,title,callback);
		},
		pc_callback:function(type){
			if(typeof(type)!='undefined' && typeof(pc_pay_callback)=='function'){
				if(type=='ok'){
					var index = layer.msg('充值成功');
				}else{
					var index = layer.msg('充值失败');
				}				
				pc_pay_callback(type,index);
			}
			return true;
		}
	};

}();



$(document).ready(function(){
	Pay.init();
});

}