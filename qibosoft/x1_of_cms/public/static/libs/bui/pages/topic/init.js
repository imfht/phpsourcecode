//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.topic = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要

		if(in_pc==true){
			$('#btn_topic').click(function(){
				var st = {
						type: 2,
						shadeClose: true,
						shade: 0.3,
						title: '站内引用',
						area: ['800px', '650px'],
						content: '/member.php/member/quote/index.html?type=bbs&uid='+uid,
				};
				if(window.parent.frames['iframe_msg']!=undefined){	//在圈子页打开
					window.parent.layer.open(st);
				}else{
					layer.open(st);
				}
			});
		}else{
			router.$("#btn_topic").click(function(){
				bui.load({ 
					url: "/public/static/libs/bui/pages/topic/index.html",
					param:{
						type:$(this).data("type"),
						uid:uid,
					}
				});
				router.$(".hack_wrap").hide();	
			});
		}
	},

	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){  //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		this.check_data(res);
	},
	check_data:function(res,type){  //检查数据中有没有推荐数据
		if(type=='cknew'){
			return ;
		}
		if(typeof(res.ext)=='object' && typeof(res.ext.live)=='object' && typeof(res.ext.live.vod_topic)=='object'){
			this.check_open(res.ext.live.vod_topic.id,res.ext.live.vod_topic.sys);
		}
	},
	check_open:function(id,sys){
		var that = this;
		if(my_uid<1 || my_uid==quninfo.uid){
			that.open(id,sys);
		}else{
			$.get("/member.php/member/quote/get.html?aid="+Math.abs(uid),function(res){
				if(res.code==0){
					that.open(id,sys);
				}
			});
		}		
	},
	open:function(id,sys,islink){
		var that = this;	//引用传递
		var s = {  
			  type: 2,    
			  title: false,  
			  fix: false,  
			  shadeClose: false,  
			  //offset: ['10px', '10px'],
			  shade: 0,
			  maxmin: false,
			  scrollbar: false,
			  closeBtn:2,  
			  area: (in_pc?['1250px','85%']:['95%','85%']),  
			  content: "/index.php/" + sys + "/content/show/id/" + id + ".html",
			  success: function(layero, index){  
					//var body = layer.getChildFrame('body', index);  //body.find('#dd').append('ff');    
					//that.winer = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();  
			  },
			  cancel:function(){
				 if(islink!=true && my_uid==quninfo.uid){
					ifstop();
				 }
			  },
			  full: function() {
			  }
		};		
		if((window.parent.frames['iframe_msg']!=undefined&&window.parent.$("body").width()>1250) || (window.$("body").width()<1250&&window.parent.$("body").width()>1250)){	//在圈子页打开
			if(window.parent.topic_index!=undefined){	//避免重复打开
				window.parent.layer.close(window.parent.exam_index);
			}
			window.parent.topic_index = window.parent.layer.open(s);			
		}else if(window.parent.frames['iframe_msg']==undefined){
			layer.open(s);
		}

		function ifstop(){
			layer.confirm('是否撤消对新来的用户弹窗显示',{btn:['撤消','不撤消']},function(index){
				layer.close(index);
				$.get("/member.php/member/quote/live.html?type=delete&aid="+Math.abs(uid),function(res){
					if(res.code==0){
						layer.msg('撤消成功');
					}else{
						layer.alert("取消失败,"+res.msg);
					}
				});
			})
		}
	}

}



//对聊天内容进行重新转义显示
format_content.topic = function(res,type){
	if(in_pc==true){
		$("body").append('<link rel="stylesheet" href="/public/static/libs/bui/pages/topic/style.css" />');
		$(".model-list").click(function(){
			var type = $(this).data("type");
			var id = $(this).data("id");
			mod_class.topic.open(id,type,true);
		});
	}else{
		router.$(".chat-panel .model-list").click(function(){
			var type = $(this).data("type");
			var imgurl = $(this).data("imgurl");
			var id = $(this).data("id");			
			if(imgurl!=""){
				$(this).find(".model-more").css({"height":"60px"});				
			}else{
				$(this).find(".model-content").css({"margin-right":"2px"});
			}
			var url = "/index.php/" + type + "/content/show/id/" + id + ".html";
			var title = $(this).find(".model-title").html().substring(0,14) + "...";
			
			bui.load({ 
					url: "/public/static/libs/bui/pages/frame/show.html",
					param:{
						url:url,
						title:title,
					}
			});
			
		});
	}
}


//类接口,WebSocket下发消息的回调接口
ws_onmsg.topic = function(obj){
	if(obj.type=='give_topic_state'){
		mod_class.topic.open(obj.data.ext_id,obj.data.ext_sys);
	}
}