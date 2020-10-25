//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.wx_share = {
	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		
		if(uid>=0){	//只有圈子才有分享功能
			return ;
		}

		var title = quninfo.title!=''?quninfo.title:'欢迎加入圈子群聊';
		var about = quninfo.content!=''?quninfo.content.replace('&nbsp;',''):'欢迎加入圈子群聊,不错过每一个精彩的直播!';
		if( typeof(res.ext)=='object' && typeof(res.ext.live)=='object' && typeof(res.ext.live.live_video)=='object' ){
			title = res.ext.live.live_video.title;
			about = res.ext.live.live_video.about;
		}
		var info = {
				url:(typeof(web_url)!='undefined'?web_url:'')+"/index.php/index/msg/index.html?uid="+uid,
				picurl:quninfo.picurl!=''?quninfo.picurl:'',
				title:title,
				about:about,
		}
		if(typeof(web_url)!='undefined'){	//APP中使用的
			var dialog;			
			var d_url = typeof(api)=='object'?'':'/';
			loader.import(d_url+"public/static/libs/bui/pages/wx_share/btn.html?",function(res){
				router.$("footer").after(res);
				dialog = bui.dialog({
					id: "#actionsheet",
					position:"bottom",
					effect:"fadeInUp",
					onMask: function (argument) {
						$('.chatbar').show();
						$('.chat_mod_btn').hide();					
						dialog.close();
					}
				});	
				
				router.$("#share_friend").click(function(){
					info.type = 'user';
					weixin_share(info);
				});
				router.$("#share_timeline").click(function(){
					info.type = 'timeline';
					weixin_share(info);
				});
				router.$("#share_fav").click(function(){
					info.type = 'fav';
					weixin_share(info);
				});
			});

			router.$("#btn_wx_share").click(function(){
				$('.chatbar').hide();
				dialog.open();
			});
		
		}else if(typeof(wx)=='object' && have_load_wx_config==true && uid<0){	//微信上访问
			weixin_share(info);
			wx.miniProgram.getEnv(function (res) { 
				if (res.miniprogram==true) { 
					var json = JSON.stringify(info); 
					wx.miniProgram.postMessage({ 
						data: info, 
					}); 
				} 
			}); 
		}
	},
	once:function(res){	//只加载一次
	},
	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){ //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},
}


//页面加载到数据的接口
load_data.wx_share = function(res,type){
}

//页面数据渲染完毕后执行的接口
format_content.wx_share = function(res,type){
	if(type!='cknew'&&typeof(api)=='undefined'){	//不是翻页		
	}else if(type!='cknew'&&typeof(api)=='object'){
	}
}