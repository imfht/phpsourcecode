var WS = function () {	//类开始

	var my_uid = 0 ;	//当前访客UID,游客的话是8位以上数字,登录用户的话是8位以下数字
	var is_login = false;
	var guest_id = 0;  //游客UID , 登录用户的话为0
	var uid = 0;	//对方UID,或客服UID,或圈子负数ID
	var kefu = 0;  //客服UID
	var userinfo = {}; //当前访客用户信息
	var quninfo = {};

	var msg_id=0,msg_sys=0;
	var member_array = [];
	//建立WebSocket长连接
	var chat_timer,clientId = '';
	//var pushIdArray = [];
	var connect_handle;
	var w_s,ws_url='wss://sock.soyixia.net/'; //'wss://x1.soyixia.net:8282/'

	//WebSocket下发消息的回调接口,当前页面可以这样使用 ws_onmsg.xxxx=function(o){} 子窗口可以这样使用 parent.ws_onmsg.xxxx=function(o){}
	var ws_onmsg = {};

	function ws_connect(){
		if(typeof(w_s)=='object'){	//强制重新建立新的连接
			console.log("#########被强制断开了,重新发起连接!!!!!!!!!!"+Math.random());
			//$("#remind_online").hide();
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
			}, 500 );
		}else{
			ws_link();
		}
	}

	//发送WebSocket信息
	function ws_send(o,getcid){
		if(typeof(w_s)=='undefined'){ //解决还没开始连接就请求发送的情况.
			wait_connect(o,getcid);
		}else if(w_s.readyState==1){		
			w_s.send( get_msg(o,getcid) );
		}else{
			ws_connect(); //已断开,重新发起一次链接
			var index = layer.msg('连接已断开,正在重新发起链接,请稍候...');
			wait_connect(o,getcid);
		}
		function wait_connect(o,getcid){
			var k = 'require_senmsg';
			if(typeof(o)=='object'){
				if(o.type!=undefined){
					k+=o.type;
				}
				if(o.tag!=undefined){
					k+=o.tag;
				}
			}
			ws_onmsg[k] = function(obj){	//收到消息时候的回调
				ws_onmsg[k] = null;		//这一行不能缺少,不然会进入死循环
				setTimeout(function(){	//避免跟注册信息同时发送
					w_s.send( get_msg(o,getcid) );
				},200);
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
		clientId = '';
		w_s = new WebSocket(ws_url);

		w_s.onmessage = function(e) {
			var obj = {};
			try {
				obj = JSON.parse(e.data);
			}catch(err){
				console.log(err);
			}
			//当前页面可以这样使用 ws_onmsg.xxxx=function(o){} 子窗口可以这样使用 parent.ws_onmsg.xxxx=function(o){}
			for(var index in ws_onmsg){
				if(typeof(ws_onmsg[index])=='function'){
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
			
		if(typeof(chat_timer)!='undefined'){
			clearInterval(chat_timer);
		}
		chat_timer = setInterval(function() {
			if(w_s.readyState!=1){	//不处于正常连接状态
				ws_connect();
			}else{
				w_s.send('{"type":"refreshServer"}');
			}			
		}, 1000*50);	//50秒发送一次心跳
	}

	ws_onmsg.sys = function(obj){
		if(obj.type=='newmsg'){	//其它地方推送消息过来,非在线群聊
			//if( (obj.data[0].qun_id>0 && uid==-obj.data[0].qun_id) || (obj.data[0].uid==uid||obj.data[0].touid==uid) ){
				//check_new_showmsg(obj);	//推数据
				//$.get("/index.php/index/wxapp.msg/update_user.html?uid="+uid+"&id="+obj.data[0].id,function(res){//更新记录
				//	console.log(res.msg);
				//});	
			//}
		}else if(obj.type=='new_msg_id'){	//圈子直播文字最后得到的真实ID
			//pushIdArray[obj.data.push_id] = obj.data.id; //删除内容的时候要用到
			//$.get("/index.php/index/wxapp.msg/update_user.html?uid="+uid+"&id="+obj.data.id,function(res){//更新记录
			//	console.log(res.msg);
			//});
		}else if(obj.type=='qun_sync_msg'){	//圈子直播文字  
			//check_new_showmsg(obj);
		}else if(obj.type=='connect'){	//建立链接时得到客户的ID
			clientId = obj.client_id;

			if(uid==0){
				return ;
			}else{
				$.get("/index.php/index/wxapp.msg/bind_group.html?uid="+uid+"&client_id="+clientId,function(res){	//绑定用户
				if(res.code==0){
						//layer.msg('欢迎到来!',{time:500});
					}else{
						layer.msg(res.msg);
					}
				});
			}
			
			var username = (my_uid>0&&userinfo.username) ? userinfo.username : '';
			var icon = (my_uid>0&&userinfo.icon) ? userinfo.icon : '';
			var is_quner = my_uid==quninfo.uid ? 1 : 0;	//是否圈主
			var is_kefu = KF.kefu_list[my_uid] ? 1 : 0;	//是否客服
			w_s.send('{"type":"connect","url":"'+window.location.href+'","uid":"'+uid+'","kefu":"'+kefu+'","my_uid":"'+my_uid+'","is_quner":"'+is_quner+'","is_kefu":"'+is_kefu+'","userAgent":"'+navigator.userAgent+'","my_username":"'+username+'","my_icon":"'+icon+'"}');
		}else if(obj.type=='count'||obj.type=='goin'){  //用户连接成功后,算出当前在线数据统计
			//show_online(obj,'goin');
		}else if(obj.type=='leave'){	//某个用户离开了
			//show_online(obj,'getout')
			//console.log(obj);
		}else if(obj.type=='give_online_user'){  //服务器给出在线用户数据
			//show_online(obj,'show')
		}else if(obj.type=='msglist'){	//需要更新列表信息
			//console.log("消息列表,有新消息来了..........");
			//console.log(e.data);
			//obj.uid==uid即本圈子提交数据(或者自己正处于跟他人私聊),不用更新列表, obj.uid它人私信自己,就要更新,obj.uid是其它圈子也要更新
			//if( (obj.uid<0 && obj.uid!=uid) || (obj.uid==my_uid && obj.from_uid!=uid ) ){
			//if( obj.uid==my_uid && obj.from_uid!=uid ){
				//check_list_new_msgnum();
			//}
		}else if(obj.type=='kehu_getout'||obj.type=='kehu_goin'||obj.type=='have_new_msg'){	//访客离开或进入.只有客服才能看得到 收到消息的话,不仅仅是客服.会员之间也可以
			let u_id = parseInt(obj.data.user_id>0?obj.data.user_id:obj.data.ip);
			member_array[u_id] = {
				uid: u_id,
				name: obj.data.user_name,
				icon: obj.data.user_icon ? obj.data.user_icon : '',
			};
		}
	}


	//发送消息
	var allowsend = true;
	function postmsg(cnt,callback){
		if(!my_uid){
			layer.alert("请先登录!!");
			return false;
		}
		var content_obj = {};
		if(typeof(cnt)=='object'){
			content_obj = cnt;		
		}else{
			var content = (typeof(cnt)=='string' && cnt!='') ? cnt : $(".msgcontent").val();
			if(content==''){
				layer.alert('消息内容不能为空');
				return ;
			}
			content_obj.content = content;
		}
		content_obj.ext_id = content_obj.ext_id ? content_obj.ext_id : msg_id;
		content_obj.ext_sys = content_obj.ext_sys ? content_obj.ext_sys :msg_sys;
		
		if(allowsend == false){
			layer.alert('请不要重复发送信息');
			allowsend = true;
			return ;
		}
		$(".msgcontent").val('');
		allowsend = false;
		content_obj.push_id = (Math.random()+'').substring(2);
		content_obj.uid = content_obj.uid ? content_obj.uid : uid;

		if(typeof(msg_from)=='string'&&msg_from!=''){
			content_obj.content += msg_from; //消息来源于哪个页面
			$.cookie("msg_from_string", msg_from, { expires: 60, path: '/' });
			msg_from='';
		}
		
		//发送给WS服务器
		ws_send({
			type:'qun_sync_msg',
			data:content_obj,
		});
		
		//发送给自己的WEB服务器
		content_obj.my_uid = my_uid;
		$.post("/member.php/member/wxapp.msg/add.html",content_obj,function(res){
			allowsend = true;
			if(res.code==0){				
				//layer.msg('发送成功',{time:500});
				//$("#hack_wrap").hide(100);
			}else{
				//$(".msgcontent").val(content);
				layer.msg('本条信息已发出，在线会员都能看，但后面来的人看不到，因为没有入库，<br>原因：'+res.msg,{time:5000});
			}
			if(typeof(callback)=='function'){
				callback(res);
			}
		});
	}

	function init(o){
		if(o.ws_url)ws_url = o.ws_url;		
		if(o.kefu!=undefined)kefu = o.kefu;
		if(o.uid){
			uid = o.uid;
		}else if(o.kefu){
			uid = o.kefu;
		}		
		if(o.my_uid)my_uid = o.my_uid;
		if(o.userinfo)userinfo = o.userinfo;
		if(o.quninfo)quninfo = o.quninfo;
		if(o.kefu_info)member_array[kefu] = o.kefu_info;
		if(my_uid>0 && my_uid<9999999)is_login=true;
	}

	return {
		init:function(o){
			init(o);
		},
		link:function(o){
			init(o);
			ws_connect();
		},
		onmsg:function(callback,keyname){
			var str = keyname ? keyname : 't' + new Date().getTime();	//keyname参数的存在,为的是方便用户清除指定的消息处理方式
			ws_onmsg[str] =function(obj){
				callback(obj);
			}
		},
		uid:function(v){
			if(v!=undefined){
				uid = v;
			}else{
				return uid;
			}		
		},
		my_uid:function(v){
			if(v!=undefined){
				my_uid = v;
			}else{
				return my_uid;
			}		
		},
		userinfo:function(v){
			if(v!=undefined){
				userinfo = v;
			}else{
				return userinfo;
			}		
		},
		quninfo:function(v){
			if(v!=undefined){
				quninfo = v;
			}else{
				return quninfo;
			}		
		},
		kefu:function(v){
			if(v!=undefined){
				kefu = v;
			}else{
				return kefu;
			}		
		},
		guest_id:function(v){
			if(v!=undefined){
				guest_id = v;
			}else{
				return guest_id;
			}		
		},
		is_login:function(v){
			return is_login;	
		},
		postmsg:function(cnt,callback){
			postmsg(cnt,callback);
		},
		user_db:function(uid,info){
			if(info){
				member_array[uid] = info;
			}else{
				return member_array[uid];
			}			
		},
		send:function(o,getcid){
			ws_send(o,getcid)
		}
	};


}();//类结束 










//定义websocket接收到的消息做处理
WS.onmsg(function(obj){
	if(obj.type=='count'){	//用户刚刚建立链接时,不仅仅是自己.还可以是其它人
		if(KF.kefu_list.length<1){
			return ; //非客服功能
		}
		if(KF.first_load==true){
			KF.first_load = false;
			if( WS.my_uid()<1 ){	//未登录游客,刚刚建立WS连接
				WS.guest_id(obj.ip);
				WS.my_uid(obj.ip);
			}			
			if( WS.kefu()>0 && WS.my_uid()!=WS.kefu() ){
				KF.welcome_msg(obj.data[0].username);
			}
		}
		//if( obj.kefu_online==1 || WS.my_uid()==WS.kefu() ){
		if( obj.online_kefus.length>0 ){
			$(".kefu_warp").removeClass("kefu_warp_offline");
			$(".kefu_warp").addClass("kefu_warp_online");				
		}else{
			$(".kefu_warp").removeClass("kefu_warp_online");
			$(".kefu_warp").addClass("kefu_warp_offline");
		}
	}else if(obj.type=='kehu_goin'||obj.type=='kehu_getout'||obj.type=='have_new_msg'){
		if( WS.my_uid()==WS.kefu() ){
			 KF.kefu_tip(obj);
		}else if(obj.type=='have_new_msg'){
			KF.kefu_tip(obj);
		}
	}else if(obj.type=='qun_sync_msg'){
		//if( WS.my_uid()!=WS.kefu() ){
		if( WS.my_uid()!=obj.data[0].uid ){	//自己就不显示
			KF.guest_tip(obj);
		}		
	}
},'default');	//加上default参数是方便大家把这里的所有方法覆盖替换








var KF = {
	kefu_list:[],
	first_load:true,	//判断是否刚刚建立链接
	tip_open:false,		//小提示窗口打开或关闭的判断
	chat_open:false,	//会话窗口的打开或关闭的判断
	tip_msg:'',			//小提示的内容
	guest_id:0,			//最近保持通信的客户ID
	win_type:function(type){	//手机版或PC版的选择
		if ((navigator.userAgent.match(/(iPhone|iPod|Android|ios|iPad)/i))){
			return type=='url'?'/index.php/index/msg/index.html#/public/static/libs/bui/pages/chat/chat':["95%","95%"];
		}else{
			return type=='url'?'/index.php/index/msg/index.html':["800px","600px"];
		}
	},
	kefu_tip:function(obj){		//客服的提示窗口
		var key = parseInt(obj.data.user_id>0 ? obj.data.user_id : obj.data.ip);
		if( KF.chat_open==true && (KF.guest_id==0 || KF.guest_id==key) ){	//已经打开了会话窗口,就不提示了
			if(obj.type=='kehu_getout'){
				layer.alert(obj.data.user_name + ' 离开了!');
			}
			KF.guest_id = key;
			return ;		
		}
		KF.guest_id = key;
		var msg = '';
		if(obj.type=='kehu_goin'){
			msg = '<div class="goin" data-id="'+key+'"><i class="fa fa-commenting"></i> '+obj.data.user_name + ' 进来了</div>';
		}else if(obj.type=='kehu_getout'){
			msg = '<div class="getout"><i class="fa fa-sign-in"></i> '+obj.data.user_name + ' 离开了</div>';
		}else if(obj.type=='have_new_msg'){
			var content  = obj.data.msgdata.content.replace(/<\/?[^>]*>/g, '');
			msg = '<div class="send-msg" data-id="'+key+'"><div><i class="glyphicon glyphicon-hand-right"></i> '+obj.data.user_name + ' 私聊你,内容如下</div><div>'+content+'</div></div>';
		}
		KF.tip_msg += msg;
		if(!KF.tip_open){
			KF.tip_open = true;
			layer.confirm("<div class='kefu_tip_msg'>"+KF.tip_msg+"</div>", {
					title: '提示!',
					btn : [ '立即交谈', '清除记录' ],
					//time: 10000,
					offset: 'rb',
					anim:2,
					shade: 0, //不显示遮罩
					cancel:function(){
						KF.tip_open = false;
					},btn2:function(){
						KF.tip_open = false;
						KF.tip_msg = '';
					},
				}, function(index) {
					layer.close(index);
					KF.tip_open = false;
					KF.chat_win(key);
				}
			);			
		}else{
			$('.kefu_tip_msg').html(KF.tip_msg);
		}
		$(".kefu_tip_msg .goin,.kefu_tip_msg .send-msg").off("click");
		$(".kefu_tip_msg .goin,.kefu_tip_msg .send-msg").click(function(){
			//KF.tip_open = false;
			var id = $(this).data('id');
			KF.chat_win(id);
		});
	},
	chat_win:function(o,type){  //打开会话窗口, 可以被重置换其它的聊天窗口
		if(typeof(o)=='object'){
			var touid = o.uid;
		}else{
			var touid = o;
		}
		var url = '';
		if(WS.guest_id()>0){	//未登录的游客
			url = KF.win_type('url')+"?my_uid="+WS.guest_id()+"&uid="+touid;
		}else if(touid){
			url = "/member.php/member/msg/index.html?uid="+touid;
		}else{
			url = "/member.php/member/msg/index.html"
		}
		if ((navigator.userAgent.match(/(iPhone|iPod|Android|ios|iPad)/i))){
			location.href=url;
			return ;
		}
		KF.chat_open = true;		
		layer.open({
			type:2,
			maxmin:true,
			shade: 0,
			area:KF.win_type('area'),
			content: url,
			cancel:function(){							
				KF.chat_open = false;
			},
		});
	},
	guest_tip:function(obj){	//访客的提示窗口
		if(KF.chat_open==true){	//已经打开了会话窗口,就不提示了
			return ;
		}
		var content = obj.data[0].content.replace(/<\/?[^>]*>/g, '');
		var msg = obj.data[0].from_username + ' 给你发了一条新消息,内容如下<br>'+content+'<br><br>';
		KF.tip_msg += msg;
		if(!KF.tip_open){
			KF.tip_open = true;
			layer.confirm("<div class='kefu_tip_msg'>"+KF.tip_msg+"</div>", {
					title: '友情提醒!',
					btn : [ '接受交谈', '关闭'],
					//time: 10000,
					offset: 'rb',
					anim:2,
					shade: 0, //不显示遮罩
					cancel:function(){
						KF.tip_open = false;
					},btn2:function(){
						KF.tip_open = false;
					},
				}, function(index) {
					layer.close(index);
					KF.tip_open = false;
					KF.chat_win(obj.data[0].uid,'guest');
					/*
					KF.chat_open = true;
					layer.open({
						type:2,
						area:KF.win_type('area'),
						content:KF.win_type('url')+"?my_uid="+WS.guest_id()+"&uid="+obj.data[0].uid ,	//接收的消息可以不是客服,可以是会员之间
						cancel:function(){							
							KF.chat_open = false;
						},
					});
					*/
				}
			);
		}else{
			$('.kefu_tip_msg').html(KF.tip_msg);
		}	
	},
	welcome_msg:function(name){return ;
		if( $.cookie('welcome_msg') ){
			return ;
		}
		$.cookie('welcome_msg', '1', { expires: 60*24, path: '/' });	//时间间隔单位分钟
		layer.confirm("你好，来自"+name+"的朋友，<br>我是客服MM很高兴为你服务，有什么问题欢迎随时Q我。我一直在线的哦。", {
			title: '欢迎光临!',
			btn : [ '立即交谈', '稍后' ],
			time: 15000,
			offset: 'rb',
			anim:2,
			shade: 0 //不显示遮罩 
			}, function(index) {
				layer.close(index);
				KF.chat_win(WS.kefu(),'guest');
				/*
				KF.chat_open = true;
				layer.open({
					type:2,
					area:KF.win_type('area'),
					content:KF.win_type('url')+"?my_uid="+WS.guest_id()+"&uid="+WS.kefu() ,
					cancel:function(){							
						KF.chat_open = false;
					},
				});
				*/
		});
	},
	callKefu:function(){	//客服按钮
		if(WS.kefu()==WS.my_uid()){
			KF.chat_win();
		}else{
			KF.chat_win(WS.kefu());
		}
	},
};

//处理网页顶部的打开消息窗口事件
function close_msg_win(){
	KF.chat_open = false;
}
function oepn_msg_win(){
	KF.chat_open = true;
}
