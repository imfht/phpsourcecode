/**
 * 聊天对话模板
 */

/*****这个IF语句里边的代码是APP专用***/
if(typeof(web_url)!='undefined'){
var ReWrite={};	//重写JQ与BUI的一些方法

ReWrite.get = $.get;
$.get = function(url,callback){
	url = Qibo.check_url(url);
	return  ReWrite.get(url,callback);//$.post(url,{},callback);	
}

ReWrite.post = $.post;
$.post = function(url,obj,callback){
	url = Qibo.check_url(url);
	return  ReWrite.post(url,obj,callback);
}

ReWrite.buiload = bui.load;
bui.load = function(obj){
	if(typeof(obj.url)!='undefined' && (obj.url).indexOf('/public/static/')==0 ){
		obj.url = obj.url.substring(1);		
	}
	ReWrite.buiload(obj);
}

ReWrite.loader = loader.import;
loader.import = function(arr,callback){
	var o = typeof(arr)=='string' ? [arr] : arr;
	console.log(o);
	o.forEach((url,i)=>{
		if( url.indexOf('/public/static/')==0 ){
			o[i] = url.substring(1);
		}
	});
	console.log(o);
	ReWrite.loader(o,callback);
}

ReWrite.layeropen = layer.open;
layer.open = function(obj){
	if(obj.type==2){
		obj.content = Qibo.check_url(obj.content);
	}
	ReWrite.layeropen(obj);
}

}
/*****上面的IF语句代码是APP专用***/

layer.closeAll = function(){console.log('不允许使用把全部层一次关闭,不然会影响到其它插件');};

var refresh_i,refresh_timenum;//这几个已弃用
var w_s,ws_url,clientId='';

//WebSocket下发消息的回调接口,当前页面可以这样使用 ws_onmsg.xxxx=function(o){} 子窗口可以这样使用 parent.ws_onmsg.xxxx=function(o){}
var ws_onmsg = {};
var load_data = {};	//接口
var first_page_data;	//初始数据,给后加载的框架使用
var in_pc = false;
var format_content ={}; //接口
var format_content_have_run ={};

//加载聊天窗口框架
function load_chat_iframe(url,callback){

	if(url==''){	//关闭窗口
		router.$("#iframe_chat").attr("src","about:blank");
		router.$(".iframe_chat").hide(500);
		setTimeout(function(){
			bui.init();
		},2000);
		return ;
	}
	
	//这里绕个弯是解决JQ的BUG.不然重复执行的话,下面的load方法会执行多次.
	var obj = $("#iframe_chat").parent();
	var str = $("#iframe_chat")[0].outerHTML;
	$("#iframe_chat").remove();	
	obj.append(str);

	if(typeof(api)=='object' && url.indexOf('/public/static/')==0 ){
		url = url.substring(1);		
	}

	$("#iframe_chat").attr("src",url);
	$(".header_content").show();
	$(".chat_iframe_div").show();
	$("#iframe_chat").show();
	$("#iframe_chat").load(function(){
		var body = $(this).contents();	//body.find("body").html(); 获取页面元素
		var win = $("#iframe_chat")[0].contentWindow;	//win.test() 执行页面方法		
		if(typeof(callback)=='function'){
			callback(win,body);
		}
		var b_height = body.find("body").height();
		if(b_height>0){
			$("#iframe_chat").height( body.find("body").height() );
		}		
		bui.init();
	});
}

//显示超长内容
function get_longmsg(id){
	ws_send({type:'user_ask_longmsg',msgid:id});
}


var chat_timer,connect_handle,qun_link_handle;

function ws_connect(){
	if(typeof(w_s)=='object'){	//强制重新建立新的连接
		console.log("#########被强制断开了,重新发起连接!!!!!!!!!!"+Math.random());
		$("#remind_online").hide();
		if(w_s.readyState==1||w_s.readyState==0){	//0是连接中,1是已连接上
			w_s.close();
		}
		if(typeof(connect_handle)!='undefined'){
			clearInterval(connect_handle); //避免重复发起定时器,而导致重复连接,把之前的连接任务全清掉
		}
		connect_handle = setInterval(function(){	//w_s.close()执行后,并不能马上关闭链接的
			if(w_s.readyState==3){	//0是连接中,1是已连接上,2是断开中,3是已完全断开
				clearInterval(connect_handle);
				ws_link();
			}
		}, 2000 );
	}else{
		ws_link();
	}
}

//发送WebSocket信息
function ws_send(o,getcid){
	//if(getcid=='user_cid'){
		//console.log('uuid========='+w_s.readyState);
	//}
	if(typeof(w_s)=='undefined' || w_s.readyState==0){ //解决还没开始连接就请求发送的情况. 0正处于连接状态,还没连接成功 2断开中, 3完全断开了
		wait_connect(o,getcid);
	}else if(w_s.readyState==1){	//处于正常通信中	
		//console.log('uuid----发出+++++-'+get_msg(o,getcid));
		w_s.send( get_msg(o,getcid) );
	}else{
		ws_connect(); //已断开,重新发起一次链接
		var index = layer.msg('连接已断开,正在重新发起链接,请稍候...');
		wait_connect(o,getcid);
	}
	function wait_connect(o,getcid){
		var name = 'require_senmsg';
		if(typeof(o)=='object'){	//尽量避免出现雷同冲突
			if(typeof(o.tag)=='string' && typeof(o.type)=='string'){
				name = o.type + '__' + o.tag;
			}else if(typeof(o.tag)=='string'){
				name = o.tag;
			}else if(typeof(o.type)=='string'){
				name = o.type;
			}
		}
		ws_onmsg[name] = function(obj){	//收到消息时候的回调
			ws_onmsg[name] = null;		//这一行不能缺少,不然会进入死循环			
			setTimeout(function(){	//避免跟注册信息同时发送
				//console.log('uuid----收到+++++-'+get_msg(o,getcid));
				w_s.send( get_msg(o,getcid) );
			}, 100*Math.ceil(Math.random()*10) );
		}
	}
	function get_msg(o,getcid){
		if(typeof(getcid)=='string' && typeof(o)=='object'){
			o[getcid] = clientId;
		}
		return typeof(o)=='object' ? JSON.stringify( o ) : o;
	}
}

function ws_link(){
	w_s = new WebSocket(ws_url);

	w_s.onmessage = function(e){
			var obj = {};
			try {
				obj = JSON.parse(e.data);
			}catch(err){
				console.log(err);
			}
			//console.log('收到消息'+obj.type);
			//当前页面可以这样使用 ws_onmsg.xxxx=function(o){} 子窗口可以这样使用 parent.ws_onmsg.xxxx=function(o){}
			for(var index in ws_onmsg){				
				if(typeof(ws_onmsg[index])=='function'){
					//console.log('uuid----方法名'+index+'*******---'+e.data);
					ws_onmsg[index](obj);
				}				
			}
	};
		
	w_s.onopen = function(e) {	//w_s.readyState CONNECTING: 0 OPEN: 1 CLOSING: 2 CLOSED: 3
		setTimeout(function() {
			if(clientId==''){
				console.log('clientId获取失败,WebSocket连接不顺畅',w_s.readyState);
				if(w_s.readyState==1||w_s.readyState==0){	////0是连接中,1是已连接上
					w_s.close();
				}
				ws_connect();
			}else{
				console.log('WebSocket成功连接上了 '+clientId,w_s.readyState);
			}		
		}, 1500 );
	};
	w_s.onerror = function(e){
		console.log("#########连接异常中断了.........."+Math.random(),e.code + ' ' + e.reason + ' ' + e.wasClean);
	};
	w_s.onclose = function(e){
		console.log("########连接被关闭了.........."+Math.random(),e.code + ' ' + e.reason + ' ' + e.wasClean);
	};
		
	if(typeof(chat_timer)!='undefined')clearInterval(chat_timer);
	chat_timer = setInterval(function() {
		if(w_s.readyState!=1){
			ws_connect();
		}else{
			w_s.send('{"type":"refresh"}');
		}			
	}, 1000*50);	//50秒发送一次心跳
}


//发送消息
function postmsg(content,callback){
		if(typeof(content)!='object' && content==''){
			layer.alert('消息内容不能为空');
			return ;
		}

		var push_id = (Math.random()+'').substring(2);
		var content_obj;
		if(typeof(content)=='object'){
			content_obj = content;				
		}else{
			content_obj = {
				'content':content,
			}
		}

		content_obj.push_id = push_id;
		content_obj.ext_id = msg_id;
		content_obj.ext_sys = msg_sys;

		$(".chatInput").val('');
		$(".chat_mod_btn").hide();
		$(".face_wrap").hide();
		
		//同步即时消息,不入库同步
		ws_send({
			type:'qun_sync_msg',
			data:content_obj
		});
		
		content_obj.uid = uid;
		content_obj.send_to = typeof(touser)=='object'?touser.uid:0;
		content_obj.my_uid = my_uid;
		
		//入库处理
		$.post("/member.php/member/wxapp.msg/add.html",content_obj,function(res){
			if(res.code==0){
				//layer.msg('发送成功');
			}else{
				//router.$("#btnSend").removeClass("disabled").addClass("primary");
				layer.alert('本条信息已发出,但并没有入库,原因:'+res.msg);
			}
			if(typeof(callback)=='function'){
				callback(res);
			}
		});
}

var msg_sys,msg_id;  //关联频道主题
var uid,my_uid,quninfo = {};
var qun_userinfo = '';	//当前用户所在当前圈子的信息

var mod_class={},iframe_body_class={};
var is_repeat = 0;
var userinfo = {};	//当前登录用户的基础信息



loader.define(function(require,exports,module) {

    var pageview = {};
	var qid;
	var msg_scroll = true;
	var show_msg_page  = 1;
	var maxid = -1;
	var getShowMsgUrl;
	var need_scroll = false;
	var touser = {uid:0};	//@TA	
	var user_list = {}; //圈子用户列表
	var user_num = 0; //圈子成员总数
	var uiSidebar;          // 侧边栏
	var pushIdArray = [];
	var online_members = []; //所有在线用户
	var roll_user_obj = null;

	ws_onmsg.chat = function(obj){

		var show_online = function(obj,type){
			var total = obj.total; //在线窗口,同一个人可能有多个窗口				 
			var data = obj.data;
			//online_members = data;
			//var usernum = obj.data.length;  //在线会员人数,已注册的会员
			var ol_obj = $('#remind_online');
			if(type=='show'){
				view_online_user(data);
			}else if(total>1){
				if(type=='goin'){
					bui.hint ( {content:"有新用户："+data[0].username+" 进来了",position:'top'} );

					//统计最近来访的用户开始
					online_members.forEach(function(rs,i){
						if(rs.username==data[0].username){
							online_members.splice(i,1);
						}						
					});					
					online_members.push(data[0]);
					if(online_members.length>1){
						var str = '';
						for(var i=online_members.length-1;i>=0;i--){
							str += '<a data-uid="'+(online_members[i].uid>0?online_members[i].uid:0)+'">'+online_members[i].username + (i==0?'</a>':'</a>、');
						}						
						if(roll_user_obj==null){
							loader.import(["/public/static/libs/swiper/jquery.liMarquee.js","/public/static/libs/swiper/jquery.liMarquee.css"],function(){
								setTimeout(function(){
									ol_obj.css('position','relative');
									ol_obj.find('.welcome-user').css('margin','0 10px');
									ol_obj.find('.welcome-user').html('<i class="fa fa-bullhorn"> 欢迎：</i>'+str);
									roll_user_obj = ol_obj.liMarquee({loop:-1,direction:'left',scrollamount:30,circular:true});
								},2500);
							});							
						}else{
							roll_user_obj.liMarquee('destroy');
							ol_obj.find('.welcome-user').html('<i class="fa fa-bullhorn"> 欢迎：</i>'+str);
							roll_user_obj.liMarquee('update');
						}
					}				
					//统计最近来访的用户结束

				}else if(type=='getout'){
					bui.hint ( {content:obj.msg,position:'top'} );
				}
				router.$(".header_content").show();ol_obj.show();bui.init();
				if(uid>0){
					ol_obj.find(".online").html('对方在线,请不要离开!');
				}else{
					ol_obj.find(".online").html('共有 '+total+' 个访客,会员有 '+obj.total_login+' 人! 查看详情');
					ol_obj.off('click');
					ol_obj.click(function(){
						layer.msg('请稍候,正在拉取数据!',{time:500});
						ws_send({type:'get_online_user',});
						//view_online_user(data);
					});
				}
			}else if( !ol_obj.is(':hidden') ){
				if(uid>0){
					bui.hint ( {content:'对方已离开!',position:'top'} );
				}else{
					bui.hint ( {content:'人全走光了!'+obj.msg,position:'top'} );
				}
				ol_obj.hide();bui.init();
			}
		}

		var view_online_user = function(data){
			var str = '';
			data.forEach((rs)=>{
				str += '<a href="/member.php/home/'+rs.uid+'.html" target="_blank">'+rs.username+'</a>、';
			});
			layer.open({
					type: 1,
					anim: 5,
					shade: 0.1,
					title: '仅列出已注册的会员，不含游客',
					shadeClose: true,
					offset:"b",
					area: ['100%', '45%'],
					content: '<div style="padding:20px;line-height:180%;">'+str+'</div>',
			});
		}
		
		if(obj.type=='newmsg'){
			check_new_showmsg(obj);	//推数据
			$.get("/index.php/index/wxapp.msg/update_user.html?uid="+uid+"&id="+obj.data[0].id,function(res){//更新记录
				console.log(res.msg);
			});	
			//console.log("有新消息来了");
			//console.log(obj);
		}else if(obj.type=='new_msg_id'){	//圈子直播文字最后得到的真实ID
			pushIdArray[obj.data.push_id] = obj.data.id; //删除内容的时候要用到
			$.get("/index.php/index/wxapp.msg/update_user.html?uid="+uid+"&id="+obj.data.id,function(res){//更新记录
				console.log(res.msg);
			});
		}else if(obj.type=='qun_sync_msg'){	//圈子直播文字  
			check_new_showmsg(obj);
		}else if(obj.type=='delete_msg'){	//删除或撤回消息
			$("#chat_win .chat-box-"+obj.data.id).hide();
		}else if(obj.type=='connect'){	//建立链接时得到客户的ID
			clientId = obj.client_id;
			if(uid==0){
				return ;
			}				
			$.get("/index.php/index/wxapp.msg/bind_group.html?uid="+uid+"&client_id="+obj.client_id,function(res){	//绑定用户
				if(res.code==0){
					//layer.msg('欢迎到来!',{time:500});
				}else{
					layer.alert(res.msg);
				}
			});
			var username = my_uid>0?userinfo.username:'';
			var icon = my_uid>0?userinfo.icon:'';
			var is_quner = my_uid==quninfo.uid ? 1 : 0;	//圈主
			w_s.send('{"type":"connect","url":"'+(typeof(web_url)!='undefined'?web_url:window.location.href)+'","uid":"'+uid+'","my_uid":"'+my_uid+'","is_quner":"'+is_quner+'","userAgent":"'+navigator.userAgent+'","my_username":"'+username+'","my_icon":"'+icon+'"}');
		}else if(obj.type=='count'){  //用户连接成功后,算出当前在线数据统计
			 show_online(obj,'goin');
		}else if(obj.type=='leave'){	//某个用户离开了
			show_online(obj,'getout')
			console.log(obj);
		}else if(obj.type=='give_online_user'){  //服务器给出在线用户数据
			show_online(obj,'show')
		}		
	}

	//加载到第一页成功后,就获得了相关数据,才好进行其它的操作
	function load_first_page(res){
		if_load = true;
		maxid = res.ext.maxid;

		quninfo = res.ext.qun_info;	//圈子信息
		window.store.set("quninfo",quninfo);
		//vues.set_quninfo(quninfo);
		router.$("#send_user_name").html(quninfo.title);
		if(uid<0)$('title').html(quninfo.title);	//设置圈子名称为title

		qun_userinfo = res.ext.qun_userinfo;	//当前圈子用户信息 不存的话,就是为空即==''
		userinfo = res.ext.userinfo;	//当前用户登录信息
		//head_menu(uid,quninfo,qun_userinfo,userinfo);

		my_uid = typeof(userinfo)=='object'?userinfo.uid:0;
		
		ws_url = res.ext.ws_url;
		

		//建立链接 延时执行,避免用户反复切换圈子
		if(typeof(qun_link_handle)!='undefined'){
			clearTimeout(qun_link_handle);
		}
		qun_link_handle = setTimeout(function(){
			ws_connect();
		},typeof(w_s)=='object'?5000:0);

		
		//first_page_data = res; //这个要放在load_data下面
		
		set_chatmod_btn(res);
		if(!is_repeat){
			set_chatmod_file(res);	//只允许执行一次 , 更换加载不同的圈子,不会重复再执行
		}else{	//页面初次加载的时候,mod_class还没有全部加载完毕,所以这里不执行,放在set_chatmod执行
			for(var index in mod_class){
				if(typeof(mod_class[index].init)!='undefined'){
					mod_class[index].init(res);			//SPA单页模式,需要重新渲染界面与绑定元素事件 ,PC端多页的话,就不要执行
				}
				if(typeof(mod_class[index].logic_init)!='undefined'){
					mod_class[index].logic_init(res);	//logic_init()方法或函数,每次加载不同的圈子,都会执行
				}
			}
			//初次加载不能批量执行,JS文件模块可以这样使用 load_data.xxxx=function(o){} 框架网页可以这样使用 parent.load_data.xxxx=function(o){}
			for(var index in load_data){
				load_data[index](res);
			}
		}
		is_repeat++;
	}


	//设置模块的功能菜单,SPA单页模式的话,换不同的圈子又会重新加载的，这里是会多次重复加载的。
	function set_chatmod_btn(res){
		var arr = res.ext.chatmod;
		var btn_str = '',btnStr = '';
		var j=0;
		arr.forEach((rs)=>{
			if(rs.icon!=''){
				btn_str += `<li class="span1"><span id="btn_${rs.keywords}" class="set-chatmod-btn"><i class="${rs.icon}"></i>${rs.name}</span></li>`;
				j++;
				if(j%5==0){
					btnStr += `<ul class="bui-box bui-box-align-center">${btn_str}</ul>`;
					btn_str = '';
				}
			}
		});
		while (btn_str!='' && j%5!=0){
			btn_str += '<li class="span1"></li>';
			j++;
		}
		if(btn_str!=''){
			btnStr += `<ul class="bui-box bui-box-align-center">${btn_str}</ul>`;
		}
		$(".chat_mod_btn").html(btnStr);		
	}
	
	//当前页面不刷新的话,只执行一次,禁止更换不同的圈子又重新执行加载JS文件
	function set_chatmod_file(res){
		var arr = res.ext.chatmod;
		var total_need_load = 0,total_have_load = 0,iframe_str = '';
		arr.forEach((rs)=>{			
			if(rs.init_iframe!=''){
				total_need_load++;
				iframe_str += `<iframe style="display:none;" class="chat_iframe_hack" data-keyword="${rs.keywords}" name="iframe_${rs.keywords}" id="iframe_${rs.keywords}" src="${rs.init_iframe}"></iframe>`;
			}
			if(rs.init_jsfile!=''){
				if(typeof(api)=='object'){
					rs.init_jsfile = rs.init_jsfile.substring(1);
				}
				total_need_load++;
				loader.require(rs.init_jsfile,function (o) {
					fisrt_load(res,rs.keywords);	//首次加载的时候,单独执行
					total_have_load++;					
					if(total_have_load>=total_need_load){
						run_mod_finsih(res)
					}
				});
				/*
				jQuery.getScript(rs.init_jsfile).done(function() {
					fisrt_load(res,rs.keywords);	//首次加载的时候,单独执行
					total_have_load++;					
					if(total_have_load>=total_need_load){
						run_mod_finsih(res)
					}
				}).fail(function(e) {
					total_have_load++;
					console.log("此文件加载失败或者是代码有错误!"+rs.init_jsfile);
				});*/
			}
			if(rs.init_jscode!=''){
				eval(rs.init_jscode);
			}
		});

		$("body").append(iframe_str);

		//框架插件接口
		$(".chat_iframe_hack").each(function(){
			var k = $(this).data('keyword');
			$(this).load(function(){			
				iframe_body_class[k] = $(this).contents();	//body.find("body").html(); 获取页面元素
				mod_class[k] = $(this)[0].contentWindow;	//win.test() 执行页面方法
				//mod_class[k].init(res);
				fisrt_load(res,k);	//首次加载的时候,单独执行
				total_have_load++;
				if(total_have_load>=total_need_load){
					run_mod_finsih(res)
				}
			});
		});
		
		//首次加载的时候,单独执行
		function fisrt_load(res,keywords){
			if(typeof(mod_class[keywords])=='object'){
				if( typeof(mod_class[keywords].init)=='function' ){
					mod_class[keywords].init(res);
				}
				if( typeof(mod_class[keywords].logic_init)=='function' ){
					mod_class[keywords].logic_init(res);
				}
				if( typeof(mod_class[keywords].once)=='function' ){
					mod_class[keywords].once(res);
				}				
			}
			if( typeof(load_data[keywords])=='function' ){
				load_data[keywords](res);
			}
			if(typeof(format_content[keywords])=='function' && format_content_have_run[keywords]!=true){
				format_content[keywords](res);
			}
		}

		//所有模块加载完毕后,检查有没有需要执行的回调方法
		function run_mod_finsih(res){
			for(var index in mod_class){
				if(typeof(mod_class[index].finish)!='undefined'){
					mod_class[index].finish(res);
				}
			}
		}
	}



    // 模块初始化定义
    pageview.init = function () {

		if(window.self!=window.top && (window.parent.location.href).indexOf('/msg/index.html')>0){	//避免重复框架
			window.parent.location.href = window.location.href;
		}

		//router.$("#headbody").css({'top':router.$("#chat_head").height()+'px;',});

		//处理软键盘破坏了界面布局，进行修复处理
		router.$(".chatInput").blur(function(){
			setTimeout(function(){
				bui.init();
			},600);
		});

        this.bind();
		this.right_btn();
		
		/*
		setTimeout(function(){
			uiSidebar = bui.sidebar({
				id      : "#sidebar",
				handle: ".page-chat",
				width   : 550
			});
		},1500);*/

		router.$("#chat_win").parent().scroll(function () {
			if(window.in_chat!=undefined && window.in_chat!==true){	//切换到非聊天窗口的时候,就上禁用滚动事件
				return ;
			} 
			var h = router.$("#chat_win").parent().scrollTop();			
			if(h<100){
				if(msg_scroll==true){
					msg_scroll = false;
					showMoreMsg(uid);
				}		
			}
		});

		if(typeof(to_uid)!='undefined' && to_uid!=""){	//详情页或发送页
			uid = to_uid;
			set_user_name(uid);	//设置当前会话的用户名
			showMoreMsg(uid);	//加载相应用户的聊天记录
			router.$(".right-tongji").on("click",function(e){
			  bui.load({ url: "/member.php/member/msg/index.html" ,reload:true});
			});
			router.$(".bui-bar-left a").removeClass('btn-back');
			router.$(".bui-bar-left i").removeClass('icon-back');
			router.$(".bui-bar-left i").addClass('fa fa-home');
			router.$(".bui-bar-left a").on("click",function(e){
			  bui.load({ url: "/",reload:true});
			});
		}else{
			router.$(".bui-bar-right a").on("click",function(e){
				clearInterval(chat_timer);
				//bui.back();
			});
			router.$(".btn-back").on("click",function(e){
				clearInterval(chat_timer);
			  //bui.back();
			});			
		}

		if(uid<0){
			setTimeout(function(){
				$.get("/index.php/qun/wxapp.visit/check_visit/id/"+(-uid)+".html",function(res){});	//更新圈子浏览日志
			},2000);
		}

    }

	pageview.right_btn = function () {
        // 初始化下拉更多操作
        var uiDropdownMore = bui.dropdown({
          id: "#right_more",
          showArrow: true,
          width: 165
        });

        // 下拉菜单有遮罩的情况
        var uiMask = bui.mask({
          appendTo:"#chat_win",
          opacity:"0.3",
          zIndex:1,
          callback: function (argument) {
            // 隐藏下拉菜单
            uiDropdownMore.hide();
          }
        });

        // 通过监听事件绑定
        uiDropdownMore.on("show",function () {
			uiMask.show();
        })
        uiDropdownMore.on("hide",function () {
			uiMask.hide();
        });
	}

    pageview.bind = function () {

		router.$("#show_hack").on("click",function () {
			router.$(".face_wrap").hide();
			if(router.$(".chat_mod_btn").is(":hidden")){
				router.$(".chat_mod_btn").show();
			}else{
				router.$(".chat_mod_btn").hide();
			}
        })

            // 发送的内容
        var $chatInput = router.$(".chatInput"),
            // 发送按钮
            $btnSend = router.$("#btnSend"),
            // 聊天的容器
            $chatPanel = router.$(".chat-panel");

        // 绑定发送按钮
        $btnSend.on("click",function (e) {
            var val = $chatInput.val();
            //var tpl = chatTpl(val);
            if( !$(this).hasClass("disabled") ){
                //$chatPanel.append(tpl);
				postmsg(val);
                //$chatInput.val('');
                $(this).removeClass("primary").addClass("disabled");
            }else{
                return false;
            }
        });
		
		 $chatInput.click(function(){
			if(typeof(userinfo.uid)=='undefined'){
				userinfo = window.store.get('userinfo');
			}
			if(userinfo.uid<1){
				layer.confirm("你还没登录，不能发言，是否立即登录？",{btn:['立即登录','取消'],title:"提示"},function () {
					window.location.href = '/index.php/index/login/index.html?fromurl='+encodeURIComponent(window.location.href);
				});
			}
		 });

        // 延迟监听输入
        $chatInput.on("input",bui.unit.debounce(function () {
            var val = $chatInput.val();
            if( val ){
                $btnSend.removeClass("disabled").addClass("primary");

            }else{
                $btnSend.removeClass("primary").addClass("disabled");

            }
        },100))

        var interval = null;
        var count = 3;
        // 安卓键盘弹出的时间较长;
        var time = bui.platform.isIos() ? 200 : 400;
        // 为input绑定事件
        $chatInput.on('focus', function () {

            var agent = navigator.userAgent.toLowerCase();
            interval = setTimeout(function() {
                if (agent.indexOf('safari') != -1 && agent.indexOf('mqqbrowser') == -1 &&
                    agent.indexOf('coast') == -1 && agent.indexOf('android') == -1 &&
                    agent.indexOf('linux') == -1 && agent.indexOf('firefox') == -1) {
                    //safari浏览器
                    window.scrollTo(0, 1000000);
                    setTimeout(function() {
                        window.scrollTo(0, window.scrollY - 45);
                    }, 50)

                } else {
                    //其他浏览器
                    window.scrollTo(0, 1000000);
                }

            }, time);
        }).on('blur', function () {
            if( interval ){
                clearTimeout(interval);
            }

            var agent = navigator.userAgent.toLowerCase();
            interval = setTimeout(function() {
                if (!(agent.indexOf('safari') != -1 && agent.indexOf('mqqbrowser') == -1 &&
                        agent.indexOf('coast') == -1 && agent.indexOf('android') == -1 &&
                        agent.indexOf('linux') == -1 && agent.indexOf('firefox') == -1)) {
                        //safari浏览器
                    window.scrollTo(0, 30);
                }
            }, 0);
        });
    }

	var num = ck_num = 0;
	//刷新会话用户中有没有新消息
	function check_new_showmsg(obj){

		if( typeof(obj)=='object' && typeof(obj.data)=='object' && obj.data.length>0 ){		//服务端推数据, 即被动获取数据
			var res = obj;
			//var that = router.$('#chat_win');
			need_scroll = true;
			
			var h2 =router.$("#chat_win").height()-router.$("#chat_main").height()+60-router.$("#chat_win").parent().scrollTop();
			if(res.data[0].uid!=my_uid && show_msg_page>2 && h2>500 ){

				need_scroll = false;	//查看历史消息,就不滚动刷新,自己发布的话,就强制滚动刷新
			
			}else if(router.$("#chat_win .bui-box-align-top").length>20){	//大于20条就自动清屏

				$('#chat_win .bui-box-align-top').each(function(i){
					if(i>10){
						if($(this).next().next().hasClass("bui-box-center")){	//时间
							$(this).next().next().remove();
						}
						if($(this).next().hasClass("show_username")){	//用户名
							$(this).next().remove();
						}
						$(this).remove();
						console.log('清除了第'+i+'条');
					}
				});
				show_msg_page = 2;
			}

			add_msg_data(res,'new');
			//if(typeof(res.ext)!='undefined')maxid = res.ext.maxid;	//不主动获取数据的话,这个用不到
			
			//当前页面可以这样使用 load_data.xxxx=function(o){} 子窗口可以这样使用 parent.load_data.xxxx=function(o){}
			for(var index in load_data){
				load_data[index](res,'cknew');
			}

		}else{	//客户端拉数据, 主动获取数据   ******已经弃用********
			$.get(getShowMsgUrl+"1&maxid="+maxid+"&uid="+uid+"&num="+num+"&my_uid="+my_uid,function(res){			
				if(res.code!=0){				
					layer.alert('页面加载失败,请刷新当前网页');
					return ;
				}
				num++;
				ck_num = num;
				if(res.data.length>0){	//有新的聊天内容
					//layer.closeAll();
					need_scroll = true;
					//vues.set_data(res.data);
					add_msg_data(res,'new');
				}
				maxid = res.ext.maxid;
				if(res.ext.lasttime<3){	//3秒内对方还在当前页面的话,就提示当前用户不要关闭当前窗口
					if(uid>0){
						router.$("#remind_online").html("对方正在输入中，请稍候...");
					}else{
						router.$("#remind_online").html("有用户在线");
					}
					router.$(".header_content").show();router.$("#remind_online").show();bui.init();
				}else{
					router.$("#remind_online").hide();bui.init();
				}

				//当前页面可以这样使用 load_data.xxxx=function(o){} 子窗口可以这样使用 parent.load_data.xxxx=function(o){}
				for(var index in load_data){
					load_data[index](res,'cknew');
				}
			});
			ck_num++;
		}
	}
	

	//加载更多的会话记录
	function showMoreMsg(uid){
		if(show_msg_page==1){
			//maxid = -1;
			var loadIndex = layer.msg("数据加载中,请稍候...");
		}else{
			var loadIndex = layer.load(3,{shade: [0.1,'#333'],time:9000});
		}
		$.get(getShowMsgUrl+show_msg_page+"&uid="+uid+"&my_uid="+my_uid,function(res){			//console.log(res);
			if(res.code==0){
				var that = router.$('#chat_win');

				if(show_msg_page==1){					
					load_first_page(res);					
				}else if(res.data.length>0){
					that.append("<div style='border-top:1px solid #ddd;border-bottom:1px solid #ddd;text-align:center;padding:5px;'>第"+show_msg_page+"页</div>");
					var old_height = that.height();
				}
				if(res.data.length<1){
					layer.close(loadIndex);
					if(show_msg_page==1){
						layer.msg("没有任何聊天记录！",{time:1000});
					}else{
						layer.msg("已经显示完了！",{time:1000});
					}
				}else{
					add_msg_data(res);

					if(show_msg_page>1 && res.data.length>0){
						setTimeout(function(){
							layer.close(loadIndex);
							var new_height = that.height();
							var top = new_height - old_height;
							that.parent().scrollTop(top);
							if( res.data.length>0 ){
								msg_scroll = true;
							}
						},500);
					}else{
						layer.close(loadIndex);
						msg_scroll = true;
					}
					show_msg_page++;
				}				
			}else{
				layer.close(loadIndex);
				layer.msg(res.msg,{time:2500});
			}
		});
	}

	//添加删除信息的功能按钮
	function content_add_btn(res,type){
		router.$(".chat-panel .del").off("click");
		router.$(".chat-panel .del").click(function(){
			var id = $(this).data("id");
			var that = $(this);
			//通知其它人也要一起删除
			ws_send({
				type:'qun_to_alluser',
				tag:'delete_msg',
				data:{
					id:id,
				},
			});
			if(pushIdArray[id]!=undefined){
				id = pushIdArray[id];
			}
			$.get("/member.php/member/wxapp.msg/delete.html?id="+id,function(res){
				if(res.code==0){
					layer.msg("删除成功");
					router.$(".chat-box-"+id).hide();
				}else{
					layer.alert(res.msg);
				}
			});
		});

		router.$(".chat-panel .chat-icon").click(function(){
			router.$(".chatbar").hide();
			setTimeout(function(){
				router.$(".bui-mask").click(function(){
					router.$(".chatbar").show();
				});
			},500);
			touser.uid = $(this).data('uid');
			touser.name = $(this).data('name');
			console.log(touser);
		});

		format_nickname();	//设置圈子昵称

		for(var index in format_content){
			if(typeof(format_content[index])=='function'){
				format_content[index](res,type);
				format_content_have_run[index] = true;
			}
		}

		if( typeof(api)=='object' ){	//APP中,把附件加上网址			
			$("#chat_win").find("img").each(function(){
				var url = $(this).attr('src');
				if(url.indexOf('/public/uploads/')==0){
					$(this).attr('src',web_url+url);
				}
			});
		}
		
		//设置用户菜单
		bui.actionsheet({
					trigger: ".chat-icon",
					opacity:"0.5",
					buttons: [{ name: "@TA", value: "1" }, { name: "与TA私聊", value: "2" }, { name: "加TA为好友", value: "3" }, { name: "访问TA的主页", value: "4" }],
					callback: function(e) {						
						var ui = this;
						var val = $(e.target).attr("value");
						switch (val) {
							case "1":
								router.$(".chatInput").val("@"+touser.name+" ").focus();
								router.$(".chatbar").show();
								ui.hide();
								break;
							case "2":
								ui.hide();
								bui.load({url: "/public/static/libs/bui/pages/chat/chat.html",param: {"uid": touser.uid}});
								break;
							case "3":
								ui.hide();
								router.$(".chatbar").show();
								add_friend(touser.uid);
								break;
							case "4":
								ui.hide();
								var url = '/member.php/home/'+touser.uid+'.html';
								bui.load({url: "/public/static/libs/bui/pages/frame/show.html",param: {"url":url,"title":touser.name+"的主页"}});
								break;
							case "cancel":
								ui.hide();
								router.$(".chatbar").show();
								break;
						}
					}
				});
	}

	function add_friend(uid){
		if(uid==userinfo.uid){
			layer.alert('你不能加自己为好友');
		}
		$.get("/member.php/member/wxapp.friend/act.html?type=add&uid="+uid,function(res){
			if(res.code==0){
				layer.msg(res.msg);
			}else{
				layer.alert(res.msg);
			}
		});
	}
	

	//设置当前聊天的用户名
	function set_user_name(uid){
		if(uid>0){
			$.get("/index.php/index/wxapp.member/getbyid.html?uid="+uid,function(res){
				if(res.code==0){
					router.$("#send_user_name").html(res.data.username);
				}
			});
		}else if(uid<0){
			$.get("/index.php/qun/wxapp.qun/getbyid.html?id="+Math.abs(uid),function(res){
				if(res.code==0){
					router.$("#send_user_name").html(res.data.title);
				}
			});
		}else{
			router.$("#send_user_name").html("系统消息");
		}		
	}
	
	
	function add_msg_data(res,type){
		var timer = setInterval(function(){
			if(typeof(userinfo.uid)=='undefined'){
				userinfo = window.store.get('userinfo');
			}
			if(typeof(userinfo.uid)!='undefined'){
				clearInterval(timer);
				var myid = typeof(my_uid)!='undefined'?my_uid:userinfo.uid;
				//console.log(res.data);
				format_msgdata_tohtml(res.data , myid , type);
				content_add_btn(res,type);
			}
			//console.log('userinfo=',userinfo);
		},500);	//定时器,为了反复刷新用户登录数据 ,用户数据登录数据如果加载不了, 可能会白屏
	}

	function format_msgdata_tohtml(array , myid , type){
		var str = '';
		var del_str = '';
		var user_str = '';
		var userdb = userinfo;
		var old_html = router.$("#chat_win").html();
		var d_url = typeof(api)=='undefined'?'/':'';
		array.forEach((rs)=>{
			if(old_html.indexOf( ' chat-box-'+rs.id )>0){
				console.log('有重复的消息'+rs.id);
				return true;
			}
			del_str = '';
			if((userdb.uid>0 && (rs.uid==userdb.uid || rs.touid==userdb.uid)) || (uid<0 && my_uid==quninfo.uid) ){
				del_str = `<i data-id="${rs.id}" class="del glyphicon glyphicon-remove-circle"></i>`;
			}
			
			if(typeof(web_url)!='undefined'){
				rs.content = (rs.content).replace(/ src=('|")\/public\/static\//g," src=$1public/static/");
				rs.content = (rs.content).replace(/ src=('|")\/public\/uploads\//g," src=$1"+web_url+"/public/uploads/");
			}			

			user_str = `<div class="chat-icon" data-uid="${rs.uid}" data-name="${rs.from_username}"><img src="${rs.from_icon}" onerror="this.src='${d_url}public/static/images/noface.png'" title="${rs.from_username}"></div>`;
			if(rs.uid!=myid){
				str += `
					<div class="bui-box-align-top chat-box-${rs.id} chat-target">
						${user_str}
						<div class="span1">
							<div class="chat-content bui-arrow-left">${rs.content}</div>
							${del_str}
						</div>
					</div>	`;
			}else{
				str += `
					<div class="bui-box-align-top chat-box-${rs.id} chat-mine">
					<div class="span1">
						<div class="bui-box-align-right">
						  ${del_str}
						  <div class="chat-content bui-arrow-right">${rs.content}</div>
						</div>
					</div>
					${user_str}
				</div>`;
			}

			if(rs.qun_id>0 && rs.uid!=myid){
				str += `<div class="show_username chat-box-${rs.id}" data-uid="${rs.uid}">${rs.from_username}</div>`;
			}

			str += `
					<div class="bui-box-center chat-box-${rs.id}">
						<div class="time">${rs.create_time}</div>
					</div>`;
		});
		if(type == 'new'){
			router.$("#chat_win").prepend(str);
		}else{
			router.$("#chat_win").append(str);
		}
		
		//对聊天中的链接地址做框架访问
		router.$(".chat-panel .chat-content a").each(function(){
			$(this).removeClass('iframe');
			$(this).addClass('iframe');
		});

		if(show_msg_page==2 || need_scroll==true){
			setTimeout(function(){
				router.$('#chat_win').parent().scrollTop(20000);
			},300);
			need_scroll = false;
		}else{
			router.$('#chat_win').parent().scrollTop(400);
		}		
		//msg_scroll = 1;
	}

	


	
	//获取所有成员信息
	pageview.get_user_list = function(id){
		$.get("/index.php/qun/wxapp.member/get_member.html?id="+id+"&rows=1000&order=update_time&get_username=0",function(res){
			if(res.code==0){
				res.data.forEach((rs)=>{
					user_list[rs.uid] = rs;
				});
				user_num = res.data.length;
				format_nickname();				
			}
		});
	}

	function format_nickname(){
		if(uid>0 || user_num<1){
			return ;
		}
		router.$("#chat_win .show_username").each(function(){
			var _uid = $(this).data('uid');
			if(typeof(user_list[_uid]) == 'object' && typeof(user_list[_uid].nickname)!='undefined' && user_list[_uid].nickname!=''){
				$(this).html(user_list[_uid].nickname);
			}
		});
	}
	

	
	var getParams = bui.getPageParams();
    getParams.done(function(result){
		console.log("当前用户ID-"+result.uid);
		if(result.uid!=undefined){
			qid = uid = result.uid;

			msg_sys = result.msg_sys!=undefined?result.msg_sys:'';
			msg_id = result.msg_id!=undefined?result.msg_id:'';
			if (my_uid<1 && result.my_uid!=undefined) {
				my_uid = result.my_uid;
			}
			getShowMsgUrl = "/index.php/index/wxapp.msg/get_more.html?rows=15&msg_sys="+msg_sys+"&msg_id="+msg_id+"&my_uid="+my_uid+"&page=";

			//vues.set_id(uid);
			//head_menu(uid);	//设置菜单
			show_msg_page = 1; //重新恢复第一页
			msg_scroll = true; //恢复可以使用滚动条			
			showMoreMsg(uid);	//加载相应用户的聊天记录
			if(uid<0){	//群聊,获取群信息
				pageview.get_user_list(-uid);
			}else{	//私聊
				console.log("当前用户UID-"+uid);
				set_user_name(uid);	//设置当前会话的用户名
			}
		}else{
			getShowMsgUrl =  "/index.php/index/wxapp.msg/get_more.html?rows=15&page=";	//显示内容页信息的时候要用到
		}
    })

    // 初始化
    pageview.init();

    // 输出模块
    return pageview;

})



/*
document.addEventListener('visibilitychange',function() {
	  var hiddenTime=0;
        if(document.visibilityState=='hidden') {
			  hiddenTime = new Date().getTime()	//记录页面隐藏时间
        }else{
           var visibleTime = new Date().getTime();
			alert('隐藏时间'+(visibleTime-hiddenTime)/1000)
        }
 })
 */