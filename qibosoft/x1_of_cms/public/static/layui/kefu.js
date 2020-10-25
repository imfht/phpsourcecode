var LayIm;

//根据游客或者是登录用户获取不同的配置参数
function get_config(obj){
	//客服列表数据
	let kefu_list = [];
	KF.kefu_list.forEach(function(rs,id){
		kefu_list.push({
			id:id,
			username:rs.name,
			avatar:rs.icon?rs.icon:'/public/static/images/noface.png',
			sign:rs.sign ? rs.sign : '顾客至上，用心服务',
		});
	});
	var local = layui.data('layim')[WS.my_uid()] || {}; //本地缓存数据

	var init = {};
	if(obj && obj.init){	//登录用户获取到数据库聊天记录的情况, 游客的话,就没有从数据库获取聊天记录了
		init = obj.init;
		local.history = init.history; 
		local.spread0 = "true";
		local.spread1 = "true";
		layui.data('layim', {
			key: WS.my_uid()
			,value: local
		});
	}else{
		//这里修改缓存,只为了默认展开客服列表而已
		local.spread0 = "true";
		layui.data('layim', {
			key: WS.is_login() ? WS.my_uid() : WS.guest_id()	//注意,guest_id这个值不会马上得到,需要在WS.onmsg执行后,才有值
			,value: local
		});
		init = {
			mine: {
				username: WS.is_login() ? '会员' : '访客',
				id: WS.is_login() ? WS.my_uid() : WS.guest_id(),
				status:'online',
				sign:'欢迎你的到来!',
				avatar:'/public/static/images/noface.png',
			},
			friend: [{
				groupname:'客服列表',
				id:1,
				online:2,
				list:kefu_list,
			},{
				groupname:'交流合作',
				id:2,
				online:0,
				list:[],
			}],
			group: [],
		};
	}
	return {
		//初始化接口		
		init: init

		//查看圈子群员接口
		,members: {
			url: '/index.php/index/wxapp.layim/getMembers.html'
			,data: {}
		}
		
		//上传图片接口
		,uploadImage: WS.is_login()?{url: '/index.php/index/attachment/upload/dir/chatpic/module/chat.html'}:false 
		
		//上传文件接口
		,uploadFile: WS.is_login()?{url: '/index.php/index/attachment/upload/dir/chatfile/module/chat.html'}:false  
		
		//截图上传接口地址
		,paseImg: WS.is_login()?'/index.php/index/attachment/upload/dir/chatpic/from/base64/module/chat.html':false
		
		,isAudio: false //开启聊天工具栏音频
		,isVideo: false //开启聊天工具栏视频
			
		//扩展工具栏
		,tool: WS.is_login() ? [{alias: 'paseimg',title: '截图上传' ,icon: '&#xe64a;' }] : []
			
		//,brief: true //是否简约模式（若开启则不显示主面板）
			
		,title: '在线客服' //自定义主面板最小化时的标题
		,right: '100px' //主面板相对浏览器右侧距离
		//,minRight: '90px' //聊天面板最小化时相对浏览器右侧距离
		,initSkin: '2.jpg' //1-5 设置初始背景
		//,skin: ['aaa.jpg'] //新增皮肤
		//,isfriend: false //是否开启好友
		,isgroup: WS.is_login()?true:false //是否开启群组
		,min: true //是否始终最小化主面板，默认false
		,notice: true //是否开启桌面消息提醒，默认false
		//,voice: false //声音提醒，默认开启，声音文件为：default.mp3
			
		,msgbox:WS.is_login()?layui.cache.dir + 'css/modules/layim/html/msgbox.html':false //消息盒子页面地址，若不开启，剔除该项即可
		//,find: layui.cache.dir + 'css/modules/layim/html/find.html' //发现页面地址，若不开启，剔除该项即可
		,chatLog: layui.cache.dir + 'css/modules/layim/html/chatlog.html' //聊天记录页面地址，若不开启，剔除该项即可
	};
}


layui.use('layim', function(layim){
	
	//layim.config();	//console.log('layim','加载成功');

	//监听自定义工具栏点击，以添加代码为例
	layim.on('tool(paseimg)', function(insert, send, obj){
		layer.tips('使用QQ或者微信截图后回到聊天输入框Ctr+V粘贴即可实现截图上传', '.layim-tool-paseimg', {
		  tips: [1, '#2F4056'],
		  time: 4000
		});
	});
 
	//修改签名
	layim.on('sign', function(value){
	  $.ajax({
				url: "/index.php/index/wxapp.layim/sign.html",
				type: "POST",
				data:{sign:value},
				success: function(info) {
					layer.msg(info.msg);
				}
			});
	});

	//监听layim建立就绪
	/*
	layim.on('ready', function(res){
		if(WS.my_uid()>0 && WS.my_uid()<9999999){
			KF.kefu_list.forEach(function(rs,id){
				layim.addList({
				  type: 'friend'
				  ,avatar: rs.icon?rs.icon:'/public/static/images/noface.png'
				  ,username: rs.name
				  ,groupid: 1
				  ,id: id
				  ,remark: rs.sign ? rs.sign : '顾客至上，用心服务'
				});
			});

		}		
	});*/

	//监听发送消息
	layim.on('sendMessage', function(data){
		console.log(data);
		var To = data.to;
		if(To.type === 'friend'){
		  //layim.setChatStatus('<span style="color:#FF5722;">对方正在输入。。。</span>');
		}

		WS.postmsg({
			content:data.mine.content,
			uid:data.to.id,
		});
	});


	//监听聊天窗口的切换
	layim.on('chatChange', function(res){
		var type = res.data.type;
		uid = res.data.id;

		if(uid>0){
			$.get("/index.php/index.php/index/wxapp.layim/set_read.html?uid="+uid,function(res){}); //标注已读
		}else{
			WS.link({"uid":uid,"kefu":0});	//跟圈子建立连接通道,才能收到那边新的即时消息
		}

		if(!WS.is_login()){
			console.log('游客的话,就不获取数据的记录了' );
			return ;
		}else{
			console.log('切换了窗口,UID是',uid);
		}
		
		var str = $.cookie('layim_msg_id');
		if(str && str.indexOf(","+uid+",")>-1 && $(".layim-"+type+uid+" .layim-msg-status").html()<1){
			return ;
		}
		str = str ? str+uid+"," : ","+uid+"," ;
		$.cookie('layim_msg_id', str, { expires: 3, path: '/' });  //expires的COOKIE时间单位是分钟

		$(".layim-"+type+uid+" .layim-msg-status").html(0).hide();
		
		//var index = layer.msg("请稍候,正在加载数据...",{time:500});
		$.get("/index.php/index.php/index/wxapp.layim/get_more_msg.html?rows=20&uid="+uid,function(res){
			//layer.close(index);
			if(res.code==0){
				for(var i=res.data.length-1;i>=0;i--){
					var rs = res.data[i];
					layim.getMessage({
						username: rs.from_username
						,avatar: rs.from_icon
						,timestamp:rs.full_time*1000
						,id: uid
						,type: type //"friend"
						,mine:WS.my_uid()==rs.uid?true:false
						,content: rs.content
					});
				}
			}else{
				layer.msg('没有任何聊天记录!');
			}
		});

		if(type === 'friend'){			
		  //模拟标注好友状态
		  //layim.setChatStatus('<span style="color:#FF5722;">在线</span>');
		} else if(type === 'group'){
		  //模拟系统消息
		  /*
		  layim.getMessage({
			system: true
			,id: res.data.id
			,type: "group"
			,content: '模拟群员'+(Math.random()*100|0) + '加入群聊'
		  });*/
		}
	});
	//网页调用聊天框
 
	$('.site-send-layim').on('click', function() {
		layim.chat({
			name: $(this).data('name'),
			type: 'friend',
			avatar: $(this).data('picurl'),
			id: $(this).data('id')
		});
	});

	LayIm = layim;
});



//登录用户获取聊天记录
$(function(){
if( WS.is_login() ){
	$.get("/index.php/index/wxapp.layim/msg_user_list.html",function(res){
		if(res.code==0){
			var timer = setInterval(function() {
				if(LayIm!=undefined){
					clearInterval( timer );
					login_member_set_im(res.data);
					$('.layui-layim-tab>li[lay-type="history"]').trigger("click");
				}
				console.log('LayIm加载中...');
			},30);
		}
	});
}
});

//有聊天记录的,登录用户初始化
function login_member_set_im(data){
	LayIm.config( get_config( data ? {init: data} : {}) );
	KF.kefu_list.forEach(function(rs,id){
		LayIm.addList({
			type: 'friend'
			,avatar: rs.icon?rs.icon:'/public/static/images/noface.png'
			,username: rs.name
			,groupid: 1
			,id: id
			,remark: rs.sign ? rs.sign : '顾客至上，用心服务'
		});
	});
}
 

var is_first_load = true;
//接收各种WS的消息处理
WS.onmsg(function(obj){
	if(obj.type=='count'){
		if(is_first_load===true){	//避免反复执行
			is_first_load = false;
			if(WS.guest_id()>0){	//游客现在才获取到IP
				var timer = setInterval(function() {
					if(LayIm!=undefined){
						clearInterval( timer );
						LayIm.config( get_config() );	//初始化游客聊天窗口,登录用户的话,不在这里处理,需要查询数据库聊天记录
					}
					console.log('游客LayIm加载中...');
				},30);
			}
		}
	}else if(obj.type=='have_new_msg' || obj.type=='qun_sync_msg'){
		var data;
		if(obj.type=='qun_sync_msg'){	//兼容群聊的模式 两端直接连通的情况
			data = obj.data[0];
			if(data.uid==WS.my_uid()){
				return ;
			}
		}else{	//私信的专有模式,两端并没有直接连通
			data = obj.data.msgdata;
		}		
		LayIm.getMessage({
			username: data.from_username
			,avatar: data.from_icon==''?'/public/static/images/noface.png':data.from_icon
			,id: data.qun_id>0 ? -data.qun_id : data.uid
			,type: data.qun_id>0 ? "group" : "friend"
			,content: data.content
			,timestamp:data.full_time*1000
			,is_new:true	//需要声音提醒有新消息
      });
	}else if(obj.type=='check_online'){
		if(obj.online==1){
			LayIm.setChatStatus('<span style="color:red;">对方在线</span>');
		}else{
			LayIm.setChatStatus('<span style="color:blue;">对方不在线</span>');
		}
	}
});


//重置会话窗口
KF.chat_win = function(touid){
	if(typeof(touid)=='object'){
		var o = touid;
		var username=o.username,type=o.type||'friend',icon=o.icon||'/public/static/images/noface.png',id=o.uid;
	}else{
		if(!touid){
			$(".layui-layim-min").trigger("click");
			return ;
		}
		layui.layim.setFriendStatus(touid, 'online');
		var username,user = WS.user_db(touid)||KF.kefu_list[touid];
		if(user){
			username = user.name;
		}else if(touid==WS.kefu()){
			username = '客服MM';
		}else if(WS.my_uid()==WS.kefu()){
			username = '客户';
		}else{
			username = '网友';
		}
		var type = 'friend',icon = user&&user.icon ? user.icon : '/public/static/images/noface.png',id = touid;
	}
	LayIm.chat({
		name: username
		,type: type
		,avatar: icon
		,id: id,
	});
}
