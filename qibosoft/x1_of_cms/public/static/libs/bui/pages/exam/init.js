//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.exam = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		if(in_pc==true){
			$('#btn_exam').click(function(){
				if(uid>0){
					layer.alert('只能在群聊使用');
					return ;
				}else if(my_uid!=quninfo.uid){
					layer.alert('圈主才能使用');
					return ;
				}
				var s = {
						type: 2,
						shadeClose: true,
						shade: 0.3,
						area: ['800px', '650px'],
						content: '/index.php/exam/vod/index.html?aid='+Math.abs(uid),
				};
				if(window.parent.frames['iframe_msg']!=undefined){	//在圈子页打开
					window.parent.layer.open(s);
				}else{
					layer.open(s);
				}
				
			});
		}else{
			router.$("#btn_exam").click(function(){
				if(uid>0){
					layer.alert('只能在群聊使用');
					return ;
				}else if(my_uid!=quninfo.uid){
					layer.alert('圈主才能使用');
					return ;
				}
				bui.load({ 
					url: "/public/static/libs/bui/pages/exam/index.html",
					param:{
						aid:Math.abs(uid),
						uid:quninfo.uid,
					}
				});
				router.$(".hack_wrap").hide();	
			});
		}
	},
	check_data:function(res,type){  //检查数据中有没有试卷数据
		if(type=='cknew'){
			return ;
		}
		if(typeof(res.ext)=='object' && typeof(res.ext.live)=='object' && typeof(res.ext.live.exam)=='object'){
			this.pid = res.ext.live.exam.id;	//试卷ID
			
			if(my_uid==quninfo.uid){
				this.goplay(res.ext.live.exam);	//打开试题
			}else{
				$.get("/index.php/exam/wxapp.paper/get_mark.html?aid="+Math.abs(uid)+"&id="+this.pid,function(res){
					if(res.code==0){
						console.log('答过题了');
					}else{
						this.goplay(res.ext.live.exam);	//打开试题
					}
				});
			}
		}
	},
	infos:{},
	haveLoadPlayer:false,
	goplay:function(obj,etype){
		if(this.haveLoadPlayer == true){
			return ;
		}
		var ext_info = {};		//圈主的当前状态信息
		if(etype=='ok'||etype=='err'){	//请求完成
			var info = obj.info;	//obj.info有可能是新地址,也有可能还是老地址 this.infos
			if(info==undefined){
				info = this.infos;
			}
			if(etype=='ok'){	//请求成功
				ext_info = {}
			}
		}else{	//请求播放信息
			this.infos = obj;	//存放起来,后来要用到
			ws_send({type:"user_ask_quner",tag:"ask_exam_state"},'user_cid');	//请求圈主当前状态
			return ; //先要执行请求,所以要终止下面的执行
		}
		this.haveLoadPlayer = true;
		this.openwin(info,ext_info);
	},
	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){  //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		this.check_data(res);
	},
	winer:null,	//框架对象,通过这个来操作播放器里的函数
	openwin:function(info,ext_info){	//打开试卷
		var that = this;	//引用传递
		var s = {  
			  type: 2,    
			  title: '考试开始了...',  
			  fix: false,  
			  shadeClose: false,  
			  //offset: ['10px', '10px'],
			  shade: 0,
			  maxmin: false,
			  scrollbar: false,
			  closeBtn:2,  
			  area: (in_pc?['500px','600px']:['95%','85%']),  
			  content: "/index.php/exam/category/index/fid/" + info.id + ".html?aid="+Math.abs(uid),
			  success: function(layero, index){  
					var body = layer.getChildFrame('body', index);  //body.find('#dd').append('ff');    
					that.winer = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();  
					//that.winer.mv_player(info,ext_info,'300px');	//播放器在上面那个框架网址那里
					//$('.layui-layer-min').trigger("click");
					//setTimeout(function(){
						//$('.layui-layer-max').trigger("click");
						//body.find(".player-button").hide();
					//},3000);
			  },
			  cancel:function(){
				that.winer = null;
				if(my_uid==quninfo.uid){
					ifstop()
				}
			  },
			  full: function() {
			  }
		};
		if(window.parent.frames['iframe_msg']!=undefined){	//在圈子页打开
			if(window.parent.exam_index!=undefined){	//避免重复打开
				window.parent.layer.close(window.parent.exam_index);
			}
			window.parent.exam_index = window.parent.layer.open(s);
		}else{
			layer.open(s);
		}
		function ifstop(){
			layer.confirm('是否将试卷撤消',{btn:['撤消','不撤消']},function(index){
				layer.close(index);
				$.get("/index.php/exam/wxapp.paper/setlive.html?type=delete&aid="+Math.abs(uid)+"&id="+that.pid,function(res){
					if(res.code==0){
						layer.msg('撤消成功');
					}else{
						layer.alert("取消失败,"+res.msg);
					}
				});
			})
		}
	},
	pid:0,//试卷ID
	open_question:function(info){	//打开试题
		var that = this;	//引用传递
		var s = {  
			  type: 2,    
			  title: '答题开始了...',  
			  fix: false,  
			  shadeClose: false,  
			  //offset: ['10px', '10px'],
			  shade: 0,
			  maxmin: false,
			  scrollbar: false,
			  closeBtn:2,  
			  area: (in_pc?['500px','600px']:['95%','85%']),  
			  content: "/index.php/exam/content/show/id/" + info.id + ".html?type=live&aid="+Math.abs(uid),
			  success: function(layero, index){  
					var body = layer.getChildFrame('body', index);  //body.find('#dd').append('ff');    
					that.winer = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();
			  },
		};		
		if(window.parent.frames['iframe_msg']!=undefined){	//在圈子页打开
			window.parent.layer.open(s);
		}else{
			layer.open(s);
		}
	},
}



//对聊天内容进行重新转义显示
format_content.exam = function(res,type){
	if(in_pc==true){
	}else{
		router.$(".chat-panel .model-list").each(function(){
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
			$(this).click(function(){
				bui.load({ 
					url: "/public/static/libs/bui/pages/frame/show.html",
					param:{
						url:url,
						title:title,
					}
				});
			});
		});
	}
}


//类接口,WebSocket下发消息的回调接口
ws_onmsg.exam = function(obj){
	//会员请求圈主的试卷信息,圈主进行反馈,圈主如有多个窗口最后一次登录窗口才收到,第一次窗口不会收到.
	//ask_exam_state访客首次打开的请求, ask_exam_sync打开以后,随时发起的请求
	if(obj.type=='ask_exam_state'||obj.type=='ask_exam_sync'){	//圈主才能收到这里的信息,会员不会执行这里的代码
		var arr = {};
		if(typeof(mod_class.exam.winer)=='object'){
			arr = {
				//play_index:mod_class.exam.winer.get_now_state()[0],
				//play_time:mod_class.exam.winer.get_now_state()[1],		
			};
		}
		arr.info = mod_class.exam.infos;  //发给访问的数据,这里可以随时更换
		var msgarray = {
			type: "quner_to_user", //群主发给指定会员的指令
			user_cid: obj.user_cid, //某个会员的ID标志
			//tag 接收标志, ask_exam_sync 是会员后来随时请求圈主当前进入哪道题, give_exam_state是对应ask_exam_state首次进来时的请求
			tag: obj.type=='ask_exam_sync'?'give_exam_sync':'give_exam_state' ,
			data: arr ,			
		}
		ws_send(msgarray); //通知服务器,将上面的信息发给指定会员
	
	}else if(obj.type=='give_exam_state'){	//此时用户未打开试题
		//通过服务器,收到上面发送的信息, 成功获取到试题信息,访客得到试题状态
		//特别提醒.圈主中途开启,也是通过这个让所有用户打开试题
		//\template\member_style\default\member\vod\index.htm模板中有个指令window.parent.w_s.send('{"type":"qun_to_alluser","tag":"give_exam_state","data":' + JSON.stringify( arr ) + '}');
		//mod_class.exam.vurls = obj.data.play_urls;
		mod_class.exam.goplay(obj.data,'ok');		//弹窗显示试题
		if(mod_class.exam.pid==0){
			mod_class.exam.pid = obj.data.info.id;
		}

	}else if(obj.type=='give_question_state'){	//通知打开试题 

		mod_class.exam.open_question(obj.data.info);

	}else if(obj.type=='give_question_result'){	//用户答题 , 来自此文件提交的数据\template\index_style\default\exam\content\show.htm
		
		var str = obj.data;
		if (in_pc == true) {
            $(".pc_show_all_msg").prepend(str);
            goto_bottom(500)
        } else {
            $("#chat_win").prepend(str);
            $('#chat_win').parent().scrollTop(20000);
        }

	}else if(obj.type=='give_exam_sync'){  //收到上面发送的同步指令,播放同步信息
		//mod_class.exam.winer.vod.sync_play({index:obj.data.play_index,time:obj.data.play_time});
	
	}else if(obj.type=='error#ask_exam_state'){	// 圈主首次进入或者圈主不在,按默认的打开
		mod_class.exam.goplay({play_urls:mod_class.exam.infos},'err');
	
	}else if(obj.type=='exam_sync_play'){  //框架对象那里发过来的同步指令
		//mod_class.exam.winer.control(obj.data);
	}
}