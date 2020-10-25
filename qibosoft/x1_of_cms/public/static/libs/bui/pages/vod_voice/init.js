//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.vod_voice = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		$("#btn_vod_voice").click(function(){
			if(uid>=0){
				layer.alert('只有群聊才能音频点播!');
				return ;
			}
			var d_url = typeof(web_url)!='undefined'?'':'/';
			if( !in_pc ){
				bui.load({ 
					url: d_url+"public/static/libs/bui/pages/vod_voice/index.html",
					param:{
						aid:Math.abs(uid),
						type:'cms',
						mid:4,
					}
				});
			}else{
				layer.open({  
				  type: 2,    
				  title: '音频点播转直播',  
				  area: $('body').width()>800?['650px','600px']:['95%','80%'],  
				  content: "/index.php/cms/vod/index.html?type=voice&aid="+Math.abs(uid),
				});
			}			
		});		
	},
	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){
		this.check_play_data(res);
	},
	once:function(){
	},
	vurls:[],	//音频地址,很多地方要调用.
	haveLoadPlayer:false,
	check_play_data:function(res,type){  //检查数据中有没有可播放的数据
		if(type=='cknew'){
			return ;
		}
		if(typeof(res.ext)=='object' && typeof(res.ext.live)=='object' && typeof(res.ext.live.vod_voice)=='object'){			
			this.goplay(res.ext.live.vod_voice);	//打开播放器	
		}
	},
	goplay:function(obj,etype){
		if(this.haveLoadPlayer == true){
			try{
				this.win_player.vod.finish({});	//如果窗口已打开,就偿试先关闭,后面再重新打开
			} catch(err){
			}			
		}
		var oo;		//圈主的当前播放状态信息
		if(etype=='ok'||etype=='err'){	//请求完成
			var url_array = obj.play_urls;	//obj.play_urls有可能是新地址,也有可能还是老地址 this.vurls
			if(url_array==undefined || url_array.length<1){
				url_array = this.vurls;
			}
			if(etype=='ok'){	//请求成功
				oo = {index:obj.play_index,time:obj.play_time}
			}
		}else{	//请求播放信息
			this.vurls = obj.urls;	//存放起来,后来要用到
			ws_send({type:"user_ask_quner",tag:"ask_vod_voice_state"},'user_cid');	//请求圈主当前播放状态			
			return ; //先要执行请求,所以要终止下面的执行
		}
		this.haveLoadPlayer = true;
		if(in_pc){			
			this.pc_player(url_array,oo);
		}else{
			this.wap_player(url_array,oo);
		}

	},
	stop:function(){	//给播放器框架窗口调用的
		$.get("/index.php/cms/vod/stop_voice.html?aid="+Math.abs(uid),function(res){
			if(res.code==0){
				layer.msg('成功结束');
			}else{
				layer.alert(res.msg);
			}
		});
	},
	play_status:function(){	//主要是给播放器框架窗口调用的,暂时后,重新播放,同步圈主的当前播放信息
		ws_send({type:"user_ask_quner",tag:"ask_vod_voice_sync",user_cid:clientId,});
	},
	win_player:null,	//播放器所在的框架对象,通过这个来操作播放器里的函数
	wap_player:function(url_array,oo){	//WAP播放器
		var that = this;	//引用传递
		load_chat_iframe("/public/static/libs/bui/pages/vod_voice/player.html?aid="+Math.abs(uid)+"&cid="+clientId+"&"+Math.random(),function(win,body){
			that.win_player = win;	//得到iframe页的窗口对象，执行iframe页的方法：win.method();  
			win.voice_player(url_array,oo);		//播放器在上面那个框架网址那里
			if(my_uid==quninfo.uid){
				setTimeout(function(){	//等待播放器加载成功才有页面元素
					body.find('.syscn').show();
					body.find('.jp-previous').show();
					body.find('.jp-next').show();
					body.find('.jp-play').off('click');
					var h = body.find("body").height();
					$("#iframe_chat").height( my_uid==quninfo.uid?h+10:h );
					bui.init();	//重新调整页面高度
				},2000);
			}		
		});
	},
	pc_player:function(url_array,oo){	//PC播放器
		var that = this;	//引用传递
		if( typeof(in_pc_qun)=='boolean' && in_pc_qun==true ){
			load_chat_iframe("/public/static/libs/bui/pages/vod_voice/player.html?aid="+Math.abs(uid)+"&cid="+clientId+"&"+Math.random(),function(win,body){
				that.win_player = win;	//得到iframe页的窗口对象，执行iframe页的方法：win.method();  
				win.voice_player(url_array,oo);		//播放器在上面那个框架网址那里
				if(my_uid==quninfo.uid){
					setTimeout(function(){	//等待播放器加载成功才有页面元素
						body.find('.syscn').show();
						body.find('.jp-previous').show();
						body.find('.jp-next').show();
						body.find('.jp-play').off('click');
						var h = body.find("body").height();
						window.parent.$("#iframe_play").height( my_uid==quninfo.uid?h+10:h );
					},2000);
				}		
			});
		}else{
			layer.open({  
			  type: 2,    
			  title: '直播开始了...',  
			  fix: false,  
			  shadeClose: false,  
			  offset: ['10px', '10px'],
			  shade: 0,
			  maxmin: true,
			  scrollbar: false,
			  closeBtn:2,  
			  area: ['450px', my_uid==quninfo.uid?'390px':'345px'],  
			  content: "/public/static/libs/bui/pages/vod_voice/player.html?aid="+Math.abs(uid)+"&cid="+clientId,
			  success: function(layero, index){  
					var body = layer.getChildFrame('body', index);  //body.find('#dd').append('ff');    
					that.win_player = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();  
					that.win_player.voice_player(url_array,oo);	//播放器在上面那个框架网址那里
					//$('.layui-layer-min').trigger("click");
					//setTimeout(function(){
						//$('.layui-layer-max').trigger("click");
						//body.find(".player-button").hide();
					//},3000);	
					if(my_uid==quninfo.uid){
						setTimeout(function(){
							body.find('.syscn').show();
							body.find('.jp-previous').show();
							body.find('.jp-next').show();
							body.find('.jp-play').off('click');
						},2000);
					}
			  },
			  cancel:function(){
				that.win_player = null;
			  }
			});
		}
	}
}


//类接口,WebSocket下发消息的回调接口
ws_onmsg.vod_voice = function(obj){
	//CMS音频 访客请求圈主的音频播放状态,圈主进行反馈,圈主如有多个窗口最后一次登录窗口才收到,第一次窗口不会收到.
	//ask_vod_voice_state访客首次打开的请求, ask_vod_voice_sync打开以后,随时发起的请求
	if(obj.type=='ask_vod_voice_state'||obj.type=='ask_vod_voice_sync'){	//圈主才能收到这里的信息,会员不会执行这里的代码
		var arr ={};
		if(typeof(mod_class.vod_voice.win_player)=='object'){
			arr = {
				play_index:mod_class.vod_voice.win_player.get_now_state()[0],
				play_time:mod_class.vod_voice.win_player.get_now_state()[1],		
			};
		}
		arr.play_urls = mod_class.vod_voice.vurls;  //发给访问的地址,这里的地址可以随时更换
		var msgarray = {
			type: "quner_to_user", //群主发给指定会员的指令
			user_cid: obj.user_cid, //某个会员的ID标志
			//tag 接收标志, ask_vod_voice_sync 是会员后来随时请求圈主的播放状态, give_vod_voice_state是对应ask_vod_voice_state首次进来时的请求
			tag: obj.type=='ask_vod_voice_sync'?'give_vod_voice_sync':'give_vod_voice_state' ,
			data: arr ,			
		}
		ws_send(msgarray); //通知服务器,将上面的信息发给指定会员
	
	}else if(obj.type=='give_vod_voice_state'){	//此时用户的播放器处于未打开状态
		//通过服务器,收到上面发送的信息, 成功获取到直播信息,访客得到音频的播放状态
		//特别提醒.圈主中途开启,也是通过这个让所有用户打开播放器器,
		//\template\member_style\default\member\vod\index.htm模板中有个指令window.parent.w_s.send('{"type":"qun_to_alluser","tag":"give_vod_voice_state","data":' + JSON.stringify( arr ) + '}');
		//mod_class.vod_voice.vurls = obj.data.play_urls;
		mod_class.vod_voice.goplay(obj.data,'ok');		//加载播放器
	
	}else if(obj.type=='give_vod_voice_sync'){  //收到上面发送的同步指令,播放同步信息
		mod_class.vod_voice.win_player.vod.sync_play({index:obj.data.play_index,time:obj.data.play_time});
	
	}else if(obj.type=='error#ask_vod_voice_state'){	//CMS音频 圈主首次进入或者圈主不在,按默认的播放
		mod_class.vod_voice.goplay({play_urls:mod_class.vod_voice.vurls},'err');
	
	}else if(obj.type=='vod_voice_sync_play'){  //播放器器框架对象那里发过来的同步指令
		mod_class.vod_voice.win_player.control(obj.data);
	}
}


