//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.menu = {
	vid:0,
	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		if(in_pc==true||uid>0){
			return ;
		}
		var that = this;
		var str = `<li data-type="chat" class="bui-btn active">互动</li>
				<li class="bui-btn" data-type="about">介绍</li>
				<li class="bui-btn" data-sys="cms" data-mid="3">往期</li>
				<li class="bui-btn" data-sys="bbs" data-mid="1">话题</li>
				`+((location.href.indexOf('//qb.net')>-1||location.href.indexOf('//x1.php168.com')>-1)?`<li class="bui-btn" data-sys="appstore" data-mid="0">应用</li>`:`<li class="bui-btn" data-sys="shop" data-mid="1">商品</li>`)+`
				<li class="bui-btn" data-type="qun">关注</li>`;
		var oo = router.$("#choose_model .bui-nav");
		oo.html(str);
		oo.find(".bui-btn").click(function(){
			oo.find(".bui-btn").removeClass("active");
			$(this).addClass("active");
			choose( $(this) );			
		});
		
		function choose(obj){
			router.$("#more_content").height( router.$("#chat_main").height() );
			type = obj.data('type');
			var id = Math.abs(uid);
			var q_uid = quninfo.uid;
			if(type=='chat'){
				window.in_chat = true;
				router.$("footer").show();
				router.$("#chat_win").show();
				router.$("#more_content").hide();
				router.$('#chat_win').parent().scrollTop(20000);
			}else{
				window.in_chat = false;
				router.$("footer").hide();
				router.$("#chat_win").hide();

				router.$("#more_content").show();
				layer.msg('内容加载中,请稍候...',{time:500,offset:'b'});
				if(type=='about'){
					router.$("#iframe_more").attr("src","/index.php/index/msg/index.html?"+Math.random()+"#/public/static/libs/bui/pages/menu/about/index.html?id="+id+"&vid="+that.vid);
				}else if(type=='qun'){
					bui.load({ 
						url: "/public/static/libs/bui/pages/frame/show.html",
						param:{
							url:"/index.php/qun/show-"+id+".html",
						}
					});
				}else{
					router.$("#iframe_more").attr("src","/index.php/index/msg/index.html?"+Math.random()+"#/public/static/libs/bui/pages/menu/common/index?sys="+obj.data('sys')+"&mid="+obj.data('mid')+"&q_uid="+q_uid);
				}
			}
		}
	},

	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){  //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},

}


//类接口,加载到聊天会话数据时执行的  刷新数据的时候也会有到.不仅仅是初次加载
load_data.menu = function(res,type){
	if(in_pc==true||uid>0){
		return ;
	}
	if(type=='cknew'){	//刷新到数据就不需要了,因为WS有另外传数据过来
		return ;
	}
	if( typeof(res.ext)=='object' && typeof(res.ext.live)=='object' && typeof(res.ext.live.live_video)=='object' ){		//直播中
		mod_class.menu.vid = res.ext.live.live_video.id;
	}else if(typeof(res.ext)=='object' && typeof(res.ext.live)=='object' && typeof(res.ext.live.vod_mv)=='object'){	//点播中
		mod_class.menu.vid = res.ext.live.vod_mv.id;
	}else{
		$.get("/index.php/p/alilive-api-get_cms_video_info.html?aid="+quninfo.id,function(res){
			if(res.code==0){
				var time = res.data._start_time;
				if(time*1000<(new Date()).getTime()){
					return ;
				}
				mod_class.menu.vid = res.data.id;				
				load_chat_iframe("/public/static/libs/bui/pages/menu/zhibo_prepare/index.html",function(win,body){
					$(".iframe_chat").height(200);
					win.init(res.data);
				});
			}
		});
	}
}