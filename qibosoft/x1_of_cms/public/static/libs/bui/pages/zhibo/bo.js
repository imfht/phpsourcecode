// 默认已经定义了main模块
loader.define(function() {

    var pageview = {};
	//var is_live = false;
	var ali_type = 'new';   //new old
	var old_aliyunLive = null;		//阿里云老接口
	var new_alivcLivePusher = null; //阿里云新接口
	var live_urls;
	var chat_timer;

	var reconnect_start_num = 0;
	
	pageview.type = 1;  //1是竖屏自拍, 2 是横屏拍景 3 是只播声音

	pageview.only_sound = false;

    // 主要业务初始化
    pageview.init = function() {
		console.log("加载了视频直播模块");
    }
	
	pageview.stop = function(){
		router.$(".post_btn_wrap").hide();
		pageview.destroy_push();
		//mod_class.zhibo.zhibo_start = false;
		pageview.only_sound = false;
	}
	
	//菜单加事件
	pageview.add_btn_fun = function (){
		router.$(".btnmenu").click(function(){
				var span = $(this).find("span");
				if(span.hasClass("open")){
					span.removeClass("open").addClass("close");
					$(".post_btn").removeClass("open").addClass("close");
				}else{
					span.removeClass("close").addClass("open");
					$(".post_btn").removeClass("close").addClass("open");
				}
		});
		
		router.$(".post_btn_wrap .btn1").click(function(){
			var type = false;
			if( $(this).hasClass("is_choose") ){
				$(this).removeClass("is_choose");
			}else{
				type = true;
				$(this).addClass("is_choose");
			}
			change_show(type)
		});
		router.$(".post_btn_wrap .btn2").click(function(){
			var type = false;
			if( $(this).hasClass("is_choose") ){
				$(this).removeClass("is_choose");
			}else{
				type = true;
				$(this).addClass("is_choose");
			}
			change_sound(type)
		});
		router.$(".post_btn_wrap .btn3").click(function(){
			var type = false;
			if( $(this).hasClass("is_choose") ){
				$(this).removeClass("is_choose");
			}else{
				type = true;
				$(this).addClass("is_choose");
			}
			change_camera()
		});
		router.$(".post_btn_wrap .btn4").click(function(){
			var type = false;
			if( $(this).hasClass("is_choose") ){
				$(this).removeClass("is_choose");
			}else{
				type = true;
				$(this).addClass("is_choose");
			}
			//is_live = 0;
			//$('#live_video').removeClass('ck');
			
			mod_class.zhibo.stop(); //pageview.stop();

			//destroy_push();
			//router.$(".post_btn_wrap").hide();
		});
	}
	
	//显示菜单
	pageview.showbtn = function(){		
		router.$(".chat_mod_btn").hide();  //隐藏底部功能菜单
		router.$(".post_btn_wrap").show();	//直播菜单		
		//router.$(".btnmenu .btn3").show(); //恢复只推音频没有切换摄像头的功能		
		setTimeout(function(){
			router.$(".btnmenu").trigger("click"); //把菜单展开
		},1000);
	}

    pageview.start = function(data){
		pageview.only_sound = false;
		if(ali_type=='old'){
			if(old_aliyunLive==null){
				old_aliyunLive = api.require('aliyunLive');
			}
		}else{
			if(new_alivcLivePusher==null){
				new_alivcLivePusher = api.require('alivcLivePusher');
			}
		}
		//mod_class.zhibo.notify_selfsever(data.self_server_api);	//自建服务器的话要做通知开播处理
		//mod_class.zhibo.dataUrls = data; //这个是用来给圈主响应用户请求使用
		live_urls = {	//这个是全局变量,要提交到服务器用的
			flv_url:data.flv_url,
			m3u8_url:data.m3u8_url,
			rtmp_url:data.rtmp_url,
			push_url:data.push_url,
		};
		layer.confirm("你要选择横屏还是竖屏，自拍一般是竖屏，拍景一般是模屏",{
			btn:["自拍竖屏","拍景横屏","仅播声音"],
			btn1:function(index){	//自拍
				layer.close(index);
				pageview.type = 1;
				pageview.start_push();
			},
			btn2:function(index){	//拍景
				layer.close(index);
				pageview.type = 2;									
				pageview.start_push();								
			},
			btn3:function(index){	//只推声音
				layer.close(index);
				pageview.type = 3;
				pageview.only_sound = true;
				pageview.start_push();							
			},
		});
    }

	pageview.start_push = function (){
		if(pageview.type==3 && ali_type=='old'){
			alert('老版没此功能，请重新选择新版');
			return ;
		}
		if(ali_type=='old'){
			old_mk_live();	
		}else{
			new_mk_live();
		}
	}

	
	//生成预览窗口
	function new_live_preview(){

		//var _cameraPosition='front';	//默认是自拍
		//var _screenOrientation = 'vertical';
		var _rect = {
				x:api.winWidth-150-5, //（可选项）数字类型；模块左上角的 x 坐标（相对于所属的 Window 或 Frame）；默认值：0
				y: 50, //（可选项）数字类型；模块左上角的 y 坐标（相对于所属的 Window 或 Frame）；默认值：0
				w: 150, //(可选项)数字类型;模块宽度（相对于所属的 Window 或 Frame;默认300
				h: 250 //(可选项)数字类型;模块高度（相对于所属的 Window 或 Frame;默认300
			};

		if(pageview.type==2){		//拍景
			//var _cameraPosition='back';
			//_screenOrientation = 'horizontal';
			_rect = {
				x:api.winHeight-250-5, //（可选项）数字类型；模块左上角的 x 坐标（相对于所属的 Window 或 Frame）；默认值：0
				y: 5, //（可选项）数字类型；模块左上角的 y 坐标（相对于所属的 Window 或 Frame）；默认值：0
				w: 250, //(可选项)数字类型;模块宽度（相对于所属的 Window 或 Frame;默认300
				h: 150, //(可选项)数字类型;模块高度（相对于所属的 Window 或 Frame;默认300
			};
		}


		var params = {
			/*
				rect: {
					x:api.winWidth-150-5, //（可选项）数字类型；模块左上角的 x 坐标（相对于所属的 Window 或 Frame）；默认值：0
					y: 50, //（可选项）数字类型；模块左上角的 y 坐标（相对于所属的 Window 或 Frame）；默认值：0
					w: 150, //(可选项)数字类型;模块宽度（相对于所属的 Window 或 Frame;默认300
					h: 250 //(可选项)数字类型;模块高度（相对于所属的 Window 或 Frame;默认300
				},*/
				rect:_rect,
				fixedOn: api.frameName,
				fixed: false,
		}	
		new_alivcLivePusher.startPreview(params,function(ret){
			if(ret.status==true){
				mod_class.zhibo.zhibo_status = true;	//为的是标志这个不要打开直播窗口
				var index = layer.msg('初始化预览成功，正在启动推流，请稍候....',{time:800});
				setTimeout(function(){
					//live_urls.push_url='rtmp://qqpush.soyixia.net/live/id315?txSecret=e7bfe8af01d931497ae15b17dc8a77c6&txTime=5DFD4525'
					new_alivcLivePusher.startPush({
						url: live_urls.push_url ,
					},function(ret){
						if(ret.status==true){	//成功启动推流，是否真正推流成功，还不确定的。
							listener(index);														
						}else{
							alert("推流启动失败,"+JSON.stringify(ret));
							pageview.destroy_push();
						}
					});
				},500);
			}else{
				alert("预览失败,"+JSON.stringify(ret));
			}
		})
	}

	//新接口,有的手机打不开
	function new_mk_live(){
		new_alivcLivePusher.initPusher({
			resolution:'540P',
			initialVideoBitrate:'800(Kbps)',
			targetVideoBitrate:'800(Kbps)',
			minVideoBitrate:'400(Kbps)',
			qualityMode:'FluencyFirst',
			cameraType:pageview.type==2?'back':'front',  // back front  front自拍 back拍景
			audioOnly:pageview.type==3?true:false,  //是否只是音频,不要视频 
			previewOrientation:pageview.type==2?'LANDSCAPE_HOME_RIGHT':'PORTRAIT', //PORTRAIT LANDSCAPE_HOME_RIGHT LANDSCAPE_HOME_LEFT 摄像头方向
		},function(ret){
			if(ret.status==true){
				new_live_preview();
			}else{
				alert("初始化失败,"+JSON.stringify(ret));
			}		
		});
	}

	
	//老接口,兼容性好
	function old_mk_live(){/*
		api.setKeepScreenOn({
			keepOn: true
		})
		var _cameraPosition='front';
		var _screenOrientation = 'vertical';
		var _rect = {
				x:api.winWidth-150-5, //（可选项）数字类型；模块左上角的 x 坐标（相对于所属的 Window 或 Frame）；默认值：0
				y: 50, //（可选项）数字类型；模块左上角的 y 坐标（相对于所属的 Window 或 Frame）；默认值：0
				w: 150, //(可选项)数字类型;模块宽度（相对于所属的 Window 或 Frame;默认300
				h: 250 //(可选项)数字类型;模块高度（相对于所属的 Window 或 Frame;默认300
			};

		if(pageview.type==2){
			var _cameraPosition='back';
			_screenOrientation = 'horizontal';
			_rect = {
				x:api.winHeight-250-5, //（可选项）数字类型；模块左上角的 x 坐标（相对于所属的 Window 或 Frame）；默认值：0
				y: 5, //（可选项）数字类型；模块左上角的 y 坐标（相对于所属的 Window 或 Frame）；默认值：0
				w: 250, //(可选项)数字类型;模块宽度（相对于所属的 Window 或 Frame;默认300
				h: 150, //(可选项)数字类型;模块高度（相对于所属的 Window 或 Frame;默认300
			};
		}
		
		//console.log("99999999999999999999");
		console.log( live_urls.push_url);
		old_aliyunLive.configStream({
			rect: _rect,
			url: live_urls.push_url,
			bitRate: {
				  videoMaxBitRate: 1500 * 1000,     
				  videoMinBitRate: 400 * 1000,       
				  videoBitRate: 600 * 1000,          
				  audioBitRate: 64 * 1000         
			},
			fps: 20,
			screenOrientation: _screenOrientation, //horizontal vertical
			reconnectTimeout: 5,
			videoResolution: '720P', 
			videoPreset: '1280*720',
			cameraPosition: _cameraPosition,	//front back 默认开启前置还是后置摄像头 
			waterMarkImage: {
				path: '',               
				location: 'leftTop',                
				maginX: 20,      
				maginY: 20               
			}, 
			fixed: true,
			//fixedOn:'videoview',
		},function(ret) {
			if(ret.status==true){				
				setTimeout(function(){
					old_aliyunLive.startStream(function(ret){
						if(ret.status==true){
							success();
						}else{
							alert("推流失败,请关闭再重新打开,"+JSON.stringify(ret));
							//router.$(".post_btn_wrap .btn4").trigger("click");
						}
					});
				},2000);
			}else{
				alert("初始化失败,"+JSON.stringify(ret));
			}
		});
		*/
	}
	
	//监听推流状态
	function listener(index){
		new_alivcLivePusher.addEventListener(function(ret){
			if(ret.eventType=='reconnectFail'){	//只有真正成功过推流，才会执行的, 一开始就推流不成功，这里不会执行
				layer.alert("推流中断了",{time:9000,offset:'b'});
				mod_class.zhibo.repeat_connect();
			}else if(ret.eventType=='reconnectStart'){
				reconnect_start_num++;
			}
			console.log("推流状态码:"+JSON.stringify(ret));
		});

		new_alivcLivePusher.getLiveStatus(function(ret){								
			layer.close(index);
			//alert("确认开始？"+(ret.livePushStatus==4?'':ret.livePushStatus));
			if(ret.livePushStatus==4){	//推流连接中	，是否真正推流成功，还不确定的
				index = layer.msg('正努力连接推流服务器，请稍候....');
				setTimeout(function(){
					layer.close(index);
					if(reconnect_start_num>1){
						layer.alert('推流异常，请检查网络或推流地址是否正常！',{time:9000,offset:'b'});						
						pageview.destroy_push();
						reconnect_start_num = 0;
					}else{
						layer.msg('直播推流成功了!'+reconnect_start_num);
						success();
						if(pageview.type==2){	//拍景横屏的话，要扭转屏幕
							api.setScreenOrientation({
									orientation: 'landscape_left',	//landscape_left auto
							});
						}
						if(mod_class.zhibo.connect_timer!=null){	//这里处理圈主直播断线重连, 只有真正成功过推流，才会执行的
							layer.msg('重连成功');
							clearInterval( mod_class.zhibo.connect_timer );
							mod_class.zhibo.connect_timer = null;
						}
					}					
				},4000);				
			}else if(ret.livePushStatus==11){
				layer.alert("推流失败",{time:9000,offset:'b'});
				pageview.destroy_push();
			}else{
				layer.alert("推流状态码："+JSON.stringify(ret),{time:9000,offset:'b'});
				pageview.destroy_push();
			}							
		});
	}

	function success(){
		mod_class.zhibo.success_push();		
		if(ali_type=='old'){
			return ;
		}		
	}
	
	//切换前后摄像头
	function change_camera(){
		if(ali_type=='old'){
			old_aliyunLive.toggleCamera();	
		}else{
			new_alivcLivePusher.switchCamera();
		}
	}

	//打开关闭预览镜像 (必须在预览或者推流成功后才能调用)
	function change_show(type){
		new_alivcLivePusher.setPreviewMirror({
			isMirror:type==true?true:false,
		});
	}

	//静音
	function change_sound(type){
		if(ali_type=='old'){
			old_aliyunLive.setMute({
				mute: type==true?'on':'off',
			});	
		}else{
			new_alivcLivePusher.setMute({
				isMute:type==true?true:false,
			});
		}
	}

	//闪光灯
	function change_light(type){
		new_alivcLivePusher.setFlash({
			isFlash:type==true?true:false,
		});
	}
	
	//停止推流
	function stopPush(){
		 new_alivcLivePusher.stopPush();
	}
	
	//销毁直播 
	pageview.destroy_push = function(){
		//stopPush();
		if(ali_type=='old'){
			old_aliyunLive.destroyStream();			
		}else{
			new_alivcLivePusher.destroy();
		}
		api.setScreenOrientation({
			orientation: 'auto'
		});		
	}

	
	/*
	function postMsg(){
		
		mod_class.zhibo.zhibo_status = true;
		layer.msg('直播推流开始了!!');
		postmsg('<div class="live_video_start">APP端开始直播了...</div>');	//发送数据到服务器		
		pageview.showbtn();  //推流成功,才显示菜单
		if(pageview.only_sound==true){
			router.$(".post_btn_wrap .btn3").hide(); //没有切换摄像头的功能
		}else{
			router.$(".btnmenu .btn3").show(); //恢复只推音频没有切换摄像头的功能	
		}

		setTimeout(function(){				
			if(ali_type=='old'){
				old_mk_live();	
			}else{
				new_mk_live();
			}
		},3000);		
		
		if(typeof(chat_timer)!='undefined')clearInterval(chat_timer);
		new_alivcLivePusher.addEventListener(function(ret){
			
			if(ret.eventType=='reconnectFail'){
				layer.alert("推流中断了");
				destroy_push();
				chat_timer = setInterval(function() {
					if(ali_type=='old'){
						old_mk_live();	
					}else{
						new_mk_live();
					}
				},3000);	//断流后,每3秒刷新一次重连
				
			}
			//console.log("状态码:"+JSON.stringify(ret));
		});
		
	}
	*/
    
    return pageview;
})

