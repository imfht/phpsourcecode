//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.p2pvideo = {
	win_player:null,
	accept_video:function(cid){
		this.open_win(cid);
	},
	is_invite:false,	//发布视频者
	open_win:function(cid){
		var that = this;
		var d_url = typeof(web_url)=='undefined' ? '/' : '';
		layer.open({
						type: 2,
						title:'视频电话',
						//shadeClose: true,
						shade: 0,
						maxmin: true,
						shadeClose: false,  
						area: (in_pc?['500px', '350px']:['90%', '60%']),
						content: d_url+'public/static/libs/bui/pages/p2pvideo/index.html',
						success: function(layero, index){  
							//var body = layer.getChildFrame('body', index);  //body.find('#dd').append('ff');    
							$(".layui-layer-iframe ").removeClass('layer-anim'); //避免视频不能全屏显示
							that.win_player = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：win.method();  
							that.win_player.palyer(cid);			
					  }
		});
	},
	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		var that = this;
		$('#btn_p2pvideo').click(function(){
			that.open_win();
		});
		return ;

		if(in_pc==true){/* */
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
	},

}



//对聊天内容进行重新转义显示
format_content.p2pvideo = function(res,type){
}

//类接口,WebSocket下发消息的回调接口
ws_onmsg.p2pvideo = function(obj){
	if(obj.type=='ask_video_phone'){	//视频通话请求,目前是群发,方便其它扩展.
		if(obj.data.uid==my_uid){ //判断是自己的视频通话,就提示要不要接收.
			if(mod_class.p2pvideo.is_invite == true){	//自己发起视频,自己收到,就不提示
				return ;
			}
			if(my_uid==2568){	//指定客服用户,自动接通视频通话
				layer.closeAll();
				layer.msg('调试自动开启');
				mod_class.p2pvideo.accept_video(obj.data.cid);
				return ;
			}
			var user = obj.data.user;
			var index = layer.confirm(user.username+"请求与你视频通话，请选择",{
						btn:["接受","回绝"],
						cancel:function(index, layero){
							ans(false,obj.data.cid);
						},
						btn1:function(){
							ans(true,obj.data.cid);
							layer.close(index);
							mod_class.p2pvideo.accept_video(obj.data.cid);
						},
						btn2:function(){
							ans(false,obj.data.cid);
							layer.close(index);						
						},
				});
		}
	}else if(obj.type=='video_phone_ask_reply'){	//收到被呼叫方的回应
		if(obj.data.accept){
			layer.alert('对方已接受,请耐心等候接通!');
		}else{
			layer.alert('对方拒绝了你的请求通话!');
		}
	}

	function ans(if_accept,cid){
		var msgarray = {
			type: "quner_to_user", //发给指定会员的标志
			user_cid: cid, //某个会员的ID标志
			tag: 'video_phone_ask_reply',		//tag 接收标志,不同插件,不能相同,避免雷同冲突
			//特别要注意:更多参数,只能通过data传递
			data: {			
				accept: if_accept ,
			},			
		}
		ws_send(msgarray); //通知服务器,将上面的信息发给指定会员
	}
}